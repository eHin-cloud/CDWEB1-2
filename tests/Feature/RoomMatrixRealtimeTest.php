<?php

namespace Tests\Feature;

use App\Events\RoomStatusUpdated;
use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RoomMatrixRealtimeTest extends TestCase
{
    use RefreshDatabase;

    protected User $landlord;
    protected Tenant $tenant;
    protected Building $building;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Khởi tạo Tenant & Role
        $role = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Chủ trọ']
        );

        $this->tenant = Tenant::create([
            'name' => 'Nhà trọ Thảo Điền',
            'email' => 'landlord@thaodien.com',
            'phone' => '0909112233',
        ]);

        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $role->id,
            'name' => 'Nguyễn Anh Quý',
            'username' => 'anhquy_landlord',
            'role' => 'landlord',
            'password' => bcrypt('password123'),
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa nhà A',
            'address' => '123 Đỗ Xuân Hợp, TP. Thủ Đức',
            'total_floors' => 3,
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'empty',
            'price' => 3500000,
            'area' => 25,
        ]);
    }

    public function test_landlord_can_update_room_status_to_cleaning_in_realtime(): void
    {
        Event::fake([RoomStatusUpdated::class]);

        $response = $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'cleaning',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('room.status', 'cleaning')
            ->assertJsonPath('room.status_label', 'Cần dọn dẹp');

        $this->assertDatabaseHas('rooms', [
            'id' => $this->room->id,
            'status' => 'cleaning',
        ]);

        Event::assertDispatched(RoomStatusUpdated::class, function ($event) {
            return $event->room->id === $this->room->id && $event->room->status === 'cleaning';
        });
    }

    public function test_landlord_cannot_set_occupied_room_to_empty_without_checkout(): void
    {
        // Gán phòng sang occupied và tạo cư dân
        $this->room->update(['status' => 'occupied']);

        Resident::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'name' => 'Trần Văn B',
            'phone' => '0988776655',
            'status' => 'active',
            'start_date' => now()->subDays(10),
        ]);

        $response = $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'empty',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('rooms', [
            'id' => $this->room->id,
            'status' => 'occupied',
        ]);
    }

    public function test_err_13_02_cannot_switch_room_to_maintenance_when_occupied_or_has_active_contract(): void
    {
        // 1. Kiểm tra phòng occupied
        $this->room->update(['status' => 'occupied']);

        $response = $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'maintenance',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_13_02')
            ->assertJsonPath('message', 'Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.');

        $this->assertDatabaseHas('rooms', [
            'id' => $this->room->id,
            'status' => 'occupied',
        ]);

        // 2. Kiểm tra phòng có cư dân active
        $this->room->update(['status' => 'empty']);
        Resident::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'name' => 'Nguyễn Văn C',
            'phone' => '0912345678',
            'status' => 'active',
            'start_date' => now(),
        ]);

        $responseResident = $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'maintenance',
            ]);

        $responseResident->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_13_02')
            ->assertJsonPath('message', 'Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.');
    }

    public function test_err_13_01_view_contains_sse_reconnection_warning_badge(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin', ['tab' => 'room-map-section']));

        $response->assertOk()
            ->assertSee('id="sse-reconnect-badge"', false)
            ->assertSee('Mất kết nối dữ liệu thời gian thực. Hệ thống đang tự động kết nối lại...', false);
    }

    public function test_err_13_02_view_contains_conflict_warning_modal(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin', ['tab' => 'room-map-section']));

        $response->assertOk()
            ->assertSee('id="modal-conflict-warning"', false)
            ->assertSee('Cảnh Báo Xung Đột Nghiệp Vụ', false)
            ->assertSee('Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.', false);
    }

    public function test_err_13_03_view_contains_floor_filter_and_empty_state(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin', ['tab' => 'room-map-section']));

        $response->assertOk()
            ->assertSee('floor-filter-btn', false)
            ->assertSee('id="floor-empty-state"', false)
            ->assertSee('Tầng được chọn chưa có phòng nào được tạo.', false);
    }

    public function test_matrix_alias_route_redirects_to_room_map_section(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get('/smartroom/admin/matrix');

        $response->assertRedirect(route('smartroom.admin', ['tab' => 'room-map-section']));
    }
}
