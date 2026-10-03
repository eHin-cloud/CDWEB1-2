<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\LandlordProfile;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\OtpService;
use App\Support\SensitiveData;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class LandlordOnboardingController extends Controller
{
    public function create(Request $request)
    {
        if ($request->has('reset')) {
            session()->forget(['landlord_reg_data', 'landlord_otp', 'landlord_otp_expires', 'landlord_otp_method', 'landlord_otp_target', 'landlord_otp_verified']);
        }

        $regData = session('landlord_reg_data', []);
        $isReset = $request->has('reset') || empty($regData);

        return view('landlord.onboarding', compact('regData', 'isReset'));
    }

    /**
     * BƯỚC 1: Đăng ký tối giản Chủ trọ
     * Nhận: username, full_name, password, phone (bắt buộc), email (tùy chọn), verification_method
     */
    public function store(Request $request)
    {
        $method = $request->input('verification_method', 'phone');

        $validated = $request->validate([
            'username' => ['required', 'string', 'alpha_dash:ascii', 'min:3', 'max:50', 'unique:users,username'],
            'full_name' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'verification_method' => ['required', 'string', 'in:email,phone'],
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $normalized = $this->normalizePhone($value);
                        $blindIndex = SensitiveData::blindIndex($normalized);
                        if ($blindIndex && User::where('phone_blind_index', $blindIndex)->exists()) {
                            $fail('Số điện thoại này đã được đăng ký tài khoản trong hệ thống.');
                        }
                    }
                },
            ],
            'email' => [
                $method === 'email' ? 'required' : 'nullable',
                'string',
                'email',
                'max:150',
                'unique:users,email',
            ],
        ], [
            'username.required' => 'Tài khoản đăng nhập là bắt buộc.',
            'username.alpha_dash' => 'Tài khoản đăng nhập chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
            'username.min' => 'Tài khoản đăng nhập tối thiểu 3 ký tự.',
            'username.unique' => 'Tài khoản đăng nhập này đã được sử dụng trong hệ thống.',
            'full_name.required' => 'Họ và tên chủ trọ là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại (bắt buộc).',
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0 (ví dụ: 0988123456).',
            'phone.unique' => 'Số điện thoại này đã được đăng ký tài khoản trong hệ thống.',
            'email.required' => 'Bạn đã chọn xác minh qua Email, vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng hợp lệ.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng trong hệ thống.',
            'verification_method.required' => 'Vui lòng chọn phương thức xác minh.',
            'verification_method.in' => 'Phương thức xác minh không hợp lệ.',
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone'] ?? null);
        $target = $method === 'email' ? trim($validated['email']) : $normalizedPhone;

        // Sinh mã OTP qua OtpService (quản lý cooldown 60s và bảng otp_codes)
        $otpRes = OtpService::generateOtp($target, 'landlord_register');
        if (!$otpRes['success']) {
            return back()->with('error', $otpRes['message'])->withInput();
        }

        $otp = $otpRes['code'];
        $expiresAt = $otpRes['expires_at'];

        // Lưu thông tin đăng ký và OTP vào session
        session([
            'landlord_reg_data' => array_merge($validated, ['phone' => $normalizedPhone]),
            'landlord_otp' => $otp,
            'landlord_otp_expires' => $expiresAt,
            'landlord_otp_method' => $method,
            'landlord_otp_target' => $target,
            'landlord_otp_verified' => false,
        ]);

        // Gửi email nếu phương thức là email
        if ($method === 'email') {
            try {
                Mail::send('emails.landlord_otp', [
                    'otp' => $otp,
                    'fullName' => $validated['full_name'],
                ], function ($msg) use ($target) {
                    $msg->to($target)->subject('SmartRoom - Mã OTP Xác Minh Tài Khoản');
                });
                Log::info("OTP email sent successfully to: {$target}");
            } catch (\Exception $e) {
                Log::error("Email send failed: " . $e->getMessage());
            }
        } else {
            Log::info("OTP generated for phone {$target}: {$otp}");
        }

        return redirect()->route('landlord.verify')
            ->with('success', 'Mã xác thực OTP đã được gửi đến ' . ($method === 'email' ? 'email ' : 'SĐT ') . $target . '. Vui lòng kiểm tra hộp thư.');
    }

    /**
     * BƯỚC 2: Hiển thị trang Xác minh OTP
     */
    public function showVerifyForm()
    {
        $regData = session('landlord_reg_data');
        $storedOtp = session('landlord_otp');

        if (!$regData && !$storedOtp) {
            return redirect()->route('landlord.register')
                ->with('error', 'Vui lòng điền thông tin đăng ký trước.');
        }

        $target = session('landlord_otp_target', $regData['email'] ?? $regData['phone'] ?? 'thông tin đã đăng ký');
        $method = session('landlord_otp_method', $regData['verification_method'] ?? 'email');

        return view('landlord.verify', compact('regData', 'target', 'method'));
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '+84')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }

    /**
     * Gửi lại mã OTP code
     */
    public function sendOtp(Request $request)
    {
        $regData = session('landlord_reg_data', []);
        $method = $request->input('method') ?: session('landlord_otp_method', $regData['verification_method'] ?? 'email');
        $target = $request->input('target') ?: session('landlord_otp_target', $regData['email'] ?? $regData['phone'] ?? '');

        if (empty($target)) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin nhận OTP.'], 422);
        }

        if ($method === 'phone') {
            $target = $this->normalizePhone($target) ?? $target;
        }

        // Sinh mã OTP qua OtpService với cơ chế cooldown 60s và lưu DB
        $otpRes = OtpService::generateOtp($target, 'landlord_register');
        if (!$otpRes['success']) {
            return response()->json([
                'success' => false,
                'message' => $otpRes['message'],
                'retry_after' => $otpRes['retry_after'] ?? null,
            ], 429);
        }

        $otp = $otpRes['code'];
        $expiresAt = $otpRes['expires_at'];

        // Store in session
        session([
            'landlord_otp' => $otp,
            'landlord_otp_expires' => $expiresAt,
            'landlord_otp_method' => $method,
            'landlord_otp_target' => $target,
            'landlord_otp_verified' => false,
        ]);

        // Attempt to send via email
        if ($method === 'email') {
            try {
                $fullName = $regData['full_name'] ?? 'Quý chủ trọ';
                Mail::send('emails.landlord_otp', [
                    'otp' => $otp,
                    'fullName' => $fullName,
                ], function ($msg) use ($target) {
                    $msg->to($target)->subject('SmartRoom - Mã OTP Xác Minh Tài Khoản');
                });
                Log::info("OTP sent to email: {$target}");
                return response()->json([
                    'success' => true,
                    'message' => 'Mã OTP mới đã được gửi đến email ' . $target . '. Vui lòng kiểm tra hộp thư đến hoặc thư rác.',
                    'expires_in' => 300,
                ]);
            } catch (\Exception $e) {
                Log::error("Email send failed: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email OTP qua máy chủ Gmail: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Phone
        Log::info("OTP for phone {$target}: {$otp}");
        return response()->json([
            'success' => true,
            'message' => 'Mã xác minh OTP đã được gửi đến SĐT ' . $target,
            'expires_in' => 300,
        ]);
    }

    /**
     * BƯỚC 2: Xác minh OTP & Kích hoạt tài khoản -> Vào Dashboard ngay
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp.size' => 'Mã OTP phải có chính xác 6 chữ số.',
        ]);

        $regData = session('landlord_reg_data');
        $target = session('landlord_otp_target');
        if (!$target && $regData) {
            $target = ($regData['verification_method'] ?? 'phone') === 'email' ? ($regData['email'] ?? null) : ($regData['phone'] ?? null);
        }

        if (empty($target)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Chưa gửi mã OTP hoặc phiên đã hết hạn. Vui lòng gửi lại.'], 422);
            }
            return back()->with('error', 'Chưa gửi mã OTP hoặc phiên đã hết hạn. Vui lòng gửi lại.');
        }

        // Xác minh bằng OtpService: kiểm tra thời hạn 5 phút, giới hạn 5 lần sai
        $verifyRes = OtpService::verifyOtp($target, 'landlord_register', $request->input('otp'));
        if (!$verifyRes['success']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $verifyRes['message']], 422);
            }
            return back()->with('error', $verifyRes['message'])->withInput();
        }

        // OTP HỢP LỆ -> Lấy thông tin đăng ký để tạo tài khoản
        if (!$regData) {
            // Trường hợp user đã tồn tại sẵn đang xác minh lại
            if (Auth::check()) {
                session(['landlord_otp_verified' => true]);
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'redirect' => route('smartroom.admin')]);
                }
                return redirect()->route('smartroom.admin')->with('success', 'Xác minh OTP thành công!');
            }
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu đăng ký. Vui lòng đăng ký lại.'], 422);
            }
            return redirect()->route('landlord.register')->with('error', 'Không tìm thấy dữ liệu đăng ký. Vui lòng đăng ký lại.');
        }

        try {
            $username = !empty($regData['username']) ? trim($regData['username']) : null;
            if (empty($username)) {
                do {
                    $username = 'landlord_' . bin2hex(random_bytes(4));
                } while (User::where('username', $username)->exists());
            }

            $user = DB::transaction(function () use ($regData, $username) {
                $role = Role::where('slug', 'unverified_landlord')->firstOrFail();
                $propertyName = 'Nhà trọ ' . $regData['full_name'];
                $propertyAddress = 'Đang cập nhật địa chỉ';
                $hashedPassword = Hash::make($regData['password']);
                $tenantEmail = !empty($regData['email']) ? $regData['email'] : ($username . '@smartroom.local');

                $tenant = Tenant::create([
                    'name' => $propertyName,
                    'email' => $tenantEmail,
                    'phone' => $regData['phone'] ?? null,
                    'verification_status' => 'unverified',
                    'onboarding_step' => 1,
                    'listing_badge' => 'unverified',
                    'boost_score' => 0,
                ]);

                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'role_id' => $role->id,
                    'name' => $regData['full_name'],
                    'username' => $username,
                    'phone' => $regData['phone'] ?? null,
                    'email' => $regData['email'] ?? null,
                    'password' => $hashedPassword,
                    'like' => 'Chủ trọ SmartRoom',
                    'role' => 'unverified_landlord',
                ]);

                LandlordProfile::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'full_name' => $regData['full_name'],
                    'username' => $username,
                    'phone' => $regData['phone'] ?? null,
                    'email' => $regData['email'] ?? null,
                    'password' => $hashedPassword,
                    'verification_method' => $regData['verification_method'],
                    'property_name' => $propertyName,
                    'property_address' => $propertyAddress,
                    'status' => 'unverified',
                ]);

                Building::create([
                    'tenant_id' => $tenant->id,
                    'name' => $propertyName,
                    'address' => $propertyAddress,
                    'description' => 'Tòa nhà quản lý của chủ trọ ' . $regData['full_name'],
                ]);

                return $user;
            });
        } catch (QueryException $e) {
            $field = 'phone';
            $message = 'Không thể kích hoạt tài khoản do thông tin bị trùng lặp trong hệ thống.';
            if (str_contains($e->getMessage(), 'users_phone_blind_unique')) {
                $field = 'phone';
                $message = 'Số điện thoại này đã được đăng ký tài khoản trong hệ thống. Vui lòng đổi số điện thoại khác.';
            } elseif (str_contains($e->getMessage(), 'users_username_unique')) {
                $field = 'username';
                $message = 'Tên tài khoản đăng nhập đã tồn tại. Vui lòng đổi tên tài khoản.';
            } elseif (str_contains($e->getMessage(), 'users_email_unique')) {
                $field = 'email';
                $message = 'Địa chỉ email đã được đăng ký. Vui lòng đổi email.';
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('landlord.register')->withErrors([$field => $message])->with('error', $message);
        }

        // Đăng nhập ngay lập tức
        Auth::login($user);

        // Xóa sạch session đăng ký & OTP
        session()->forget(['landlord_reg_data', 'landlord_otp', 'landlord_otp_expires', 'landlord_otp_method', 'landlord_otp_target', 'landlord_otp_verified']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xác minh thành công! Đang chuyển hướng vào Dashboard...',
                'redirect' => route('smartroom.admin')
            ]);
        }

        return redirect()
            ->route('smartroom.admin')
            ->with('success', 'Xác minh tài khoản thành công! Chào mừng bạn đến với SmartRoom Dashboard.');
    }
}
