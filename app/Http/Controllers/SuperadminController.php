<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\Role;
use App\Models\Room;
use App\Models\SystemConfig;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\SensitiveData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperadminController extends Controller
{
    protected AuditLogService $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Màn hình Bảng điều khiển Quản trị nền tảng hệ thống Superadmin Console
     * GET /admin/dashboard hoặc GET /dashboard (đối với Superadmin)
     */
    public function dashboard(Request $request)
    {
        // 1. Tải toàn bộ chỉ số KPI hệ thống (kpi_cards)
        $kpi = [
            'total_users' => User::count(),
            'landlord_count' => User::where('role', 'landlord')
                ->orWhereHas('roleRecord', fn ($q) => $q->whereIn('slug', ['landlord', 'unverified_landlord']))
                ->count(),
            'resident_count' => User::where('role', 'user')
                ->orWhereHas('roleRecord', fn ($q) => $q->whereIn('slug', ['resident', 'tenant', 'guest']))
                ->count(),
            'admin_count' => User::whereIn('role', ['admin', 'superadmin'])
                ->orWhereHas('roleRecord', fn ($q) => $q->whereIn('slug', ['admin', 'superadmin']))
                ->count(),
            'total_revenue' => (float) Bill::where('status', 'paid')->sum('total_amount'),
            'total_rooms' => Room::count(),
            'active_rooms' => Room::where('status', 'occupied')->count(),
            'total_tenants' => Tenant::count(),
        ];

        // 2. Xử lý bộ lọc tìm kiếm người dùng (tblUsers)
        $query = User::with(['roleRecord', 'tenant']);

        // Input 1: search_user (Email, họ tên hoặc số điện thoại)
        if ($request->filled('search_user')) {
            $search = trim($request->search_user);
            $blindIndex = SensitiveData::blindIndex($search);

            $query->where(function ($q) use ($search, $blindIndex) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");

                if ($blindIndex) {
                    $q->orWhere('phone_blind_index', $blindIndex);
                }
            });
        }

        // Input 2: role_filter (Tất cả, Chủ trọ, Khách thuê, Quản trị viên,...)
        if ($request->filled('role_filter') && $request->role_filter !== 'all') {
            $roleFilter = $request->role_filter;
            if ($roleFilter === 'landlord') {
                $query->where(function ($q) {
                    $q->where('role', 'landlord')
                      ->orWhereHas('roleRecord', fn ($sq) => $sq->whereIn('slug', ['landlord', 'unverified_landlord']));
                });
            } elseif ($roleFilter === 'tenant' || $roleFilter === 'resident') {
                $query->where(function ($q) {
                    $q->where('role', 'user')
                      ->orWhereHas('roleRecord', fn ($sq) => $sq->whereIn('slug', ['resident', 'tenant', 'guest']));
                });
            } elseif ($roleFilter === 'admin') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin', 'superadmin'])
                      ->orWhereHas('roleRecord', fn ($sq) => $sq->whereIn('slug', ['admin', 'superadmin']));
                });
            } else {
                $query->where(function ($q) use ($roleFilter) {
                    $q->where('role', $roleFilter)
                      ->orWhereHas('roleRecord', fn ($sq) => $sq->where('slug', $roleFilter));
                });
            }
        }

        // Input 3: status_filter (Đang hoạt động, Đã khóa, Chờ duyệt)
        if ($request->filled('status_filter') && $request->status_filter !== 'all') {
            $statusFilter = $request->status_filter;
            $query->where('status', $statusFilter);
        }

        $users = $query->orderByDesc('id')->paginate(15)->withQueryString();

        // 3. Tải các danh mục phục vụ thao tác và cấu hình
        $roles = Role::orderBy('id')->get();
        $tenants = Tenant::orderBy('name')->get();
        $systemConfigs = SystemConfig::all()->keyBy('key');
        
        // 4. Lấy 10 vết thao tác kiểm toán an ninh gần nhất
        $recentAuditLogs = AuditLog::with(['actor' => fn ($q) => $q->select('id', 'name', 'username')])
            ->latest('id')
            ->take(10)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'kpi' => $kpi,
                'users' => $users,
                'configs' => $systemConfigs,
            ]);
        }

        return view('admin.superadmin.dashboard', compact(
            'kpi',
            'users',
            'roles',
            'tenants',
            'systemConfigs',
            'recentAuditLogs'
        ));
    }

    /**
     * Nhật ký kiểm toán an ninh truy vết bất biến toàn hệ thống
     * GET /admin/audit-logs
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with(['actor' => fn ($q) => $q->select('id', 'name', 'username', 'email')])
            ->latest('id');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('resource_type', 'like', "%{$search}%")
                  ->orWhere('resource_id', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'logs' => $logs,
            ]);
        }

        return view('admin.superadmin.audit_logs', compact('logs'));
    }

    /**
     * Thiết lập tham số toàn sàn, hạn ngạch tài khoản và chính sách phí
     * POST /admin/system/config
     */
    public function updateSystemConfig(Request $request)
    {
        $validated = $request->validate([
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'free_listing_quota' => 'nullable|integer|min:0',
            'platform_fee_fixed' => 'nullable|numeric|min:0',
            'auto_approve_landlord' => 'nullable|in:0,1',
            'maintenance_mode' => 'nullable|in:0,1',
            'system_hotline' => 'nullable|string|max:50',
            'system_email' => 'nullable|email|max:150',
        ]);

        $oldConfigs = SystemConfig::all()->pluck('value', 'key')->toArray();

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                SystemConfig::set($key, (string) $value);
            }
        }

        // Ghi vết kiểm toán an ninh
        $this->auditLogService->record([
            'action' => 'system_config_updated',
            'resource_type' => 'SystemConfig',
            'resource_id' => 'all',
            'sensitive_fields' => array_keys($validated),
            'reason' => 'Cập nhật cấu hình tham số toàn sàn và chính sách phí từ Superadmin Console.',
            'metadata' => [
                'old_configs' => $oldConfigs,
                'new_configs' => $validated,
                'actor_username' => Auth::user()?->username,
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu thiết lập tham số hệ thống thành công!',
            ]);
        }

        return back()->with('success', 'Đã lưu thiết lập tham số hệ thống thành công!');
    }

    /**
     * Phân quyền tài khoản (Nâng cấp / Hạ quyền / Gán cơ sở lưu trú)
     * POST /admin/users/{user}/role
     */
    public function updateUserRole(Request $request, User $user)
    {
        // 1. Kiểm tra chọn vai trò: ERR_20_01
        $roleSlug = $request->input('role_slug');
        if (empty($roleSlug)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_20_01',
                    'message' => 'Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.',
                ], 422);
            }

            return back()->withErrors([
                'role_slug' => 'Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.'
            ])->with('error_code', 'ERR_20_01')->withInput();
        }

        $validRoles = Role::pluck('slug')->toArray();
        if (!in_array($roleSlug, $validRoles, true) && !in_array($roleSlug, ['admin', 'superadmin', 'landlord', 'manager', 'receptionist', 'housekeeper', 'resident', 'guest', 'user'], true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_20_01',
                    'message' => 'Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.',
                ], 422);
            }

            return back()->withErrors([
                'role_slug' => 'Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.'
            ])->with('error_code', 'ERR_20_01')->withInput();
        }

        // 2. Kiểm tra gán cơ sở lưu trú: ERR_20_02
        // "Nhân viên hoặc Quản lý bắt buộc phải được gán vào một cơ sở lưu trú cụ thể."
        $staffRoles = ['manager', 'receptionist', 'housekeeper'];
        $tenantId = $request->input('tenant_id');
        if (in_array($roleSlug, $staffRoles, true) && empty($tenantId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_20_02',
                    'message' => 'Nhân viên hoặc Quản lý bắt buộc phải được gán vào một cơ sở lưu trú cụ thể.',
                ], 422);
            }

            return back()->withErrors([
                'tenant_id' => 'Nhân viên hoặc Quản lý bắt buộc phải được gán vào một cơ sở lưu trú cụ thể.'
            ])->with('error_code', 'ERR_20_02')->withInput();
        }

        // Lấy Role model
        $targetRole = Role::where('slug', $roleSlug)->first();
        $oldRole = $user->roleSlug();

        // Cập nhật thông tin User
        $user->role_id = $targetRole?->id;
        $user->role = match ($roleSlug) {
            'superadmin' => 'superadmin',
            'admin' => 'admin',
            'landlord' => 'landlord',
            'unverified_landlord' => 'unverified_landlord',
            'manager' => 'manager',
            'receptionist' => 'receptionist',
            'housekeeper' => 'housekeeper',
            'resident', 'user' => 'user',
            'guest' => 'guest',
            default => $roleSlug,
        };

        if (in_array($roleSlug, ['superadmin', 'admin', 'guest'], true)) {
            $user->tenant_id = null;
        } elseif (!empty($tenantId)) {
            $user->tenant_id = (int) $tenantId;
        }

        $user->save();

        // 3. Ghi vết kiểm toán bất biến toàn hệ thống (DoD 3)
        $this->auditLogService->record([
            'tenant_id' => $user->tenant_id,
            'actor_user_id' => Auth::id(),
            'action' => 'user_role_updated',
            'resource_type' => 'User',
            'resource_id' => (string) $user->id,
            'sensitive_fields' => ['role', 'role_id', 'tenant_id'],
            'reason' => "Superadmin thay đổi phân quyền người dùng '{$user->username}' từ {$oldRole} sang {$roleSlug}.",
            'metadata' => [
                'target_user_id' => $user->id,
                'target_username' => $user->username,
                'target_name' => $user->name,
                'old_role' => $oldRole,
                'new_role' => $roleSlug,
                'assigned_tenant_id' => $user->tenant_id,
            ],
        ]);

        $successMessage = 'Đã cập nhật vai trò và phân quyền tài khoản thành công!';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'error_code' => 'ERR_20_04',
                'message' => $successMessage,
                'user' => $user->fresh(['roleRecord', 'tenant']),
            ]);
        }

        return back()->with('success', $successMessage)->with('toast_code', 'ERR_20_04');
    }

    /**
     * Khóa hoặc Mở khóa tài khoản người dùng
     * POST /admin/users/{user}/status
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        $currentAuthId = Auth::id();
        $targetStatus = $request->input('status'); // 'locked' hoặc 'active'
        if (!$targetStatus) {
            $targetStatus = ($user->status === 'locked') ? 'active' : 'locked';
        }

        // Quy tắc nghiệp vụ: Không được phép tự khóa chính tài khoản Superadmin đang đăng nhập: ERR_20_03
        if ($user->id === $currentAuthId && $targetStatus === 'locked') {
            $errorMessage = 'Không thể tự khóa tài khoản quản trị viên đang thực hiện phiên làm việc!';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_20_03',
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->with('error', $errorMessage)->with('error_code', 'ERR_20_03');
        }

        $oldStatus = $user->status;
        $user->status = $targetStatus;
        $user->save();

        // Ghi vết kiểm toán an ninh
        $this->auditLogService->record([
            'tenant_id' => $user->tenant_id,
            'actor_user_id' => $currentAuthId,
            'action' => 'user_status_changed',
            'resource_type' => 'User',
            'resource_id' => (string) $user->id,
            'sensitive_fields' => ['status'],
            'reason' => "Superadmin thay đổi trạng thái tài khoản '{$user->username}' sang {$targetStatus}.",
            'metadata' => [
                'target_username' => $user->username,
                'old_status' => $oldStatus,
                'new_status' => $targetStatus,
            ],
        ]);

        $msg = ($targetStatus === 'locked') ? 'Đã khóa tài khoản thành công.' : 'Đã mở khóa tài khoản thành công.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $targetStatus,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Đặt lại mật khẩu tài khoản người dùng
     * POST /admin/users/{user}/password
     */
    public function resetUserPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:6|max:100',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Ghi vết kiểm toán
        $this->auditLogService->record([
            'tenant_id' => $user->tenant_id,
            'actor_user_id' => Auth::id(),
            'action' => 'user_password_reset',
            'resource_type' => 'User',
            'resource_id' => (string) $user->id,
            'sensitive_fields' => ['password'],
            'reason' => "Superadmin đặt lại mật khẩu cho tài khoản '{$user->username}'.",
            'metadata' => [
                'target_username' => $user->username,
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt lại mật khẩu cho người dùng thành công!',
            ]);
        }

        return back()->with('success', 'Đã đặt lại mật khẩu cho người dùng thành công!');
    }
}
