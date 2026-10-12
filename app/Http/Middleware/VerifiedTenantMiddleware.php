<?php

namespace App\Http\Middleware;

use App\Models\Contract;
use App\Models\Resident;
use App\Models\Room;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware verified_tenant:
 * Đảm bảo chỉ người dùng đã đăng nhập và có hợp đồng thuê mới được phép đánh giá phòng thực tế.
 */
class VerifiedTenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập tài khoản để thực hiện đánh giá.',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập tài khoản để thực hiện đánh giá.');
        }

        // Admin hoặc Landlord được phép đánh giá/kiểm thử
        if ($user->isAdmin() || $user->isSuperAdmin() || $user->isLandlord()) {
            return $next($request);
        }

        $roomId = $request->route('id') ?? $request->input('room_id');

        // Kiểm tra xem người dùng có hợp đồng thuê phòng này hay không
        $hasContractForRoom = false;

        if ($roomId) {
            $hasContractForRoom = Contract::where('room_id', $roomId)
                ->where(function ($query) use ($user) {
                    $query->whereHas('resident', function ($rq) use ($user) {
                        $rq->where('user_id', $user->id)
                            ->orWhere('phone', $user->phone)
                            ->orWhere('email', $user->email);
                    });
                })->exists();

            if (!$hasContractForRoom) {
                // Kiểm tra trực tiếp trong bảng resident
                $hasContractForRoom = Resident::where('room_id', $roomId)
                    ->where(function ($rq) use ($user) {
                        $rq->where('user_id', $user->id)
                            ->orWhere('phone', $user->phone)
                            ->orWhere('email', $user->email);
                    })->exists();
            }
        }

        // Hoặc người dùng là cư dân có hợp đồng trong hệ thống (verified tenant)
        $isVerifiedTenant = $hasContractForRoom
            || $user->isResident()
            || Contract::whereHas('resident', function ($rq) use ($user) {
                $rq->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone)
                    ->orWhere('email', $user->email);
            })->exists();

        if (!$isVerifiedTenant) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ người có hợp đồng thuê phòng mới được quyền gửi đánh giá thực tế.',
                ], 403);
            }

            return redirect()->back()->with('error', 'Chỉ người có hợp đồng thuê phòng mới được quyền gửi đánh giá thực tế.');
        }

        return $next($request);
    }
}
