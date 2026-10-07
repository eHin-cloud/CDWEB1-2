<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Hybrid Search Service: Kiến trúc Dual Retrieval (Sparse BM25 + Dense Semantic)
 * và Reciprocal Rank Fusion (RRF) theo chuẩn Enterprise Search Architecture.
 */
class HybridSearchService
{
    protected VietnameseTokenizerService $tokenizer;

    // Các hằng số thuật toán chuẩn
    protected float $k1 = 1.2;  // BM25 term saturation
    protected float $b = 0.75;  // BM25 document length normalization
    protected int $rrfK = 60;   // RRF rank smoothing constant (Chuẩn Elasticsearch / Pinecone)

    public function __construct(VietnameseTokenizerService $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Thực thi quy trình tìm kiếm kết hợp (Hybrid Search with RRF)
     *
     * @param string $query Chuỗi tìm kiếm của người dùng
     * @param Collection $rooms Tập hợp các phòng (đã được decorate hoặc nạp data)
     * @param array $options Cấu hình tuỳ chọn (k, weights, v.v.)
     * @return Collection Danh sách phòng đã được xếp hạng lại theo RRF
     */
    public function search(string $query, Collection $rooms, array $options = []): Collection
    {
        if ($rooms->isEmpty() || trim($query) === '') {
            return $rooms;
        }

        $kConstant = (int) ($options['rrf_k'] ?? $this->rrfK);
        $wSparse = (float) ($options['w_sparse'] ?? 1.0);
        $wDense = (float) ($options['w_dense'] ?? 1.0);

        // Bước 1: Phân tích và tách từ tiếng Việt dựa trên Viet74K
        $queryTokens = $this->tokenizer->tokenize($query);
        $queryCompounds = $this->tokenizer->extractCompoundWords($query);
        $cleanQuery = $this->tokenizer->removeAccents(mb_strtolower($query, 'UTF-8'));

        // Bước 2: Luồng Sparse Retrieval (Okapi BM25)
        $sparseRanked = $this->runSparseBM25($queryTokens, $queryCompounds, $cleanQuery, $rooms);

        // Bước 3: Luồng Dense Retrieval (Semantic Feature Cosine Matching)
        $denseRanked = $this->runDenseSemantic($query, $rooms);

        // Bước 4: Score Fusion bằng Reciprocal Rank Fusion (RRF)
        return $this->fuseWithRRF($rooms, $sparseRanked, $denseRanked, $kConstant, $wSparse, $wDense, [
            'query_tokens' => $queryTokens,
            'query_compounds' => $queryCompounds,
        ]);
    }

    /**
     * Luồng 1: Sparse Retrieval Engine (BM25)
     * Bắt chính xác Exact Keyword, mã phòng, số phòng, địa danh, tiện ích cụ thể.
     */
    protected function runSparseBM25(array $queryTokens, array $queryCompounds, string $cleanQuery, Collection $rooms): array
    {
        // 1. Chuẩn bị tài liệu và tính Document Frequency (DF)
        $docTokensMap = [];
        $docLengths = [];
        $totalDocLength = 0;
        $totalDocs = $rooms->count();

        // Gộp các token cần tính điểm: gồm token đơn, từ ghép, và cả chuỗi không dấu
        $allSearchTerms = array_unique(array_filter(array_merge(
            $queryTokens,
            $queryCompounds,
            [$cleanQuery]
        )));

        $dfMap = [];
        foreach ($allSearchTerms as $term) {
            $dfMap[$term] = 0;
        }

        foreach ($rooms as $room) {
            $roomId = $room['id'] ?? spl_object_id($room);

            // Xây dựng kho văn bản có thể tìm kiếm của phòng
            $docContent = $this->buildRoomSearchableText($room);
            $tokens = $this->tokenizer->tokenize($docContent);
            $cleanDocText = $this->tokenizer->removeAccents(mb_strtolower($docContent, 'UTF-8'));

            $docTokensMap[$roomId] = [
                'tokens' => $tokens,
                'clean_text' => $cleanDocText,
                'room_number' => (string) ($room['room_number'] ?? ''),
            ];

            $docLen = count($tokens);
            $docLengths[$roomId] = $docLen;
            $totalDocLength += $docLen;

            // Đếm Document Frequency (DF)
            foreach ($allSearchTerms as $term) {
                $termClean = $this->tokenizer->removeAccents(mb_strtolower($term, 'UTF-8'));
                if (in_array($term, $tokens, true) || str_contains($cleanDocText, $termClean)) {
                    $dfMap[$term]++;
                }
            }
        }

        $avgdl = $totalDocs > 0 ? ($totalDocLength / $totalDocs) : 1;

        // 2. Tính điểm Okapi BM25 cho từng phòng
        $sparseScores = [];
        foreach ($rooms as $room) {
            $roomId = $room['id'] ?? spl_object_id($room);
            $docInfo = $docTokensMap[$roomId];
            $docLen = $docLengths[$roomId];
            $score = 0.0;

            foreach ($allSearchTerms as $term) {
                $df = $dfMap[$term] ?? 0;
                if ($df === 0) {
                    continue;
                }

                // IDF: Robertson-Spärck Jones formula
                $idf = log(1 + ($totalDocs - $df + 0.5) / ($df + 0.5));
                if ($idf < 0) {
                    $idf = 0.05;
                }

                // Tính TF (Term Frequency)
                $tf = 0;
                $termClean = $this->tokenizer->removeAccents(mb_strtolower($term, 'UTF-8'));
                foreach ($docInfo['tokens'] as $t) {
                    if (mb_strtolower($t, 'UTF-8') === mb_strtolower($term, 'UTF-8')) {
                        $tf++;
                    }
                }
                if ($tf === 0 && str_contains($docInfo['clean_text'], $termClean)) {
                    $tf = 1;
                }

                // Trọng số nhân thêm nếu là từ ghép tiếng Việt (Compound word boost)
                $termWeight = str_contains($term, ' ') ? 1.5 : 1.0;

                // Công thức BM25:
                $numerator = $tf * ($this->k1 + 1);
                $denominator = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLen / max(1, $avgdl)));
                $score += $idf * ($numerator / max(0.001, $denominator)) * $termWeight;
            }

            // Exact match boost: Khớp chính xác số phòng
            if ($cleanQuery !== '' && $docInfo['room_number'] === $cleanQuery) {
                $score += 25.0;
            }

            $sparseScores[$roomId] = $score;
        }

        // Sắp xếp giảm dần theo Sparse Score để tính Rank
        arsort($sparseScores);
        $ranked = [];
        $rank = 1;
        foreach ($sparseScores as $roomId => $score) {
            $ranked[$roomId] = [
                'rank' => $rank++,
                'score' => round($score, 4),
            ];
        }

        return $ranked;
    }

    /**
     * Luồng 2: Dense Retrieval Engine (Semantic Cosine Matching)
     * Bắt ý định người dùng (User intent), phong cách, đối tượng, khoảng giá, từ đồng nghĩa.
     */
    protected function runDenseSemantic(string $query, Collection $rooms): array
    {
        // 1. Vectorize câu query thành Dense Intent Vector
        $queryVector = $this->buildSemanticVector($query);

        $denseScores = [];
        foreach ($rooms as $room) {
            $roomId = $room['id'] ?? spl_object_id($room);

            // Vectorize phòng
            $roomContent = $this->buildRoomSearchableText($room);
            $roomVector = $this->buildSemanticVector($roomContent, $room);

            // Tính Cosine Similarity: dot_product / (|A| * |B|)
            $cosineSim = $this->computeCosineSimilarity($queryVector, $roomVector);

            // Khuyến khích rating và trust badge trong ngữ nghĩa uy tín
            $trustBoost = isset($room['trust_badge']['label']) && str_contains($room['trust_badge']['label'], 'Thẩm Định') ? 0.05 : 0;
            $denseScores[$roomId] = min(1.0, max(0.0, $cosineSim + $trustBoost));
        }

        // Sắp xếp giảm dần theo Dense Score để tính Rank
        arsort($denseScores);
        $ranked = [];
        $rank = 1;
        foreach ($denseScores as $roomId => $score) {
            $ranked[$roomId] = [
                'rank' => $rank++,
                'score' => round($score, 4),
            ];
        }

        return $ranked;
    }

    /**
     * Luồng 3: Hợp nhất điểm theo Reciprocal Rank Fusion (RRF)
     * Công thức chuẩn: RRF_Score(d) = (W_sparse / (k + Rank_sparse)) + (W_dense / (k + Rank_dense))
     */
    protected function fuseWithRRF(
        Collection $rooms,
        array $sparseRanked,
        array $denseRanked,
        int $k,
        float $wSparse,
        float $wDense,
        array $meta = []
    ): Collection {
        return $rooms->map(function ($room) use ($sparseRanked, $denseRanked, $k, $wSparse, $wDense, $meta) {
            $roomId = $room['id'] ?? spl_object_id($room);

            $sparseInfo = $sparseRanked[$roomId] ?? ['rank' => 999, 'score' => 0.0];
            $denseInfo = $denseRanked[$roomId] ?? ['rank' => 999, 'score' => 0.0];

            $sRank = $sparseInfo['rank'];
            $dRank = $denseInfo['rank'];

            // Công thức RRF
            $rrfScore = ($wSparse / ($k + $sRank)) + ($wDense / ($k + $dRank));

            // Bổ sung siêu dữ liệu Hybrid Search vào từng phòng
            if (is_array($room)) {
                $room['retrieval_method'] = 'hybrid_rrf';
                $room['rrf_score'] = round($rrfScore, 6);
                $room['sparse_rank'] = $sRank;
                $room['dense_rank'] = $dRank;
                $room['sparse_score'] = $sparseInfo['score'];
                $room['dense_score'] = $denseInfo['score'];
                $room['vietnamese_tokens'] = $meta['query_tokens'] ?? [];
                $room['vietnamese_compounds'] = $meta['query_compounds'] ?? [];

                // Quy đổi sang thang relevance_score tương thích hệ thống cũ
                $room['hybrid_score'] = (int) round($rrfScore * 10000);
                if (isset($room['relevance_score'])) {
                    $room['relevance_score'] = max((int) $room['relevance_score'], $room['hybrid_score']);
                } else {
                    $room['relevance_score'] = $room['hybrid_score'];
                }
            }

            return $room;
        })->sortByDesc(function ($room) {
            return is_array($room) ? ($room['rrf_score'] ?? 0) : 0;
        })->values();
    }

    /**
     * Xây dựng văn bản tổng hợp của phòng để phục vụ tìm kiếm
     */
    protected function buildRoomSearchableText($room): string
    {
        if (is_array($room)) {
            $num = $room['room_number'] ?? '';
            $title = $room['title'] ?? '';
            $building = $room['building_name'] ?? '';
            $address = $room['address'] ?? '';
            $areaName = $room['area_name'] ?? '';
            $amenities = implode(' ', (array) ($room['amenities'] ?? []));
            $desc = $room['location_description'] ?? ($room['description'] ?? '');

            return "{$num} {$title} {$building} {$address} {$areaName} {$amenities} {$desc}";
        }

        return "{$room->room_number} {$room->building?->name} {$room->building?->address} {$room->description}";
    }

    /**
     * Biểu diễn văn bản dưới dạng Semantic Intent Vector (Dense Vector)
     * Bao gồm các chiều không gian ngữ nghĩa: Ngân sách, Phong cách sống, Tiện nghi, Khu vực
     */
    protected function buildSemanticVector(string $text, $roomContext = null): array
    {
        $clean = mb_strtolower($this->tokenizer->removeAccents($text), 'UTF-8');

        // Định nghĩa 15 chiều ngữ nghĩa trọng điểm (Semantic Dimensions)
        $dimensions = [
            'budget_student' => ['sinh vien', 'gia re', 'duoi 3 trieu', 'duoi 3tr', 'tiet kiem', 'o ghep'],
            'budget_mid' => ['3 den 5 trieu', 'tam trung', 'hop ly', '4 trieu', '5 trieu'],
            'budget_premium' => ['cao cap', 'sang trong', 'duplex', 'penthouse', 'full noi that', 'tien nghi cao'],
            'type_studio' => ['studio', 'studo', 'can ho mini'],
            'type_shared' => ['phong tro', 'nha tro', 'o ghep'],
            'amenity_cooling' => ['dieu hoa', 'may lanh', 'mat me', 'tranh nong', 'mat ruoi'],
            'amenity_laundry' => ['may giat', 'san phoi', 'giat do', 'may giat rieng'],
            'amenity_loft' => ['gac lung', 'gac xep', 'gac', 'tang lung'],
            'amenity_balcony' => ['ban cong', 'thoang mat', 'anh sang', 'cua so', 'don nang', 'view thoang'],
            'amenity_wc' => ['khep kin', 'wc rieng', 've sinh rieng', 've sinh khep kin'],
            'lifestyle_pet' => ['thu cung', 'nuoi pet', 'cho meo', 'pet friendly', 'nuoi cun', 'nuoi cho'],
            'lifestyle_cooking' => ['nau an', 'ke bep', 'bep rieng', 'nau nuong', 'tu nau an', 'hut mui'],
            'lifestyle_security' => ['an ninh', 'camera', 'khoa van tay', 'khoa tu', 'bao ve', 'an toan', 'gio giac tu do'],
            'lifestyle_chill' => ['view dep', 'ngam hoang hon', 'chill', 'song ao', 'thoang dang', 'view trieu do', 'sang sua', 'dep mat'],
            'lifestyle_privacy' => ['o mot minh', 'rieng tu', 'yen tinh', 'on thi', 'khong chung chu', 'tu do', 'cach am'],
            'lifestyle_worker' => ['nguoi di lam', 'cong so', 'xach vali vao o', 'full noi that', 'day du do', 'chuyen nghiep'],
            'location_north' => ['cau giay', 'thanh xuan', 'dong da', 'hai ba trung', 'ba dinh', 'bach khoa', 'ha noi'],
            'location_south' => ['quan 1', 'quan 3', 'quan 7', 'binh thanh', 'thu duc', 'go vap', 'hutech', 'sai gon'],
        ];

        $vector = [];
        foreach ($dimensions as $dim => $keywords) {
            $val = 0.0;
            foreach ($keywords as $kw) {
                if (str_contains($clean, $kw)) {
                    $val += 1.0;
                }
            }

            // Nếu có roomContext, kết hợp thêm dữ liệu trường cấu trúc
            if ($roomContext && is_array($roomContext)) {
                if ($dim === 'lifestyle_pet' && !empty($roomContext['pets'])) $val += 1.5;
                if ($dim === 'amenity_loft' && !empty($roomContext['loft'])) $val += 1.5;
                if ($dim === 'amenity_balcony' && !empty($roomContext['balcony'])) $val += 1.5;
                if ($dim === 'lifestyle_chill' && !empty($roomContext['balcony'])) $val += 1.2;
                if ($dim === 'amenity_wc' && !empty($roomContext['wc'])) $val += 1.5;
                if ($dim === 'lifestyle_privacy' && !empty($roomContext['wc'])) $val += 1.2;
                if ($dim === 'budget_student' && ($roomContext['price'] ?? 0) <= 3000000) $val += 1.2;
                if ($dim === 'budget_mid' && ($roomContext['price'] ?? 0) > 3000000 && ($roomContext['price'] ?? 0) <= 5000000) $val += 1.2;
                if ($dim === 'budget_premium' && ($roomContext['price'] ?? 0) > 5000000) $val += 1.5;
                if ($dim === 'lifestyle_worker' && ($roomContext['price'] ?? 0) >= 3500000) $val += 1.0;
            }

            $vector[$dim] = $val;
        }

        return $vector;
    }

    /**
     * Tính độ tương đồng Cosine Similarity giữa hai Dense Vector
     */
    protected function computeCosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $dim => $valA) {
            $valB = $vecB[$dim] ?? 0.0;
            $dotProduct += ($valA * $valB);
            $normA += ($valA * $valA);
            $normB += ($valB * $valB);
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
