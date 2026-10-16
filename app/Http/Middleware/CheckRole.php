<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->roleSlug()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền truy cập.',
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tiếp tục.');
        }

        $userRole = $user->roleSlug();
        $isUserAdmin = $user->isAdmin() || in_array($userRole, ['admin', 'superadmin'], true) || in_array($user->role, ['admin', 'superadmin'], true);

        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        if ($isUserAdmin && (in_array('admin', $roles, true) || in_array('superadmin', $roles, true))) {
            return $next($request);
        }

        if (in_array('superadmin', $roles, true) || in_array('admin', $roles, true) || $request->is('admin/*') || $request->is('admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truy cập bị từ chối. Chỉ Quản trị viên mới có quyền truy cập.',
                ], 403);
            }

            abort(403, 'Truy cập bị từ chối. Chỉ Quản trị viên mới có quyền truy cập.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có vai trò phù hợp để thực hiện hành động này.',
            ], 403);
        }

        return redirect()
            ->route($this->homeRouteFor($user))
            ->with('error', 'Bạn không có quyền truy cập chức năng này.');
    }

    private function homeRouteFor($user): string
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return 'admin.superadmin.dashboard';
        }

        if ($user->isAdmin()) {
            return 'user.list';
        }

        if ($user->isHousekeeper()) {
            return 'admin.housekeeping.index';
        }

        if ($user->canAccessLandlordDashboard()) {
            return 'smartroom.admin';
        }

        if ($user->isResident()) {
            return 'smartroom.resident';
        }

        return 'renty.user';
    }
}
