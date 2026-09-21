<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TicketManagementTest extends TestCase
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

    private function createTenant(string $name = 'Ký Túc Xá Test'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'tenant_' . uniqid() . '@example.test',
            'domain' => 'tenant-' . uniqid(),
        ]);
    }

    private function createRoom(Tenant $tenant, string $roomNumber = '101', array $attributes = []): Room
    {
        $building = Building::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa nhà A',
        ], [
            'address' => '123 Đường Test, Hà Nội',
        ]);

        return Room::create(array_merge([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 1,
            'price' => 3500000,
            'status' => 'occupied',
        ], $attributes));
    }

    public function test_resident_can_submit_ticket_with_specific_location()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '999', ['floor' => 9]);

        $user = User::create([
            'name' => 'Nguyễn Cư Dân Test',
            'username' => 'resident_test_' . uniqid(),
            'email' => 'resident_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Nguyễn Cư Dân Test',
            'phone' => '0912345678',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('smartroom.resident.tickets.store'), [
            'title' => 'Vòi nước bị rỉ nước liên tục',
            'category' => 'water',
            'specific_location' => 'Bồn rửa mặt trong nhà vệ sinh',
            'description' => 'Vòi nước nóng lạnh bị rò rỉ nước ở chân van, cần kiểm tra gấp.',
        ]);

        $response->assertRedirect(route('smartroom.resident') . '?tab=tickets');

        $this->assertDatabaseHas('tickets', [
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Vòi nước bị rỉ nước liên tục',
            'category' => 'water',
            'specific_location' => 'Bồn rửa mặt trong nhà vệ sinh',
            'status' => 'pending',
        ]);
    }

    public function test_resident_portal_displays_room_and_specific_location()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '888', ['floor' => 8]);

        $user = User::create([
            'name' => 'Trần Văn Test',
            'username' => 'resident_test_' . uniqid(),
            'email' => 'resident_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Trần Văn Test',
            'phone' => '0987654321',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Hỏng ổ cắm điện',
            'category' => 'electric',
            'specific_location' => 'Gần bàn học ban công',
            'description' => 'Cắm sạc laptop bị tóe lửa',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('smartroom.resident') . '?tab=tickets');
        $response->assertStatus(200);
        $response->assertSee('Gần bàn học ban công');
        $response->assertSee('P.' . $room->room_number);
    }

    public function test_admin_can_update_ticket_status_and_technician()
    {
        $tenant = $this->createTenant();
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Test',
            'username' => 'landlord_admin_' . uniqid(),
            'email' => 'landlord_' . uniqid() . '@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $room = $this->createRoom($tenant, '777', ['floor' => 7]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Khách thuê 777',
            'phone' => '0911223344',
            'email' => 'khach777@test.com',
            'start_date' => now(),
            'status' => 'active',
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Máy lạnh không lạnh',
            'category' => 'maintenance',
            'specific_location' => 'Phòng ngủ chính',
            'description' => 'Máy lạnh phả ra gió nóng',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('smartroom.admin.ticket.update', $ticket->id), [
            'status' => 'processing',
            'assigned_to' => 'Thợ Tuấn Điện Lạnh',
        ]);

        $response->assertRedirect(route('smartroom.admin') . '?tab=ticket-section');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'processing',
            'assigned_to' => 'Thợ Tuấn Điện Lạnh',
        ]);
    }

    public function test_resident_can_submit_housekeeping_ticket()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '102');

        $user = User::create([
            'name' => 'Lê Khách Thuê',
            'username' => 'resident_hk_' . uniqid(),
            'email' => 'resident_hk_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Lê Khách Thuê',
            'phone' => '0933445566',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('smartroom.resident.tickets.store'), [
            'title' => 'Yêu cầu dọn vệ sinh buồng phòng',
            'category' => 'housekeeping',
            'specific_location' => 'Toàn bộ phòng và toilet',
            'description' => 'Cần dọn vệ sinh phòng vào sáng thứ 7 lúc 9h, giặt rèm và cọ rửa toilet.',
        ]);

        $response->assertRedirect(route('smartroom.resident') . '?tab=tickets');

        $this->assertDatabaseHas('tickets', [
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'category' => 'housekeeping',
            'title' => 'Yêu cầu dọn vệ sinh buồng phòng',
        ]);
    }

    public function test_ticket_submission_fails_when_description_is_empty()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '103');

        $user = User::create([
            'name' => 'Trần Văn Test Lỗi',
            'username' => 'resident_err_' . uniqid(),
            'email' => 'resident_err_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Trần Văn Test Lỗi',
            'phone' => '0944556677',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->from(route('smartroom.resident'))->post(route('smartroom.resident.tickets.store'), [
            'title' => 'Cháy bóng đèn ban công',
            'category' => 'electric',
            'specific_location' => 'Ban công',
            'description' => '', // Trống mô tả
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_ticket_submission_fails_when_image_exceeds_10mb()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '104');

        $user = User::create([
            'name' => 'Trần Văn Test Ảnh',
            'username' => 'resident_img_' . uniqid(),
            'email' => 'resident_img_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Trần Văn Test Ảnh',
            'phone' => '0955667788',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        // Tạo fake image vượt quá 10MB (11MB = 11264KB)
        $file = UploadedFile::fake()->create('large_image.jpg', 11264, 'image/jpeg');

        $response = $this->actingAs($user)->from(route('smartroom.resident'))->post(route('smartroom.resident.tickets.store'), [
            'title' => 'Chập điện cầu chì',
            'category' => 'electric',
            'description' => 'Cầu chì bị cháy đen sau khi bật máy lạnh',
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_admin_can_poll_new_tickets_in_realtime()
    {
        $tenant = $this->createTenant();
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Polling Test',
            'username' => 'landlord_poll_' . uniqid(),
            'email' => 'landlord_poll_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
        ]);

        $room = $this->createRoom($tenant, '505', ['floor' => 5]);
        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Lê Khắc Tiệp',
            'phone' => '0933445566',
            'email' => 'tiep_' . uniqid() . '@example.com',
            'start_date' => now(),
            'status' => 'active',
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Vòi nước bồn rửa mặt bị gãy ren',
            'category' => 'water',
            'specific_location' => 'Nhà tắm phòng 505',
            'description' => 'Nước xịt mạnh ra sàn',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->getJson(route('smartroom.admin.tickets.poll', ['last_id' => 0]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'has_new' => true,
        ]);
        $response->assertJsonPath('tickets.0.id', $ticket->id);
        $response->assertJsonPath('tickets.0.title', 'Vòi nước bồn rửa mặt bị gãy ren');
        $response->assertJsonPath('tickets.0.specific_location', 'Nhà tắm phòng 505');
        $response->assertJsonPath('tickets.0.room_number', '505');
        $response->assertJsonPath('tickets.0.resident_name', 'Lê Khắc Tiệp');
        $response->assertJsonPath('stats.pending', 1);
    }
}
