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

        $room = Room::where('tenant_id', $tenantId)->findOrFail($id);
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

        // 1. Lưu vào Cache để SSE Stream đọc ngay lập tức
        $cacheKey = "room_matrix_latest_event_{$tenantId}";
        Cache::put($cacheKey, $payload, 120);

        // 2. Kích hoạt Broadcast Event (cho Laravel Reverb / Echo)
        try {
            event(new RoomStatusUpdated($room));
        } catch (\Throwable $e) {
            // Không chặn request nếu broadcast driver chưa cấu hình
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
            // Đóng buffer để gửi dữ liệu tức thì
            if (ob_get_level()) {
                ob_end_clean();
            }

            $cacheKey = "room_matrix_latest_event_{$tenantId}";
            $lastSentTimestamp = 0;
            $startTime = time();

            // Giữ kết nối trong tối đa 25 giây (tránh timeout proxy web)
            while ((time() - $startTime) < 25) {
                if (connection_aborted()) {
                    break;
                }

                $latestEvent = Cache::get($cacheKey);

                if ($latestEvent && isset($latestEvent['updated_at']) && $latestEvent['updated_at'] > $lastSentTimestamp) {
                    $lastSentTimestamp = $latestEvent['updated_at'];
                    echo "event: room-updated\n";
                    echo "data: " . json_encode($latestEvent) . "\n\n";
                    flush();
                } else {
                    // Gửi heartbeat giữ kết nối sống
                    echo ": ping\n\n";
                    flush();
                }

                usleep(800000); // kiểm tra mỗi 0.8 giây
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Connection', 'keep-alive');
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

        if (!$tenantId) {
            return response()->json(['has_update' => false]);
        }

        $cacheKey = "room_matrix_latest_event_{$tenantId}";
        $latestEvent = Cache::get($cacheKey);

        if ($latestEvent && isset($latestEvent['updated_at']) && $latestEvent['updated_at'] > $since) {
            return response()->json([
                'has_update' => true,
                'event' => $latestEvent,
            ]);
        }

        return response()->json(['has_update' => false]);
    }
}
