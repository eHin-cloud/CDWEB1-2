<?php

namespace App\Http\Controllers;

use Hash;
use Session;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantProfile;
use App\Models\TenantPreference;
use App\Models\User;
use App\Services\OtpService;
use App\Support\SensitiveData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * CRUD User controller
 */
class CrudUserController extends Controller
{

    /**
     * Login page
     */
    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $targetRoute = match (true) {
                $user->isAdmin() => route('user.list'),
                $user->canAccessLandlordDashboard() => route('smartroom.admin'),
                $user->isResident() => route('smartroom.resident'),
                default => route('renty.user'),
            };

            return redirect($targetRoute);
        }

        // Khi người dùng vào trang đăng nhập, reset phiên đăng ký chủ trọ cũ
        session()->forget(['landlord_reg_data', 'landlord_otp', 'landlord_otp_expires', 'landlord_otp_method', 'landlord_otp_target', 'landlord_otp_verified']);

        return view('login.login', ['page' => 'login']);
    }

    /**
     * User submit form login
     */
    public function authUser(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        $user = null;

        // 1. Tìm theo số điện thoại (sử dụng blind index của trường phone)
        $phoneBlindIndex = \App\Support\SensitiveData::blindIndex($login);
        if ($phoneBlindIndex) {
            $user = User::where('phone_blind_index', $phoneBlindIndex)->first();
        }

        // 2. Nếu không thấy, tìm theo username hoặc email
        if (!$user) {
            $user = User::where('username', $login)
                ->orWhere('email', $login)
                ->first();
        }

        // 3. Kiểm tra mật khẩu và đăng nhập
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            
            $defaultRoute = match (true) {
                $user->isAdmin() => route('user.list'),
                $user->canAccessLandlordDashboard() => route('smartroom.admin'),
                $user->isResident() => route('smartroom.resident'),
                default => route('renty.user'),
            };
            return redirect()->intended($defaultRoute)
                ->with('success', 'Đăng nhập thành công! Chào mừng ' . ($user->name ?? 'bạn') . ' quay trở lại.');
        }

        return redirect("login")
            ->withInput($request->only('login'))
            ->with('error', 'Tài khoản hoặc mật khẩu không chính xác! Vui lòng kiểm tra lại tên đăng nhập, số điện thoại hoặc email.');
    }

    /**
     * Registration page
     */
    public function createUser()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('renty.user');
        }

        return view('login.login', ['page' => 'create']);
    }

    /**
     * User submit form register
     */
    public function postUser(Request $request)
    {
        // Nếu admin đang tạo user mới từ danh sách quản trị viên /list hoặc /create:
        if (Auth::check() && Auth::user()->isAdmin()) {
            $request->validate([
                'name' => 'required',
                'username' => 'required|alpha_dash|unique:users',
                'phone' => [
                    'required',
                    'regex:/^0[0-9]{9}$/',
                    function ($attribute, $value, $fail) {
                        if (!empty($value)) {
                            $norm = $this->normalizePhone($value);
                            $blind = \App\Support\SensitiveData::blindIndex($norm);
                            if ($blind && User::where('phone_blind_index', $blind)->exists()) {
                                $fail('Số điện thoại này đã được đăng ký tài khoản trong hệ thống.');
                            }
                        }
                    },
                ],
                'email' => 'nullable|email|unique:users',
                'password' => 'required|min:6',
                'like' => 'nullable|max:255',
            ], [
                'name.required' => 'Vui lòng nhập họ và tên.',
                'username.required' => 'Vui lòng nhập tên đăng nhập.',
                'username.alpha_dash' => 'Tên đăng nhập chỉ gồm chữ cái, số, dấu gạch ngang và gạch dưới.',
                'username.unique' => 'Tên đăng nhập này đã được sử dụng.',
                'phone.required' => 'Vui lòng nhập số điện thoại.',
                'phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số (bắt đầu bằng số 0).',
                'email.email' => 'Địa chỉ email không đúng định dạng.',
                'email.unique' => 'Địa chỉ email này đã được sử dụng.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            ]);

            $data = $request->all();
            $guestRole = Role::where('slug', 'guest')->first();
            $guestRoleId = $guestRole ? $guestRole->id : null;

            User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'phone' => $this->normalizePhone($data['phone']),
                'email' => $data['email'] ?? null,
                'like' => $data['like'] ?? null,
                'role' => 'guest',
                'role_id' => $guestRoleId,
                'password' => Hash::make($data['password'])
            ]);

            return redirect("list")->with('success', 'Thêm thành viên mới thành công!');
        }

        // ĐĂNG KÝ KHÁCH THUÊ: Xác minh phương thức nhận OTP (SĐT bắt buộc, Email tùy chọn)
        $method = $request->input('verification_method', 'phone');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]{3,50}$/',
                'unique:users,username'
            ],
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'verification_method' => ['required', 'string', 'in:email,phone'],
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $norm = $this->normalizePhone($value);
                        $blind = SensitiveData::blindIndex($norm);
                        if ($blind && User::where('phone_blind_index', $blind)->exists()) {
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
                'unique:users,email'
            ],
        ], [
            'name.required' => 'Họ và tên là bắt buộc.',
            'username.required' => 'Vui lòng nhập tên đăng nhập (bắt buộc).',
            'username.min' => 'Tên đăng nhập phải có ít nhất 3 ký tự.',
            'username.max' => 'Tên đăng nhập không được vượt quá 50 ký tự.',
            'username.regex' => 'Tên đăng nhập chỉ gồm chữ cái, số, gạch nối (-) hoặc gạch dưới (_).',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng trong hệ thống.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'verification_method.required' => 'Vui lòng chọn phương thức xác minh.',
            'verification_method.in' => 'Phương thức xác minh không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại (bắt buộc).',
            'phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số (bắt đầu bằng số 0).',
            'email.required' => 'Bạn đã chọn xác minh qua Email, vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng hợp lệ.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng trong hệ thống.',
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone'] ?? null);
        $target = $method === 'email' ? trim($validated['email']) : $normalizedPhone;

        // Sinh mã OTP qua OtpService (quản lý cooldown 60s và bảng otp_codes)
        $otpRes = OtpService::generateOtp($target, 'guest_register');
        if (!$otpRes['success']) {
            return back()->with('error', $otpRes['message'])->withInput();
        }

        $otp = $otpRes['code'];
        $expiresAt = $otpRes['expires_at'];

        // Lưu thông tin đăng ký và OTP vào session
        session([
            'guest_reg_data' => array_merge($validated, ['phone' => $normalizedPhone]),
            'guest_otp' => $otp,
            'guest_otp_expires' => $expiresAt,
            'guest_otp_method' => $method,
            'guest_otp_target' => $target,
            'guest_otp_verified' => false,
        ]);

        // Gửi email nếu phương thức là email
        if ($method === 'email') {
            try {
                Mail::send('emails.guest_otp', [
                    'otp' => $otp,
                    'fullName' => $validated['name'],
                ], function ($msg) use ($target) {
                    $msg->to($target)->subject('SmartRoom & Renty - Mã OTP Xác Minh Tài Khoản Khách Thuê');
                });
                Log::info("Guest OTP email sent successfully to: {$target}");
            } catch (\Exception $e) {
                Log::error("Guest email send failed: " . $e->getMessage());
            }
        } else {
            Log::info("Guest OTP generated for phone {$target}: {$otp}");
        }

        return redirect()->route('guest.verify')
            ->with('success', 'Mã xác thực OTP đã được gửi đến ' . ($method === 'email' ? 'email ' : 'SĐT ') . $target . '. Vui lòng kiểm tra và nhập mã để vào Renty.');
    }

    /**
     * Hiển thị trang Xác minh OTP Khách thuê
     */
    public function showVerifyOtp()
    {
        $regData = session('guest_reg_data');
        $storedOtp = session('guest_otp');

        if (!$regData && !$storedOtp) {
            return redirect()->route('user.createUser')
                ->with('error', 'Vui lòng điền thông tin đăng ký trước.');
        }

        $target = session('guest_otp_target', $regData['email'] ?? $regData['phone'] ?? 'thông tin đã đăng ký');
        $method = session('guest_otp_method', $regData['verification_method'] ?? 'email');

        return view('login.guest_verify', compact('regData', 'target', 'method'));
    }

    /**
     * Gửi lại mã OTP cho khách thuê (AJAX)
     */
    public function sendGuestOtp(Request $request)
    {
        $regData = session('guest_reg_data', []);
        $method = $request->input('method') ?: session('guest_otp_method', $regData['verification_method'] ?? 'email');
        $target = $request->input('target') ?: session('guest_otp_target', $regData['email'] ?? $regData['phone'] ?? '');

        if (empty($target)) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin nhận OTP.'], 422);
        }

        if ($method === 'phone') {
            $target = $this->normalizePhone($target) ?? $target;
        }

        // Sinh OTP qua OtpService với cơ chế cooldown 60s và bảng otp_codes
        $otpRes = OtpService::generateOtp($target, 'guest_register');
        if (!$otpRes['success']) {
            return response()->json([
                'success' => false,
                'message' => $otpRes['message'],
                'retry_after' => $otpRes['retry_after'] ?? null,
            ], 429);
        }

        $otp = $otpRes['code'];
        $expiresAt = $otpRes['expires_at'];

        session([
            'guest_otp' => $otp,
            'guest_otp_expires' => $expiresAt,
            'guest_otp_method' => $method,
            'guest_otp_target' => $target,
            'guest_otp_verified' => false,
        ]);

        if ($method === 'email') {
            try {
                $fullName = $regData['name'] ?? 'Bạn';
                Mail::send('emails.guest_otp', [
                    'otp' => $otp,
                    'fullName' => $fullName,
                ], function ($msg) use ($target) {
                    $msg->to($target)->subject('SmartRoom & Renty - Mã OTP Xác Minh Tài Khoản Khách Thuê');
                });
                Log::info("Guest OTP resent to email: {$target}");
                return response()->json([
                    'success' => true,
                    'message' => 'Mã OTP mới đã được gửi đến email ' . $target . '. Vui lòng kiểm tra hộp thư đến.',
                    'expires_in' => 300,
                ]);
            } catch (\Exception $e) {
                Log::error("Guest email resend failed: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email OTP: ' . $e->getMessage(),
                ], 500);
            }
        }

        Log::info("Guest OTP for phone {$target}: {$otp}");
        return response()->json([
            'success' => true,
            'message' => 'Mã xác minh OTP đã được gửi đến SĐT ' . $target,
            'expires_in' => 300,
        ]);
    }

    /**
     * Xác minh OTP và Kích hoạt tài khoản Khách thuê -> Chuyển vào trang Renty
     */
    public function verifyGuestOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp.size' => 'Mã OTP phải có chính xác 6 chữ số.',
        ]);

        $regData = session('guest_reg_data');
        $target = session('guest_otp_target');
        if (!$target && $regData) {
            $target = ($regData['verification_method'] ?? 'phone') === 'email' ? ($regData['email'] ?? null) : ($regData['phone'] ?? null);
        }

        if (empty($target)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Chưa gửi mã OTP hoặc phiên đã hết hạn. Vui lòng gửi lại.'], 422);
            }
            return back()->with('error', 'Chưa gửi mã OTP hoặc phiên đã hết hạn. Vui lòng gửi lại.');
        }

        // Xác minh bằng OtpService: kiểm tra hạn 5 phút, giới hạn 5 lần sai
        $verifyRes = OtpService::verifyOtp($target, 'guest_register', $request->input('otp'));
        if (!$verifyRes['success']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $verifyRes['message']], 422);
            }
            return back()->with('error', $verifyRes['message'])->withInput();
        }

        if (!$regData) {
            if (Auth::check()) {
                return redirect()->route('renty.user')->with('success', 'Xác minh OTP thành công!');
            }
            return redirect()->route('user.createUser')->with('error', 'Không tìm thấy dữ liệu đăng ký. Vui lòng đăng ký lại.');
        }

        try {
            $tenantRole = Role::where('slug', 'tenant')->first() ?: Role::where('slug', 'guest')->first();
            $tenantRoleId = $tenantRole ? $tenantRole->id : null;

            $normalizedPhone = $this->normalizePhone($regData['phone'] ?? null);

            // Dùng username người dùng tự đặt nếu có, nếu không thì tự sinh ngẫu nhiên bảo mật: tenant_{random_hex}
            $customUsername = !empty($regData['username']) ? trim($regData['username']) : null;
            if ($customUsername && !User::where('username', $customUsername)->exists()) {
                $username = $customUsername;
            } else {
                do {
                    $username = 'tenant_' . bin2hex(random_bytes(4));
                } while (User::where('username', $username)->exists());
            }

            $user = DB::transaction(function () use ($regData, $normalizedPhone, $username, $tenantRoleId) {
                $newUser = User::create([
                    'name' => $regData['name'],
                    'username' => $username,
                    'phone' => $normalizedPhone,
                    'email' => !empty($regData['email']) ? trim($regData['email']) : null,
                    'role' => 'tenant',
                    'role_id' => $tenantRoleId,
                    'password' => Hash::make($regData['password']),
                ]);

                // Khởi tạo hồ sơ khách thuê ban đầu
                TenantProfile::firstOrCreate(['user_id' => $newUser->id]);

                return $newUser;
            });

            session()->forget(['guest_reg_data', 'guest_otp', 'guest_otp_expires', 'guest_otp_method', 'guest_otp_target', 'guest_otp_verified']);

            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect' => route('tenant.preferences')]);
            }

            return redirect()->route('tenant.preferences')->with('success', 'Xác minh tài khoản thành công! Hãy chọn khu vực bạn quan tâm.');
        } catch (\Exception $e) {
            Log::error("Guest user creation failed: " . $e->getMessage());
            return back()->with('error', 'Lỗi khi tạo tài khoản: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Màn hình Onboarding: Sở thích / Khu vực tìm phòng cho Khách thuê
     */
    public function showPreferences()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();
        $preference = TenantPreference::where('user_id', $user->id)->first();
        $savedTags = $preference && is_array($preference->area_tags) ? $preference->area_tags : [];

        return view('tenant.preferences', compact('user', 'savedTags'));
    }

    /**
     * Lưu sở thích tìm phòng cho Khách thuê
     */
    public function savePreferences(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Nếu bấm "Bỏ qua"
        if ($request->has('skip') || $request->input('action') === 'skip') {
            return redirect()->route('renty.user')
                ->with('info', 'Chào mừng bạn đến với Renty! Bạn có thể cập nhật sở thích tìm phòng bất cứ lúc nào.');
        }

        $tagsInput = $request->input('area_tags');
        $tags = [];

        if (is_array($tagsInput)) {
            $tags = array_values(array_filter(array_map('trim', $tagsInput)));
        } elseif (is_string($tagsInput)) {
            $exploded = explode(',', $tagsInput);
            $tags = array_values(array_filter(array_map('trim', $exploded)));
        }

        TenantPreference::updateOrCreate(
            ['user_id' => $user->id],
            ['area_tags' => array_unique($tags)]
        );

        return redirect()->route('renty.user')
            ->with('success', 'Đã lưu sở thích tìm phòng thành công! Renty đã sẵn sàng các gợi ý tốt nhất cho bạn.');
    }

    /**
     * Chuẩn hóa định dạng số điện thoại
     */
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
     * View user detail page
     */
    public function readUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('login.login', ['page' => 'read', 'messi' => $user]);
    }

    /**
     * Delete user by id
     */
    public function deleteUser($id)
    {
        // Ngăn admin tự xóa chính mình
        if (Auth::id() == $id) {
            return redirect("list")->with('error', 'Không thể xóa chính tài khoản đang đăng nhập!');
        }

        $user = User::find($id);
        if (!$user) {
            return redirect("list")->with('error', 'Người dùng không tồn tại hoặc đã bị xóa trước đó!');
        }

        $user->delete();

        return redirect("list")->with('success', 'Xóa tài khoản người dùng thành công!');
    }

    /**
     * Form update user page
     */
    public function updateUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('login.login', ['page' => 'update', 'user' => $user]);
    }

    /**
     * Submit form update user
     */
    public function postUpdateUser(Request $request)
    {
        $input = $request->all();

        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . $input['id'],
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                function ($attribute, $value, $fail) use ($input) {
                    if (!empty($value)) {
                        $norm = preg_replace('/\s+/', '', $value);
                        $blind = \App\Support\SensitiveData::blindIndex($norm);
                        if ($blind && User::where('phone_blind_index', $blind)->where('id', '!=', $input['id'])->exists()) {
                            $fail('Số điện thoại này đã được đăng ký tài khoản trong hệ thống.');
                        }
                    }
                },
            ],
            'email' => 'nullable|email|unique:users,email,' . $input['id'],
            'like' => 'nullable|max:255',
            'password' => 'nullable|min:6',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số (bắt đầu bằng số 0).',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);

        $user = User::find($input['id']);
        $user->name = $input['name'];
        $user->username = $input['username'];
        $user->phone = $input['phone'];
        $user->email = $input['email'] ?? null;
        $user->like = $input['like'];
        if (!empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }
        $user->save();

        return redirect()->route('user.list')->with('success', 'Cập nhật thông tin quản trị viên thành công!');
    }

    /**
     * List of users
     */
    public function listUser()
    {
        if (Auth::check()) {
            $users = User::with(['roleRecord', 'tenant'])->orderByDesc('id')->paginate(10);
            $roleOrder = ['admin', 'unverified_landlord', 'landlord', 'manager', 'resident', 'guest'];
            $roles = Role::whereIn('slug', $roleOrder)
                ->get()
                ->sortBy(function ($role) use ($roleOrder) {
                    return array_search($role->slug, $roleOrder, true);
                })
                ->values();
            $tenants = Tenant::orderBy('name')->get();

            return view('login.login', [
                'page' => 'list',
                'users' => $users,
                'roles' => $roles,
                'tenants' => $tenants,
            ]);
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function updateRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_slug' => 'required|in:admin,unverified_landlord,landlord,manager,resident,guest',
            'tenant_id' => 'nullable|integer|exists:tenants,id',
        ]);

        if (in_array($validated['role_slug'], ['unverified_landlord', 'landlord', 'manager'], true) && empty($validated['tenant_id'])) {
            return back()->with('error', 'Chu tro hoac nhan vien quan ly phai duoc gan nha tro/tenant.');
        }

        $role = Role::where('slug', $validated['role_slug'])->firstOrFail();
        $user = User::findOrFail($validated['user_id']);

        $user->role_id = $role->id;
        $user->tenant_id = in_array($validated['role_slug'], ['admin', 'guest'], true)
            ? null
            : ($validated['tenant_id'] ?? $user->tenant_id);
        $user->role = match ($validated['role_slug']) {
            'admin' => 'admin',
            'unverified_landlord' => 'unverified_landlord',
            'landlord' => 'admin',
            'manager' => 'manager',
            'resident' => 'user',
            'guest' => 'guest',
        };
        $user->save();

        return back()->with('success', 'Da cap nhat vai tro tai khoan.');
    }

    /**
     * Sign out
     */
    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
