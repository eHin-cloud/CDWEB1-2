<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\HotelFolioItem;
use App\Services\BillingEngine;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HotelReceptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !in_array($user->roleSlug(), ['admin', 'landlord', 'unverified_landlord', 'manager', 'receptionist'], true)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Không có quyền truy cập lễ tân.'], 403);
                }
                return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập khu vực Lễ tân.');
            }
            return $next($request);
        });
    }

    /**
     * Tiếp nhận Check-in tức thì
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'guest_name' => 'required|string|max:150',
            'guest_phone' => 'nullable|string|max:20',
            'guest_cccd' => 'nullable|string|max:20',
            'rental_type' => 'required|in:day,hour',
            'deposit_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id ?: Room::where('id', $validated['room_id'])->value('tenant_id');

        $room = Room::where('id', $validated['room_id'])
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        if ($room->status === 'occupied') {
            return back()->with('error', 'Phòng này hiện đang có khách lưu trú.');
        }

        $unitRate = $validated['rental_type'] === 'hour'
            ? ($room->price_per_hour ?: 120000)
            : ($room->price_per_day ?: ($room->price ? round($room->price / 30) : 350000));

        $booking = HotelBooking::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'booking_code' => 'HB-' . strtoupper(Str::random(6)),
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'] ?? null,
            'guest_cccd' => $validated['guest_cccd'] ?? null,
            'rental_type' => $validated['rental_type'],
            'check_in_at' => now(),
            'unit_rate' => $unitRate,
            'deposit_amount' => $validated['deposit_amount'] ?? 0,
            'status' => 'checked_in',
            'payment_status' => 'unpaid',
            'note' => $validated['note'] ?? null,
        ]);

        // Cập nhật trạng thái phòng sang Đang ở (occupied)
        $room->update([
            'status' => 'occupied',
            'cleaning_status' => 'clean',
        ]);

        AdminActivityLogger::log(
            'check_in',
            'hotel_bookings',
            "Khách {$booking->guest_name} Check-in phòng {$room->room_number} ({$validated['rental_type']})",
            $booking,
            ['booking_code' => $booking->booking_code]
        );

        return back()->with('success', "Đã Check-in thành công cho khách {$booking->guest_name} vào phòng {$room->room_number}!");
    }

    /**
     * Ghi nhận sử dụng Minibar / Dịch vụ phòng
     */
    public function addFolioItem(Request $request, $bookingId)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100',
            'item_type' => 'nullable|in:minibar,service,surcharge',
            'quantity' => 'required|integer|min:1|max:100',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $booking = HotelBooking::findOrFail($bookingId);
        $subtotal = $validated['quantity'] * $validated['unit_price'];

        $item = HotelFolioItem::create([
            'booking_id' => $booking->id,
            'item_name' => $validated['item_name'],
            'item_type' => $validated['item_type'] ?? 'minibar',
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'subtotal' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã thêm {$item->item_name} vào hóa đơn phòng.",
            'item' => $item
        ]);
    }

    /**
     * Check-out trả phòng, tính tiền tức thời & chuyển trạng thái dọn dẹp
     */
    public function checkOut(Request $request, $bookingId)
    {
        $booking = HotelBooking::with(['room', 'folioItems'])->findOrFail($bookingId);
        $room = $booking->room;

        if ($booking->status === 'checked_out') {
            return back()->with('error', 'Lượt đặt phòng này đã được trả trước đó.');
        }

        $calc = BillingEngine::calculateHotelCheckout($booking, now());

        $booking->update([
            'actual_check_out_at' => now(),
            'room_amount' => $calc['room_amount'],
            'surcharge_amount' => $calc['surcharge_amount'],
            'service_amount' => $calc['service_amount'],
            'total_amount' => $calc['total_amount'],
            'status' => 'checked_out',
            'payment_status' => 'paid',
            'payment_method' => $request->input('payment_method', 'vietqr'),
        ]);

        // Phòng chuyển ngay sang trạng thái Cần dọn dẹp (cleaning) để buồng phòng vào xử lý
        $room->update([
            'status' => 'cleaning',
            'cleaning_status' => 'dirty',
        ]);

        AdminActivityLogger::log(
            'check_out',
            'hotel_bookings',
            "Phòng {$room->room_number} đã Check-out. Tổng thanh toán: " . number_format($calc['total_amount']) . "đ",
            $booking,
            ['total_amount' => $calc['total_amount']]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Trả phòng thành công. Phòng đã chuyển trạng thái Cần dọn dẹp.',
                'calculation' => $calc,
            ]);
        }

        return redirect()->route('admin.hotel.folio', $booking->id)->with('success', 'Trả phòng thành công! Đã xuất bảng kê Folio.');
    }

    /**
     * Bảng kê hóa đơn Folio chi tiết
     */
    public function printFolio($bookingId)
    {
        $booking = HotelBooking::with(['room.building', 'folioItems', 'tenant'])->findOrFail($bookingId);
        $calc = BillingEngine::calculateHotelCheckout($booking, $booking->actual_check_out_at ?: now());

        return view('admin.hotel.folio', compact('booking', 'calc'));
    }
}
