<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Review;
use App\Services\AiManagementService;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    // Tìm kiếm và lọc nâng cao phòng trọ
    public function index(Request $request)
    {
        $query = Room::with(['building', 'reviews']);

        // Bộ lọc theo Building
        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        // Bộ lọc theo tầng
        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        // Bộ lọc giá thuê
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Bộ lọc diện tích
        if ($request->has('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }
        if ($request->has('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        // Bộ lọc trạng thái (mặc định chỉ tìm phòng trống nếu không được chỉ định)
        $status = $request->input('status', 'empty');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Bộ lọc tiện ích (amenities) - ví dụ: ['gác lửng', 'cho nuôi thú cưng']
        if ($request->has('amenities')) {
            $amenities = $request->amenities;
            if (is_string($amenities)) {
                $amenities = explode(',', $amenities);
            }
            if (is_array($amenities) && count($amenities) > 0) {
                foreach ($amenities as $amenity) {
                    $query->whereJsonContains('amenities', trim($amenity));
                }
            }
        }

        $rooms = $query->get();

        // Mapped dữ liệu để trả về đẹp mắt cho frontend
        $result = $rooms->map(function ($room) {
            $ratingAvg = $room->reviews->avg('rating') ?: 0;
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => $room->price,
                'area' => $room->area,
                'amenities' => $room->amenities,
                'description' => $room->description,
                'building' => [
                    'id' => $room->building->id,
                    'name' => $room->building->name,
                    'address' => $room->building->address,
                ],
                'rating_avg' => round($ratingAvg, 1),
                'reviews_count' => $room->reviews->count()
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $result->count(),
            'rooms' => $result
        ]);
    }

    // Bản đồ trực quan: trả về danh sách Marker phòng kèm tọa độ toà nhà (giả lập tọa độ địa lý)
    public function map(Request $request)
    {
        $rooms = Room::with('building')->where('status', 'empty')->get();

        $markers = $rooms->map(function ($room) {
            // Giả lập toạ độ dựa trên id của building để hiển thị trên bản đồ trực quan
            $lat = 21.036 + (($room->building->id * 7) % 100) / 10000;
            $lng = 105.782 + (($room->building->id * 13) % 100) / 10000;

            return [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'price' => $room->price,
                'area' => $room->area,
                'building_name' => $room->building->name,
                'address' => $room->building->address,
                'latitude' => $lat,
                'longitude' => $lng,
            ];
        });

        return response()->json([
            'success' => true,
            'markers' => $markers
        ]);
    }

    // Đọc đánh giá của một phòng trọ
    public function reviews($roomId)
    {
        $room = Room::with(['reviews' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->find($roomId);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'room_number' => $room->room_number,
            'rating_avg' => round($room->reviews->avg('rating') ?: 0, 1),
            'reviews_count' => $room->reviews->count(),
            'reviews' => $room->reviews
        ]);
    }

    public function reviewSummary($roomId, AiManagementService $aiManagementService)
    {
        $room = Room::with(['building', 'reviews' => function ($query) {
            $query->latest()->take(30);
        }])->find($roomId);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Phong khong ton tai',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'summary' => $aiManagementService->summarizeReviews([
                'room_number' => $room->room_number,
                'price' => (int) $room->price,
                'area' => (int) $room->area,
                'building' => $room->building->name ?? null,
                'address' => $room->building->address ?? null,
                'rating_avg' => round($room->reviews->avg('rating') ?: 0, 1),
                'reviews_count' => $room->reviews->count(),
            ], $room->reviews->map(fn (Review $review) => [
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'author_name' => $review->author_name,
            ])->values()->all()),
        ]);
    }

    // Viết đánh giá (Yêu cầu xác thực tài khoản)
    public function storeReview(Request $request, $roomId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5'
        ]);

        $room = Room::find($roomId);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng không tồn tại'
            ], 404);
        }

        $user = Auth::user();

        $review = Review::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'author_name' => $user->name,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi đánh giá thành công',
            'review' => $review
        ], 201);
    }

    // So sánh phòng (Nhận tối thiểu 2, tối đa 3 phòng theo spec ERR_23_01)
    public function compare(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array|min:2|max:3',
            'room_ids.*' => 'exists:rooms,id'
        ], [
            'room_ids.required' => 'Vui lòng cung cấp danh sách ID phòng so sánh.',
            'room_ids.min' => 'Vui lòng chọn ít nhất 2 phòng để tiến hành so sánh đối đầu.',
            'room_ids.max' => 'Bạn chỉ có thể so sánh đối đầu tối đa 3 phòng cùng lúc.'
        ]);

        $rooms = Room::with(['building', 'reviews'])->whereIn('id', $request->room_ids)->get();

        $minPrice = $rooms->min('price');
        $maxArea = $rooms->max('area');
        $maxRating = $rooms->map(fn ($r) => (float) ($r->reviews->avg('rating') ?: 5.0))->max();

        $comparison = $rooms->map(function ($room) use ($minPrice, $maxArea, $maxRating) {
            $ratingAvg = round((float) ($room->reviews->avg('rating') ?: 5.0), 1);
            $area = (float) ($room->area ?: 20.0);
            $price = (int) $room->price;
            $pricePerM2 = $area > 0 ? (int) round($price / $area) : 0;

            // Tính toán chi phí định kỳ (80 số điện + 4 khối nước + 150k dịch vụ chuẩn)
            $elecPrice = (int) ($room->electricity_price ?? 3500);
            $waterPrice = (int) ($room->water_price ?? 25000);
            $serviceFee = 150000;
            $estMonthlyTotal = $price + (80 * $elecPrice) + (4 * $waterPrice) + $serviceFee;

            $amenities = is_array($room->amenities) ? $room->amenities : [];
            $amenitiesStr = mb_strtolower(implode(' ', $amenities));

            $hasWc = (bool) ($room->has_wc ?? (str_contains($amenitiesStr, 'wc') || str_contains($amenitiesStr, 'khép kín')));
            $hasBalcony = (bool) ($room->has_balcony ?? str_contains($amenitiesStr, 'ban công'));
            $hasLoft = (bool) ($room->has_loft ?? (str_contains($amenitiesStr, 'gác') || str_contains($amenitiesStr, 'gác lửng')));
            $allowPets = (bool) ($room->allow_pets ?? (str_contains($amenitiesStr, 'thú cưng') || str_contains($amenitiesStr, 'pet')));
            $hasAc = str_contains($amenitiesStr, 'điều hòa') || str_contains($amenitiesStr, 'máy lạnh');
            $hasHeater = str_contains($amenitiesStr, 'nóng lạnh');
            $hasElevator = str_contains($amenitiesStr, 'thang máy');
            $hasLock = str_contains($amenitiesStr, 'khóa vân tay') || str_contains($amenitiesStr, 'vân tay');

            // Tính điểm số Radar Chart (0 - 100)
            $priceScore = max(20, min(100, (int) round(100 - (($price - 1500000) / 6500000) * 80)));
            $areaScore = max(25, min(100, (int) round(($area / 45) * 100)));
            $ratingScore = max(20, min(100, (int) round(($ratingAvg / 5) * 100)));
            $amenityCount = collect([$hasWc, $hasBalcony, $hasLoft, $allowPets, $hasAc, $hasHeater, $hasElevator, $hasLock])->filter()->count();
            $amenityScore = max(20, min(100, (int) round(($amenityCount / 8) * 100)));
            $economicScore = max(20, min(100, (int) round(100 - (($estMonthlyTotal - 2000000) / 8000000) * 80)));

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => $price,
                'price_formatted' => number_format($price, 0, ',', '.') . ' đ',
                'price_per_m2' => $pricePerM2,
                'price_per_m2_formatted' => number_format($pricePerM2, 0, ',', '.') . ' đ/m²',
                'area' => $area,
                'area_formatted' => $area . ' m²',
                'deposit' => (int) ($room->deposit ?? $price),
                'deposit_formatted' => number_format((int) ($room->deposit ?? $price), 0, ',', '.') . ' đ',
                'electricity_price' => $elecPrice,
                'electricity_formatted' => number_format($elecPrice, 0, ',', '.') . ' đ/kWh',
                'water_price' => $waterPrice,
                'water_formatted' => number_format($waterPrice, 0, ',', '.') . ' đ/khối',
                'estimated_monthly_total' => $estMonthlyTotal,
                'estimated_monthly_formatted' => number_format($estMonthlyTotal, 0, ',', '.') . ' đ',
                'cover_image' => $room->cover_image ?? '/images/room-placeholder.jpg',
                'amenities' => $amenities,
                'description' => $room->description,
                'building_name' => $room->building->name ?? 'Tòa nhà',
                'address' => $room->building->address ?? 'Đang cập nhật',
                'rating_avg' => $ratingAvg,
                'reviews_count' => $room->reviews->count(),
                'badges' => [
                    'best_price' => $price === $minPrice,
                    'largest_area' => $area === $maxArea,
                    'top_rated' => $ratingAvg === $maxRating,
                ],
                'amenities_checklist' => [
                    'wc_private' => $hasWc,
                    'balcony' => $hasBalcony,
                    'loft' => $hasLoft,
                    'pets' => $allowPets,
                    'air_conditioner' => $hasAc,
                    'water_heater' => $hasHeater,
                    'elevator' => $hasElevator,
                    'fingerprint_lock' => $hasLock,
                ],
                'radar_scores' => [
                    'price' => $priceScore,
                    'area' => $areaScore,
                    'amenity' => $amenityScore,
                    'rating' => $ratingScore,
                    'economic' => $economicScore,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $comparison->count(),
            'comparison' => $comparison
        ]);
    }

    // AI Phân tích so sánh phòng chuyên sâu (Google Gemini RAG + Fallback)
    public function compareAi(Request $request, AiManagementService $aiService)
    {
        $request->validate([
            'room_ids' => 'required|array|min:2|max:3',
            'room_ids.*' => 'exists:rooms,id'
        ], [
            'room_ids.min' => 'Vui lòng chọn ít nhất 2 phòng để AI tiến hành phân tích đối chiếu.',
            'room_ids.max' => 'AI chỉ hỗ trợ phân tích tối đa 3 phòng cùng lúc.'
        ]);

        $rooms = Room::with(['building', 'reviews'])->whereIn('id', $request->room_ids)->get();

        $insight = $aiService->compareRoomsInsight($rooms);

        return response()->json([
            'success' => true,
            'insight' => $insight
        ]);
    }
}
