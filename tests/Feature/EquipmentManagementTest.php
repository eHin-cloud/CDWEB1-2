<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Equipment;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EquipmentManagementTest extends TestCase
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
     * 1. Chủ trọ xem trang quản lý thiết bị
     */
    public function test_landlord_can_view_equipment_page(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->get(route('admin.equipment.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Thông Tin Thiết Bị');
    }

    /**
     * 2. Spec ERR_12_01: Để trống tên thiết bị
     */
    public function test_spec_err_12_01_empty_name_validation(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.equipment.store'), [
            'code' => 'TB_01',
            'name' => '',
            'unit' => 'cai',
            'quantity' => 5,
        ]);

        $response->assertSessionHasErrors([
            'name' => 'Tên trang thiết bị tài sản không được để trống.'
        ]);
    }

    /**
     * 3. Spec ERR_12_02: Để trống mã thiết bị
     */
    public function test_spec_err_12_02_empty_code_validation(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.equipment.store'), [
            'code' => '',
            'name' => 'Điều hòa Panasonic',
            'unit' => 'cai',
            'quantity' => 5,
        ]);

        $response->assertSessionHasErrors([
            'code' => 'Mã quản lý thiết bị là bắt buộc.'
        ]);
    }

    /**
     * 4. Spec ERR_12_03: Nhập số lượng tồn kho nhỏ hơn 0
     */
    public function test_spec_err_12_03_quantity_less_than_zero(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->post(route('admin.equipment.store'), [
            'code' => 'TB_03',
            'name' => 'Tủ lạnh mini',
            'unit' => 'cai',
            'quantity' => -1,
        ]);

        $response->assertSessionHasErrors([
            'quantity' => 'Số lượng trang thiết bị nhập kho phải lớn hơn hoặc bằng 0.'
        ]);
    }

    /**
     * 5. Spec ERR_12_05: Bàn giao số lượng vượt quá tồn kho
     */
    public function test_spec_err_12_05_allocate_exceeds_stock(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Test',
            'address' => 'Hà Nội',
            'total_floors' => 3,
            'status' => 'active',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 3000000,
            'status' => 'empty',
        ]);

        $equipment = Equipment::create([
            'tenant_id' => $tenant->id,
            'code' => 'QUAT01',
            'name' => 'Quạt treo tường',
            'unit' => 'cai',
            'total_quantity' => 3,
            'allocated_quantity' => 2, // Còn tồn 1 cái
            'version' => 1,
        ]);

        // Cố tình bàn giao 2 cái vượt quá tồn 1 cái
        $response = $this->actingAs($landlord)->post(route('admin.equipment.allocate'), [
            'equipment_id' => $equipment->id,
            'room_id' => $room->id,
            'quantity' => 2,
        ]);

        $response->assertSessionHas('error', 'Số lượng thiết bị bàn giao vượt quá số lượng tồn kho khả dụng!');
    }

    /**
     * 6. Spec ERR_12_06: Bàn giao thiết bị vào phòng thành công
     */
    public function test_spec_err_12_06_allocate_success(): void
    {
        $tenant = $this->createTenant('Tenant 1');
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Test',
            'address' => 'Hà Nội',
            'total_floors' => 3,
            'status' => 'active',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '102',
            'floor' => 1,
            'price' => 3000000,
            'status' => 'empty',
        ]);

        $equipment = Equipment::create([
            'tenant_id' => $tenant->id,
            'code' => 'DIEUHOA01',
            'name' => 'Điều hòa Daikin',
            'unit' => 'cai',
            'total_quantity' => 5,
            'allocated_quantity' => 0,
            'version' => 1,
        ]);

        $response = $this->actingAs($landlord)->post(route('admin.equipment.allocate'), [
            'equipment_id' => $equipment->id,
            'room_id' => $room->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('admin.equipment.index'));
        $response->assertSessionHas('success', 'Đã bàn giao trang thiết bị vào phòng thành công.');

        $this->assertDatabaseHas('room_equipment', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'equipment_id' => $equipment->id,
            'quantity' => 1,
        ]);
    }
}
