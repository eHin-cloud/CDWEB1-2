<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomAlert;
use Illuminate\Support\Facades\Log;

class RoomAlertService
{
    /**
     * Kích hoạt bắn thông báo tới các alert phù hợp khi phòng chuyển sang 'empty' hoặc phòng mới được tạo
     */
    public static function notifySubscribersForRoom(Room $room): int
    {
        if ($room->status !== 'empty') {
            return 0;
        }

        $room->loadMissing('building');
        $district = $room->building?->district ?? '';
        $roomPrice = (int) $room->price;

        // Tìm các alert đang active và thỏa điều kiện:
        // 1. Alert đăng ký đúng phòng này (room_id == $room->id)
        // 2. Alert đăng ký theo khu vực (room_id null hoặc bất kỳ) có target_district khớp và max_budget >= roomPrice
        $matchingAlerts = RoomAlert::where('status', 'active')
            ->where(function ($query) use ($room, $district, $roomPrice) {
                $query->where('room_id', $room->id);

                if (!empty($district)) {
                    $query->orWhere(function ($q) use ($district, $roomPrice) {
                        $q->where('max_budget', '>=', $roomPrice)
                          ->where(function ($sub) use ($district) {
                              $sub->where('target_district', 'like', "%{$district}%")
                                  ->orWhereRaw('? LIKE CONCAT("%", target_district, "%")', [$district]);
                          });
                    });
                }
            })
            ->get();

        $count = 0;
        foreach ($matchingAlerts as $alert) {
            $alert->update([
                'status' => 'notified',
                'notified_at' => now(),
            ]);

            Log::info("FEAT_24_ROOM_ALERT: Đã gửi thông báo phòng trống tới [{$alert->contact_info}] cho phòng [{$room->room_number}] - Tòa [{$room->building?->name}]");
            $count++;
        }

        return $count;
    }
}
