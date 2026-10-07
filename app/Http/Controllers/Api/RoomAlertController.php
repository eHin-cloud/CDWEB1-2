<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomAlertController extends Controller
{
    /**
     * FEAT_24_ROOM_ALERT: Đăng ký nhận chuông báo phòng trống thông minh
     * POST /api/renty/room-alerts
     */
    public function store(Request $request): JsonResponse
    {
        $contactInfo = trim((string) $request->input('contact_info', ''));
        $targetDistrict = trim((string) $request->input('target_district', ''));
        $maxBudgetRaw = $request->input('max_budget');
        $roomId = $request->input('room_id');

        // Bảng 4: Kiểm tra Để trống số điện thoại hoặc email -> ERR_24_01
        if ($contactInfo === '') {
            return response()->json([
                'success' => false,
                'code' => 'ERR_24_01',
                'field' => 'contact_info',
                'message' => 'Vui lòng nhập số điện thoại hoặc email nhận thông báo.',
            ], 422);
        }

        // Bảng 4: Kiểm tra Email / Số điện thoại sai định dạng -> ERR_24_02
        $isEmail = filter_var($contactInfo, FILTER_VALIDATE_EMAIL) !== false;
        $isPhone = preg_match('/^(0|\+84)[3|5|7|8|9][0-9]{8}$/', $contactInfo) || preg_match('/^[0-9]{10}$/', $contactInfo);

        if (!$isEmail && !$isPhone) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_24_02',
                'field' => 'contact_info',
                'message' => 'Địa chỉ email không đúng định dạng hợp lệ.',
            ], 422);
        }

        // Bảng 4: Kiểm tra Để trống khu vực mong muốn -> ERR_24_03
        if ($targetDistrict === '') {
            return response()->json([
                'success' => false,
                'code' => 'ERR_24_03',
                'field' => 'target_district',
                'message' => 'Vui lòng chọn quận/huyện bạn đang muốn tìm phòng.',
            ], 422);
        }

        // Kiểm tra ngân sách tối đa > 0
        if ($maxBudgetRaw === null || $maxBudgetRaw === '' || !is_numeric($maxBudgetRaw) || (float) $maxBudgetRaw <= 0) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_24_BUDGET',
                'field' => 'max_budget',
                'message' => 'Ngân sách tối đa phải lớn hơn 0 VNĐ',
            ], 422);
        }

        $maxBudget = (int) $maxBudgetRaw;

        // Xử lý Multi-tenancy và liên kết phòng / người dùng
        $tenantId = null;
        $room = null;

        if (!empty($roomId)) {
            $room = Room::with('building')->find($roomId);
            if ($room) {
                $tenantId = $room->tenant_id;
            }
        }

        $userId = auth('sanctum')->id() ?? auth()->id();

        // Lưu thông tin vào bảng room_alerts
        $alert = RoomAlert::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'room_id' => $room?->id,
            'contact_info' => $contactInfo,
            'target_district' => $targetDistrict,
            'max_budget' => $maxBudget,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'code' => 'ERR_24_04',
            'message' => 'Đăng ký nhận thông báo phòng trống thành công! Bạn sẽ nhận tin ngay khi có phòng mới.',
            'data' => [
                'id' => $alert->id,
                'contact_info' => $alert->contact_info,
                'target_district' => $alert->target_district,
                'max_budget' => $alert->max_budget,
                'room_id' => $alert->room_id,
                'status' => $alert->status,
                'created_at' => $alert->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * FEAT_24_ROOM_ALERT: Quản lý danh sách chuông báo phòng đã thiết lập của người dùng
     * GET /api/renty/room-alerts/my
     */
    public function myAlerts(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $alerts = RoomAlert::with(['room.building'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('contact_info', $user->email)
                      ->orWhere('contact_info', $user->phone);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'count' => $alerts->count(),
            'data' => $alerts,
        ]);
    }

    /**
     * Hủy chuông báo
     * DELETE /api/renty/room-alerts/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $alert = RoomAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chuông báo phòng.',
            ], 404);
        }

        if ($user && $alert->user_id && $alert->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền hủy chuông báo này.',
            ], 403);
        }

        $alert->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy chuông báo phòng thành công.',
        ]);
    }
}
