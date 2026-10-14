<?php

namespace Tests\Feature;

use App\Models\LandlordVerificationRequest;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandlordVerificationCelebrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ / Quản lý']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin hệ thống']);
    }

    public function test_landlord_sees_congratulation_on_login_after_kyc_approved(): void
    {
        $tenant = Tenant::create([
            'name' => 'Nhà Trọ Cầu Giấy',
            'email' => 'trocaugiay@example.com',
            'verification_status' => 'kyc_verified',
            'listing_badge' => 'kyc_verified',
        ]);

        $landlordRole = Role::where('slug', 'landlord')->first();
        $landlord = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Hoàng',
            'username' => 'chutrohoang',
            'email' => 'hoang@example.com',
            'password' => Hash::make('password123'),
            'role' => 'landlord',
        ]);

        // Tạo yêu cầu KYC vừa được duyệt nhưng chưa chúc mừng
        $request = LandlordVerificationRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $landlord->id,
            'type' => 'kyc',
            'status' => 'approved',
            'reviewed_at' => now(),
            'congratulated_at' => null,
        ]);

        // Chủ trọ đăng nhập lại
        $response = $this->post('/login', [
            'login' => 'chutrohoang',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('smartroom.admin'));
        $response->assertSessionHas('verification_congratulation');
        $sessionData = session('verification_congratulation');
        $this->assertEquals('kyc', $sessionData['type']);
        $this->assertStringContainsString('KYC', $sessionData['title']);

        // congratulated_at phải được đánh dấu
        $this->assertNotNull($request->fresh()->congratulated_at);
    }

    public function test_landlord_sees_congratulation_on_login_after_premium_approved(): void
    {
        $tenant = Tenant::create([
            'name' => 'Chung Cư Mini Tích Xanh',
            'email' => 'tichxanh@example.com',
            'verification_status' => 'premium_verified',
            'listing_badge' => 'premium_verified',
        ]);

        $landlordRole = Role::where('slug', 'landlord')->first();
        $landlord = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Tích Xanh',
            'username' => 'chutrotichxanh',
            'email' => 'tichxanh@example.com',
            'password' => Hash::make('password123'),
            'role' => 'landlord',
        ]);

        // Tạo yêu cầu Premium vừa được duyệt nhưng chưa chúc mừng
        $request = LandlordVerificationRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $landlord->id,
            'type' => 'premium',
            'status' => 'approved',
            'reviewed_at' => now(),
            'congratulated_at' => null,
        ]);

        // Chủ trọ đăng nhập lại
        $response = $this->post('/login', [
            'login' => 'chutrotichxanh',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('smartroom.admin'));
        $response->assertSessionHas('verification_congratulation');
        $sessionData = session('verification_congratulation');
        $this->assertEquals('premium', $sessionData['type']);
        $this->assertStringContainsString('Tích Xanh', $sessionData['title']);

        // congratulated_at phải được đánh dấu
        $this->assertNotNull($request->fresh()->congratulated_at);
    }

    public function test_congratulation_not_shown_again_on_subsequent_login(): void
    {
        $tenant = Tenant::create([
            'name' => 'Nhà Trọ Đã Chúc Mừng',
            'email' => 'dachucmung@example.com',
            'verification_status' => 'kyc_verified',
        ]);

        $landlordRole = Role::where('slug', 'landlord')->first();
        $landlord = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Đã Xem',
            'username' => 'chutrodaxem',
            'email' => 'daxem@example.com',
            'password' => Hash::make('password123'),
            'role' => 'landlord',
        ]);

        // Yêu cầu đã được chúc mừng trước đó
        LandlordVerificationRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $landlord->id,
            'type' => 'kyc',
            'status' => 'approved',
            'reviewed_at' => now()->subDay(),
            'congratulated_at' => now()->subHours(2),
        ]);

        // Đăng nhập lần tiếp theo
        $response = $this->post('/login', [
            'login' => 'chutrodaxem',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('smartroom.admin'));
        $response->assertSessionMissing('verification_congratulation');
    }
}
