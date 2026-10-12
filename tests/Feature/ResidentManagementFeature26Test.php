<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FEAT_26_RESIDENTS: Quản lý Cư dân & Khách thuê (Resident Management)
 * Phụ trách: Huỳnh Văn Vĩnh Em
 */
class ResidentManagementFeature26Test extends TestCase
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

    private function createTenant(string $name = 'Nhà Trọ Vĩnh Em'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'vinhem_' . uniqid() . '@smartroom.vn',
            'domain' => 'vinhem-' . uniqid(),
        ]);
    }

    private function createLandlordUser(Tenant $tenant): User
    {
        $role = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        return User::create([
            'name' => 'Huỳnh Văn Vĩnh Em',
            'username' => 'landlord_' . uniqid(),
            'email' => 'landlord_' . uniqid() . '@smartroom.vn',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'phone' => '0988776655',
        ]);
    }

    private function createRoom(Tenant $tenant, string $roomNumber = '101', string $status = 'empty'): Room
    {
        $building = Building::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
        ], [
            'address' => '123 Đường Cầu Giấy, Hà Nội',
        ]);

        return Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 1,
            'price' => 3500000,
            'status' => $status,
            'cleaning_status' => 'clean',
        ]);
    }

    /**
     * Kiểm tra endpoint GET /smartroom/admin/residents yêu cầu auth
     */
    public function test_get_residents_page_requires_authentication(): void
    {
        $response = $this->get('/smartroom/admin/residents');
        $response->assertRedirect(route('login'));
    }

    /**
     * Chủ trọ truy cập trang Quản lý cư dân thành công và thấy bảng danh sách
     */
    public function test_landlord_can_access_residents_page(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room = $this->createRoom($tenant, '201', 'empty');

        $response = $this->actingAs($landlord)->get('/smartroom/admin/residents');

        $response->assertStatus(200);
        $response->assertSee('tblResidents');
        $response->assertSee('Danh sách cư dân');
        $response->assertSee('btnSaveResident');
    }

    /**
     * Kịch bản lỗi 1 (ERR_26_01): Để trống họ và tên cư dân
     */
    public function test_validation_error_when_full_name_is_empty_err_26_01(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room = $this->createRoom($tenant, '102', 'empty');

        $payload = [
            'full_name' => '',
            'phone' => '0912345678',
            'id_card' => '079201001234',
            'room_id' => $room->id,
            'temporary_residence' => 1,
        ];

        // Gửi qua JSON API
        $responseJson = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);
        $responseJson->assertStatus(422);
        $responseJson->assertJson([
            'code' => 'ERR_26_01',
            'message' => 'Họ và tên cư dân là bắt buộc.',
        ]);

        // Gửi qua form POST thông thường
        $responseRedirect = $this->actingAs($landlord)->post('/smartroom/admin/residents/store', $payload);
        $responseRedirect->assertSessionHas('error', 'Họ và tên cư dân là bắt buộc.');
        $responseRedirect->assertSessionHas('error_code', 'ERR_26_01');
    }

    /**
     * Kịch bản lỗi 2 (ERR_26_02): Để trống số CCCD
     */
    public function test_validation_error_when_id_card_is_empty_err_26_02(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room = $this->createRoom($tenant, '103', 'empty');

        $payload = [
            'full_name' => 'Nguyễn Văn A',
            'phone' => '0912345678',
            'id_card' => '',
            'room_id' => $room->id,
            'temporary_residence' => 1,
        ];

        $response = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_26_02',
            'message' => 'Số Căn cước công dân là bắt buộc để đăng ký cư trú.',
        ]);
    }

    /**
     * Kịch bản kiểm thử DoD STT 2: Nhập CCCD không đủ 12 số (ví dụ '123456')
     */
    public function test_validation_error_when_id_card_length_is_invalid(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room = $this->createRoom($tenant, '104', 'empty');

        $payload = [
            'full_name' => 'Trần Văn C',
            'phone' => '0912345678',
            'id_card' => '123456',
            'room_id' => $room->id,
            'temporary_residence' => 0,
        ];

        $response = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Số CCCD phải gồm đúng 12 chữ số',
        ]);
    }

    /**
     * Kịch bản lỗi 3 (ERR_26_03): Số CCCD bị trùng trong hệ thống
     */
    public function test_validation_error_when_id_card_is_duplicated_err_26_03(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room1 = $this->createRoom($tenant, '105', 'occupied');
        $room2 = $this->createRoom($tenant, '106', 'empty');

        $idCard = '079201009999';

        // Tạo cư dân đầu tiên
        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'name' => 'Cư dân Cũ',
            'phone' => '0911223344',
            'cccd' => $idCard,
            'start_date' => now()->toDateString(),
            'status' => 'active',
            'temporary_residence_status' => 'registered',
            'version' => 1,
        ]);

        // Cố tình thêm cư dân thứ 2 có trùng CCCD
        $payload = [
            'full_name' => 'Cư dân Mới Trùng CCCD',
            'phone' => '0988776655',
            'id_card' => $idCard,
            'room_id' => $room2->id,
            'temporary_residence' => 1,
        ];

        $response = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_26_03',
            'message' => 'Số Căn cước công dân này đã được đăng ký cho một cư dân khác.',
        ]);
    }

    /**
     * Kịch bản lỗi 4 (ERR_26_04): Chưa chọn phòng bàn giao cho cư dân
     */
    public function test_validation_error_when_room_id_is_missing_err_26_04(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        $payload = [
            'full_name' => 'Trần Văn C',
            'phone' => '0912345678',
            'id_card' => '079201001111',
            'room_id' => '',
            'temporary_residence' => 1,
        ];

        $response = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_26_04',
            'message' => 'Vui lòng chọn phòng lưu trú bàn giao cho cư dân ở.',
        ]);
    }

    /**
     * Kịch bản kiểm thử Multi-tenancy: Không thể chọn phòng thuộc cơ sở (tenant) khác
     */
    public function test_cannot_assign_resident_to_another_tenant_room(): void
    {
        $tenant1 = $this->createTenant('Tenant 1');
        $tenant2 = $this->createTenant('Tenant 2');
        $landlord1 = $this->createLandlordUser($tenant1);
        $roomTenant2 = $this->createRoom($tenant2, '999', 'empty');

        $payload = [
            'full_name' => 'Trần Văn C',
            'phone' => '0912345678',
            'id_card' => '079201002222',
            'room_id' => $roomTenant2->id,
            'temporary_residence' => 1,
        ];

        $response = $this->actingAs($landlord1)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_26_04',
            'message' => 'Vui lòng chọn phòng lưu trú bàn giao cho cư dân ở.',
        ]);
    }

    /**
     * Kịch bản Happy Case (DoD STT 3 & ERR_26_05): Thêm cư dân thành công và phòng chuyển sang Đã thuê (occupied)
     */
    public function test_successful_resident_creation_and_room_becomes_occupied_err_26_05(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $room = $this->createRoom($tenant, '205', 'empty');

        $this->assertEquals('empty', $room->status);

        $payload = [
            'full_name' => 'Trần Văn C',
            'phone' => '0987654321',
            'id_card' => '079201008888',
            'room_id' => $room->id,
            'temporary_residence' => 1,
        ];

        $response = $this->actingAs($landlord)->postJson('/smartroom/admin/residents/store', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'code' => 'ERR_26_05',
            'message' => 'Đã lưu hồ sơ cư dân thành công! Phòng đã tự động chuyển sang trạng thái Đã thuê.',
        ]);

        // Xác thực trong cơ sở dữ liệu
        $this->assertDatabaseHas('residents', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Trần Văn C',
            'temporary_residence_status' => 'registered',
        ]);

        // Xác thực trạng thái phòng đã tự động chuyển sang occupied (Đã thuê)
        $room->refresh();
        $this->assertEquals('occupied', $room->status);
        $this->assertEquals('Đã thuê', $room->status_label);
    }
}
