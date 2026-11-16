<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Room;
use App\Models\RoomAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $address = $room->building?->address ?? '';
        $buildingName = $room->building?->name ?? '';
        $roomPrice = (int) $room->price;

        // Tìm các alert đang active và thỏa điều kiện:
        // 1. Alert đăng ký đúng phòng này (room_id == $room->id)
        // 2. Alert đăng ký theo khu vực (room_id null hoặc bất kỳ) có target_district khớp và max_budget >= roomPrice
        $matchingAlerts = RoomAlert::where('status', 'active')
            ->where(function ($query) use ($room, $roomPrice) {
                $query->where('room_id', $room->id)
                      ->orWhere('max_budget', '>=', $roomPrice);
            })
            ->get()
            ->filter(function ($alert) use ($room, $district, $address, $buildingName) {
                if ($alert->room_id && (int) $alert->room_id === (int) $room->id) {
                    return true;
                }
                if (empty($alert->target_district)) {
                    return true;
                }
                $target = mb_strtolower(trim($alert->target_district));
                $haystack = mb_strtolower($district . ' ' . $address . ' ' . $buildingName);
                return str_contains($haystack, $target) || str_contains($target, 'tất cả') || str_contains($target, 'toàn');
            });

        $count = 0;
        foreach ($matchingAlerts as $alert) {
            $alert->update([
                'status' => 'notified',
                'notified_at' => now(),
            ]);

            // Bắn email thông báo nếu contact_info là email
            if (filter_var($alert->contact_info, FILTER_VALIDATE_EMAIL)) {
                $isDirect = $alert->room_id && (int) $alert->room_id === (int) $room->id;
                $subject = $isDirect
                    ? '🔔 Phòng ' . $room->room_number . ' bạn theo dõi tại Renty vừa chuyển sang TRỐNG!'
                    : '🔔 Có phòng trống mới phù hợp tại ' . ($alert->target_district ?: 'khu vực bạn tìm kiếm') . ' - Renty';

                try {
                    Mail::send('emails.room_alert_empty_notification', [
                        'alert' => $alert,
                        'room' => $room,
                        'email' => $alert->contact_info,
                        'isDirect' => $isDirect,
                    ], function ($message) use ($alert, $subject) {
                        $message->to($alert->contact_info)->subject($subject);
                    });
                    Log::info("FEAT_24_ROOM_ALERT: Đã gửi email thông báo phòng trống tới [{$alert->contact_info}] cho phòng [{$room->room_number}]");
                } catch (\Throwable $e) {
                    Log::error("FEAT_24_ROOM_ALERT: Lỗi gửi email phòng trống tới [{$alert->contact_info}]: " . $e->getMessage());
                }

                try {
                    NotificationLog::create([
                        'type' => 'room_alert_empty_notified',
                        'channel' => 'email',
                        'recipient_contact' => $alert->contact_info,
                        'recipient_name' => explode('@', $alert->contact_info)[0],
                        'subject' => $subject,
                        'message' => "Phòng {$room->room_number} tại {$room->building?->name} đã chuyển sang trạng thái Trống.",
                        'sent_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    //
                }
            }

            Log::info("FEAT_24_ROOM_ALERT: Đã kích hoạt thông báo phòng trống tới [{$alert->contact_info}] cho phòng [{$room->room_number}] - Tòa [{$room->building?->name}]");
            $count++;
        }

        return $count;
    }
}
