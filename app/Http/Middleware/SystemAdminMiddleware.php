<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Kiểm tra user hiện tại có vai trò 'admin' hay không.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Kiểm tra đã đăng nhập chưa
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập. Vui lòng cung cấp mã xác thực Bearer token.'
            ], 401);
        }

        // 2. Kiểm tra tài khoản có bị khóa không
        if ($user->status === 'locked') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản quản trị viên này đã bị khóa.'
            ], 403);
        }

        // 3. Kiểm tra vai trò admin (hỗ trợ cả roleSlug và cột role)
        $role = method_exists($user, 'roleSlug') ? $user->roleSlug() : $user->role;

        if ($role !== 'admin' && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Truy cập bị từ chối. Bạn không có quyền Quản trị viên hệ thống (admin).'
            ], 403);
        }

        return $next($request);
    }
}
