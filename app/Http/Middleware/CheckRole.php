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
                    'message' => 'Khong co quyen truy cap.',
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Ban can dang nhap de tiep tuc.');
        }

        if (in_array($user->roleSlug(), $roles, true) || ($user->isSuperAdmin() && in_array('admin', $roles, true))) {
            return $next($request);
        }

        if (in_array('superadmin', $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truy cập bị từ chối. Chỉ Superadmin mới có quyền truy cập.',
                ], 403);
            }

            abort(403, 'Truy cập bị từ chối. Chỉ Superadmin mới có quyền truy cập.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong co vai tro phu hop de thuc hien hanh dong nay.',
            ], 403);
        }

        return redirect()
            ->route($this->homeRouteFor($user))
            ->with('error', 'Ban khong co quyen truy cap chuc nang nay.');
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
