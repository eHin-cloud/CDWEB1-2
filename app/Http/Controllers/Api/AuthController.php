<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::with(['roleRecord', 'tenant'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác'
            ], 412);
        }

        if ($user->status === 'locked') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa bởi Quản trị viên hệ thống.'
            ], 403);
        }

        // Tạo token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->roleSlug(),
                'tenant' => $user->tenant ? [
                    'id' => $user->tenant->id,
                    'name' => $user->tenant->name,
                ] : null
            ]
        ]);
    }

    // Đăng ký cho Khách tìm trọ (Guest)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $roleGuest = Role::where('slug', 'guest')->first();

        $user = User::create([
            'tenant_id' => null,
            'role_id' => $roleGuest->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => 'guest'
            ]
        ], 201);
    }

    // Lấy thông tin cá nhân (Profile)
    public function profile(Request $request)
    {
        $user = User::with(['roleRecord', 'tenant'])->find(Auth::id());

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->roleSlug(),
                'tenant' => $user->tenant ? [
                    'id' => $user->tenant->id,
                    'name' => $user->tenant->name,
                    'bank_name' => $user->tenant->bank_name,
                    'bank_account_no' => $user->tenant->bank_account_no,
                    'bank_account_name' => $user->tenant->bank_account_name,
                ] : null
            ]
        ]);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * POST /api/auth/check-availability
     * Kiểm tra nhanh SĐT hoặc Email đã tồn tại chưa khi người dùng blur khỏi ô input
     */
    public function checkAvailability(Request $request)
    {
        $type = $request->input('type'); // 'username' | 'phone' | 'email'
        if (!$type) {
            if ($request->has('username')) $type = 'username';
            elseif ($request->has('phone')) $type = 'phone';
            elseif ($request->has('email')) $type = 'email';
        }

        $value = trim((string) ($request->input('value') ?? $request->input($type) ?? ''));

        if (empty($value)) {
            return response()->json([
                'available' => true,
                'message' => ''
            ]);
        }

        if ($type === 'username') {
            if (!preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $value)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Tên tài khoản 3–50 ký tự, chỉ gồm chữ cái, số, dấu gạch ngang hoặc gạch dưới'
                ]);
            }

            $exists = User::where('username', $value)->exists();
            if ($exists) {
                return response()->json([
                    'available' => false,
                    'message' => 'Tên tài khoản đăng nhập này đã được sử dụng trong hệ thống'
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Tên tài khoản hợp lệ và có thể sử dụng'
            ]);
        }

        if ($type === 'phone') {
            $norm = preg_replace('/\s+/', '', $value);
            if (str_starts_with($norm, '+84')) {
                $norm = '0' . substr($norm, 3);
            }

            if (!preg_match('/^0[0-9]{9}$/', $norm)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Số điện thoại phải gồm đúng 10 chữ số bắt đầu bằng số 0'
                ]);
            }

            $blind = \App\Support\SensitiveData::blindIndex($norm);
            $exists = $blind ? User::where('phone_blind_index', $blind)->exists() : false;

            if ($exists) {
                return response()->json([
                    'available' => false,
                    'message' => 'Số điện thoại này đã được đăng ký tài khoản trong hệ thống'
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Số điện thoại hợp lệ và có thể sử dụng'
            ]);
        }

        if ($type === 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Địa chỉ email không đúng định dạng hợp lệ'
                ]);
            }

            $exists = User::where('email', $value)->exists();
            if ($exists) {
                return response()->json([
                    'available' => false,
                    'message' => 'Địa chỉ email này đã được sử dụng trong hệ thống'
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Địa chỉ email hợp lệ và có thể sử dụng'
            ]);
        }

        return response()->json(['available' => true, 'message' => '']);
    }
}
