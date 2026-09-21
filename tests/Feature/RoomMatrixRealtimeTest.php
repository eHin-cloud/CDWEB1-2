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

    public function test_matrix_poll_returns_latest_room_update(): void
    {
        $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'maintenance',
            ]);

        $pollResponse = $this->actingAs($this->landlord)
            ->getJson('/smartroom/admin/rooms/matrix/poll?since=0');

        $pollResponse->assertOk()
            ->assertJsonPath('has_update', true)
            ->assertJsonPath('event.id', $this->room->id)
            ->assertJsonPath('event.status', 'maintenance')
            ->assertJsonPath('event.status_label', 'Bảo trì');
    }
}
