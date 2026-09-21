<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SmartSearchService
{
    /**
     * Từ điển khu vực, trường học, tiện ích và loại phòng cho hệ thống bất động sản
     */
    protected array $dictionary = [
        // Khu vực Hà Nội
        'Cầu Giấy' => ['cau giay', 'cg', 'cau giya', 'cau giau'],
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

                foreach ($this->dictionary as $canonical => $aliases) {
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

        return [
            'original' => $raw,
            'normalized' => $normalized,
            'corrected' => $correctedStr,
            'did_you_mean' => $didYouMean,
            'recognized_terms' => array_unique($matchedTerms),
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
            'khép kín' => ['khep kin', 'wc rieng', 'wc khep kin'],
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
     * Thực hiện tìm kiếm thông minh và xếp hạng kết quả (Smart Search Ranking)
     */
    public function search(string $query, array $options = []): array
    {
        $analysis = $this->analyzeAndCorrect($query);
        $limit = $options['limit'] ?? 10;
        $status = $options['status'] ?? 'all';

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
                'rooms' => [],
                'suggestions' => $this->getQuickSuggestions(),
            ];
        }

        $searchTerms = array_filter(array_merge(
            $analysis['recognized_terms'],
            explode(' ', $analysis['normalized']),
            [$analysis['corrected']]
        ));

        // Tính điểm Relevance Score cho từng phòng
        $scoredRooms = $allRooms->map(function ($room) use ($analysis, $searchTerms) {
            $building = $room->building;
            $tenant = $room->tenant;

            $buildingName = $building?->name ?? '';
            $buildingAddress = $building?->address ?? '';
            $roomNumber = (string) $room->room_number;
            $amenities = is_array($room->amenities) ? implode(' ', $room->amenities) : '';
            $description = (string) $room->description;

            $fullSearchable = $this->normalize("{$roomNumber} {$buildingName} {$buildingAddress} {$amenities} {$description}");

            $score = 0;

            // 1. Khớp từ khóa cụ thể trong tên tòa nhà / địa chỉ / số phòng
            if ($analysis['normalized'] !== '') {
                if (str_contains($fullSearchable, $analysis['normalized'])) {
                    $score += 50;
                }
                if ($analysis['did_you_mean'] && str_contains($fullSearchable, $this->normalize($analysis['did_you_mean']))) {
                    $score += 45;
                }
            }

            // 2. Khớp các thực thể nhận diện (recognized terms)
            foreach ($analysis['recognized_terms'] as $term) {
                $normTerm = $this->normalize($term);
                if (str_contains($fullSearchable, $normTerm)) {
                    $score += 30;
                }
            }

            // 3. Khớp điều kiện giá
            if ($analysis['filters']['max_price'] !== null) {
                if ($room->price <= $analysis['filters']['max_price']) {
                    $score += 20;
                } else {
                    $score -= 30; // Trừ điểm nếu vượt mức giá yêu cầu
                }
            }

            // 4. Khớp tiện ích
            foreach ($analysis['filters']['amenities'] as $am) {
                $normAm = $this->normalize($am);
                if (str_contains($this->normalize($amenities), $normAm)) {
                    $score += 15;
                }
            }

            // 5. Khớp địa danh bóc tách
            foreach ($analysis['filters']['locations'] as $loc) {
                if (str_contains($this->normalize($buildingAddress), $this->normalize($loc))) {
                    $score += 35;
                }
            }

            // 6. Điểm uy tín của phòng / chủ trọ
            $avgRating = $room->reviews->avg('rating') ?: 4.0;
            $score += (int) round($avgRating * 2);

            $boostScore = (int) ($tenant->boost_score ?? 0);
            $score += $boostScore;

            return [
                'room' => $room,
                'score' => $score,
            ];
        });

        // Lọc lấy phòng có score > 0 (hoặc lấy tất cả nếu query trống)
        if ($analysis['normalized'] !== '') {
            $scoredRooms = $scoredRooms->filter(fn ($item) => $item['score'] > 0);
        }

        $sortedRooms = $scoredRooms->sortByDesc('score')->take($limit)->values();

        $formattedRooms = $sortedRooms->map(function ($item) {
            $room = $item['room'];
            $building = $room->building;
            $ratingAvg = $room->reviews->avg('rating') ?: 4.2;

            $images = is_array($room->images) && count($room->images) > 0 ? $room->images : [];
            $coverImage = $room->image ?: ($images[0] ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80');

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'title' => ($building?->name ?? 'Phòng trọ') . " - Phòng " . $room->room_number,
                'price' => (int) $room->price,
                'price_formatted' => number_format($room->price, 0, ',', '.') . 'đ',
                'area' => (int) ($room->area ?? 25),
                'area_formatted' => ($room->area ?? 25) . ' m²',
                'status' => $room->status,
                'building_name' => $building?->name ?? '',
                'address' => $building?->address ?? '',
                'cover_image' => $coverImage,
                'rating' => round($ratingAvg, 1),
                'reviews_count' => $room->reviews->count(),
                'amenities' => $room->amenities ?? [],
                'relevance_score' => $item['score'],
                'url' => route('renty.room.show', ['id' => $room->id], false),
            ];
        });

        return [
            'analysis' => $analysis,
            'count' => $formattedRooms->count(),
            'rooms' => $formattedRooms,
            'suggestions' => $this->getDynamicSuggestions($analysis),
        ];
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
