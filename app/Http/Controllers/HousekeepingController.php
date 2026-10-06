<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Ticket;
use App\Events\RoomStatusUpdated;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousekeepingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !in_array($user->roleSlug(), ['admin', 'landlord', 'unverified_landlord', 'manager', 'housekeeper'], true)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Không có quyền truy cập Buồng phòng.'], 403);
                }
                return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập Buồng phòng.');
            }
            return $next($request);
        });
    }

    /**
     * Màn hình danh sách phòng cần dọn dẹp tối ưu cho Mobile
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $rooms = Room::with(['building', 'residents', 'tickets' => function ($q) {
                $q->where('category', 'housekeeping')->whereIn('status', ['pending', 'processing'])->latest();
            }])
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($query) {
                $query->whereIn('status', ['cleaning'])
                      ->orWhereIn('cleaning_status', ['dirty', 'cleaning'])
                      ->orWhereHas('tickets', function ($t) {
                          $t->where('category', 'housekeeping')->whereIn('status', ['pending', 'processing']);
                      });
            })
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        $allRoomsCount = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->count();
        $cleanRoomsCount = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('cleaning_status', 'clean')
            ->where('status', '!=', 'cleaning')
            ->count();

        return view('admin.housekeeping.index', compact('rooms', 'allRoomsCount', 'cleanRoomsCount'));
    }

    /**
     * Nhân viên buồng phòng bấm cập nhật tiến độ dọn dẹp
     */
    public function updateStatus(Request $request, $roomId)
    {
        $validated = $request->validate([
            'cleaning_status' => 'required|in:cleaning,clean,inspected',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $room = Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->findOrFail($roomId);

        $newCleaningStatus = $validated['cleaning_status'];
        $newRoomStatus = $room->status;

        // Nếu đã dọn xong (clean/inspected) và phòng đang ở trạng thái 'cleaning' -> trả về 'empty' (sẵn sàng đón khách)
        if (in_array($newCleaningStatus, ['clean', 'inspected'], true) && $room->status === 'cleaning') {
            $newRoomStatus = 'empty';
        } elseif ($newCleaningStatus === 'cleaning') {
            $newRoomStatus = 'cleaning';
        }

        $room->update([
            'status' => $newRoomStatus,
            'cleaning_status' => $newCleaningStatus,
            'version' => $room->version + 1,
        ]);

        // Tự động đồng bộ các yêu cầu dọn phòng của cư dân
        if (in_array($newCleaningStatus, ['clean', 'inspected'], true)) {
            Ticket::where('room_id', $room->id)
                ->where('category', 'housekeeping')
                ->whereIn('status', ['pending', 'processing'])
                ->update(['status' => 'resolved']);
        } elseif ($newCleaningStatus === 'cleaning') {
            Ticket::where('room_id', $room->id)
                ->where('category', 'housekeeping')
                ->where('status', 'pending')
                ->update(['status' => 'processing']);
        }

        // Phát sự kiện Realtime cập nhật tức thì lên Sơ đồ ma trận phòng của Lễ tân
        try {
            event(new RoomStatusUpdated($room, $newRoomStatus, 'housekeeper_update'));
        } catch (\Throwable $e) {
            // Không chặn luồng nếu broadcast driver chưa bật
        }

        AdminActivityLogger::log(
            'housekeeping_update',
            'rooms',
            "Phòng {$room->room_number} cập nhật vệ sinh: {$newCleaningStatus}",
            $room,
            ['cleaning_status' => $newCleaningStatus]
        );

        $message = $newCleaningStatus === 'clean' || $newCleaningStatus === 'inspected'
            ? 'Phòng đã chuyển sang trạng thái Đã sạch (Clean), sẵn sàng đón khách.'
            : "Phòng {$room->room_number} đang được nhân viên buồng phòng dọn dẹp.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'code' => $newCleaningStatus === 'clean' ? 'ERR_28_04' : 'SUCCESS',
                'message' => $message,
                'room' => $room,
                'status_label' => $newCleaningStatus === 'clean' ? 'Đã sạch (Clean)' : 'Đang dọn dẹp'
            ]);
        }

        return back()->with('success', $message)->with('code', $newCleaningStatus === 'clean' ? 'ERR_28_04' : null);
    }
}
