<?php

namespace App\Services;

use App\Models\Room;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SolrSearchService
{
    protected ?Client $client = null;
    protected SmartSearchService $smartSearchService;
    protected string $host;
    protected int $port;
    protected string $core;

    public function __construct(SmartSearchService $smartSearchService)
    {
        $this->smartSearchService = $smartSearchService;
        $this->host = env('SOLR_HOST', '127.0.0.1');
        $this->port = (int) env('SOLR_PORT', 8983);
        $this->core = env('SOLR_CORE', 'rooms');

        $this->initClient();
    }

    /**
     * Khởi tạo kết nối Solarium Client
     */
    protected function initClient(): void
    {
        try {
            if (!class_exists(\Solarium\Client::class) || !class_exists(\Solarium\Core\Client\Adapter\Curl::class)) {
                $this->client = null;
                return;
            }
            $adapter = new Curl();
            $dispatcher = new EventDispatcher();
            $options = [
                'endpoint' => [
                    'localhost' => [
                        'host' => $this->host,
                        'port' => $this->port,
                        'path' => '/',
                        'core' => $this->core,
                        'timeout' => 5,
                    ]
                ]
            ];
            $this->client = new Client($adapter, $dispatcher, $options);
        } catch (\Throwable $e) {
            Log::error('Solr Client Init Error: ' . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Kiểm tra Solr Server có đang hoạt động hay không
     */
    public function ping(): bool
    {
        if (!$this->client) {
            return false;
        }
        try {
            $ping = $this->client->createPing();
            $result = $this->client->ping($ping);
            return $result->getStatus() === 0;
        } catch (Exception $e) {
            Log::warning('Solr Ping Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xoá toàn bộ index trong Solr core
     */
    public function flush(): bool
    {
        if (!$this->client) {
            return false;
        }
        try {
            $update = $this->client->createUpdate();
            $update->addDeleteQuery('*:*');
            $update->addCommit();
            $this->client->update($update);
            return true;
        } catch (Exception $e) {
            Log::error('Solr Flush Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xoá một phòng khỏi Solr
     */
    public function deleteRoom(int|string $id): bool
    {
        if (!$this->client) {
            return false;
        }

        try {
            $update = $this->client->createUpdate();
            $update->addDeleteById((string) $id);
            $update->addCommit();
            $this->client->update($update);
            return true;
        } catch (Exception $e) {
            Log::error('Solr Delete Room Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh chỉ mục một hoặc nhiều phòng trọ vào Solr
     */
    public function indexRooms(Collection|array $rooms): int
    {
        if (!$this->client) {
            return 0;
        }

        $update = $this->client->createUpdate();
        $docs = [];

        foreach ($rooms as $room) {
            /** @var Room $room */
            $amenitiesText = '';
            if (is_array($room->amenities)) {
                $amenitiesText = implode(' ', $room->amenities);
            } elseif (is_string($room->amenities)) {
                $amenitiesText = $room->amenities;
            }

            $doc = $update->createDocument();
            $doc->id = (string) $room->id;
            $doc->room_id = (int) $room->id;
            $doc->room_number = (string) ($room->room_number ?? '');
            $doc->room_type = (string) ($room->room_type ?? '');
            $doc->price = (float) ($room->price ?? 0);
            $doc->area = (float) ($room->area ?? 0);
            $doc->status = (string) ($room->status ?? 'available');
            $doc->description = (string) ($room->description ?? '');
            $doc->amenities = $amenitiesText;
            $doc->floor = (int) ($room->floor ?? 1);

            // Thông tin toà nhà / địa chỉ
            if ($room->building) {
                $doc->building_name = (string) ($room->building->name ?? '');
                $doc->building_address = (string) ($room->building->address ?? '');
            } else {
                $doc->building_name = '';
                $doc->building_address = '';
            }

            $docs[] = $doc;
        }

        if (!empty($docs)) {
            $update->addDocuments($docs);
            $update->addCommit();
            $this->client->update($update);
        }

        return count($docs);
    }

    /**
     * Tìm kiếm phòng trọ bằng Apache Solr Engine
     *
     * @param string $query Từ khoá tìm kiếm
     * @param array $options Tuỳ chọn lọc (limit, page, status, min_price, max_price, sort, amenities, min_rating)
     * @return array Kết quả tìm kiếm đã chuẩn hoá
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

        if (!$this->client || !$this->ping()) {
            Log::warning('Solr service unavailable. Falling back to SmartSearchService.');
            return $this->smartSearchService->search($query, $options);
        }

        try {
            $solrQuery = $this->client->createQuery($this->client::QUERY_SELECT);

            // Cấu hình tìm kiếm eDisMax đa trường
            if ($cleanQuery !== '') {
                $solrQuery->setQuery($cleanQuery);
                $edismax = $solrQuery->getEDisMax();
                $edismax->setQueryFields('room_number^4 description^2 amenities^2 building_name^3 building_address^2 room_type^1');
                $edismax->setMinimumMatch('1');
            } else {
                $solrQuery->setQuery('*:*');
            }

            // Bộ lọc trạng thái
            if ($status !== 'all') {
                $solrQuery->createFilterQuery('status_filter')->setQuery('status:' . $status);
            }

            // Bộ lọc giá
            if ($minPrice !== null && $maxPrice !== null) {
                $solrQuery->createFilterQuery('price_filter')->setQuery("price:[{$minPrice} TO {$maxPrice}]");
            } elseif ($minPrice !== null) {
                $solrQuery->createFilterQuery('price_filter')->setQuery("price:[{$minPrice} TO *]");
            } elseif ($maxPrice !== null) {
                $solrQuery->createFilterQuery('price_filter')->setQuery("price:[* TO {$maxPrice}]");
            }

            // Bộ lọc loại phòng
            if (!empty($options['room_type'])) {
                $solrQuery->createFilterQuery('type_filter')->setQuery('room_type:' . $options['room_type']);
            }

            // Sắp xếp
            match ($sortBy) {
                'price_asc' => $solrQuery->addSort('price', $solrQuery::SORT_ASC),
                'price_desc' => $solrQuery->addSort('price', $solrQuery::SORT_DESC),
                'latest' => $solrQuery->addSort('room_id', $solrQuery::SORT_DESC),
                default => $cleanQuery !== '' 
                    ? $solrQuery->addSort('score', $solrQuery::SORT_DESC) 
                    : $solrQuery->addSort('room_id', $solrQuery::SORT_DESC),
            };

            // Phân trang
            $start = ($page - 1) * $limit;
            $solrQuery->setStart($start);
            $solrQuery->setRows($limit);

            $result = $this->client->select($solrQuery);
            $totalFound = $result->getNumFound();

            $roomIds = [];
            foreach ($result as $doc) {
                $id = (int) ($doc->room_id ?? $doc->id);
                if ($id > 0) {
                    $roomIds[] = $id;
                }
            }

            if (empty($roomIds)) {
                return [
                    'analysis' => [
                        'original' => $query,
                        'normalized' => $cleanQuery,
                        'corrected' => $cleanQuery,
                        'did_you_mean' => null,
                        'recognized_terms' => [],
                        'filters' => [
                            'price_min' => $minPrice,
                            'price_max' => $maxPrice,
                            'status' => $status,
                        ],
                    ],
                    'count' => 0,
                    'total' => 0,
                    'page' => $page,
                    'per_page' => $limit,
                    'has_more' => false,
                    'rooms' => [],
                    'suggestions' => [],
                ];
            }

            // Eager-load từ MySQL theo đúng thứ tự mà Solr đã rank xếp hạng
            $roomsMap = Room::with(['building', 'tenant', 'reviews'])
                ->whereIn('id', $roomIds)
                ->get()
                ->keyBy('id');

            $sortedRooms = collect();
            foreach ($roomIds as $id) {
                if (isset($roomsMap[$id])) {
                    $sortedRooms->push($roomsMap[$id]);
                }
            }

            // Chuẩn hoá thông tin phòng (Decorator)
            $decoratedCollection = $this->smartSearchService->decorateRoomsCollection($sortedRooms);

            // Lọc phụ (tiện ích và rating nếu có)
            if (!empty($requestedAmenities) || $minRating !== null) {
                $decoratedCollection = $decoratedCollection->filter(function ($room) use ($requestedAmenities, $minRating) {
                    if ($minRating !== null && (float) ($room['rating'] ?? 0) < $minRating) {
                        return false;
                    }
                    return true;
                })->values();
            }

            return [
                'analysis' => [
                    'original' => $query,
                    'normalized' => $cleanQuery,
                    'corrected' => $cleanQuery,
                    'did_you_mean' => null,
                    'recognized_terms' => [],
                    'filters' => [
                        'price_min' => $minPrice,
                        'price_max' => $maxPrice,
                        'status' => $status,
                    ],
                ],
                'count' => $decoratedCollection->count(),
                'total' => $totalFound,
                'page' => $page,
                'per_page' => $limit,
                'has_more' => ($start + $limit) < $totalFound,
                'rooms' => $decoratedCollection->values()->all(),
                'suggestions' => [],
            ];
        } catch (Exception $e) {
            Log::error('Solr Search Exception: ' . $e->getMessage());
            // Fallback sang SmartSearch nếu Solr gặp lỗi
            return $this->smartSearchService->search($query, $options);
        }
    }
}

