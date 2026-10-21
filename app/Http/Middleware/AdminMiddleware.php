<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Bạn phải đăng nhập tài khoản quản trị.');
        }

        $user = auth()->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return redirect()
                ->route($user->isSuperAdmin() ? 'admin.superadmin.dashboard' : 'user.list')
                ->with('error', 'Admin hệ thống không truy cập trực tiếp dashboard chủ trọ.');
        }

        if (!$user->canAccessLandlordDashboard()) {
            return redirect()
                ->route($user->isResident() ? 'smartroom.resident' : 'renty.user')
                ->with('error', 'Tài khoản của bạn không có quyền truy cập cổng Admin.');
        }

        return $next($request);
    }
}
