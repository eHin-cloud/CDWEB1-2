<?php

namespace Tests\Feature;

use App\Models\LandlordProfile;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandlordProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    private function setupLandlord(): array
    {
        $tenant = Tenant::create([
            'name' => 'Khu Trọ Bình An',
            'email' => 'landlord-profile@example.com',
            'verification_status' => 'kyc_verified',
        ]);

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Nguyễn Thanh Hiền',
            'username' => 'thanh_hien_landlord',
            'email' => 'hien.landlord@example.com',
            'phone' => '0912345678',
            'role' => 'landlord',
            'password' => Hash::make('password123'),
        ]);

        $profile = LandlordProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'full_name' => 'Nguyễn Thanh Hiền',
            'phone' => '0912345678',
            'property_name' => 'Khu Trọ Bình An',
            'property_address' => '123 Đường Số 1, Thủ Đức, TP.HCM',
            'status' => 'approved',
        ]);

        return [$user, $tenant, $profile];
    }

    /**
     * Kiểm tra Route GET /smartroom/admin/profile điều hướng đúng
     */
    public function test_can_access_profile_route(): void
    {
        [$user] = $this->setupLandlord();

        $response = $this->actingAs($user)->get(route('smartroom.admin.profile'));
        $response->assertRedirect(route('smartroom.admin', ['tab' => 'profile-section']));
    }

    /**
     * DoD 1 (ERR_02_01): Xóa trắng trường Họ và tên và bấm Lưu -> Báo lỗi 'Họ và tên chủ cơ sở không được để trống.'
     */
    public function test_dod_1_validation_fails_when_full_name_is_empty(): void
    {
        [$user] = $this->setupLandlord();

        $response = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => '',
            'phone' => '0912345678',
        ]);

        $response->assertSessionHasErrors('full_name');
        $errors = session('errors')->get('full_name');
        $this->assertContains('Họ và tên chủ cơ sở không được để trống.', $errors);
    }

    /**
     * DoD 2 (ERR_02_02): Nhập SĐT chữ: 'abcdef' -> Báo lỗi 'Vui lòng nhập số điện thoại liên hệ chính của chủ trọ.'
     */
    public function test_dod_2_validation_fails_when_phone_is_invalid(): void
    {
        [$user] = $this->setupLandlord();

        // Nhập chữ cái
        $responseChar = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => 'Nguyễn Thanh Hiền',
            'phone' => 'abcdef',
        ]);
        $responseChar->assertSessionHasErrors('phone');
        $errorsChar = session('errors')->get('phone');
        $this->assertContains('Vui lòng nhập số điện thoại liên hệ chính của chủ trọ.', $errorsChar);

        // Nhập thiếu số
        $responseShort = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => 'Nguyễn Thanh Hiền',
            'phone' => '0912345',
        ]);
        $responseShort->assertSessionHasErrors('phone');
        $errorsShort = session('errors')->get('phone');
        $this->assertContains('Vui lòng nhập số điện thoại liên hệ chính của chủ trọ.', $errorsShort);
    }

    /**
     * ERR_02_07: Số tài khoản ngân hàng chứa chữ cái -> Báo lỗi 'Số tài khoản ngân hàng chỉ được bao gồm các chữ số nguyên.'
     */
    public function test_validation_fails_when_bank_account_contains_letters(): void
    {
        [$user] = $this->setupLandlord();

        $response = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => 'Nguyễn Thanh Hiền',
            'phone' => '0912345678',
            'bank_name' => 'MB Bank',
            'bank_account_number' => 'MB12345ABC',
        ]);

        $response->assertSessionHasErrors('bank_account_number');
        $errors = session('errors')->get('bank_account_number');
        $this->assertContains('Số tài khoản ngân hàng chỉ được bao gồm các chữ số nguyên.', $errors);
    }

    /**
     * ERR_02_05: Có bank_name mà thiếu bank_account_number
     */
    public function test_validation_fails_when_bank_account_is_missing_with_bank_name(): void
    {
        [$user] = $this->setupLandlord();

        $response = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => 'Nguyễn Thanh Hiền',
            'phone' => '0912345678',
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '',
        ]);

        $response->assertSessionHasErrors('bank_account_number');
        $errors = session('errors')->get('bank_account_number');
        $this->assertContains('Số tài khoản ngân hàng không được để trống để tạo mã VietQR.', $errors);
    }

    /**
     * DoD 3 (Happy Case & ERR_02_08): Nhập thông tin hợp lệ -> Lưu CSDL thành công, đồng bộ users và landlord_profiles
     */
    public function test_dod_3_can_update_profile_successfully_with_valid_data(): void
    {
        [$user, $tenant, $profile] = $this->setupLandlord();

        $response = $this->actingAs($user)->post(route('smartroom.admin.profile.update'), [
            'full_name' => 'nguyễn thanh hiền', // Test viết hoa chữ cái đầu theo spec
            'phone' => '0988776655',
            'email' => 'hien.new@example.com',
            'property_name' => 'Chung Cư Mini Diamond',
            'property_address' => '456 Lê Văn Việt, Tăng Nhơn Phú B, TP. Thủ Đức',
            'national_id' => '079200008899',
            'permanent_address' => 'Hồ Chí Minh',
            'bank_name' => 'MB Bank (Quân Đội)',
            'bank_account_number' => '0988776655001',
            'business_license' => 'MST998877',
        ]);

        $response->assertRedirect(route('smartroom.admin', ['tab' => 'profile-section']))
            ->assertSessionHas('success', 'Cập nhật hồ sơ chủ trọ và tài khoản nhận tiền thành công!');

        // Kiểm tra đồng bộ bảng users (Họ tên viết hoa chữ cái đầu: Nguyễn Thanh Hiền)
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nguyễn Thanh Hiền',
            'email' => 'hien.new@example.com',
        ]);
        $this->assertSame('0988776655', $user->fresh()->phone);

        // Kiểm tra đồng bộ bảng landlord_profiles
        $this->assertDatabaseHas('landlord_profiles', [
            'user_id' => $user->id,
            'full_name' => 'Nguyễn Thanh Hiền',
            'property_name' => 'Chung Cư Mini Diamond',
            'property_address' => '456 Lê Văn Việt, Tăng Nhơn Phú B, TP. Thủ Đức',
            'bank_name' => 'MB Bank (Quân Đội)',
        ]);
        $this->assertSame('0988776655', $profile->fresh()->phone);

        // Kiểm tra đồng bộ bảng tenants
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Chung Cư Mini Diamond',
            'bank_name' => 'MB Bank (Quân Đội)',
        ]);
    }
}
