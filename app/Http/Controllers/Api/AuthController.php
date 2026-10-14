<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Đăng nhập
    public function login(Request $request)
    {
        $loginId = trim((string) ($request->input('login_id') ?? $request->input('login') ?? $request->input('email') ?? $request->input('phone')));
        $password = (string) $request->input('password');

        if (empty($loginId) || empty($password)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_01_01',
                'message' => 'Vui lòng nhập tài khoản đăng nhập và mật khẩu.'
            ], 422);
        }

        $user = null;

        // 1. Tìm theo số điện thoại qua Blind Index
        $phoneBlind = \App\Support\SensitiveData::blindIndex($loginId);
        if ($phoneBlind) {
            $user = User::with(['roleRecord', 'tenant'])->where('phone_blind_index', $phoneBlind)->first();
        }

        // 2. Tìm theo email hoặc username
        if (!$user) {
            $user = User::with(['roleRecord', 'tenant'])
                ->where('email', $loginId)
                ->orWhere('username', $loginId)
                ->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_01_01',
                'message' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.'
            ], 422);
        }

        if ($user->status === 'locked') {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_01_13',
                'message' => 'Tài khoản của bạn đã bị khóa bởi Quản trị viên hệ thống.'
            ], 403);
        }

        // Tạo token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'error_code' => 'ERR_01_12',
            'message' => 'Đăng nhập thành công! Chào mừng bạn quay trở lại SmartRoom.',
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

    /**
     * POST /api/auth/otp/verify
     * Xác thực mã OTP gửi về số điện thoại/email (FEAT_01_AUTH)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'target' => 'required|string',
            'otp' => 'required|string|size:6',
            'type' => 'nullable|string',
        ], [
            'target.required' => 'Vui lòng cung cấp số điện thoại hoặc email nhận mã.',
            'otp.required' => 'Mã xác thực OTP là bắt buộc.',
            'otp.size' => 'Mã xác thực OTP phải gồm đúng 6 chữ số.',
        ]);

        $target = trim((string) $request->input('target'));
        $otp = trim((string) $request->input('otp'));
        $type = (string) $request->input('type', 'guest_register');

        // Chuẩn hóa số điện thoại nếu là phone
        if (preg_match('/^[0-9+]/', $target)) {
            $norm = preg_replace('/\s+/', '', $target);
            if (str_starts_with($norm, '+84')) {
                $norm = '0' . substr($norm, 3);
            }
            $target = $norm;
        }

        $verifyRes = \App\Services\OtpService::verifyOtp($target, $type, $otp);

        if (!$verifyRes['success']) {
            $isExpired = str_contains($verifyRes['message'], 'hết hạn') || str_contains($verifyRes['message'], 'hết hiệu lực');
            return response()->json([
                'success' => false,
                'error_code' => $isExpired ? 'ERR_01_11' : 'ERR_01_10',
                'message' => $verifyRes['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'error_code' => 'ERR_01_12',
            'message' => 'Xác thực mã OTP thành công!',
        ]);
    }

    /**
     * POST /api/auth/password/forgot
     * Gửi mã OTP xác thực khôi phục mật khẩu qua Email (FEAT_01_AUTH)
     */
    public function forgotPasswordSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ], [
            'email.required' => 'Vui lòng cung cấp địa chỉ email nhận mã.',
            'email.email' => 'Địa chỉ email không đúng định dạng hợp lệ.',
        ]);

        $email = trim(strtolower($request->input('email')));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Địa chỉ email này chưa được đăng ký trong hệ thống.',
            ], 404);
        }

        if ($user->status === 'locked') {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_01_13',
                'message' => 'Tài khoản của bạn đã bị khóa bởi Quản trị viên hệ thống.',
            ], 403);
        }

        $otpRes = \App\Services\OtpService::generateOtp($user->email, 'forgot_password');
        if (!$otpRes['success'] && !empty($otpRes['cooldown'])) {
            return response()->json([
                'success' => false,
                'retry_after' => $otpRes['retry_after'] ?? 60,
                'message' => $otpRes['message'],
            ], 429);
        }

        $otp = $otpRes['code'];

        try {
            Mail::send('emails.forgot_password_otp', [
                'otp' => $otp,
                'fullName' => $user->name,
                'email' => $user->email,
            ], function ($msg) use ($user) {
                $msg->to($user->email, $user->name)
                    ->subject('SmartRoom & Renty - Mã OTP Khôi Phục Mật Khẩu');
            });
        } catch (\Exception $e) {
            Log::error("Failed to send API forgot password email: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Mã OTP xác thực đã được gửi đến hộp thư {$user->email}.",
            'retry_after' => $otpRes['retry_after'] ?? 60,
            'expires_in' => $otpRes['expires_in'] ?? 300,
        ]);
    }

    /**
     * POST /api/auth/password/reset
     * Xác thực mã OTP và đặt lại mật khẩu mới (FEAT_01_AUTH)
     */
    public function forgotPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng cung cấp địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng hợp lệ.',
            'otp.required' => 'Mã xác thực OTP là bắt buộc.',
            'otp.size' => 'Mã xác thực OTP phải gồm đúng 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
        ]);

        $email = trim(strtolower($request->input('email')));
        $otp = trim((string) $request->input('otp'));

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản người dùng.',
            ], 404);
        }

        $verifyRes = \App\Services\OtpService::verifyOtp($email, 'forgot_password', $otp);
        if (!$verifyRes['success']) {
            $isExpired = str_contains($verifyRes['message'], 'hết hạn') || str_contains($verifyRes['message'], 'hết hiệu lực');
            return response()->json([
                'success' => false,
                'error_code' => $isExpired ? 'ERR_01_11' : 'ERR_01_10',
                'message' => $verifyRes['message'],
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay với mật khẩu mới.',
        ]);
    }
}

