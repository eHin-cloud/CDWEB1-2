<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ScoutSearchService
{
    protected SmartSearchService $smartSearchService;

    public function __construct(SmartSearchService $smartSearchService)
    {
        $this->smartSearchService = $smartSearchService;
    }

    /**
     * Thực hiện tìm kiếm phòng trọ bằng Laravel Scout Engine
     *
     * @param string $query Từ khoá tìm kiếm
     * @param array $options Tuỳ chọn lọc (limit, page, status, min_price, max_price, sort, amenities, min_rating)
     * @return array Kết quả tìm kiếm đã được chuẩn hoá
     */
    public function search(string $query, array $options = []): array
    {
        $cleanQuery = trim(mb_substr($query, 0, 100));
        $limit = max(1, min(50, (int) ($options['limit'] ?? 12)));
        $page = max(1, (int) ($options['page'] ?? 1));
        $status = (string) ($options['status'] ?? 'all');
        $sortBy = (string) ($options['sort'] ?? $options['sort_by'] ?? 'default');

        $minPrice = isset($options['min_price']) && is_numeric($options['min_price']) && (int) $options['min_price'] > 0
            ? (int) $options['min_price']
            : null;

        $maxPrice = isset($options['max_price']) && is_numeric($options['max_price']) && (int) $options['max_price'] > 0
            ? (int) $options['max_price']
            : null;

        $minRating = isset($options['min_rating']) && is_numeric($options['min_rating']) && (float) $options['min_rating'] > 0
            ? (float) $options['min_rating']
            : null;

        $requestedAmenities = array_filter((array) ($options['amenities'] ?? []));

        // Khởi tạo Scout Builder
        $scoutBuilder = Room::search($cleanQuery);

        // Áp dụng query callback trên Eloquent builder để xử lý eager loading & các bộ lọc nâng cao
        $scoutBuilder->query(function ($builder) use ($status, $minPrice, $maxPrice, $cleanQuery, $sortBy, $requestedAmenities) {
            $builder->with(['building', 'tenant', 'reviews']);

            if ($status !== 'all') {
                $builder->where('status', $status);
            }

            if ($minPrice !== null) {
                $builder->where('price', '>=', $minPrice);
            }

            if ($maxPrice !== null) {
                $builder->where('price', '<=', $maxPrice);
            }

            if (!empty($requestedAmenities)) {
                $builder->where(function ($qb) use ($requestedAmenities) {
                    foreach ($requestedAmenities as $amenity) {
                        $norm = mb_strtolower(trim($amenity));
                        $qb->where(function ($sub) use ($norm) {
                            match ($norm) {
                                'air_conditioner', 'ac', 'may lanh', 'máy lạnh', 'dieu hoa', 'điều hòa' => $sub->where('amenities', 'like', '%ac%')
                                    ->orWhere('amenities', 'like', '%air_conditioner%')
                                    ->orWhere('amenities', 'like', '%máy lạnh%')
                                    ->orWhere('description', 'like', '%máy lạnh%')
                                    ->orWhere('description', 'like', '%điều hòa%'),
                                'washing_machine', 'washer', 'may giat', 'máy giặt' => $sub->where('amenities', 'like', '%washer%')
                                    ->orWhere('amenities', 'like', '%washing_machine%')
                                    ->orWhere('amenities', 'like', '%máy giặt%')
                                    ->orWhere('description', 'like', '%máy giặt%'),
                                'fridge', 'refrigerator', 'tu lanh', 'tủ lạnh' => $sub->where('amenities', 'like', '%fridge%')
                                    ->orWhere('amenities', 'like', '%refrigerator%')
                                    ->orWhere('amenities', 'like', '%tủ lạnh%')
                                    ->orWhere('description', 'like', '%tủ lạnh%'),
                                'wifi', 'internet', 'mạng' => $sub->where('amenities', 'like', '%wifi%')
                                    ->orWhere('amenities', 'like', '%internet%')
                                    ->orWhere('description', 'like', '%wifi%'),
                                default => $sub->where('amenities', 'like', "%{$norm}%")
                                    ->orWhere('description', 'like', "%{$norm}%"),
                            };
                        });
                    }
                });
            }

            // Mở rộng tìm kiếm sang toà nhà nếu có từ khoá
            if ($cleanQuery !== '') {
                $builder->orWhereHas('building', function ($q) use ($cleanQuery) {
                    $q->where('name', 'like', "%{$cleanQuery}%")
                      ->orWhere('address', 'like', "%{$cleanQuery}%");
                });
            }

            // Áp dụng sắp xếp trên Database
            match ($sortBy) {
                'price_asc' => $builder->orderBy('price', 'asc'),
                'price_desc' => $builder->orderBy('price', 'desc'),
                'latest' => $builder->orderBy('id', 'desc'),
                default => $builder->orderBy('id', 'desc'),
            };
        });

        /** @var LengthAwarePaginator $paginator */
        $paginator = $scoutBuilder->paginate($limit, 'page', $page);

        // Chuẩn hóa và làm giàu dữ liệu phòng (Decorator)
        $decoratedCollection = $this->smartSearchService->decorateRoomsCollection($paginator->getCollection());

        // Áp dụng lọc tiện ích và đánh giá nếu có
        if (!empty($requestedAmenities) || $minRating !== null) {
            $decoratedCollection = $decoratedCollection->filter(function ($room) use ($requestedAmenities, $minRating) {
                if ($minRating !== null && (float) ($room['rating'] ?? 0) < $minRating) {
                    return false;
                }

                if (!empty($requestedAmenities)) {
                    foreach ($requestedAmenities as $amenity) {
                        $normAmenity = mb_strtolower(trim($amenity));
                        $has = match ($normAmenity) {
                            'air_conditioner', 'ac', 'may lanh', 'máy lạnh', 'dieu hoa', 'điều hòa' => !empty($room['ac']),
                            'washing_machine', 'washer', 'may giat', 'máy giặt' => !empty($room['washer']),
                            'fridge', 'refrigerator', 'tu lanh', 'tủ lạnh' => !empty($room['fridge']),
                            'wifi', 'internet', 'mạng' => !empty($room['wifi']),
                            'pets', 'pet', 'thú cưng' => !empty($room['pets']),
                            'loft', 'gác lửng' => !empty($room['loft']),
                            'balcony', 'ban công' => !empty($room['balcony']),
                            'wc', 'khép kín' => !empty($room['wc']),
                            default => collect((array) ($room['amenities'] ?? []))->contains(fn ($ra) => str_contains(mb_strtolower($ra), $normAmenity)),
                        };

                        if (!$has) {
                            return false;
                        }
                    }
                }

                return true;
            })->values();
        }

        // Sắp xếp theo rating nếu người dùng chọn rating_desc
        if ($sortBy === 'rating_desc') {
            $decoratedCollection = $decoratedCollection->sortByDesc(fn ($r) => (float) ($r['rating'] ?? 0))->values();
        }

        return [
            'engine' => 'laravel_scout',
            'driver' => config('scout.driver', 'database'),
            'query' => $cleanQuery,
            'count' => $decoratedCollection->count(),
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'has_more' => $paginator->hasMorePages(),
            'rooms' => $decoratedCollection->all(),
            'suggestions' => $this->smartSearchService->getQuickSuggestions(),
        ];
    }
}
