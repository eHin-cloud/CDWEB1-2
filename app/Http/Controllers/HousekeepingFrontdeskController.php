<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use App\Models\User;
use App\Models\HousekeepingLog;
use App\Models\HotelBooking;
use App\Models\HotelFolioItem;
use App\Services\BillingEngine;
use App\Services\AdminActivityLogger;
use App\Events\RoomStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HousekeepingFrontdeskController extends Controller
{
    /**
     * Danh mục vật tư tiêu hao Minibar mẫu để đối soát
     */
    public const DEFAULT_MINIBAR_ITEMS = [
        ['item_name' => 'Nước khoáng Lavie 500ml', 'unit_price' => 10000, 'unit' => 'Chai'],
        ['item_name' => 'Nước ngọt Coca-Cola lon', 'unit_price' => 15000, 'unit' => 'Lon'],
        ['item_name' => 'Bia Tiger lon 330ml', 'unit_price' => 25000, 'unit' => 'Lon'],
        ['item_name' => 'Mì ly Handy Hảo Hảo', 'unit_price' => 15000, 'unit' => 'Ly'],
        ['item_name' => 'Bim bim Khoai tây Oishi', 'unit_price' => 12000, 'unit' => 'Gói'],
        ['item_name' => 'Khăn lạnh cao cấp', 'unit_price' => 5000, 'unit' => 'Cái'],
    ];

    /**
     * Sơ đồ buồng phòng thời gian thực, hiển thị trực quan mã màu FSM dọn phòng
     * GET /smartroom/admin/housekeeping/matrix
     */
    public function matrix(Request $request)
    {
        $user = Auth::user();
        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        // Danh sách tòa nhà thuộc tenant
        $buildings = Building::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->get();

        // Query danh sách phòng
        $query = Room::with([
                'building',
                'assignedStaff',
                'inspector',
                'activeHotelBooking',
                'residents'
            ])
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));

        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        // Lọc trạng thái dọn dẹp FSM
        $statusFilter = $request->input('status_filter', 'all');
        if ($statusFilter !== 'all' && in_array($statusFilter, Room::HOUSEKEEPING_STATUSES, true)) {
            $query->where('housekeeping_status', $statusFilter);
        }

        // Tìm kiếm số phòng (Regex: /^[A-Z0-9\.\-]+$/i)
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (preg_match('/^[A-Z0-9\.\-]+$/i', $search)) {
                $query->where('room_number', 'LIKE', "%{$search}%");
            }
        }

        $isHousekeeper = $user->isHousekeeper();

        $rooms = $query->orderBy('floor')->orderBy('room_number')->get();

        // Danh sách nhân viên buồng phòng và nhân sự thuộc cơ sở để phân công
        $housekeepers = User::where(function ($q) {
                $q->whereHas('roleRecord', fn ($r) => $r->whereIn('slug', ['housekeeper', 'manager', 'receptionist']))
                  ->orWhereIn('role', ['housekeeper', 'manager', 'receptionist', 'staff']);
            })
            ->where('status', '!=', 'locked')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        // Thống kê số lượng theo mã màu FSM
        $allRoomsBase = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $stats = [
            'total' => (clone $allRoomsBase)->count(),
            'dirty' => (clone $allRoomsBase)->where('housekeeping_status', 'dirty')->count(),
            'cleaning' => (clone $allRoomsBase)->where('housekeeping_status', 'cleaning')->count(),
            'clean' => (clone $allRoomsBase)->where('housekeeping_status', 'clean')->count(),
            'inspected' => (clone $allRoomsBase)->where('housekeeping_status', 'inspected')->count(),
            'out_of_service' => (clone $allRoomsBase)->where('housekeeping_status', 'out_of_service')->count(),
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'rooms' => $rooms,
                'housekeepers' => $housekeepers,
                'minibar_catalogs' => self::DEFAULT_MINIBAR_ITEMS,
            ]);
        }

        return view('admin.housekeeping.matrix', [
            'rooms' => $rooms,
            'buildings' => $buildings,
            'housekeepers' => $housekeepers,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'currentBuildingId' => $request->building_id,
            'minibarCatalogs' => self::DEFAULT_MINIBAR_ITEMS,
            'isHousekeeper' => $isHousekeeper,
        ]);
    }

    /**
     * Phân công nhân viên buồng phòng dọn dẹp theo ca
     * POST /smartroom/admin/housekeeping/assign
     */
    public function assign(Request $request)
    {
        $roomId = $request->input('room_id');
        $staffId = $request->input('assigned_staff_id');
        $priority = $request->input('priority', 'normal');
        $notes = $request->input('inspection_notes', $request->input('notes'));

        // Kiểm tra chọn phòng (ERR_18_01)
        if (empty($roomId)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_01',
                'message' => 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.',
                'field' => 'room_id'
            ], 422);
        }

        $user = Auth::user();
        if ($user->isHousekeeper()) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_07',
                'message' => 'Nhân viên buồng phòng chỉ tiếp nhận phân công, không có quyền phân công cho người khác.',
            ], 403);
        }

        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->find($roomId);
        if (!$room) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_01',
                'message' => 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.',
                'field' => 'room_id'
            ], 422);
        }

        // Kiểm tra chọn nhân viên buồng phòng phụ trách (ERR_18_03)
        if (empty($staffId)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_03',
                'message' => 'Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.',
                'field' => 'assigned_staff_id'
            ], 422);
        }

        $staff = User::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('status', '!=', 'locked')
            ->find($staffId);

        if (!$staff) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_03',
                'message' => 'Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.',
                'field' => 'assigned_staff_id'
            ], 422);
        }

        $validPriorities = Room::HOUSEKEEPING_PRIORITIES;
        if (!in_array($priority, $validPriorities, true)) {
            $priority = 'normal';
        }

        // Nếu phòng chưa ở trạng thái cần dọn, khi phân công dọn ca định kỳ chuyển sang 'dirty'
        $fromStatus = $room->housekeeping_status;
        $toStatus = in_array($room->housekeeping_status, ['clean', 'inspected'], true) ? 'dirty' : $room->housekeeping_status;

        $room->update([
            'housekeeping_status' => $toStatus,
            'cleaning_status' => $toStatus,
            'assigned_staff_id' => $staff->id,
            'priority' => $priority,
            'inspection_notes' => $notes ? Str::limit($notes, 255) : $room->inspection_notes,
            'version' => $room->version + 1,
        ]);

        HousekeepingLog::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'assigned_staff_id' => $staff->id,
            'action' => 'assign',
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'priority' => $priority,
            'notes' => "Phân công dọn phòng cho {$staff->name}. Mức độ: {$priority}",
        ]);

        AdminActivityLogger::log(
            'housekeeping_assign',
            'rooms',
            "Phân công dọn dẹp phòng {$room->room_number} cho {$staff->name} (Ưu tiên: {$priority})",
            $room,
            ['assigned_staff_id' => $staff->id, 'priority' => $priority]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã phân công nhân viên {$staff->name} phụ trách phòng {$room->room_number} thành công!",
            'room' => $room->fresh(['building', 'assignedStaff']),
        ]);
    }

    /**
     * Cập nhật tiến độ dọn phòng (Bắt đầu dọn -> Dọn sạch sẽ)
     * POST /smartroom/admin/housekeeping/status
     */
    public function updateStatus(Request $request)
    {
        $roomId = $request->input('room_id');
        $newStatus = $request->input('housekeeping_status');

        if (empty($roomId)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_01',
                'message' => 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.',
            ], 422);
        }

        $user = Auth::user();
        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->findOrFail($roomId);

        // Kiểm tra trạng thái thuộc enum [dirty, cleaning, clean, inspected, out_of_service]
        if (!in_array($newStatus, Room::HOUSEKEEPING_STATUSES, true)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_04',
                'message' => 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).',
            ], 422);
        }

        $currentStatus = $room->housekeeping_status ?: 'dirty';

        // Quy tắc máy trạng thái FSM:
        // 1. Nếu cố tình chuyển sang 'inspected' khi phòng chưa 'clean'
        if ($newStatus === 'inspected' && $currentStatus !== 'clean') {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_04',
                'message' => 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).',
            ], 422);
        }

        // 2. Không cho phép nhảy cóc từ 'dirty' thẳng sang 'inspected'
        if ($currentStatus === 'dirty' && in_array($newStatus, ['inspected'], true)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_04',
                'message' => 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).',
            ], 422);
        }

        // Cập nhật trạng thái
        $roomStatus = $room->status;
        if (in_array($newStatus, ['clean', 'inspected'], true) && $room->status === 'cleaning') {
            $roomStatus = 'empty';
        } elseif ($newStatus === 'cleaning' && $room->status !== 'occupied') {
            $roomStatus = 'cleaning';
        }

        $updateData = [
            'status' => $roomStatus,
            'housekeeping_status' => $newStatus,
            'cleaning_status' => $newStatus,
            'version' => $room->version + 1,
        ];

        // Tự động gán cho nhân viên buồng phòng khi họ chủ động bấm chọn phòng cần dọn
        if ($newStatus === 'cleaning' && empty($room->assigned_staff_id) && $user->isHousekeeper()) {
            $updateData['assigned_staff_id'] = $user->id;
            $room->assigned_staff_id = $user->id;
        }

        $room->update($updateData);

        HousekeepingLog::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'assigned_staff_id' => $room->assigned_staff_id,
            'action' => $newStatus === 'cleaning' ? 'start_cleaning' : 'clean_done',
            'from_status' => $currentStatus,
            'to_status' => $newStatus,
            'priority' => $room->priority ?: 'normal',
            'notes' => "Cập nhật tiến độ: {$currentStatus} -> {$newStatus}",
        ]);

        try {
            event(new RoomStatusUpdated($room, $roomStatus, 'housekeeping_status_change'));
        } catch (\Throwable $e) {}

        $statusLabels = [
            'cleaning' => 'đang được dọn dẹp vệ sinh',
            'clean' => 'đã hoàn tất vệ sinh (Đã sạch)',
            'inspected' => 'đã được nghiệm thu đạt chuẩn',
            'dirty' => 'cần được dọn dẹp',
            'out_of_service' => 'tạm dừng phục vụ',
        ];

        return response()->json([
            'success' => true,
            'message' => "Phòng {$room->room_number} " . ($statusLabels[$newStatus] ?? 'đã cập nhật trạng thái') . ".",
            'room' => $room->fresh(['building', 'assignedStaff', 'inspector']),
        ]);
    }

    /**
     * Lễ tân nghiệm thu buồng phòng đạt chuẩn sẵn sàng đón khách
     * POST /smartroom/admin/housekeeping/inspect
     */
    public function inspect(Request $request)
    {
        $roomId = $request->input('room_id');
        $notes = $request->input('inspection_notes');

        if (empty($roomId)) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_01',
                'message' => 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.',
            ], 422);
        }

        $user = Auth::user();
        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->findOrFail($roomId);

        // Guard Check FSM: Phòng bắt buộc phải qua bước Sạch (Clean) trước khi Nghiệm thu (Inspected)
        if ($room->housekeeping_status !== 'clean') {
            return response()->json([
                'success' => false,
                'code' => 'ERR_18_04',
                'message' => 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).',
            ], 422);
        }

        $fromStatus = $room->housekeeping_status;

        $updateData = [
            'status' => $room->status === 'cleaning' ? 'empty' : $room->status,
            'housekeeping_status' => 'inspected',
            'cleaning_status' => 'inspected',
            'inspected_by' => $user->id,
            'inspected_at' => now(),
            'inspection_notes' => $notes ? Str::limit($notes, 255) : 'Đạt tiêu chuẩn bàn giao đón khách',
            'version' => $room->version + 1,
        ];

        // Nếu phòng seeder cũ chưa có nhân viên phụ trách mà buồng phòng nghiệm thu, tự động ghi nhận nhân viên đó
        if (empty($room->assigned_staff_id) && $user->isHousekeeper()) {
            $updateData['assigned_staff_id'] = $user->id;
            $room->assigned_staff_id = $user->id;
        }

        $room->update($updateData);

        HousekeepingLog::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'assigned_staff_id' => $room->assigned_staff_id,
            'action' => 'inspect_passed',
            'from_status' => $fromStatus,
            'to_status' => 'inspected',
            'priority' => $room->priority ?: 'normal',
            'notes' => $room->inspection_notes,
        ]);

        AdminActivityLogger::log(
            'housekeeping_inspect',
            'rooms',
            "Nghiệm thu buồng phòng {$room->room_number} đạt chuẩn sẵn sàng đón khách",
            $room,
            ['inspection_notes' => $room->inspection_notes]
        );

        try {
            event(new RoomStatusUpdated($room, $room->status, 'inspect_passed'));
        } catch (\Throwable $e) {}

        // ERR_18_06: Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.
        return response()->json([
            'success' => true,
            'code' => 'ERR_18_06',
            'message' => 'Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.',
            'room' => $room->fresh(['building', 'assignedStaff', 'inspector']),
        ]);
    }

    /**
     * Check-in khách lưu trú (Guard Check chặn tuyệt đối phòng Dirty)
     * POST /smartroom/admin/frontdesk/checkin
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer',
            'guest_name' => 'required|string|max:150',
            'guest_phone' => 'nullable|string|max:20',
            'guest_cccd' => 'nullable|string|max:20',
            'rental_type' => 'required|in:day,hour,month',
            'deposit_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        if ($user->isHousekeeper()) {
            $msg = 'Chỉ Lễ tân hoặc Quản lý cơ sở mới có quyền thực hiện Check-in đón khách.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($validated['room_id']);

        // Guard Check 1: Chặn tuyệt đối phòng Dirty (ERR_18_02)
        if ($room->housekeeping_status === 'dirty' || $room->cleaning_status === 'dirty') {
            $errorMessage = "Phòng {$room->room_number} đang ở trạng thái Cần dọn (Dirty). Không thể thực hiện Check-in đón khách!";
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ERR_18_02',
                    'message' => $errorMessage,
                    'room_number' => $room->room_number,
                ], 422);
            }
            return back()->with('error', $errorMessage)->with('code', 'ERR_18_02');
        }

        // Guard Check 2: Chặn phòng chưa hoàn tất vệ sinh nghiệm thu (cleaning hoặc out_of_service)
        if (!in_array($room->housekeeping_status, ['clean', 'inspected'], true)) {
            $errorMessage = "Phòng {$room->room_number} chưa hoàn tất nghiệm thu vệ sinh. Không thể thực hiện Check-in đón khách!";
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ERR_18_02',
                    'message' => $errorMessage,
                    'room_number' => $room->room_number,
                ], 422);
            }
            return back()->with('error', $errorMessage);
        }

        // Guard Check 3: Chặn phòng đã có người ở
        if ($room->status === 'occupied') {
            $errorMessage = "Phòng {$room->room_number} hiện đang có khách lưu trú.";
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }
            return back()->with('error', $errorMessage);
        }

        $unitRate = match ($validated['rental_type']) {
            'hour' => ($room->price_per_hour ?: 120000),
            'day' => ($room->price_per_day ?: ($room->price ? round($room->price / 30) : 350000)),
            default => ($room->price ?: 3500000),
        };

        $booking = HotelBooking::create([
            'tenant_id' => $room->tenant_id,
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

        $room->update([
            'status' => 'occupied',
            'version' => $room->version + 1,
        ]);

        AdminActivityLogger::log(
            'check_in',
            'hotel_bookings',
            "Khách {$booking->guest_name} Check-in phòng {$room->room_number} ({$validated['rental_type']})",
            $booking,
            ['booking_code' => $booking->booking_code]
        );

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Check-in thành công cho khách {$booking->guest_name} vào phòng {$room->room_number}!",
                'booking' => $booking,
                'room' => $room->fresh(),
            ]);
        }

        return back()->with('success', "Check-in thành công cho khách {$booking->guest_name} vào phòng {$room->room_number}!");
    }

    /**
     * Check-out trả phòng, tự động chuyển phòng sang Dirty và đối soát minibar
     * POST /smartroom/admin/frontdesk/checkout
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        if ($user->isHousekeeper()) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ Lễ tân hoặc Quản lý cơ sở mới có quyền thực hiện Check-out trả phòng.',
            ], 403);
        }

        $tenantId = $request->attributes->get('scoped_tenant_id') ?? $user->tenant_id;

        $roomId = $request->input('room_id');
        $bookingId = $request->input('booking_id');

        if (!$bookingId && $roomId) {
            $booking = HotelBooking::where('room_id', $roomId)
                ->where('status', 'checked_in')
                ->latest('id')
                ->first();
        } else {
            $booking = HotelBooking::find($bookingId);
        }

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lượt lưu trú cần trả phòng.',
            ], 404);
        }

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($booking->room_id);

        // Đối soát vật tư tiêu hao Minibar
        $minibarItems = $request->input('minibar_items', []);
        $totalMinibarAmount = 0;

        if (is_array($minibarItems) && !empty($minibarItems)) {
            foreach ($minibarItems as $item) {
                $qty = $item['quantity'] ?? 0;

                // Validate ERR_18_05: Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0
                if (!is_numeric($qty) || intval($qty) != $qty || intval($qty) < 0) {
                    return response()->json([
                        'success' => false,
                        'code' => 'ERR_18_05',
                        'message' => 'Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0.',
                        'field' => 'quantity',
                    ], 422);
                }

                $qty = intval($qty);
                if ($qty > 0) {
                    $unitPrice = floatval($item['unit_price'] ?? 0);
                    $subtotal = $qty * $unitPrice;
                    $totalMinibarAmount += $subtotal;

                    HotelFolioItem::create([
                        'booking_id' => $booking->id,
                        'item_name' => $item['item_name'] ?? 'Vật tư tiêu hao',
                        'item_type' => 'minibar',
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                }
            }
        }

        // Tính toán thanh toán
        $calc = BillingEngine::calculateHotelCheckout($booking->fresh(['folioItems']), now());

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

        // TỰ ĐỘNG CHUYỂN PHÒNG SANG DIRTY THEO ĐẶC TẢ FSM
        $fromStatus = $room->housekeeping_status;
        $room->update([
            'status' => 'cleaning',
            'housekeeping_status' => 'dirty',
            'cleaning_status' => 'dirty',
            'assigned_staff_id' => null,
            'priority' => 'normal',
            'inspection_notes' => 'Khách vừa trả phòng, cần vệ sinh dọn phòng và kiểm kê minibar.',
            'inspected_by' => null,
            'inspected_at' => null,
            'version' => $room->version + 1,
        ]);

        HousekeepingLog::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'action' => 'checkout_dirty',
            'from_status' => $fromStatus,
            'to_status' => 'dirty',
            'priority' => 'normal',
            'notes' => "Khách trả phòng (Booking #{$booking->booking_code}). Tự động chuyển phòng sang Dirty.",
        ]);

        AdminActivityLogger::log(
            'check_out',
            'hotel_bookings',
            "Phòng {$room->room_number} Check-out, tự động chuyển sang Cần dọn (Dirty). Tổng tiền: " . number_format($calc['total_amount']) . "đ",
            $booking,
            ['total_amount' => $calc['total_amount']]
        );

        try {
            event(new RoomStatusUpdated($room, 'cleaning', 'checkout_dirty'));
        } catch (\Throwable $e) {}

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Trả phòng thành công! Phòng {$room->room_number} đã tự động chuyển sang trạng thái Cần dọn (Dirty).",
                'calculation' => $calc,
                'room' => $room->fresh(),
            ]);
        }

        return redirect()->route('admin.hotel.folio', $booking->id)
            ->with('success', "Trả phòng thành công! Phòng {$room->room_number} đã chuyển sang trạng thái Cần dọn (Dirty).");
    }
}
