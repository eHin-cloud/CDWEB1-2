<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmartSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartSearchController extends Controller
{
    protected SmartSearchService $searchService;

    public function __construct(SmartSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * API Tìm kiếm phòng trọ thông minh với hỗ trợ sửa lỗi từ ngữ & xếp hạng liên quan
     * GET /api/renty/rooms/smart-search
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', $request->input('query', ''));
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

        return response()->json([
            'success' => true,
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
            'suggestions' => $result['suggestions'],
        ]);
    }

    /**
     * API Gợi ý từ khoá và sửa lỗi nhanh (Autocomplete / Quick suggestions)
     * GET /api/renty/rooms/suggest
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', $request->input('query', ''));

        if (trim($query) === '') {
            return response()->json([
                'success' => true,
                'did_you_mean' => null,
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
        ]);
    }
}
