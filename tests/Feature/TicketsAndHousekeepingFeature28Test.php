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

class TicketsAndHousekeepingFeature28Test extends TestCase
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

    private function createTenant(string $name = 'Ký Túc Xá FEAT 28'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'tenant_' . uniqid() . '@example.test',
            'domain' => 'tenant-' . uniqid(),
        ]);
    }

    private function createRoom(Tenant $tenant, string $roomNumber = '301', array $attributes = []): Room
    {
        $building = Building::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
        ], [
            'address' => '456 Đường Cầu Giấy, Hà Nội',
        ]);

        return Room::create(array_merge([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 3,
            'price' => 4500000,
            'status' => 'occupied',
            'cleaning_status' => 'clean',
        ], $attributes));
    }

    private function createResidentUser(Tenant $tenant, Room $room): array
    {
        $residentRole = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);

        $user = User::create([
            'name' => 'Huỳnh Văn Vĩnh Em Resident',
            'username' => 'vinhem_' . uniqid(),
            'email' => 'vinhem_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $residentRole->id,
            'role' => 'user',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => 'Huỳnh Văn Vĩnh Em Resident',
            'phone' => '0988776655',
            'email' => $user->email,
            'start_date' => now(),
            'status' => 'active',
        ]);

        return [$user, $resident];
    }

    private function createLandlordUser(Tenant $tenant): User
    {
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        return User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Trần Văn Hoàng (Chủ trọ)',
            'username' => 'landlord_hoang_' . uniqid(),
            'email' => 'hoang_' . uniqid() . '@example.com',
            'role' => 'landlord',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * DoD STT 1: Để trống ô tiêu đề và bấm Gửi phiếu
     * Kết quả mong đợi: Báo lỗi 'Vui lòng nhập tiêu đề sự cố cần sửa chữa'
     */
    public function test_dod_1_fails_when_title_is_empty()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '301');
        [$user, $resident] = $this->createResidentUser($tenant, $room);

        $response = $this->actingAs($user)
            ->from(route('smartroom.resident'))
            ->post(route('smartroom.resident.tickets.store'), [
                'title' => '',
                'category' => 'electric',
                'description' => 'Mô tả sự cố chi tiết vượt quá 10 ký tự',
                'urgency' => 'normal',
            ]);

        $response->assertSessionHasErrors(['title']);
        $errors = session('errors')->get('title');
        $this->assertContains('Vui lòng nhập tiêu đề sự cố cần sửa chữa', $errors);
    }

    /**
     * DoD STT 2: Nhập mô tả 4 ký tự: 'Hỏng'
     * Kết quả mong đợi: Báo lỗi 'Mô tả sự cố phải có ít nhất 10 ký tự'
     */
    public function test_dod_2_fails_when_description_under_10_characters()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302');
        [$user, $resident] = $this->createResidentUser($tenant, $room);

        $response = $this->actingAs($user)
            ->from(route('smartroom.resident'))
            ->post(route('smartroom.resident.tickets.store'), [
                'title' => 'Máy lạnh hỏng không mát',
                'category' => 'electric',
                'description' => 'Hỏng', // 4 ký tự
                'urgency' => 'normal',
            ]);

        $response->assertSessionHasErrors(['description']);
        $errors = session('errors')->get('description');
        $this->assertContains('Mô tả sự cố phải có ít nhất 10 ký tự', $errors);
    }

    /**
     * DoD STT 3: Nhập đầy đủ thông tin và đính kèm ảnh
     * Kết quả mong đợi: Tạo ticket thành công, trạng thái 'pending' (Chờ tiếp nhận), trả về ERR_28_03
     */
    public function test_dod_3_happy_case_creates_ticket_pending_with_image_and_urgency()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '303');
        [$user, $resident] = $this->createResidentUser($tenant, $room);

        $fakeImage = UploadedFile::fake()->create('su_co_ong_nuoc.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($user)
            ->from(route('smartroom.resident'))
            ->post(route('smartroom.resident.tickets.store'), [
                'title' => 'Máy lạnh chảy nước ở dàn lạnh',
                'category' => 'water',
                'urgency' => 'urgent',
                'specific_location' => 'Phòng ngủ góc trái',
                'description' => 'Nước chảy tong tỏng ướt hết sàn gỗ từ trưa nay',
                'image' => $fakeImage,
            ]);

        $response->assertRedirect(route('smartroom.resident') . '?tab=tickets');
        $response->assertSessionHas('success', 'Đã gửi yêu cầu sửa chữa sự cố thành công! Kỹ thuật viên sẽ xử lý sớm nhất.');

        $this->assertDatabaseHas('tickets', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Máy lạnh chảy nước ở dàn lạnh',
            'category' => 'water',
            'urgency' => 'urgent',
            'status' => 'pending', // Chờ tiếp nhận
        ]);
    }

    /**
     * DoD STT 4 & ERR_28_04: Nhân viên buồng phòng bấm dọn xong
     * Kết quả mong đợi: Phòng chuyển sang Clean, trả về ERR_28_04: 'Phòng đã chuyển sang trạng thái Đã sạch (Clean), sẵn sàng đón khách.'
     */
    public function test_dod_4_housekeeping_marks_room_clean_with_err_28_04()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '204', [
            'status' => 'cleaning',
            'cleaning_status' => 'cleaning',
        ]);

        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)
            ->post(route('admin.housekeeping.update', $room->id), [
                'cleaning_status' => 'clean',
            ]);

        $response->assertSessionHas('success', 'Phòng đã chuyển sang trạng thái Đã sạch (Clean), sẵn sàng đón khách.');

        $room->refresh();
        $this->assertEquals('clean', $room->cleaning_status);
        $this->assertEquals('empty', $room->status);
    }

    /**
     * Kiểm tra API scope của Admin Tickets:
     * GET /smartroom/admin/tickets
     * POST /smartroom/admin/tickets/{id}/status
     */
    public function test_admin_tickets_index_and_status_endpoints()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '105');
        [$resUser, $resident] = $this->createResidentUser($tenant, $room);
        $landlord = $this->createLandlordUser($tenant);

        $ticket = Ticket::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'title' => 'Hỏng khóa cửa thông minh',
            'category' => 'lock',
            'urgency' => 'emergency',
            'description' => 'Không quẹt được thẻ từ để mở cửa vào phòng',
            'status' => 'pending',
        ]);

        // 1. GET /smartroom/admin/tickets
        $getIndex = $this->actingAs($landlord)->get('/smartroom/admin/tickets');
        $getIndex->assertStatus(200);
        $getIndex->assertSee('Hỏng khóa cửa thông minh');

        // 2. POST /smartroom/admin/tickets/{id}/status
        $postStatus = $this->actingAs($landlord)->post("/smartroom/admin/tickets/{$ticket->id}/status", [
            'status' => 'processing',
            'assigned_to' => 'Thợ Khóa Cấp Tốc',
        ]);

        $postStatus->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals('processing', $ticket->status);
        $this->assertEquals('Thợ Khóa Cấp Tốc', $ticket->assigned_to);
    }
}
