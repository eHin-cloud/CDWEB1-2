<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Review;
use App\Services\AiManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Cổng tìm kiếm & Bộ lọc phòng trọ thông minh Renty (FEAT_21_SMART_SEARCH)
     * GET /api/renty/filter
     */
    public function filter(Request $request)
    {
        // 1. Kiểm tra ký tự chữ vào ô giá hoặc diện tích (ERR_21_03)
        $rawMinPrice = $request->input('min_price', $request->input('price_min'));
        $rawMaxPrice = $request->input('max_price', $request->input('price_max'));
        $rawMinArea = $request->input('min_area', $request->input('area_min'));
        $rawMaxArea = $request->input('max_area', $request->input('area_max'));

        if ($rawMinPrice !== null && $rawMinPrice !== '' && !is_numeric($rawMinPrice)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_03',
                'message' => 'Giá phòng và diện tích chỉ được nhập số.',
                'errors' => ['min_price' => ['Giá phòng và diện tích chỉ được nhập số.']]
            ], 422);
        }

        if ($rawMaxPrice !== null && $rawMaxPrice !== '' && !is_numeric($rawMaxPrice)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_03',
                'message' => 'Giá phòng và diện tích chỉ được nhập số.',
                'errors' => ['max_price' => ['Giá phòng và diện tích chỉ được nhập số.']]
            ], 422);
        }

        if ($rawMinArea !== null && $rawMinArea !== '' && !is_numeric($rawMinArea)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_03',
                'message' => 'Giá phòng và diện tích chỉ được nhập số.',
                'errors' => ['min_area' => ['Giá phòng và diện tích chỉ được nhập số.']]
            ], 422);
        }

        if ($rawMaxArea !== null && $rawMaxArea !== '' && !is_numeric($rawMaxArea)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_03',
                'message' => 'Giá phòng và diện tích chỉ được nhập số.',
                'errors' => ['max_area' => ['Giá phòng và diện tích chỉ được nhập số.']]
            ], 422);
        }

        $minPrice = ($rawMinPrice !== null && $rawMinPrice !== '') ? (float) $rawMinPrice : null;
        $maxPrice = ($rawMaxPrice !== null && $rawMaxPrice !== '') ? (float) $rawMaxPrice : null;
        $minArea = ($rawMinArea !== null && $rawMinArea !== '') ? (float) $rawMinArea : null;
        $maxArea = ($rawMaxArea !== null && $rawMaxArea !== '') ? (float) $rawMaxArea : null;

        // 2. Validate Min > Max (ERR_21_01 & ERR_21_02)
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_01',
                'message' => 'Giá tối thiểu không thể lớn hơn giá tối đa.',
                'errors' => [
                    'min_price' => ['Giá tối thiểu không thể lớn hơn giá tối đa.'],
                    'price' => ['Khoảng giá lọc không hợp lệ: Giá tối thiểu không được lớn hơn giá tối đa']
                ]
            ], 422);
        }

        if ($minArea !== null && $maxArea !== null && $minArea > $maxArea) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_21_02',
                'message' => 'Diện tích tối thiểu không thể lớn hơn diện tích tối đa.',
                'errors' => [
                    'min_area' => ['Diện tích tối thiểu không thể lớn hơn diện tích tối đa.']
                ]
            ], 422);
        }

        // Cache Tags / Key cho Redis và File Cache
        $cacheKey = 'renty_filter_' . md5(json_encode($request->all()));

        $rooms = Cache::remember($cacheKey, 60, function () use ($request, $minPrice, $maxPrice, $minArea, $maxArea) {
            $query = Room::with(['building', 'reviews']);

            // Lọc khoảng giá
            if ($minPrice !== null) {
                $query->where('price', '>=', (int) $minPrice);
            }
            if ($maxPrice !== null) {
                $query->where('price', '<=', (int) $maxPrice);
            }

            // Lọc diện tích
            if ($minArea !== null) {
                $query->where('area', '>=', $minArea);
            }
            if ($maxArea !== null) {
                $query->where('area', '<=', $maxArea);
            }

            // Loại phòng (room_type)
            if ($request->filled('room_type')) {
                $roomType = trim((string) $request->input('room_type'));
                $query->where(function ($q) use ($roomType) {
                    $q->where('room_type', $roomType)
                      ->orWhereHas('building', function ($bq) use ($roomType) {
                          $bq->where('name', 'like', "%{$roomType}%");
                      });
                });
            }

            // Trạng thái phòng (mặc định cho phép lọc hoặc lấy phòng trống)
            $status = $request->input('status', 'all');
            if ($status !== 'all') {
                $query->where('status', $status);
            }

            // Bộ lọc tiện ích (Bắt buộc phòng phải có TẤT CẢ các tiện ích đã chọn)
            if ($request->has('amenities')) {
                $amenities = $request->input('amenities');
                if (is_string($amenities)) {
                    $amenities = array_filter(array_map('trim', explode(',', $amenities)));
                }
                if (is_array($amenities) && count($amenities) > 0) {
                    foreach ($amenities as $amenity) {
                        $trimmedAmenity = trim($amenity);
                        if ($trimmedAmenity !== '') {
                            $query->where(function ($subQ) use ($trimmedAmenity) {
                                $subQ->whereJsonContains('amenities', $trimmedAmenity)
                                     ->orWhere('amenities', 'like', '%"' . $trimmedAmenity . '"%')
                                     ->orWhere('amenities', 'like', '%' . $trimmedAmenity . '%');
                            });
                        }
                    }
                }
            }

            // Lọc theo từ khóa tìm kiếm (q / keyword)
            $keyword = trim((string) $request->input('q', $request->input('keyword', '')));
            if ($keyword !== '') {
                $query->where(function ($kq) use ($keyword) {
                    $kq->where('room_number', 'like', "%{$keyword}%")
                       ->orWhere('description', 'like', "%{$keyword}%")
                       ->orWhereHas('building', function ($bq) use ($keyword) {
                           $bq->where('name', 'like', "%{$keyword}%")
                              ->orWhere('address', 'like', "%{$keyword}%");
                       });
                });
            }

            // Lọc theo đánh giá tối thiểu (rating)
            if ($request->filled('rating') && $request->input('rating') !== 'all') {
                $minRating = (float) $request->input('rating');
                $query->whereHas('reviews', function ($rq) use ($minRating) {
                    $rq->havingRaw('AVG(rating) >= ?', [$minRating]);
                });
            }

            // Sắp xếp (sort_by)
            $sortBy = $request->input('sort_by', 'default');
            match ($sortBy) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'area_desc' => $query->orderBy('area', 'desc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                default => $query->orderBy('id', 'asc'),
            };

            return $query->get();
        });

        // Kịch bản ERR_21_04: Không tìm thấy phòng nào phù hợp với bộ lọc
        if ($rooms->isEmpty()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'rooms' => [],
                'error_code' => 'ERR_21_04',
                'message' => 'Không tìm thấy phòng nào phù hợp với bộ lọc bạn đã chọn.'
            ]);
        }

        $total = $rooms->count();

        // Xử lý phân trang
        $page = max(1, (int) $request->input('page', 1));
        $perPageInput = $request->input('per_page', $request->input('limit'));
        $perPage = ($perPageInput !== null && $perPageInput !== 'all') ? max(1, min(50, (int) $perPageInput)) : 9;

        $pagedRooms = ($perPageInput === 'all') ? $rooms : $rooms->forPage($page, $perPage)->values();

        $result = $pagedRooms->map(function ($room) {
            $ratingAvg = round((float) ($room->reviews->avg('rating') ?: 5.0), 1);
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => (int) $room->price,
                'price_formatted' => number_format($room->price, 0, ',', '.') . ' VNĐ',
                'area' => (float) $room->area,
                'area_formatted' => $room->area . ' m²',
                'amenities' => $room->amenities ?? [],
                'building_name' => $room->building->name ?? 'Tòa nhà',
                'address' => $room->building->address ?? 'Đang cập nhật',
                'rating_avg' => $ratingAvg,
                'reviews_count' => $room->reviews->count(),
                'cover_image' => $room->cover_image ?? '/images/room-placeholder.jpg',
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $result->count(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
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
