<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\SystemConfig;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminConsoleFeature20Test extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $tenantUser;
    protected User $normalUser;
    protected Tenant $tenant;
    protected Role $superadminRole;
    protected Role $adminRole;
    protected Role $landlordRole;
    protected Role $managerRole;
    protected Role $residentRole;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Khởi tạo các vai trò
        $this->superadminRole = Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Quản trị viên tối cao']);
        $this->adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin hệ thống']);
        $this->landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $this->managerRole = Role::firstOrCreate(['slug' => 'manager'], ['name' => 'Quản lý']);
        $this->residentRole = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);

        // 2. Khởi tạo cơ sở lưu trú
        $this->tenant = Tenant::firstOrCreate(
            ['name' => 'SmartRoom Cầu Giấy'],
            ['address' => 'Số 123 Cầu Giấy, Hà Nội', 'phone' => '0988111222', 'email' => 'caugiay@smartroom.test']
        );

        // 3. Khởi tạo tài khoản Superadmin
        $this->superadmin = User::firstOrCreate(
            ['username' => 'superadmin_test'],
            [
                'name' => 'Nguyễn Anh Quý (Superadmin)',
                'email' => 'superadmin@smartroom.test',
                'password' => bcrypt('123456'),
                'role' => 'superadmin',
                'role_id' => $this->superadminRole->id,
                'status' => 'active',
            ]
        );
        $this->superadmin->phone = '0999999999';
        $this->superadmin->save();

        // 4. Khởi tạo tài khoản Khách thuê
        $this->tenantUser = User::firstOrCreate(
            ['username' => 'khachthue_test'],
            [
                'name' => 'Trần Khách Thuê',
                'email' => 'khachthue@smartroom.test',
                'password' => bcrypt('123456'),
                'role' => 'user',
                'role_id' => $this->residentRole->id,
                'status' => 'active',
            ]
        );
        $this->tenantUser->phone = '0912345678';
        $this->tenantUser->save();

        // 5. Khởi tạo tài khoản thường có số điện thoại '0901234567'
        $this->normalUser = User::firstOrCreate(
            ['username' => 'user_phone_test'],
            [
                'name' => 'Lê Người Dùng Test',
                'email' => 'user_test@smartroom.test',
                'password' => bcrypt('123456'),
                'role' => 'user',
                'role_id' => $this->residentRole->id,
                'status' => 'active',
            ]
        );
        $this->normalUser->phone = '0901234567';
        $this->normalUser->save();
    }

    /**
     * DoD STT 1: Tài khoản Khách thuê cố tình truy cập /dashboard
     * Kết quả mong đợi: Hệ thống chặn truy cập và hiển thị lỗi 403 Forbidden (Kiểm tra Role Superadmin)
     */
    public function test_dod_1_tenant_access_dashboard_is_blocked_with_403_forbidden(): void
    {
        // 1. Thử truy cập /dashboard
        $response = $this->actingAs($this->tenantUser)
            ->get('/dashboard');

        $response->assertStatus(403);

        // 2. Thử truy cập /admin/dashboard
        $responseAdmin = $this->actingAs($this->tenantUser)
            ->get('/admin/dashboard');

        $responseAdmin->assertStatus(403);
    }

    /**
     * DoD STT 2: Tìm kiếm người dùng theo số điện thoại '0901234567'
     * Kết quả mong đợi: Bảng lọc đúng người dùng có số điện thoại tương ứng (Kiểm tra Search user)
     */
    public function test_dod_2_search_user_by_phone_number_filters_correctly(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get('/admin/dashboard?search_user=0901234567');

        $response->assertStatus(200);
        $response->assertSee('Lê Người Dùng Test');
        $response->assertSee('user_phone_test');

        // Test API JSON
        $jsonResponse = $this->actingAs($this->superadmin)
            ->getJson('/admin/dashboard?search_user=0901234567');

        $jsonResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $userData = $jsonResponse->json('users.data');
        $this->assertNotEmpty($userData);
        $this->assertEquals('Lê Người Dùng Test', $userData[0]['name']);
    }

    /**
     * DoD STT 3: Nâng quyền một người dùng từ 'User' lên 'Admin'
     * Kết quả mong đợi: Cập nhật quyền thành công và ghi nhận vào Audit Log (Happy case Phân quyền)
     */
    public function test_dod_3_upgrade_user_from_user_to_admin_success_and_logs_audit(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson("/admin/users/{$this->normalUser->id}/role", [
                'role_slug' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('error_code', 'ERR_20_04')
            ->assertJsonPath('message', 'Đã cập nhật vai trò và phân quyền tài khoản thành công!');

        // Kiểm tra trong CSDL tài khoản đã được nâng lên admin
        $this->normalUser->refresh();
        $this->assertEquals('admin', $this->normalUser->role);
        $this->assertEquals($this->adminRole->id, $this->normalUser->role_id);

        // Kiểm tra bản ghi trong bảng AuditLog
        $log = AuditLog::where('resource_type', 'User')
            ->where('resource_id', (string) $this->normalUser->id)
            ->where('action', 'user_role_updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($this->superadmin->id, $log->actor_user_id);
        $this->assertNotNull($log->row_hash);
    }

    /**
     * Quy tắc nghiệp vụ & UI Error ERR_20_03:
     * Không thể tự khóa tài khoản quản trị viên đang thực hiện phiên làm việc!
     */
    public function test_self_lock_prevention_returns_err_20_03(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson("/admin/users/{$this->superadmin->id}/status", [
                'status' => 'locked',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_20_03')
            ->assertJsonPath('message', 'Không thể tự khóa tài khoản quản trị viên đang thực hiện phiên làm việc!');

        $this->superadmin->refresh();
        $this->assertEquals('active', $this->superadmin->status);
    }

    /**
     * UI Error ERR_20_01: Chưa chọn vai trò khi phân quyền
     */
    public function test_role_update_fails_without_role_returns_err_20_01(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson("/admin/users/{$this->normalUser->id}/role", [
                'role_slug' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_20_01')
            ->assertJsonPath('message', 'Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.');
    }

    /**
     * UI Error ERR_20_02: Gán vai trò Nhân viên/Quản lý nhưng thiếu cơ sở lưu trú
     */
    public function test_role_update_staff_without_tenant_returns_err_20_02(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson("/admin/users/{$this->normalUser->id}/role", [
                'role_slug' => 'manager',
                'tenant_id' => null,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_20_02')
            ->assertJsonPath('message', 'Nhân viên hoặc Quản lý bắt buộc phải được gán vào một cơ sở lưu trú cụ thể.');
    }

    /**
     * Cấu hình tham số toàn sàn: POST /admin/system/config
     */
    public function test_update_system_config_stores_and_audits(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson('/admin/system/config', [
                'commission_rate' => 12.5,
                'free_listing_quota' => 8,
                'platform_fee_fixed' => 60000,
                'auto_approve_landlord' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('12.5', SystemConfig::get('commission_rate'));
        $this->assertEquals('8', SystemConfig::get('free_listing_quota'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'system_config_updated',
            'actor_user_id' => $this->superadmin->id,
        ]);
    }

    /**
     * Màn hình nhật ký kiểm toán an ninh GET /admin/audit-logs
     */
    public function test_superadmin_can_view_audit_logs(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get('/admin/audit-logs');

        $response->assertStatus(200);
        $response->assertSee('Nhật Ký Kiểm Toán An Ninh');
    }

    /**
     * Màn hình danh sách người dùng GET /list: Hiển thị đầy đủ thông tin và thống kê
     */
    public function test_user_list_displays_full_information_and_stats(): void
    {
        // Tạo thêm 15 user để có tổng cộng > 10 user (chắc chắn có phân trang)
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'username' => 'test_page_user_' . $i,
                'name' => 'Người Dùng Phân Trang ' . $i,
                'email' => "page_user_{$i}@test.com",
                'phone' => '09870000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'password' => bcrypt('123456'),
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get('/list');

        $response->assertStatus(200);
        $response->assertSee('Quản Trị Hệ Thống');
        $response->assertSee('Tổng thành viên');
        $response->assertSee('Quản trị viên');
        $response->assertSee('Chủ nhà trọ');
        $response->assertSee('Cư dân thuê trọ');
        
        // Kiểm tra thấy user ở trang 1
        $response->assertSee('test_page_user_15');

        // Kiểm tra thấy thông tin ngày tạo, vai trò, tình trạng
        $response->assertSee('Hoạt động');

        // Kiểm tra phân trang xuất hiện (có link trang 2)
        $response->assertSee('page=2');
        $response->assertSee('smartroom-pagination-nav');

        // Khi sang trang 2, kiểm tra thấy superadmin
        $page2Response = $this->actingAs($this->superadmin)->get('/list?page=2');
        $page2Response->assertStatus(200);
        $page2Response->assertSee($this->superadmin->username);
    }

    /**
     * Kiểm tra phân trang thông minh hiển thị dạng 1 2 3 ... TrangCuoi và hỗ trợ jump box
     */
    public function test_pagination_renders_smart_jump_dots_when_many_pages(): void
    {
        // Tạo thêm 90 user để có tổng cộng 10 trang
        for ($i = 1; $i <= 90; $i++) {
            User::create([
                'username' => 'many_user_' . $i,
                'name' => 'Người Dùng Trang Lớn ' . $i,
                'email' => "many_user_{$i}@test.com",
                'phone' => '098600' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => bcrypt('123456'),
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get('/list');

        $response->assertStatus(200);
        // Kiểm tra thấy cấu trúc jump-box và jump-input
        $response->assertSee('jump-box');
        $response->assertSee('jump-input');
        $response->assertSee('dots-after');
    }

    /**
     * Chi tiết người dùng GET /read?id=... hiển thị đầy đủ thông tin
     */
    public function test_user_read_displays_full_details(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get('/read?id=' . $this->normalUser->id);

        $response->assertStatus(200);
        $response->assertSee($this->normalUser->name);
        $response->assertSee($this->normalUser->username);
        $response->assertSee($this->normalUser->phone);
        $response->assertSee($this->normalUser->email);
        $response->assertSee('Đang hoạt động');
        $response->assertSee('Ngày đăng ký');
    }

    /**
     * Cập nhật thông tin người dùng POST /update hỗ trợ status và chống tự khóa tài khoản
     */
    public function test_user_update_handles_status_and_prevents_self_lock(): void
    {
        // 1. Cập nhật thông tin normalUser thành công
        $updateResponse = $this->actingAs($this->superadmin)
            ->post('/update', [
                'id' => $this->normalUser->id,
                'name' => 'Lê Người Dùng Đã Sửa',
                'username' => 'user_phone_edited',
                'phone' => '0901234567',
                'email' => 'user_edited@smartroom.test',
                'status' => 'locked',
            ]);

        $updateResponse->assertRedirect(route('user.list'));
        $this->normalUser->refresh();
        $this->assertEquals('Lê Người Dùng Đã Sửa', $this->normalUser->name);
        $this->assertEquals('locked', $this->normalUser->status);

        // 2. Chống tự khóa chính mình
        $selfLockResponse = $this->actingAs($this->superadmin)
            ->post('/update', [
                'id' => $this->superadmin->id,
                'name' => $this->superadmin->name,
                'username' => $this->superadmin->username,
                'phone' => $this->superadmin->phone,
                'email' => $this->superadmin->email,
                'status' => 'locked',
            ]);

        $selfLockResponse->assertSessionHas('error', 'Không thể tự khóa tài khoản của chính mình!');
        $this->superadmin->refresh();
        $this->assertEquals('active', $this->superadmin->status);
    }
}

