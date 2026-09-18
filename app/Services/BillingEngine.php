<?php

namespace App\Services;

use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\UtilityRecord;
use Carbon\Carbon;

class BillingEngine
{
    public const DEFAULT_SERVICE_FEE = 150000;

    /**
     * Tính toán hóa đơn tiền trọ / chung cư mini theo tháng
     */
    public static function calculateMonthlyRent(Room $room, UtilityRecord $utility): array
    {
        $roomPrice = (float) $room->price;
        $electricityUsed = max(0, (int) $utility->new_electricity - (int) $utility->old_electricity);
        $waterUsed = max(0, (int) $utility->new_water - (int) $utility->old_water);

        $electricityAmount = $electricityUsed * (float) $utility->electricity_price;
        $waterAmount = $waterUsed * (float) $utility->water_price;
        $serviceAmount = self::DEFAULT_SERVICE_FEE;

        $totalAmount = $roomPrice + $electricityAmount + $waterAmount + $serviceAmount;

        return [
            'type' => 'monthly',
            'room_price' => $roomPrice,
            'electricity_used' => $electricityUsed,
            'electricity_amount' => $electricityAmount,
            'water_used' => $waterUsed,
            'water_amount' => $waterAmount,
            'service_amount' => $serviceAmount,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Tính toán tiền phòng và chi phí Check-out khách sạn (theo Ngày hoặc Giờ)
     */
    public static function calculateHotelCheckout(HotelBooking $booking, ?Carbon $checkoutTime = null): array
    {
        $checkout = $checkoutTime ?: ($booking->actual_check_out_at ?: now());
        $checkin = Carbon::parse($booking->check_in_at);
        $room = $booking->room;

        $rentalType = $booking->rental_type ?: ($room?->rental_type ?: 'day');
        $roomAmount = 0;
        $surchargeAmount = 0;
        $breakdown = [];

        if ($rentalType === 'hour') {
            // Tính theo giờ: Block đầu + giờ phụ trội
            $totalMinutes = max(15, $checkin->diffInMinutes($checkout));
            $totalHours = ceil($totalMinutes / 60);

            $baseRate = (float) ($booking->unit_rate ?: ($room?->price_per_hour ?: 120000));
            $extraHourRate = (float) ($room?->price_extra_hour ?: ($baseRate * 0.35));

            if ($totalHours <= 2) {
                $roomAmount = $baseRate;
                $breakdown[] = [
                    'label' => "Giá phòng theo giờ (Block 2h đầu)",
                    'quantity' => 1,
                    'unit_price' => $baseRate,
                    'amount' => $baseRate
                ];
            } else {
                $roomAmount = $baseRate;
                $breakdown[] = [
                    'label' => "Giá phòng theo giờ (Block 2h đầu)",
                    'quantity' => 1,
                    'unit_price' => $baseRate,
                    'amount' => $baseRate
                ];

                $extraHours = (int) ($totalHours - 2);
                $extraAmount = $extraHours * $extraHourRate;
                $roomAmount += $extraAmount;
                $breakdown[] = [
                    'label' => "Phụ thu thêm giờ ({$extraHours} giờ)",
                    'quantity' => $extraHours,
                    'unit_price' => $extraHourRate,
                    'amount' => $extraAmount
                ];
            }
        } else {
            // Tính theo ngày / đêm (Mặc định 1 ngày = qua đêm, Check-out chuẩn 12:00)
            $dayRate = (float) ($booking->unit_rate ?: ($room?->price_per_day ?: ($room?->price ? round($room->price / 30) : 350000)));
            $diffHours = max(1, $checkin->diffInHours($checkout));
            $days = max(1, (int) ceil($diffHours / 24));

            $roomAmount = $days * $dayRate;
            $breakdown[] = [
                'label' => "Tiền phòng theo ngày ({$days} ngày)",
                'quantity' => $days,
                'unit_price' => $dayRate,
                'amount' => $roomAmount
            ];

            // Kiểm tra phụ thu check-out trễ nếu quá 14:00 (+30% ngày), quá 18:00 (+100% ngày)
            $checkoutHour = (int) $checkout->format('H');
            if ($checkoutHour >= 18) {
                $lateFee = $dayRate;
                $surchargeAmount += $lateFee;
                $breakdown[] = [
                    'label' => "Phụ thu trả phòng sau 18h (Tính 100% ngày)",
                    'quantity' => 1,
                    'unit_price' => $lateFee,
                    'amount' => $lateFee
                ];
            } elseif ($checkoutHour >= 14) {
                $lateFee = round($dayRate * 0.3);
                $surchargeAmount += $lateFee;
                $breakdown[] = [
                    'label' => "Phụ thu trả phòng muộn (14h - 18h)",
                    'quantity' => 1,
                    'unit_price' => $lateFee,
                    'amount' => $lateFee
                ];
            }
        }

        // Tính tiền Minibar / Dịch vụ đã dùng
        $serviceAmount = (float) $booking->folioItems()->sum('subtotal');
        $depositAmount = (float) ($booking->deposit_amount ?: 0);
        $totalAmount = max(0, $roomAmount + $surchargeAmount + $serviceAmount - $depositAmount);

        return [
            'type' => 'hotel',
            'rental_type' => $rentalType,
            'check_in_at' => $checkin->toIso8601String(),
            'check_out_at' => $checkout->toIso8601String(),
            'room_amount' => $roomAmount,
            'surcharge_amount' => $surchargeAmount,
            'service_amount' => $serviceAmount,
            'deposit_amount' => $depositAmount,
            'total_amount' => $totalAmount,
            'breakdown' => $breakdown,
        ];
    }
}
