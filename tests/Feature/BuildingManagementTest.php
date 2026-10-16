<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BuildingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();
        $this->withoutVite();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    private function createTenant(string $name = 'Tenant'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)) . '_' . uniqid() . '@example.com',
        ]);
    }

    private function createLandlordUser(Tenant $tenant, string $username = 'landlord-test')
    {
        $role = Role::firstOrCreate(['slug' => 'landlord'], [
            'name' => 'Chủ trọ',
        ]);

        return User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Landlord ' . $username,
            'username' => $username,
            'email' => $username . '@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * 1. Chủ nhà có thể xem danh sách cơ sở lưu trú của mình
     */
    public function test_landlord_can_view_building_list(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Thử Nghiệm A',
            'address' => '123 Đường Cầu Giấy, Hà Nội',
            'phone' => '0912345678',
            'total_floors' => 5,
            'status' => 'active'
        ]);

        $response = $this->actingAs($landlord)->get(route('admin.buildings.index'));

        $response->assertStatus(200);
        $response->assertSee('Tòa Nhà Thử Nghiệm A');
        $response->assertSee('123 Đường Cầu Giấy, Hà Nội');
    }

    /**
     * 2. Chủ nhà có thể tạo mới cơ sở lưu trú
     */
    public function test_landlord_can_create_building(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $data = [
            'name' => 'Tòa Nhà Xanh EcoHome',
            'address' => '456 Tây Sơn, Đống Đa, Hà Nội',
            'phone' => '0988776655',
            'total_floors' => 7,
            'status' => 'active',
            'description' => 'Tòa nhà mới xây 100%, đầy đủ nội thất.',
            'amenities' => ['Thang máy', 'Hầm để xe rộng rãi', 'Bảo vệ 24/7']
        ];

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), $data);

        $response->assertRedirect(route('admin.buildings.index'));
        $this->assertDatabaseHas('buildings', [
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh EcoHome',
            'phone' => '0988776655',
            'total_floors' => 7,
        ]);
    }

    /**
     * 3. Chủ nhà có thể cập nhật thông tin cơ sở lưu trú
     */
    public function test_landlord_can_update_building(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Cũ',
            'address' => 'Địa chỉ cũ',
            'phone' => '0111111111',
            'total_floors' => 3,
            'status' => 'maintenance'
        ]);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.update', $building->id), [
            'name' => 'Tòa Đã Cải Tạo Mới',
            'address' => 'Địa chỉ mới cập nhật',
            'phone' => '0999999999',
            'total_floors' => 4,
            'status' => 'active',
            'amenities' => ['Thang máy']
        ]);

        $response->assertRedirect(route('admin.buildings.index'));
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'name' => 'Tòa Đã Cải Tạo Mới',
            'phone' => '0999999999',
            'status' => 'active'
        ]);
    }

    /**
     * 4. Tính cô lập dữ liệu (Tenant Isolation): Không thể can thiệp cơ sở của chủ nhà khác
     */
    public function test_tenant_isolation_cannot_modify_other_tenant_building(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $tenantB = $this->createTenant('Tenant B');

        $landlordA = $this->createLandlordUser($tenantA, 'landlord-a');
        $landlordB = $this->createLandlordUser($tenantB, 'landlord-b');

        $buildingB = Building::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Tòa Nhà Của B',
            'address' => 'Hà Nội',
            'total_floors' => 3,
            'status' => 'active'
        ]);

        // Landlord A cố tình sửa tòa nhà của B
        $response = $this->actingAs($landlordA)->post(route('admin.buildings.update', $buildingB->id), [
            'name' => 'Tên Bị Chiếm Đoạt',
            'address' => 'Địa chỉ mới',
            'total_floors' => 2,
            'status' => 'active'
        ]);
        $response->assertStatus(404);

        // Landlord A cố tình xóa tòa nhà của B
        $responseDelete = $this->actingAs($landlordA)->delete(route('admin.buildings.destroy', $buildingB->id));
        $responseDelete->assertStatus(404);
    }

    /**
     * 5. Guard Check: Không được xóa cơ sở lưu trú nếu vẫn còn phòng trực thuộc
     */
    public function test_cannot_delete_building_with_rooms(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Đang Hoạt Động',
            'address' => 'Hà Nội',
            'total_floors' => 4,
            'status' => 'active'
        ]);

        // Tạo phòng trực thuộc tòa nhà
        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 3500000,
            'status' => 'empty'
        ]);

        $response = $this->actingAs($landlord)->delete(route('admin.buildings.destroy', $building->id));

        $response->assertRedirect(route('admin.buildings.index'));
        $response->assertSessionHas('error');

        // Bản ghi tòa nhà vẫn an toàn tồn tại trong CSDL
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'deleted_at' => null
        ]);
    }

    /**
     * 6. Cho phép xóa an toàn (SoftDelete) khi cơ sở hoàn toàn trống phòng
     */
    public function test_can_delete_building_without_rooms(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Trống Không Còn Phòng',
            'address' => 'Hà Nội',
            'total_floors' => 2,
            'status' => 'inactive'
        ]);

        $response = $this->actingAs($landlord)->delete(route('admin.buildings.destroy', $building->id));

        $response->assertRedirect(route('admin.buildings.index'));
        $response->assertSessionHas('success');

        // Tòa nhà đã được soft delete an toàn
        $this->assertSoftDeleted('buildings', [
            'id' => $building->id
        ]);
    }

    /**
     * 7. REST API Guard Check: Chặn xóa cơ sở còn phòng qua API
     */
    public function test_api_guard_check_prevents_deleting_building_with_rooms(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa API Test',
            'address' => 'Đà Nẵng',
            'total_floors' => 3,
            'status' => 'active'
        ]);

        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 4000000,
            'status' => 'empty'
        ]);

        $response = $this->actingAs($landlord, 'sanctum')->deleteJson('/api/admin/buildings/' . $building->id);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false
        ]);
    }

    /**
     * 8. Kiểm tra spec ERR_11_01: Để trống tên cơ sở / tòa nhà
     */
    public function test_spec_err_11_01_empty_name_validation(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), [
            'name' => '',
            'address' => '123 Cầu Giấy',
            'total_floors' => 3,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'Tên cơ sở lưu trú không được để trống.'
        ]);
    }

    /**
     * 9. Kiểm tra spec ERR_11_02: Để trống địa chỉ cơ sở
     */
    public function test_spec_err_11_02_empty_address_validation(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), [
            'name' => 'Tòa Nhà Test',
            'address' => '',
            'total_floors' => 3,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors([
            'address' => 'Địa chỉ cơ sở lưu trú là bắt buộc.'
        ]);
    }

    /**
     * 10. Kiểm tra spec ERR_11_03: Để trống số tầng
     */
    public function test_spec_err_11_03_empty_total_floors_validation(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), [
            'name' => 'Tòa Nhà Test',
            'address' => '123 Cầu Giấy',
            'total_floors' => '',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors([
            'total_floors' => 'Vui lòng nhập số tầng của tòa nhà.'
        ]);
    }

    /**
     * 11. Kiểm tra spec ERR_11_04: Nhập số tầng nhỏ hơn 1
     */
    public function test_spec_err_11_04_floors_less_than_one_validation(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), [
            'name' => 'Tòa Nhà Test',
            'address' => '123 Cầu Giấy',
            'total_floors' => 0,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors([
            'total_floors' => 'Số tầng tòa nhà phải là số nguyên dương lớn hơn hoặc bằng 1.'
        ]);
    }

    /**
     * 12. Kiểm tra spec ERR_11_05: Xóa tòa nhà còn phòng đang cho thuê
     */
    public function test_spec_err_11_05_cannot_delete_building_with_occupied_rooms(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Đang Cho Thuê',
            'address' => 'Hà Nội',
            'total_floors' => 5,
            'status' => 'active',
        ]);

        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '102',
            'floor' => 1,
            'price' => 3000000,
            'status' => 'occupied',
        ]);

        $response = $this->actingAs($landlord)->delete(route('admin.buildings.destroy', $building->id));

        $response->assertRedirect(route('admin.buildings.index'));
        $response->assertSessionHas('error', 'Không thể xóa cơ sở lưu trú đang có phòng có cư dân thuê hoạt động.');
    }

    /**
     * 13. Kiểm tra spec ERR_11_06: Lưu cơ sở lưu trú thành công
     */
    public function test_spec_err_11_06_store_building_success_message(): void
    {
        $tenant = $this->createTenant('Tenant Test');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.buildings.store'), [
            'name' => 'Tòa Nhà Thành Công',
            'address' => '789 Đường Láng, Hà Nội',
            'total_floors' => 4,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.buildings.index'));
        $response->assertSessionHas('success', 'Lưu thông tin cơ sở lưu trú thành công!');
    }
}
