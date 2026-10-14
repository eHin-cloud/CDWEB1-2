<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Services\OtpService;
use App\Support\SensitiveData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Test case đối soát kỹ thuật: FEAT_01_AUTH
 * Đăng ký & Đăng nhập hệ thống (Xác thực OTP & Mật khẩu)
 */
class AuthFeature01Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['slug' => 'guest'], ['name' => 'Khách Vãng Lai']);
        Role::firstOrCreate(['slug' => 'tenant'], ['name' => 'Khách Thuê']);
        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ Trọ']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    }

    /**
     * Kịch bản 1: Màn hình đăng nhập GET /login và GET /register hoạt động bình thường
     */
    public function test_login_and_register_pages_are_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SmartRoom &amp; Renty', false);
        $response->assertSee('signin-login');
        $response->assertSee('signin-password');

        $responseRegister = $this->get('/register');
        $responseRegister->assertStatus(200);
    }

    /**
     * Kịch bản 2: Để trống thông tin đăng nhập trên Web -> Báo lỗi validation
     */
    public function test_login_web_fails_when_fields_are_empty(): void
    {
        $response = $this->post('/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['login', 'password']);
    }

    /**
     * Kịch bản 3: Nhập sai mật khẩu trên Web -> Báo thông tin tài khoản hoặc mật khẩu không chính xác (ERR_01_01)
     */
    public function test_login_web_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser_' . bin2hex(random_bytes(3)),
            'password' => Hash::make('CorrectPassword123'),
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error_code', 'ERR_01_01');
        $response->assertSessionHas('error', 'Thông tin tài khoản hoặc mật khẩu không chính xác.');
    }

    /**
     * Kịch bản 4: Đăng nhập tài khoản bị khóa -> Trả lỗi tài khoản bị khóa
     */
    public function test_login_web_fails_when_account_is_locked(): void
    {
        $user = User::factory()->create([
            'username' => 'lockeduser_' . bin2hex(random_bytes(3)),
            'password' => Hash::make('Password123!'),
            'status' => 'locked',
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error_code', 'ERR_01_13');
        $response->assertSessionHas('error', 'Tài khoản của bạn đã bị khóa bởi Quản trị viên hệ thống.');
    }

    /**
     * Kịch bản 5: Đăng nhập thành công bằng Số điện thoại (sử dụng Blind Index)
     */
    public function test_login_web_success_with_phone_number(): void
    {
        $phone = '0988123456';
        $user = User::factory()->create([
            'username' => 'tenant_' . bin2hex(random_bytes(3)),
            'phone' => $phone,
            'password' => Hash::make('Secret123!'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => $phone,
            'password' => 'Secret123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertSessionHas('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại SmartRoom.');
    }

    /**
     * Kịch bản 6: API POST /api/auth/login với thông tin trống -> Trả 422
     */
    public function test_api_login_fails_with_empty_data(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'login_id' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_01_01',
        ]);
    }

    /**
     * Kịch bản 7: API POST /api/auth/login sai mật khẩu -> Trả 422 và message chuẩn
     */
    public function test_api_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user_' . bin2hex(random_bytes(3)) . '@example.com',
            'password' => Hash::make('ValidPass123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login_id' => $user->email,
            'password' => 'BadPass999',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_01_01',
            'message' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.',
        ]);
    }

    /**
     * Kịch bản 8: API POST /api/auth/login thành công bằng Email hoặc Phone -> Trả Bearer Token
     */
    public function test_api_login_success(): void
    {
        $email = 'api_user_' . bin2hex(random_bytes(3)) . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('ValidPass123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login_id' => $email,
            'password' => 'ValidPass123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error_code' => 'ERR_01_12',
            'message' => 'Đăng nhập thành công! Chào mừng bạn quay trở lại SmartRoom.',
        ]);
        $this->assertNotEmpty($response->json('access_token'));
    }

    /**
     * Kịch bản 9: API POST /api/auth/otp/verify kiểm tra xác thực mã OTP
     */
    public function test_api_otp_verify_flow(): void
    {
        $target = '0909999888';
        $otpRes = OtpService::generateOtp($target, 'guest_register');
        $this->assertTrue($otpRes['success']);
        $validCode = $otpRes['code'];

        // 9.1: Thử mã sai -> báo lỗi 422 (ERR_01_10)
        $failResponse = $this->postJson('/api/auth/otp/verify', [
            'target' => $target,
            'otp' => '000000',
            'type' => 'guest_register',
        ]);
        $failResponse->assertStatus(422);
        $failResponse->assertJson(['success' => false, 'error_code' => 'ERR_01_10']);

        // 9.2: Thử mã đúng -> trả 200 (ERR_01_12)
        $successResponse = $this->postJson('/api/auth/otp/verify', [
            'target' => $target,
            'otp' => $validCode,
            'type' => 'guest_register',
        ]);
        $successResponse->assertStatus(200);
        $successResponse->assertJson(['success' => true, 'error_code' => 'ERR_01_12']);
    }

    /**
     * Kịch bản 10: Quy trình Quên mật khẩu qua Email OTP trên Web (FEAT_01_AUTH)
     */
    public function test_web_forgot_password_full_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'hien_test_' . bin2hex(random_bytes(3)) . '@example.com',
            'password' => Hash::make('OldPassword123!'),
            'status' => 'active',
        ]);

        // 10.1: Truy cập trang quên mật khẩu
        $res = $this->get('/forgot-password');
        $res->assertStatus(200);
        $res->assertSeeText('Khôi Phục Mật Khẩu');

        // 10.2: Nhập email không tồn tại -> báo lỗi
        $resFail = $this->post('/forgot-password/send-otp', [
            'email' => 'nonexistent_email_12345@example.com',
        ]);
        $resFail->assertSessionHas('error');

        // 10.3: Nhập email hợp lệ -> lưu OTP và redirect
        $resSend = $this->post('/forgot-password/send-otp', [
            'email' => $user->email,
        ]);
        $resSend->assertRedirect(route('password.forgot'));
        $resSend->assertSessionHas('forgot_pwd_email', $user->email);
        $this->assertNotNull(session('forgot_pwd_otp'));
        $otp = session('forgot_pwd_otp');

        // 10.4: Nhập sai OTP -> báo lỗi
        $resWrongOtp = $this->post('/forgot-password/reset', [
            'otp' => '000000',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);
        $resWrongOtp->assertSessionHas('error');

        // 10.5: Nhập đúng OTP -> đổi mật khẩu thành công và redirect login
        $resSuccess = $this->post('/forgot-password/reset', [
            'otp' => $otp,
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);
        $resSuccess->assertRedirect(route('login'));
        $resSuccess->assertSessionHas('success');

        // 10.6: Đăng nhập lại với mật khẩu mới
        $resLogin = $this->post('/login', [
            'login' => $user->email,
            'password' => 'NewPassword456!',
        ]);
        $this->assertAuthenticatedAs($user->fresh());
    }

    /**
     * Kịch bản 11: Quy trình Quên mật khẩu qua Email OTP trên REST API (FEAT_01_AUTH)
     */
    public function test_api_forgot_password_full_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'api_forgot_' . bin2hex(random_bytes(3)) . '@example.com',
            'password' => Hash::make('OldPassAPI123!'),
            'status' => 'active',
        ]);

        // 11.1: Gửi OTP với email không tồn tại -> 404
        $resNotFound = $this->postJson('/api/auth/password/forgot', [
            'email' => 'notfound_' . bin2hex(random_bytes(3)) . '@example.com',
        ]);
        $resNotFound->assertStatus(404);

        // 11.2: Gửi OTP với email hợp lệ -> 200
        $resSend = $this->postJson('/api/auth/password/forgot', [
            'email' => $user->email,
        ]);
        $resSend->assertStatus(200);
        $resSend->assertJson(['success' => true]);

        // Lấy OTP từ OtpCode mới nhất
        $latestOtp = \App\Models\OtpCode::where('target', $user->email)
            ->where('type', 'forgot_password')
            ->latest('id')
            ->first();
        $this->assertNotNull($latestOtp);
        $otpCode = $latestOtp->code;

        // 11.3: Reset với OTP sai -> 422
        $resWrongOtp = $this->postJson('/api/auth/password/reset', [
            'email' => $user->email,
            'otp' => '999999',
            'password' => 'NewPassApi789!',
            'password_confirmation' => 'NewPassApi789!',
        ]);
        $resWrongOtp->assertStatus(422);

        // 11.4: Reset với OTP đúng -> 200
        $resSuccess = $this->postJson('/api/auth/password/reset', [
            'email' => $user->email,
            'otp' => $otpCode,
            'password' => 'NewPassApi789!',
            'password_confirmation' => 'NewPassApi789!',
        ]);
        $resSuccess->assertStatus(200);
        $resSuccess->assertJson(['success' => true]);

        // 11.5: Đăng nhập API với mật khẩu mới -> thành công
        $resLogin = $this->postJson('/api/auth/login', [
            'login_id' => $user->email,
            'password' => 'NewPassApi789!',
        ]);
        $resLogin->assertStatus(200);
        $resLogin->assertJson(['success' => true]);
        $this->assertNotEmpty($resLogin->json('access_token'));
    }
}

