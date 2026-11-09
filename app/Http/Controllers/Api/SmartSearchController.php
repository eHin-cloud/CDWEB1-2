<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ScoutSearchService;
use App\Services\SmartSearchService;
use App\Services\SolrSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartSearchController extends Controller
{
    protected SmartSearchService $searchService;
    protected ScoutSearchService $scoutSearchService;
    protected SolrSearchService $solrSearchService;

    public function __construct(
        SmartSearchService $searchService,
        ScoutSearchService $scoutSearchService,
        SolrSearchService $solrSearchService
    ) {
        $this->searchService = $searchService;
        $this->scoutSearchService = $scoutSearchService;
        $this->solrSearchService = $solrSearchService;
    }

    /**
     * API Tìm kiếm phòng trọ thông minh với hỗ trợ sửa lỗi từ ngữ & xếp hạng liên quan
     * GET /api/renty/rooms/smart-search
     */
    public function search(Request $request): JsonResponse
    {
        $query = mb_substr((string) $request->input('q', $request->input('query', '')), 0, 100);
        $limit = min(50, max(1, (int) $request->input('limit', 12)));
        $page = max(1, (int) $request->input('page', 1));
        $status = (string) $request->input('status', 'all');
        $sort = (string) $request->input('sort', $request->input('sort_by', 'default'));

        $minPrice = $request->filled('min_price')
            ? (int) $request->input('min_price')
            : ($request->filled('price_min') ? (int) $request->input('price_min') : null);

        $maxPrice = $request->filled('max_price')
            ? (int) $request->input('max_price')
            : ($request->filled('price_max') ? (int) $request->input('price_max') : null);

        // Kịch bản bẫy lỗi theo tài liệu nghiệm thu: Giá min > Giá max
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).',
                'errors' => [
                    'price' => ['Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).']
                ]
            ], 422);
        }

        $amenities = $request->input('amenities', []);
        if (is_string($amenities)) {
            $amenities = array_filter(explode(',', $amenities));
        }

        $rating = $request->filled('rating') && $request->input('rating') !== 'all'
            ? (float) $request->input('rating')
            : ($request->filled('min_rating') ? (float) $request->input('min_rating') : null);

        $engine = (string) $request->input('engine', env('SEARCH_ENGINE', 'scout'));

        // Tìm kiếm bằng Apache Solr Search Engine nếu được yêu cầu
        if ($engine === 'solr') {
            return $this->solrSearch($request);
        }

        // Mặc định sử dụng Laravel Scout Search Engine (Elasticsearch)
        if ($engine === 'scout') {
            return $this->scoutSearch($request);
        }

        $result = $this->searchService->search($query, [
            'limit' => $limit,
            'page' => $page,
            'status' => $status,
            'sort' => $sort,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'amenities' => $amenities,
            'min_rating' => $rating,
        ]);

        $footballData = $this->searchPlayersAndMatches($query);

        return response()->json([
            'success' => true,
            'engine' => 'hybrid',
            'original_query' => $result['analysis']['original'],
            'normalized_query' => $result['analysis']['normalized'],
            'corrected_query' => $result['analysis']['corrected'],
            'did_you_mean' => $result['analysis']['did_you_mean'],
            'has_correction' => !empty($result['analysis']['did_you_mean']),
            'recognized_terms' => $result['analysis']['recognized_terms'],
            'filters' => $result['analysis']['filters'],
            'count' => $result['count'],
            'total' => $result['total'] ?? $result['count'],
            'page' => $result['page'] ?? $page,
            'per_page' => $result['per_page'] ?? $limit,
            'has_more' => $result['has_more'] ?? false,
            'rooms' => $result['rooms'],
            'players' => $footballData['players'],
            'matches' => $footballData['matches'],
            'suggestions' => $result['suggestions'],
            'hybrid_search' => $result['hybrid_search'] ?? null,
        ]);
    }

    /**
     * API Tìm kiếm phòng trọ bằng Laravel Scout Engine
     * GET /api/renty/rooms/scout-search
     */
    public function scoutSearch(Request $request): JsonResponse
    {
        $query = mb_substr((string) $request->input('q', $request->input('query', '')), 0, 100);
        $limit = min(50, max(1, (int) $request->input('limit', 12)));
        $page = max(1, (int) $request->input('page', 1));
        $status = (string) $request->input('status', 'all');
        $sort = (string) $request->input('sort', $request->input('sort_by', 'default'));

        $minPrice = $request->filled('min_price')
            ? (int) $request->input('min_price')
            : ($request->filled('price_min') ? (int) $request->input('price_min') : null);

        $maxPrice = $request->filled('max_price')
            ? (int) $request->input('max_price')
            : ($request->filled('price_max') ? (int) $request->input('price_max') : null);

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).',
                'errors' => [
                    'price' => ['Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).']
                ]
            ], 422);
        }

        $amenities = $request->input('amenities', []);
        if (is_string($amenities)) {
            $amenities = array_filter(explode(',', $amenities));
        }

        $rating = $request->filled('rating') && $request->input('rating') !== 'all'
            ? (float) $request->input('rating')
            : ($request->filled('min_rating') ? (float) $request->input('min_rating') : null);

        $analysis = $this->searchService->analyzeAndCorrect($query);

        $result = $this->scoutSearchService->search($query, [
            'limit' => $limit,
            'page' => $page,
            'status' => $status,
            'sort' => $sort,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'amenities' => $amenities,
            'min_rating' => $rating,
        ]);

        $footballData = $this->searchPlayersAndMatches($query);

        return response()->json([
            'success' => true,
            'engine' => 'laravel_scout',
            'driver' => $result['driver'] ?? config('scout.driver'),
            'original_query' => $query,
            'normalized_query' => $analysis['normalized'] ?? $query,
            'corrected_query' => $analysis['corrected'] ?? $query,
            'did_you_mean' => $analysis['did_you_mean'] ?? null,
            'has_correction' => !empty($analysis['did_you_mean']),
            'recognized_terms' => $analysis['recognized_terms'] ?? [],
            'filters' => [
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'amenities' => $amenities,
                'locations' => $analysis['filters']['locations'] ?? [],
            ],
            'count' => $result['count'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'last_page' => $result['last_page'] ?? 1,
            'has_more' => $result['has_more'],
            'rooms' => $result['rooms'],
            'players' => $footballData['players'],
            'matches' => $footballData['matches'],
            'suggestions' => $result['suggestions'],
        ]);
    }

    /**
     * API Gợi ý từ khoá và sửa lỗi nhanh (Autocomplete / Quick suggestions)
     * GET /api/renty/rooms/suggest
     */
    /**
     * Tìm kiếm phòng trọ bằng Apache Solr Engine
     */
    public function solrSearch(Request $request): JsonResponse
    {
        $query = mb_substr((string) $request->input('q', $request->input('query', '')), 0, 100);
        $limit = min(50, max(1, (int) $request->input('limit', 12)));
        $page = max(1, (int) $request->input('page', 1));
        $status = (string) $request->input('status', 'all');
        $sort = (string) $request->input('sort', $request->input('sort_by', 'default'));

        $minPrice = $request->filled('min_price')
            ? (int) $request->input('min_price')
            : ($request->filled('price_min') ? (int) $request->input('price_min') : null);

        $maxPrice = $request->filled('max_price')
            ? (int) $request->input('max_price')
            : ($request->filled('price_max') ? (int) $request->input('price_max') : null);

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).',
                'errors' => [
                    'price' => ['Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa).']
                ]
            ], 422);
        }

        $amenities = $request->input('amenities', []);
        if (is_string($amenities)) {
            $amenities = array_filter(explode(',', $amenities));
        }

        $rating = $request->filled('rating') && $request->input('rating') !== 'all'
            ? (float) $request->input('rating')
            : ($request->filled('min_rating') ? (float) $request->input('min_rating') : null);

        $result = $this->solrSearchService->search($query, [
            'limit' => $limit,
            'page' => $page,
            'status' => $status,
            'sort' => $sort,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'amenities' => $amenities,
            'min_rating' => $rating,
            'room_type' => $request->input('room_type'),
        ]);

        $footballData = $this->searchPlayersAndMatches($query);

        return response()->json([
            'success' => true,
            'engine' => 'apache_solr',
            'original_query' => $query,
            'normalized_query' => $result['analysis']['normalized'] ?? $query,
            'corrected_query' => $result['analysis']['corrected'] ?? $query,
            'did_you_mean' => null,
            'has_correction' => false,
            'recognized_terms' => [],
            'filters' => [
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'amenities' => $amenities,
                'status' => $status,
            ],
            'count' => $result['count'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'last_page' => ceil(max(1, $result['total']) / $limit),
            'has_more' => $result['has_more'],
            'rooms' => $result['rooms'],
            'players' => $footballData['players'],
            'matches' => $footballData['matches'],
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = mb_substr((string) $request->input('q', $request->input('query', '')), 0, 100);

        if (trim($query) === '') {
            return response()->json([
                'success' => true,
                'did_you_mean' => null,
                'players' => [],
                'suggestions' => $this->searchService->getQuickSuggestions(),
            ]);
        }

        $analysis = $this->searchService->analyzeAndCorrect($query);

        return response()->json([
            'success' => true,
            'original' => $analysis['original'],
            'corrected' => $analysis['corrected'],
            'did_you_mean' => $analysis['did_you_mean'],
            'has_correction' => !empty($analysis['did_you_mean']),
            'recognized_terms' => $analysis['recognized_terms'],
            'filters' => $analysis['filters'],
            'players' => $this->searchPlayers($query),
        ]);
    }

    /**
     * API chuyên biệt trả về hồ sơ siêu sao Lionel Messi (M10)
     * GET /api/messi
     * GET /api/m10
     */
    /**
     * API chuyên biệt trả về hồ sơ siêu sao Cristiano Ronaldo (CR7, Ronado)
     * GET /api/ronaldo
     * GET /api/cr7
     * GET /api/cr
     */
    public function getRonaldo(Request $request): JsonResponse
    {
        $ronaldo = \App\Models\Player::where('name', 'like', '%Ronaldo%')
            ->orWhere('nickname', 'like', '%CR7%')
            ->first();

        if (!$ronaldo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ cầu thủ Cristiano Ronaldo trong hệ thống.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'api' => 'ronaldo',
            'endpoint' => '/api/ronaldo',
            'player' => [
                'id' => $ronaldo->id,
                'name' => $ronaldo->name,
                'full_name' => $ronaldo->full_name,
                'nickname' => $ronaldo->nickname,
                'date_of_birth' => $ronaldo->date_of_birth ? $ronaldo->date_of_birth->format('d/m/Y') : null,
                'birth_place' => $ronaldo->birth_place,
                'nationality' => $ronaldo->nationality,
                'position' => $ronaldo->position,
                'jersey_number' => $ronaldo->jersey_number,
                'current_club' => $ronaldo->current_club,
                'former_clubs' => $ronaldo->former_clubs,
                'height' => $ronaldo->height,
                'preferred_foot' => $ronaldo->preferred_foot,
                'biography' => $ronaldo->biography,
                'career_stats' => $ronaldo->career_stats,
                'achievements' => $ronaldo->achievements,
                'famous_matches' => $ronaldo->famous_matches,
                'avatar_url' => $ronaldo->avatar_url,
            ]
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getMessi(Request $request): JsonResponse
    {
        $messi = \App\Models\Player::where('name', 'like', '%Messi%')
            ->orWhere('nickname', 'like', '%M10%')
            ->first();

        if (!$messi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ cầu thủ Lionel Messi trong hệ thống.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'api' => 'messi',
            'endpoint' => '/api/messi',
            'player' => [
                'id' => $messi->id,
                'name' => $messi->name,
                'full_name' => $messi->full_name,
                'nickname' => $messi->nickname,
                'date_of_birth' => $messi->date_of_birth ? $messi->date_of_birth->format('d/m/Y') : null,
                'birth_place' => $messi->birth_place,
                'nationality' => $messi->nationality,
                'position' => $messi->position,
                'jersey_number' => $messi->jersey_number,
                'current_club' => $messi->current_club,
                'former_clubs' => $messi->former_clubs,
                'height' => $messi->height,
                'preferred_foot' => $messi->preferred_foot,
                'biography' => $messi->biography,
                'career_stats' => $messi->career_stats,
                'achievements' => $messi->achievements,
                'famous_matches' => $messi->famous_matches,
                'avatar_url' => $messi->avatar_url,
            ]
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Tìm kiếm thông tin cầu thủ bóng đá trong bảng players
     *
     * @param string $query
     * @return array
     */
    /**
     * Tìm kiếm thông tin cầu thủ và tách riêng từng trận đấu độc lập
     * Không gộp chung các trận đấu lại mà phân tách theo đúng từ khóa tìm kiếm
     *
     * @param string $query
     * @return array{players: array, matches: array}
     */
    protected function searchPlayersAndMatches(string $query): array
    {
        $clean = trim($query);
        if (mb_strlen($clean) < 2) {
            return ['players' => [], 'matches' => []];
        }

        try {
            $normQuery = $this->searchService->normalize($clean);
            $tokens = array_filter(explode(' ', $normQuery));
            $allPlayers = \App\Models\Player::all();

            // Cụm từ đầy đủ & các ký tự viết tắt chuẩn cho Lionel Messi (ms, m10, lm10, leo...)
            $messiFullPhrases = ['messi', 'lionel messi', 'lionel', 'leomessi', 'la pulga', 'inter miami', 'barca', 'barcelona', 'argentina'];
            $messiAbbreviations = ['ms', 'm10', 'lm10', 'leo', 'pulga'];

            // Cụm từ đầy đủ & các ký tự viết tắt chuẩn cho Cristiano Ronaldo (cr, cr7, ro, rn, r7...)
            $ronaldoFullPhrases = ['ronaldo', 'ronado', 'el bicho', 'al nassr', 'real madrid', 'bo dao nha', 'portugal'];
            $ronaldoAbbreviations = ['cr', 'cr7', 'ro', 'rn', 'r7', 'siu', 'siuuu'];

            $isSearchMessi = false;
            if (in_array($normQuery, $messiAbbreviations) || array_intersect($tokens, $messiAbbreviations)) {
                $isSearchMessi = true;
            } else {
                foreach ($messiFullPhrases as $kw) {
                    if ($normQuery === $kw || str_contains($normQuery, $kw)) {
                        $isSearchMessi = true;
                        break;
                    }
                }
            }

            $isSearchRonaldo = false;
            if (in_array($normQuery, $ronaldoAbbreviations) || array_intersect($tokens, $ronaldoAbbreviations)) {
                $isSearchRonaldo = true;
            } else {
                foreach ($ronaldoFullPhrases as $kw) {
                    if ($normQuery === $kw || str_contains($normQuery, $kw)) {
                        $isSearchRonaldo = true;
                        break;
                    }
                }
            }

            $targetPlayers = collect();
            if ($isSearchMessi && !$isSearchRonaldo) {
                $targetPlayers = $allPlayers->filter(fn($p) => str_contains(strtolower($p->name), 'messi'));
            } elseif ($isSearchRonaldo && !$isSearchMessi) {
                $targetPlayers = $allPlayers->filter(fn($p) => str_contains(strtolower($p->name), 'ronaldo'));
            }

            $matchedMatches = [];
            $matchedPlayerIds = [];

            if ($targetPlayers->isNotEmpty()) {
                foreach ($targetPlayers as $p) {
                    $matchedPlayerIds[] = $p->id;
                    $famousMatches = is_array($p->famous_matches) ? $p->famous_matches : json_decode($p->famous_matches, true) ?? [];
                    foreach ($famousMatches as $idx => $m) {
                        $matchedMatches[] = [
                            'id' => 'match_' . $p->id . '_' . ($idx + 1),
                            'player_id' => $p->id,
                            'player_name' => $p->name,
                            'player_nickname' => $p->nickname,
                            'player_number' => $p->jersey_number,
                            'player_club' => $p->current_club,
                            'player_avatar' => $p->avatar_url,
                            'match' => $m['match'] ?? '',
                            'tournament' => $m['tournament'] ?? 'Trận cầu đỉnh cao',
                            'stadium' => $m['stadium'] ?? '',
                            'date' => $m['date'] ?? '',
                            'highlight' => $m['highlight'] ?? '',
                        ];
                    }
                }
            } else {
                // Tìm kiếm theo từ khóa trận đấu cụ thể
                foreach ($allPlayers as $p) {
                    $famousMatches = is_array($p->famous_matches) ? $p->famous_matches : json_decode($p->famous_matches, true) ?? [];
                    foreach ($famousMatches as $idx => $m) {
                        $matchText = ($m['match'] ?? '') . ' ' . ($m['tournament'] ?? '') . ' ' . ($m['stadium'] ?? '') . ' ' . ($m['highlight'] ?? '');
                        $normMatchText = $this->searchService->normalize($matchText);

                        $matched = false;
                        if (str_contains($normMatchText, $normQuery)) {
                            $matched = true;
                        } else {
                            $tokens = array_filter(explode(' ', $normQuery));
                            $tokenMatchCount = 0;
                            foreach ($tokens as $token) {
                                if (mb_strlen($token) >= 3 && str_contains($normMatchText, $token)) {
                                    $tokenMatchCount++;
                                }
                            }
                            if ($tokenMatchCount >= 2 || ($tokenMatchCount >= 1 && count($tokens) === 1)) {
                                $matched = true;
                            }
                        }

                        if ($matched) {
                            $matchedMatches[] = [
                                'id' => 'match_' . $p->id . '_' . ($idx + 1),
                                'player_id' => $p->id,
                                'player_name' => $p->name,
                                'player_nickname' => $p->nickname,
                                'player_number' => $p->jersey_number,
                                'player_club' => $p->current_club,
                                'player_avatar' => $p->avatar_url,
                                'match' => $m['match'] ?? '',
                                'tournament' => $m['tournament'] ?? 'Trận cầu đỉnh cao',
                                'stadium' => $m['stadium'] ?? '',
                                'date' => $m['date'] ?? '',
                                'highlight' => $m['highlight'] ?? '',
                            ];
                            $matchedPlayerIds[] = $p->id;
                        }
                    }
                }
            }

            $matchedPlayerIds = array_unique($matchedPlayerIds);
            $players = $allPlayers->whereIn('id', $matchedPlayerIds)->values()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'full_name' => $p->full_name,
                    'nickname' => $p->nickname,
                    'date_of_birth' => $p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : null,
                    'birth_place' => $p->birth_place,
                    'nationality' => $p->nationality,
                    'position' => $p->position,
                    'jersey_number' => $p->jersey_number,
                    'current_club' => $p->current_club,
                    'former_clubs' => $p->former_clubs,
                    'height' => $p->height,
                    'preferred_foot' => $p->preferred_foot,
                    'biography' => $p->biography,
                    'career_stats' => $p->career_stats,
                    'achievements' => $p->achievements,
                    'famous_matches' => $p->famous_matches,
                    'avatar_url' => $p->avatar_url,
                ];
            })->all();

            return [
                'players' => $players,
                'matches' => $matchedMatches
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('searchPlayersAndMatches error: ' . $e->getMessage());
            return ['players' => [], 'matches' => []];
        }
    }

    /**
     * Tìm kiếm thông tin cầu thủ bóng đá trong bảng players (tương thích ngược)
     *
     * @param string $query
     * @return array
     */
    protected function searchPlayers(string $query): array
    {
        return $this->searchPlayersAndMatches($query)['players'];
    }
}
