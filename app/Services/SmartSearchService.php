<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SmartSearchService
{
    protected VietnameseTokenizerService $tokenizer;
    protected HybridSearchService $hybridSearch;

    public function __construct(
        ?VietnameseTokenizerService $tokenizer = null,
        ?HybridSearchService $hybridSearch = null
    ) {
        $this->tokenizer = $tokenizer ?: app(VietnameseTokenizerService::class);
        $this->hybridSearch = $hybridSearch ?: app(HybridSearchService::class);
    }
    /**
     * Từ điển khu vực, trường học, tiện ích và loại phòng cho hệ thống bất động sản
     */
    protected array $dictionary = [
        // Khu vực Hà Nội
        'Cầu Giấy' => ['cau giay', 'cg', 'cau giya', 'cau giau', 'cau gaiy'],
        'Thanh Xuân' => ['thanh xuan', 'tx', 'thah xuan', 'thanh xuanh'],
        'Đống Đa' => ['dong da', 'dd', 'dong daa'],
        'Hai Bà Trưng' => ['hai ba trung', 'hbt', 'haibatrung'],
        'Ba Đình' => ['ba dinh', 'bd', 'badinh'],
        'Tây Hồ' => ['tay ho', 'th', 'tayho'],
        'Hoàng Mai' => ['hoang mai', 'hm'],
        'Hà Đông' => ['ha dong', 'hadong'],
        'Nam Từ Liêm' => ['nam tu liem', 'ntl'],
        'Bắc Từ Liêm' => ['bac tu liem', 'btl'],
        'Xã Đàn' => ['xa dan', 'xadan'],

        // Khu vực TP. Hồ Chí Minh
        'Quận 1' => ['quan 1', 'q1', 'quan1'],
        'Quận 3' => ['quan 3', 'q3', 'quan3'],
        'Quận 4' => ['quan 4', 'q4', 'quan4'],
        'Quận 7' => ['quan 7', 'q7', 'quan7'],
        'Quận 9' => ['quan 9', 'q9', 'quan9'],
        'Quận 10' => ['quan 10', 'q10', 'quan10'],
        'Bình Thạnh' => ['binh thanh', 'bt', 'binh thnah', 'binh than'],
        'Tân Bình' => ['tan binh', 'tb', 'tan bih'],
        'Gò Vấp' => ['go vap', 'gv', 'govap'],
        'Thủ Đức' => ['thu duc', 'td', 'thuduc', 'thu duch'],
        'Phú Mỹ Hưng' => ['phu my hung', 'pmh', 'phumyhung'],
        'Làng Đại Học' => ['lang dai hoc', 'ldh', 'lang daihoc'],
        'Điện Biên Phủ' => ['dien bien phu', 'dbp'],
        'Nguyễn Gia Trí' => ['nguyen gia tri', 'd2'],
        'Tôn Thất Thuyết' => ['ton that thuyet'],
        'Nguyễn Oanh' => ['nguyen oanh'],
        'Phan Văn Trị' => ['phan van tri'],
        'Xa Lộ Hà Nội' => ['xa lo ha noi', 'xlhn'],

        // Trường Đại học / Địa danh phổ biến
        'Bách Khoa' => ['bach khoa', 'bk', 'dhbk', 'bckh'],
        'Kinh Tế Quốc Dân' => ['kinh te quoc dan', 'neu'],
        'Xây Dựng' => ['xay dung', 'nuce'],
        'Ngoại Thương' => ['ngoai thuong', 'ftu'],
        'HUTECH' => ['hutech', 'dh hutech'],
        'Giao Thông Vận Tải' => ['giao thong van tai', 'gtvt', 'utc'],
        'Đại Học Quốc Gia' => ['dai hoc quoc gia', 'dhqg', 'vnu'],

        // Tiện ích phòng trọ
        'Gác lửng' => ['gac lung', 'gac xep', 'gac', 'gac lug', 'gac lun'],
        'Ban công' => ['ban cong', 'bancog', 'ban con'],
        'Thú cưng' => ['thu cung', 'nuoi pet', 'cho meo', 'pet', 'pets'],
        'Khép kín' => ['khep kin', 'wc rieng', 'wc khep kin', 'khep kn', 'khep kni'],
        'Điều hòa' => ['dieu hoa', 'may lanh', 'deiu hoa', 'dieu hoa'],
        'Nóng lạnh' => ['nong lanh', 'binh nong lanh', 'nog lanh'],
        'Tủ lạnh' => ['tu lanh', 'tulanh'],
        'Máy giặt' => ['may giat', 'maygiat'],
        'Thang máy' => ['thang may', 'thangmay'],
        'Khóa vân tay' => ['khoa van tay', 'khoa tu'],
        'An ninh' => ['an ninh', 'camera'],

        // Loại phòng
        'Studio' => ['studio', 'studo'],
        'Chung cư mini' => ['chung cu mini', 'ccmn'],
        'Phòng trọ' => ['phong tro', 'nha tro', 'phog tro'],
        'Căn hộ' => ['can ho', 'canho'],
        'Duplex' => ['duplex', 'can ho duplex'],
    ];

    /**
     * Chuẩn hoá chuỗi tiếng Việt (loại bỏ dấu và ký tự đặc biệt)
     */
    public function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $text);
        $text = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $text);
        $text = preg_replace('/[ìíịỉĩ]/u', 'i', $text);
        $text = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $text);
        $text = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $text);
        $text = preg_replace('/[ỳýỵỷỹ]/u', 'y', $text);
        $text = preg_replace('/[đ]/u', 'd', $text);
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        return preg_replace('/\s+/', ' ', $text);
    }

    /**
     * Kiểm tra và tự động phát hiện / sửa lỗi từ ngữ
     * Trả về mảng:
     * - original: từ khoá gốc
     * - normalized: chuỗi chuẩn hoá không dấu
     * - corrected: chuỗi sau khi sửa lỗi hoặc chuẩn hoá chính xác
     * - did_you_mean: chuỗi gợi ý nếu phát hiện lỗi gõ sai
     * - recognized_terms: danh sách các thực thể nhận diện được (địa danh, tiện ích...)
     * - filters: các bộ lọc bóc tách được (giá, tiện ích, vị trí)
     */
    public function analyzeAndCorrect(string $query): array
    {
        $raw = trim($query);
        if ($raw === '') {
            return [
                'original' => '',
                'normalized' => '',
                'corrected' => '',
                'did_you_mean' => null,
                'recognized_terms' => [],
                'filters' => [
                    'max_price' => null,
                    'min_price' => null,
                    'amenities' => [],
                    'locations' => [],
                ],
            ];
        }

        $normalized = $this->normalize($raw);
        $filters = $this->extractFilters($normalized);

        // Phát hiện các thực thể phù hợp từ từ điển
        $matchedTerms = [];
        $correctedTokens = [];
        $didYouMeanTokens = [];
        $hasCorrection = false;

        $words = explode(' ', $normalized);
        $i = 0;
        $count = count($words);

        while ($i < $count) {
            $matched = false;

            // Thử so khớp cụm 3 từ, 2 từ, 1 từ với từ điển
            for ($len = min(3, $count - $i); $len >= 1; $len--) {
                $phrase = implode(' ', array_slice($words, $i, $len));

                foreach ($this->getDictionary() as $canonical => $aliases) {
                    $normCanonical = $this->normalize($canonical);

                    // 1. Khớp chính xác canonical hoặc alias
                    if ($phrase === $normCanonical || in_array($phrase, $aliases, true)) {
                        $matchedTerms[] = $canonical;
                        $correctedTokens[] = $canonical;
                        $didYouMeanTokens[] = $canonical;
                        // Nếu người dùng gõ khác với tên chuẩn có dấu thì coi là có gợi ý chuẩn hoá/sửa lỗi
                        if ($phrase !== $normCanonical || mb_strtolower($raw, 'UTF-8') !== mb_strtolower($canonical, 'UTF-8')) {
                            $hasCorrection = true;
                        }
                        $matched = true;
                        $i += $len;
                        break 2;
                    }

                    // 2. So khớp mờ (Fuzzy matching Levenshtein / Similarity)
                    // Chỉ áp dụng khi từ có độ dài >= 3 ký tự
                    if (strlen($phrase) >= 3) {
                        $lev = levenshtein($phrase, $normCanonical);
                        $maxLen = max(strlen($phrase), strlen($normCanonical));
                        $similarity = 1 - ($lev / $maxLen);

                        // Hoặc kiểm tra với từng alias
                        $bestAliasSim = 0;
                        foreach ($aliases as $alias) {
                            if (strlen($alias) >= 3) {
                                $aLev = levenshtein($phrase, $alias);
                                $aSim = 1 - ($aLev / max(strlen($phrase), strlen($alias)));
                                if ($aSim > $bestAliasSim) {
                                    $bestAliasSim = $aSim;
                                }
                            }
                        }

                        if ($similarity >= 0.70 || $bestAliasSim >= 0.70) {
                            $matchedTerms[] = $canonical;
                            $correctedTokens[] = $canonical;
                            $didYouMeanTokens[] = $canonical;
                            $hasCorrection = true;
                            $matched = true;
                            $i += $len;
                            break 2;
                        }
                    }
                }
            }

            if (!$matched) {
                // Từ thông thường không nằm trong từ điển (vd: 'tim', 'phong', số, v.v.)
                $correctedTokens[] = $words[$i];
                $didYouMeanTokens[] = $words[$i];
                $i++;
            }
        }

        $correctedStr = implode(' ', $correctedTokens);
        $didYouMean = $hasCorrection ? implode(' ', $didYouMeanTokens) : null;

        // Bổ sung các địa danh từ filter vào recognized_terms
        foreach ($filters['locations'] as $loc) {
            if (!in_array($loc, $matchedTerms, true)) {
                $matchedTerms[] = $loc;
            }
        }

        // Bổ sung các cụm từ ghép có nghĩa từ từ điển Viet74K vào recognized_terms
        try {
            $compounds = $this->tokenizer->extractCompoundWords($raw);
            foreach ($compounds as $cmp) {
                if (!in_array($cmp, $matchedTerms, true)) {
                    $matchedTerms[] = $cmp;
                }
            }
        } catch (\Throwable $e) {
            // Không làm gián đoạn nếu tokenizer gặp lỗi
        }

        return [
            'original' => $raw,
            'normalized' => $normalized,
            'corrected' => $correctedStr,
            'did_you_mean' => $didYouMean,
            'recognized_terms' => array_values(array_unique($matchedTerms)),
            'vietnamese_tokens' => $this->tokenizer->tokenize($raw),
            'filters' => $filters,
        ];
    }

    /**
     * Bóc tách các tiêu chí giá, tiện ích, vị trí từ ngôn ngữ tự nhiên
     */
    protected function extractFilters(string $normalized): array
    {
        $filters = [
            'max_price' => null,
            'min_price' => null,
            'amenities' => [],
            'locations' => [],
        ];

        // 1. Phân tích giá: vd "duoi 3 trieu", "duoi 3tr", "tam 4tr", "< 5 trieu", "tu 3 den 5 trieu"
        if (preg_match('/(?:duoi|nho hon|toi da|<=?|tam|khoang)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/u', $normalized, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
            $filters['max_price'] = $amount < 100 ? (int) ($amount * 1000000) : (int) $amount;
        } elseif (preg_match('/(?:tren|lon hon|>=?)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/u', $normalized, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
            $filters['min_price'] = $amount < 100 ? (int) ($amount * 1000000) : (int) $amount;
        }

        // 2. Phân tích tiện ích
        $amenityMap = [
            'thú cưng' => ['thu cung', 'pet', 'pets'],
            'gác lửng' => ['gac lung', 'gac xep', 'gac'],
            'ban công' => ['ban cong'],
            'khép kín' => ['khep kin', 'wc rieng', 'wc khep kin', 'khep kn', 'khep kni'],
            'điều hòa' => ['dieu hoa', 'may lanh'],
            'nóng lạnh' => ['nong lanh'],
        ];

        foreach ($amenityMap as $amenity => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($normalized, $kw)) {
                    $filters['amenities'][] = $amenity;
                    break;
                }
            }
        }

        // 3. Phân tích vị trí
        $locationPool = [
            'Cầu Giấy', 'Thanh Xuân', 'Đống Đa', 'Hai Bà Trưng', 'Ba Đình', 'Tây Hồ',
            'Quận 1', 'Quận 3', 'Quận 4', 'Quận 7', 'Quận 10', 'Bình Thạnh', 'Tân Bình', 'Gò Vấp', 'Thủ Đức',
            'Bách Khoa', 'HUTECH', 'Phú Mỹ Hưng', 'Làng Đại Học',
        ];

        foreach ($locationPool as $loc) {
            $normLoc = $this->normalize($loc);
            if (str_contains($normalized, $normLoc)) {
                $filters['locations'][] = $loc;
            }
        }

        return $filters;
    }

    /**
     * Lấy từ điển tìm kiếm động kết hợp database và cache
     */
    public function getDictionary(): array
    {
        return Cache::remember('smart_search_dynamic_dict', 3600, function () {
            $dict = $this->dictionary;
            try {
                $buildings = Building::select('name', 'address')->get();
                foreach ($buildings as $b) {
                    if ($b->name && !isset($dict[$b->name])) {
                        $dict[$b->name] = array_unique([$this->normalize($b->name)]);
                    }
                }
            } catch (\Throwable $e) {
                // Giữ nguyên dictionary tĩnh nếu database chưa sẵn sàng
            }
            return $dict;
        });
    }

    /**
     * Thực hiện tìm kiếm thông minh và xếp hạng kết quả (Smart Search Ranking)
     */
    public function search(string $query, array $options = []): array
    {
        $analysis = $this->analyzeAndCorrect($query);
        $limit = max(1, min(50, (int) ($options['limit'] ?? 12)));
        $page = max(1, (int) ($options['page'] ?? 1));
        $status = (string) ($options['status'] ?? 'all');
        $sortBy = (string) ($options['sort'] ?? 'default');

        // Tiêu chí khoảng giá: ưu tiên options truyền vào trực tiếp từ UI, fallback về NLP bóc tách
        $minPrice = isset($options['min_price']) && is_numeric($options['min_price']) && (int) $options['min_price'] > 0
            ? (int) $options['min_price']
            : $analysis['filters']['min_price'];

        $maxPrice = isset($options['max_price']) && is_numeric($options['max_price']) && (int) $options['max_price'] > 0
            ? (int) $options['max_price']
            : $analysis['filters']['max_price'];

        // Tiêu chí tiện ích: gộp tiện ích từ UI và tiện ích bóc tách từ NLP
        $requestedAmenities = array_map('mb_strtolower', (array) ($options['amenities'] ?? []));
        $nlpAmenities = $analysis['filters']['amenities'] ?? [];

        // Tiêu chí số sao đánh giá tối thiểu
        $minRating = isset($options['min_rating']) && is_numeric($options['min_rating']) && (float) $options['min_rating'] > 0
            ? (float) $options['min_rating']
            : null;

        // Lấy danh sách phòng kèm quan hệ
        $roomsQuery = Room::with(['building', 'tenant', 'reviews']);

        if ($status !== 'all') {
            $roomsQuery->where('status', $status);
        }

        $allRooms = $roomsQuery->get();

        if ($allRooms->isEmpty()) {
            return [
                'analysis' => $analysis,
                'count' => 0,
                'total' => 0,
                'rooms' => [],
                'suggestions' => $this->getQuickSuggestions(),
            ];
        }

        // Decorate toàn bộ phòng trước để có đầy đủ thông tin chuẩn hóa
        $decoratedList = $this->decorateRoomsCollection($allRooms);

        // Lọc cứng theo tiêu chí người dùng chọn trên UI
        $filtered = $decoratedList->filter(function ($room) use ($minPrice, $maxPrice, $requestedAmenities, $nlpAmenities, $minRating) {
            // 1. Khoảng giá
            if ($minPrice !== null && $room['price'] < $minPrice) {
                return false;
            }
            if ($maxPrice !== null && $room['price'] > $maxPrice) {
                return false;
            }

            // 2. Số sao đánh giá tối thiểu
            if ($minRating !== null && (float) $room['rating'] < $minRating) {
                return false;
            }

            // 3. Tiện ích chọn trên giao diện
            if (in_array('pets', $requestedAmenities, true) && !$room['pets']) return false;
            if (in_array('loft', $requestedAmenities, true) && !$room['loft']) return false;
            if (in_array('balcony', $requestedAmenities, true) && !$room['balcony']) return false;
            if (in_array('wc', $requestedAmenities, true) && !$room['wc']) return false;

            // 4. Tiện ích bóc tách từ câu hỏi tự nhiên (NLP)
            if (!empty($nlpAmenities)) {
                $rawAmenities = implode(' ', (array) ($room['amenities'] ?? []));
                foreach ($nlpAmenities as $am) {
                    if ($am === 'thú cưng' && !$room['pets']) return false;
                    if ($am === 'gác lửng' && !$room['loft']) return false;
                    if ($am === 'ban công' && !$room['balcony']) return false;
                    if ($am === 'khép kín' && !$room['wc']) return false;
                }
            }

            return true;
        });

        // Tính điểm Relevance Score khi có từ khóa tìm kiếm
        $scoredRooms = $filtered->map(function ($room) use ($analysis) {
            $fullSearchable = $this->normalize(
                "{$room['room_number']} {$room['title']} {$room['building_name']} {$room['address']} {$room['area_name']} " .
                implode(' ', (array) ($room['amenities'] ?? [])) . " {$room['location_description']}"
            );

            $score = 0;

            if ($analysis['normalized'] !== '') {
                // Khớp chính xác query
                if (str_contains($fullSearchable, $analysis['normalized'])) {
                    $score += 60;
                }
                // Khớp từ đã sửa chính tả Did you mean
                if ($analysis['did_you_mean'] && str_contains($fullSearchable, $this->normalize($analysis['did_you_mean']))) {
                    $score += 50;
                }
            }

            // Khớp các thực thể nhận diện (recognized terms)
            foreach ($analysis['recognized_terms'] as $term) {
                $normTerm = $this->normalize($term);
                if (str_contains($fullSearchable, $normTerm)) {
                    $score += 35;
                }
            }

            // Khớp địa danh bóc tách
            foreach ($analysis['filters']['locations'] as $loc) {
                if (str_contains($this->normalize($room['address']), $this->normalize($loc))) {
                    $score += 40;
                }
            }

            // Điểm rating và boost score
            $score += (int) round(((float) $room['rating']) * 2);
            $score += (int) ($room['boost_score'] ?? 0);

            $room['relevance_score'] = $score;
            return $room;
        });

        // Nếu có nhập từ khóa, áp dụng Hybrid Search (Sparse BM25 + Dense Semantic + RRF Fusion)
        if ($analysis['normalized'] !== '') {
            try {
                $scoredRooms = $this->hybridSearch->search($query, $scoredRooms, $options);
            } catch (\Throwable $e) {
                // Giữ nguyên baseline scoring nếu hybrid search có exception
            }
            $scoredRooms = $scoredRooms->filter(fn ($item) => ($item['relevance_score'] ?? 0) > 0 || ($item['rrf_score'] ?? 0) > 0);
        }

        // Sắp xếp kết quả (Ưu tiên điểm RRF và relevance score)
        $sortedRooms = match ($sortBy) {
            'price_asc' => $scoredRooms->sortBy('price')->values(),
            'price_desc' => $scoredRooms->sortByDesc('price')->values(),
            'rating_desc' => $scoredRooms->sortByDesc('rating')->values(),
            'latest' => $scoredRooms->sortByDesc('id')->values(),
            default => $scoredRooms->sortByDesc(function ($item) {
                $rrfBonus = isset($item['rrf_score']) ? ((float) $item['rrf_score'] * 1000) : 0;
                return $rrfBonus + (int) ($item['relevance_score'] ?? 0) + (int) ($item['boost_score'] ?? 0);
            })->values(),
        };

        $totalCount = $sortedRooms->count();
        $pagedRooms = $sortedRooms->forPage($page, $limit)->values();

        return [
            'analysis' => $analysis,
            'hybrid_search' => [
                'enabled' => $analysis['normalized'] !== '',
                'retrieval_architecture' => 'dual_sparse_dense',
                'fusion_algorithm' => 'reciprocal_rank_fusion_rrf',
                'rrf_k' => 60,
                'vietnamese_tokens' => $this->tokenizer->tokenize($query),
                'vietnamese_compounds' => $this->tokenizer->extractCompoundWords($query),
            ],
            'count' => $pagedRooms->count(),
            'total' => $totalCount,
            'page' => $page,
            'per_page' => $limit,
            'has_more' => ($page * $limit) < $totalCount,
            'rooms' => $pagedRooms,
            'suggestions' => $this->getDynamicSuggestions($analysis),
        ];
    }

    /**
     * Decorate toàn bộ collection phòng chuẩn cấu trúc hiển thị Renty
     */
    protected function decorateRoomsCollection(Collection $rooms): Collection
    {
        $mapped = $rooms->map(function (Room $room) {
            $num = intval($room->room_number);
            $building = $room->building;
            $tenant = $room->tenant;
            $dbReviews = $room->reviews;

            $rating = $dbReviews->count() > 0 ? (float) $dbReviews->avg('rating') : (3.6 + (($num * 7) % 15) / 10);
            if ($rating > 5.0) $rating = 5.0;

            $distance = 0.4 + (($num * 3) % 12) / 10;
            $buildingAddress = $building?->address ?? 'Khu vực trung tâm';
            $buildingName = $building?->name ?? 'Renty Residence';

            $areaName = 'Khu vực';
            $areaMap = [
                'Thanh Xuân', 'Cầu Giấy', 'Đống Đa', 'Hai Bà Trưng', 'Tây Hồ', 'Ba Đình',
                'Quận 10', 'Quận 1', 'Quận 7', 'Quận 4',
                'Bình Thạnh', 'Tân Bình', 'Gò Vấp', 'Thủ Đức', 'Phú Mỹ Hưng',
            ];
            foreach ($areaMap as $area) {
                if (str_contains($buildingAddress, $area)) {
                    $areaName = $area;
                    break;
                }
            }

            $amenities = collect($room->amenities ?? [])->map(fn ($item) => mb_strtolower($item));
            $pets = $amenities->contains(fn ($item) => str_contains($item, 'thú cưng')) || ($num % 2 == 1);
            $loft = $amenities->contains(fn ($item) => str_contains($item, 'gác') || str_contains($item, 'gac')) || (($num % 3) != 2);
            $balcony = $amenities->contains(fn ($item) => str_contains($item, 'ban công') || str_contains($item, 'ban cong')) || (($num % 4) != 0);
            $wc = $amenities->contains(fn ($item) => str_contains($item, 'khép kín') || str_contains($item, 'wc') || str_contains($item, 'vệ sinh')) || (($num % 5) != 3);

            $title = $buildingName . " - Phòng " . $room->room_number;
            $area = (int) ($room->area ?? (22 + ($num % 9)));

            $locationDescription = ($building?->description ?: "Nằm tại khu vực {$areaName}, thuận tiện di chuyển.") . " Địa chỉ: {$buildingAddress}.";

            $uploadedImages = collect($room->images ?? []);
            if ($room->image) {
                $uploadedImages->prepend($room->image);
            }

            $mediaUrl = fn ($path) => str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                ? $path
                : Storage::url($path);

            $imageUrls = $uploadedImages->filter()->unique()->values()->map(fn ($path) => $mediaUrl($path))->all();

            if (empty($imageUrls)) {
                $fallbackSets = [
                    [
                        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
                    ],
                ];
                $imageUrls = $fallbackSets[$num % count($fallbackSets)];
            }

            $verificationStatus = $tenant?->verification_status ?? 'unverified';
            $trustBadge = match ($verificationStatus) {
                'premium_verified' => [
                    'label' => 'Tích Xanh Thẩm Định',
                    'icon' => 'fa-badge-check',
                    'class' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
                ],
                'kyc_verified' => [
                    'label' => 'Đã Xác Minh KYC',
                    'icon' => 'fa-shield-check',
                    'class' => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30'
                ],
                default => [
                    'label' => 'Đang Chờ Xác Minh',
                    'icon' => 'fa-clock',
                    'class' => 'bg-slate-700/40 text-slate-300 border-slate-600/40'
                ],
            };

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'price' => (int) $room->price,
                'price_formatted' => number_format($room->price, 0, ',', '.') . 'đ',
                'status' => $room->status,
                'rating' => number_format($rating, 1),
                'reviews_count' => $dbReviews->count(),
                'distance' => $distance,
                'pets' => $pets,
                'loft' => $loft,
                'balcony' => $balcony,
                'wc' => $wc,
                'pets_txt' => $pets ? 'Có' : 'Không',
                'loft_txt' => $loft ? 'Có' : 'Không',
                'balcony_txt' => $balcony ? 'Có' : 'Không',
                'wc_txt' => $wc ? 'Có' : 'Không',
                'title' => $title,
                'building_name' => $buildingName,
                'address' => $buildingAddress,
                'area_name' => $areaName,
                'area' => $area,
                'area_text' => $area . ' m²',
                'location_description' => $locationDescription,
                'cover_image' => $imageUrls[0] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
                'image_urls' => $imageUrls,
                'media_source_label' => !empty($uploadedImages) ? 'Ảnh thực tế' : 'Ảnh tham khảo',
                'trust_badge' => $trustBadge,
                'boost_score' => (int) ($tenant->boost_score ?? 0),
                'amenities' => $room->amenities ?? [],
                'url' => route('renty.room.show', ['id' => $room->id], false),
                'price_warning' => null,
            ];
        });

        // Tính cảnh báo giá dị biệt
        $averagePrice = (int) round($mapped->count() > 0 ? $mapped->avg('price') : 3500000);
        return $mapped->map(function ($room) use ($averagePrice) {
            $priceDiff = $averagePrice > 0 ? (($room['price'] - $averagePrice) / $averagePrice) * 100 : 0;
            if ($priceDiff <= -25) {
                $room['price_warning'] = [
                    'type' => 'low',
                    'label' => 'Giá quá rẻ',
                    'message' => 'Thấp hơn khoảng ' . abs(round($priceDiff)) . '% so với mặt bằng.',
                ];
            } elseif ($priceDiff >= 25) {
                $room['price_warning'] = [
                    'type' => 'high',
                    'label' => 'Giá cao',
                    'message' => 'Cao hơn khoảng ' . round($priceDiff) . '% so với mặt bằng.',
                ];
            }
            return $room;
        });
    }

    /**
     * Gợi ý từ khóa tìm kiếm nhanh
     */
    public function getQuickSuggestions(): array
    {
        return [
            ['label' => 'Cầu Giấy', 'query' => 'Cầu Giấy', 'icon' => 'fa-location-dot', 'type' => 'location'],
            ['label' => 'Bách Khoa', 'query' => 'Bách Khoa', 'icon' => 'fa-graduation-cap', 'type' => 'school'],
            ['label' => 'Bình Thạnh', 'query' => 'Bình Thạnh', 'icon' => 'fa-location-dot', 'type' => 'location'],
            ['label' => 'Thủ Đức', 'query' => 'Thủ Đức', 'icon' => 'fa-location-dot', 'type' => 'location'],
            ['label' => 'Phòng dưới 3 triệu', 'query' => 'dưới 3 triệu', 'icon' => 'fa-tags', 'type' => 'price'],
            ['label' => 'Phòng có gác lửng', 'query' => 'gác lửng', 'icon' => 'fa-stairs', 'type' => 'amenity'],
            ['label' => 'Nuôi thú cưng', 'query' => 'thú cưng', 'icon' => 'fa-cat', 'type' => 'amenity'],
        ];
    }

    /**
     * Sinh gợi ý thông minh dựa trên phân tích từ khoá
     */
    protected function getDynamicSuggestions(array $analysis): array
    {
        $suggestions = [];

        if ($analysis['did_you_mean']) {
            $suggestions[] = [
                'label' => 'Có phải bạn muốn tìm: ' . $analysis['did_you_mean'],
                'query' => $analysis['did_you_mean'],
                'icon' => 'fa-wand-magic-sparkles',
                'type' => 'correction',
            ];
        }

        foreach ($analysis['recognized_terms'] as $term) {
            $suggestions[] = [
                'label' => "Phòng trọ tại {$term}",
                'query' => $term,
                'icon' => 'fa-map-pin',
                'type' => 'location',
            ];
        }

        if (empty($suggestions)) {
            return $this->getQuickSuggestions();
        }

        return $suggestions;
    }
}
