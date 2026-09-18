<?php

namespace Tests\Feature;

use App\Events\RoomStatusUpdated;
use App\Events\TicketCreated;
use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReverbRealtimeBroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected User $landlord;
    protected Tenant $tenant;
    protected Building $building;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $roleLandlord = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Chủ trọ']
        );

        $this->tenant = Tenant::create([
            'name' => 'Ký Túc Xá Realtime',
            'email' => 'realtime@smartroom.vn',
            'phone' => '0912345678',
            'address' => '123 Đường Công Nghệ, Q.1',
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Reverb',
            'address' => '123 Đường Công Nghệ, Q.1',
        ]);

        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $roleLandlord->id,
            'name' => 'Nguyễn Anh Quý',
            'username' => 'anhquy_admin',
            'email' => 'anhquy@smartroom.vn',
            'password' => bcrypt('password123'),
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '305',
            'floor' => 3,
            'price' => 3200000,
            'status' => 'empty',
            'max_occupants' => 2,
        ]);
    }

    public function test_room_status_update_dispatches_room_status_updated_event(): void
    {
        Event::fake([RoomStatusUpdated::class]);

        $response = $this->actingAs($this->landlord)
            ->postJson("/smartroom/admin/rooms/{$this->room->id}/quick-status", [
                'status' => 'cleaning',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'room' => [
                    'id' => $this->room->id,
                    'status' => 'cleaning',
                ],
            ]);

        Event::assertDispatched(RoomStatusUpdated::class, function ($event) {
            return $event->room->id === $this->room->id
                && $event->roomData['status'] === 'cleaning'
                && in_array('tenant.' . $this->tenant->id . '.room-matrix', array_map(fn($c) => $c->name, $event->broadcastOn()));
        });
    }

    public function test_resident_ticket_submission_dispatches_ticket_created_event(): void
    {
        Event::fake([TicketCreated::class]);

        $roleResident = Role::firstOrCreate(
            ['slug' => 'resident'],
            ['name' => 'Cư dân']
        );

        $residentUser = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $roleResident->id,
            'name' => 'Trần Văn Bình',
            'username' => 'binhtran_resident',
            'email' => 'binh@resident.local',
            'phone' => '0988776655',
            'password' => bcrypt('password123'),
        ]);

        $resident = Resident::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $residentUser->id,
            'room_id' => $this->room->id,
            'name' => 'Trần Văn Bình',
            'phone' => '0988776655',
            'status' => 'active',
            'start_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($residentUser)
            ->post(route('smartroom.resident.tickets.store'), [
                'title' => 'Vòi nước bị rò rỉ',
                'description' => 'Vòi nước trong nhà vệ sinh bị rỉ nước liên tục từ tối qua.',
                'category' => 'water',
            ]);

        $response->assertRedirect(route('smartroom.resident', ['tab' => 'tickets']));

        Event::assertDispatched(TicketCreated::class, function ($event) {
            return $event->ticket->title === 'Vòi nước bị rò rỉ'
                && $event->ticketData['room_number'] === '305'
                && in_array('tenant.' . $this->tenant->id . '.dashboard', array_map(fn($c) => $c->name, $event->broadcastOn()));
        });
    }
}
