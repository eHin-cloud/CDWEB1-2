<?php

namespace App\Http\Controllers;

use App\Models\Room;
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

        $rooms = Room::with('building')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where(function ($query) {
                $query->whereIn('status', ['cleaning'])
                      ->orWhereIn('cleaning_status', ['dirty', 'cleaning']);
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Phòng {$room->room_number} đã chuyển sang trạng thái: {$newCleaningStatus}",
                'room' => $room
            ]);
        }

        return back()->with('success', "Phòng {$room->room_number} đã cập nhật vệ sinh thành công.");
    }
}
