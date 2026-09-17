<?php

namespace App\Http\Controllers;

use App\Events\RoomStatusUpdated;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoomMatrixRealtimeController extends Controller
{
    /**
     * Cập nhật nhanh trạng thái phòng (Housekeeping: cleaning, empty, maintenance)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $request->validate([
            'status' => 'required|string|in:empty,occupied,overdue,cleaning,maintenance',
        ]);

        $room = Room::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($id);
        
        $tenantId = $room->tenant_id;
        $oldStatus = $room->status;
        $newStatus = $request->input('status');

        // Logic bảo vệ an toàn nghiệp vụ
        if ($oldStatus === 'occupied' && in_array($newStatus, ['empty', 'cleaning'], true)) {
            $hasActiveResidents = $room->activeResidents()->exists();
            if ($hasActiveResidents) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng đang có cư dân ở. Vui lòng làm thủ tục trả phòng cho cư dân trước khi chuyển sang phòng trống hoặc dọn dẹp.',
                ], 422);
            }
        }

        $room->status = $newStatus;
        $room->save();

        // Chuẩn bị payload realtime
        $payload = [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'floor' => $room->floor,
            'status' => $room->status,
            'status_label' => $room->status_label,
            'badge_class' => $room->badge_class,
            'status_class' => $room->status_class,
            'price_formatted' => number_format($room->price) . 'đ',
            'updated_at' => now()->timestamp,
            'updated_by' => $user->name,
        ];

        // 1. Lưu vào Cache để SSE/Polling đọc ngay lập tức
        Cache::put("room_matrix_latest_event_{$tenantId}", $payload, 120);
        Cache::put("room_matrix_latest_event_global", $payload, 120);

        // 2. Kích hoạt Broadcast Event (cho Laravel Reverb / Echo)
        try {
            event(new RoomStatusUpdated($room));
        } catch (\Throwable $e) {
            // Ghi log để chẩn đoán nếu broadcast gặp trục trặc
            \Log::warning('RoomStatusUpdated broadcast notice: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật trạng thái phòng {$room->room_number} sang \"{$room->status_label}\".",
            'room' => $payload,
        ]);
    }

    /**
     * Server-Sent Events (SSE) stream kết nối thời gian thực cho Sơ đồ ma trận phòng
     */
    public function stream(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $tenantId = $user ? $user->tenant_id : null;

        if (!$tenantId) {
            abort(403);
        }

        $response = new StreamedResponse(function () use ($tenantId) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $cacheKey = "room_matrix_latest_event_{$tenantId}";
            $latestEvent = Cache::get($cacheKey);

            if ($latestEvent) {
                echo "event: room-updated\n";
                echo "data: " . json_encode($latestEvent) . "\n\n";
            } else {
                echo ": connected\n\n";
            }
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Connection', 'close');
        $response->headers->set('X-Accel-Buffering', 'no'); // Tắt buffering trên Nginx

        return $response;
    }

    /**
     * Endpoint fallback kiểm tra nhanh thay đổi (nếu trình duyệt bị ngắt SSE)
     */
    public function checkUpdates(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user ? $user->tenant_id : null;
        $since = (int) $request->query('since', 0);

        $cacheKey = $tenantId ? "room_matrix_latest_event_{$tenantId}" : "room_matrix_latest_event_global";
        $latestEvent = Cache::get($cacheKey) ?? Cache::get("room_matrix_latest_event_global");

        if ($latestEvent && isset($latestEvent['updated_at']) && $latestEvent['updated_at'] > $since) {
            return response()->json([
                'has_update' => true,
                'event' => $latestEvent,
            ]);
        }

        return response()->json(['has_update' => false]);
    }
}
