<?php

namespace App\Observers;

use App\Models\Room;
use App\Services\SolrSearchService;
use Exception;
use Illuminate\Support\Facades\Log;

class RoomSolrObserver
{
    protected SolrSearchService $solrService;

    public function __construct(SolrSearchService $solrService)
    {
        $this->solrService = $solrService;
    }

    /**
     * Tự động thêm/cập nhật phòng vào Solr khi tạo mới hoặc sửa thông tin
     */
    public function saved(Room $room): void
    {
        try {
            if (!$room->relationLoaded('building')) {
                $room->load('building');
            }
            $this->solrService->indexRooms([$room]);
        } catch (Exception $e) {
            Log::warning('Solr auto-sync on saved failed: ' . $e->getMessage());
        }
    }

    /**
     * Tự động xoá phòng khỏi Solr khi phòng bị xoá khỏi CSDL
     */
    public function deleted(Room $room): void
    {
        try {
            $this->solrService->deleteRoom($room->id);
        } catch (Exception $e) {
            Log::warning('Solr auto-sync on deleted failed: ' . $e->getMessage());
        }
    }

    /**
     * Khi khôi phục phòng (nếu dùng soft delete)
     */
    public function restored(Room $room): void
    {
        $this->saved($room);
    }
}

