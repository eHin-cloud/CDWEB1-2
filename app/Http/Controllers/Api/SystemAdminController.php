<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandlordProfile;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemAdminController extends Controller
{
    /**
     * GET /api/admin/landlords
     * Danh sách chủ trọ, hỗ trợ filter theo verification_status, phân trang (page, limit).
     */
    public function getLandlords(Request $request)
    {
        $status = $request->query('verification_status');
        $search = $request->query('search');
        $limit = max(1, min(100, (int) $request->query('limit', 10)));

        // Lấy danh sách các tài khoản là chủ trọ hoặc có hồ sơ landlord_profile
        $query = User::with(['landlordProfile', 'properties'])
            ->where(function ($q) {
                $q->whereIn('role', ['landlord', 'unverified_landlord'])
                  ->orWhereHas('landlordProfile');
            });

        // Filter theo verification_status của landlord_profile
        if (!empty($status) && $status !== 'all') {
            $query->whereHas('landlordProfile', function ($q) use ($status) {
                $q->where('verification_status', $status);
            });
        }

        // Tìm kiếm theo tên, email, phone hoặc username
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $landlords = $query->latest('id')->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách chủ trọ thành công',
            'data' => $landlords->items(),
            'pagination' => [
                'current_page' => $landlords->currentPage(),
                'total_pages' => $landlords->lastPage(),
                'total_items' => $landlords->total(),
                'per_page' => $landlords->perPage(),
            ]
        ]);
    }

    /**
     * GET /api/admin/landlords/:id
     * Chi tiết 1 chủ trọ, gồm landlord_profiles đầy đủ.
     */
    public function getLandlordDetail($id)
    {
        $landlord = User::with(['landlordProfile', 'properties', 'tenant'])
            ->where(function ($q) {
                $q->whereIn('role', ['landlord', 'unverified_landlord'])
                  ->orWhereHas('landlordProfile');
            })
            ->find($id);

        if (!$landlord) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin chủ trọ này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết chủ trọ thành công',
            'data' => $landlord
        ]);
    }

    /**
     * PATCH /api/admin/landlords/:id/verify
     * Body: { action: 'approve' | 'reject', reason?: string }
     * Duyệt hoặc từ chối hồ sơ pháp lý (MST, CCCD) của chủ trọ, cập nhật verification_status.
     */
    public function verifyLandlord(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:500'
        ], [
            'action.required' => 'Vui lòng chọn hành động (approve hoặc reject)',
            'action.in' => 'Hành động không hợp lệ',
        ]);

        $landlord = User::with('landlordProfile')->find($id);

        if (!$landlord) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ trọ'
            ], 404);
        }

        $action = $request->input('action');
        $reason = $request->input('reason');

        DB::transaction(function () use ($landlord, $action, $reason) {
            $newStatus = ($action === 'approve') ? 'verified' : 'rejected';

            // Tạo profile nếu chưa có sẵn
            $profile = $landlord->landlordProfile ?: new LandlordProfile(['user_id' => $landlord->id]);
            $profile->verification_status = $newStatus;
            $profile->reject_reason = ($action === 'reject') ? $reason : null;
            $profile->save();

            // Nếu duyệt thành công, thăng hạng role từ unverified_landlord sang landlord
            if ($action === 'approve') {
                $landlordRole = Role::where('slug', 'landlord')->first();
                $landlord->role = 'landlord';
                if ($landlordRole) {
                    $landlord->role_id = $landlordRole->id;
                }
                $landlord->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => ($action === 'approve') 
                ? 'Đã duyệt hồ sơ pháp lý của chủ trọ thành công' 
                : 'Đã từ chối hồ sơ pháp lý của chủ trọ',
            'data' => [
                'user_id' => $landlord->id,
                'verification_status' => ($action === 'approve') ? 'verified' : 'rejected',
                'reason' => $reason
            ]
        ]);
    }

    /**
     * PATCH /api/admin/landlords/:id/lock
     * Khóa/mở khóa tài khoản chủ trọ (cập nhật status trong bảng users thành 'locked' hoặc 'active').
     */
    public function toggleLockLandlord(Request $request, $id)
    {
        $landlord = User::find($id);

        if (!$landlord) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản người dùng'
            ], 404);
        }

        // Không cho phép tự khóa tài khoản của chính mình
        if ($landlord->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự khóa tài khoản của chính mình'
            ], 400);
        }

        $currentStatus = $landlord->status ?? 'active';
        $newStatus = ($currentStatus === 'locked') ? 'active' : 'locked';

        $landlord->status = $newStatus;
        $landlord->save();

        return response()->json([
            'success' => true,
            'message' => ($newStatus === 'locked') 
                ? "Đã khóa tài khoản chủ trọ #{$landlord->id} thành công" 
                : "Đã mở khóa tài khoản chủ trọ #{$landlord->id} thành công",
            'data' => [
                'user_id' => $landlord->id,
                'name' => $landlord->name,
                'status' => $newStatus
            ]
        ]);
    }

    /**
     * GET /api/admin/properties
     * Danh sách nhà trọ/tin đăng, filter theo status.
     */
    public function getProperties(Request $request)
    {
        $status = $request->query('status');
        $limit = max(1, min(100, (int) $request->query('limit', 10)));

        $query = Property::with('landlord');

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $properties = $query->latest('id')->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tin đăng nhà trọ thành công',
            'data' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'total_pages' => $properties->lastPage(),
                'total_items' => $properties->total(),
                'per_page' => $properties->perPage(),
            ]
        ]);
    }

    /**
     * PATCH /api/admin/properties/:id/moderate
     * Body: { action: 'approve' | 'reject', reason?: string }
     * Duyệt/từ chối tin đăng công khai.
     */
    public function moderateProperty(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:500'
        ], [
            'action.required' => 'Vui lòng chọn hành động (approve hoặc reject)',
            'action.in' => 'Hành động không hợp lệ',
        ]);

        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin đăng nhà trọ'
            ], 404);
        }

        $action = $request->input('action');
        $reason = $request->input('reason');

        $property->status = ($action === 'approve') ? 'approved' : 'rejected';
        $property->reject_reason = ($action === 'reject') ? $reason : null;
        $property->save();

        return response()->json([
            'success' => true,
            'message' => ($action === 'approve') 
                ? 'Đã duyệt tin đăng nhà trọ thành công' 
                : 'Đã từ chối tin đăng nhà trọ',
            'data' => [
                'property_id' => $property->id,
                'status' => $property->status,
                'reject_reason' => $property->reject_reason
            ]
        ]);
    }

    /**
     * GET /api/admin/stats
     * Trả về: tổng số chủ trọ, số chủ trọ đã verify, tổng số nhà trọ, 
     * số tin đang chờ duyệt, số đăng ký mới trong 7 ngày gần nhất.
     */
    public function getStats()
    {
        // 1. Tổng số chủ trọ
        $totalLandlords = User::where(function ($q) {
            $q->whereIn('role', ['landlord', 'unverified_landlord'])
              ->orWhereHas('landlordProfile');
        })->count();

        // 2. Số chủ trọ đã verify
        $verifiedLandlords = LandlordProfile::where('verification_status', 'verified')->count();

        // 3. Tổng số nhà trọ
        $totalProperties = Property::count();

        // 4. Số tin đang chờ duyệt
        $pendingProperties = Property::where('status', 'pending')->count();

        // 5. Số đăng ký mới trong 7 ngày gần nhất
        $recentRegistrations7Days = User::where('created_at', '>=', now()->subDays(7))->count();

        return response()->json([
            'success' => true,
            'message' => 'Lấy số liệu thống kê tổng quan thành công',
            'data' => [
                'total_landlords' => $totalLandlords,
                'verified_landlords' => $verifiedLandlords,
                'total_properties' => $totalProperties,
                'pending_properties' => $pendingProperties,
                'recent_registrations_7days' => $recentRegistrations7Days,
            ]
        ]);
    }
}
