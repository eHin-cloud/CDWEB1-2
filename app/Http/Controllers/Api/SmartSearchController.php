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
        $limit = min(20, max(1, (int) $request->input('limit', 6)));
        $status = (string) $request->input('status', 'all');

        $result = $this->searchService->search($query, [
            'limit' => $limit,
            'status' => $status,
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
