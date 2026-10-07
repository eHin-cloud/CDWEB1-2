<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeMiddleware
{
    /**
     * Handle an incoming request for multi-tenancy scoping.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Kiểm tra vai trò được phép truy cập phân hệ Buồng phòng & Lễ tân
        $allowedRoles = ['admin', 'landlord', 'unverified_landlord', 'manager', 'receptionist', 'housekeeper'];
        if (!in_array($user->roleSlug(), $allowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập phân hệ Lễ tân & Buồng phòng.'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập phân hệ này.');
        }

        // Thiết lập tenant_id hợp lệ vào request attributes
        $scopedTenantId = $user->tenant_id;

        // Nếu là admin hệ thống, có thể xem tenant chỉ định qua query/header hoặc tất cả
        if ($user->isAdmin()) {
            $requestedTenantId = $request->input('tenant_id') ?? $request->header('X-Tenant-Id');
            if ($requestedTenantId) {
                $scopedTenantId = (int) $requestedTenantId;
            }
        } else {
            // Không phải admin: nếu request cố tình gửi tenant_id khác của mình -> chặn
            $requestedTenantId = $request->input('tenant_id');
            if ($requestedTenantId && (int) $requestedTenantId !== (int) $user->tenant_id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không được phép truy cập dữ liệu ngoài phạm vi cơ sở của bạn.'
                    ], 403);
                }
                return back()->with('error', 'Không được phép truy cập dữ liệu ngoài cơ sở của bạn.');
            }
        }

        $request->attributes->set('scoped_tenant_id', $scopedTenantId);

        return $next($request);
    }
}
