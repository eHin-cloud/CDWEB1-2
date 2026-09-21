<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomManagementCrudTest extends TestCase
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

    private function setupTenantAdminAndBuilding(): array
    {
        $tenant = Tenant::create([
            'name' => 'Công Ty Test Thuê Phòng',
            'email' => 'landlord-crud@example.com',
        ]);

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Admin',
            'username' => 'landlord_admin',
            'email' => 'landlord@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Diamond',
            'address' => '123 Đường Số 1, Quận 9, TP.HCM',
        ]);

        return [$admin, $tenant, $building];
    }

    public function test_can_create_room_with_full_details_and_new_specifications(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();
        Storage::fake('public');

        $image = UploadedFile::fake()->create('room1.webp', 2000, 'image/webp'); // 2MB <= 5MB
        $video = UploadedFile::fake()->create('tour.mp4', 15000, 'video/mp4'); // 15MB <= 30MB

        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.101',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'deluxe',
            'rental_type' => 'month',
            'price' => 4500000,
            'deposit' => 2000000,
            'area' => 28,
            'amenities' => ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock'],
            'description' => 'Phòng Deluxe sang trọng, đầy đủ tiện nghi SmartLock và Minibar.',
            'images' => [$image],
            'video' => $video,
        ]);

        $response->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'P.101',
            'floor' => 1,
            'room_type' => 'deluxe',
            'rental_type' => 'month',
            'price' => 4500000,
            'deposit' => 2000000,
            'area' => 28,
        ]);

        $room = Room::where('room_number', 'P.101')->first();
        $this->assertNotNull($room);
        $this->assertContains('Máy lạnh', $room->amenities);
        $this->assertContains('SmartLock', $room->amenities);
        $this->assertNotNull($room->video);
    }

    public function test_validation_fails_when_price_or_area_is_not_positive(): void
    {
        [$admin, , $building] = $this->setupTenantAdminAndBuilding();

        // Thử giá = 0
        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.102',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'standard',
            'rental_type' => 'day',
            'price' => 0, // Không được phép <= 0
            'deposit' => 0,
            'area' => 25,
        ]);
        $response->assertSessionHasErrors('price');

        // Thử diện tích = 0
        $response2 = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.103',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'standard',
            'rental_type' => 'day',
            'price' => 300000,
            'deposit' => 0,
            'area' => 0, // Không được phép <= 0
        ]);
        $response2->assertSessionHasErrors('area');
    }

    public function test_prevents_duplicate_room_number_in_same_building(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();

        // Tạo phòng P.201 đầu tiên
        Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'P.201',
            'floor' => 2,
            'status' => 'empty',
            'room_type' => 'studio',
            'rental_type' => 'month',
            'price' => 5000000,
            'deposit' => 5000000,
            'area' => 32,
            'version' => 1,
        ]);

        // Thử tạo trùng phòng P.201 trong cùng tòa nhà
        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.201',
            'floor' => 2,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 5500000,
            'deposit' => 5500000,
            'area' => 35,
        ]);

        $response->assertSessionHasErrors('room_number');
    }

    public function test_blocks_invalid_file_formats_and_excessive_sizes(): void
    {
        [$admin, , $building] = $this->setupTenantAdminAndBuilding();
        Storage::fake('public');

        // 1. Chặn file lạ đuôi .exe hoặc .pdf khi upload ảnh
        $exeFile = UploadedFile::fake()->create('malware.exe', 100);
        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.301',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'images' => [$exeFile],
        ]);
        $response->assertSessionHasErrors('images.0');

        $pdfFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        $responsePdf = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.302',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'image' => $pdfFile,
        ]);
        $responsePdf->assertSessionHasErrors('image');

        // 2. Chặn ảnh vượt quá 5MB (5120KB)
        $largeImage = UploadedFile::fake()->create('huge.jpg', 6000, 'image/jpeg'); // 6MB > 5MB
        $responseLargeImage = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.303',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'images' => [$largeImage],
        ]);
        $responseLargeImage->assertSessionHasErrors('images.0');

        // 3. Chặn video vượt quá 30MB (30720KB)
        $largeVideo = UploadedFile::fake()->create('heavy.mp4', 35000, 'video/mp4'); // 35MB > 30MB
        $responseLargeVideo = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'building_id' => $building->id,
            'room_number' => 'P.304',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'video' => $largeVideo,
        ]);
        $responseLargeVideo->assertSessionHasErrors('video');
    }

    public function test_can_update_and_delete_room_successfully(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();

        $room = Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'P.401',
            'floor' => 4,
            'status' => 'empty',
            'room_type' => 'standard',
            'rental_type' => 'month',
            'price' => 3000000,
            'deposit' => 1500000,
            'area' => 22,
            'version' => 1,
        ]);

        // Cập nhật lên hạng VIP, đổi hình thức sang theo ngày
        $response = $this->actingAs($admin)->post(route('admin.rooms.update', $room->id), [
            'version' => 1,
            'building_id' => $building->id,
            'room_number' => 'P.401-VIP',
            'floor' => 4,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'day',
            'price' => 500000,
            'deposit' => 200000,
            'area' => 25,
            'amenities' => ['SmartLock', 'Minibar'],
        ]);

        $response->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'room_number' => 'P.401-VIP',
            'room_type' => 'vip',
            'rental_type' => 'day',
            'price' => 500000,
            'deposit' => 200000,
        ]);

        // Xóa phòng
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room->id));
        $deleteResponse->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);
    }

    public function test_ai_generate_room_description_validates_required_fields(): void
    {
        [$admin] = $this->setupTenantAdminAndBuilding();

        // Gửi thiếu price và area
        $response = $this->actingAs($admin)
            ->postJson(route('admin.rooms.description.ai'), [
                'room_number' => 'P.101',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price', 'area']);
    }

    public function test_ai_generate_room_description_returns_description_successfully(): void
    {
        [$admin] = $this->setupTenantAdminAndBuilding();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.rooms.description.ai'), [
                'room_number' => 'P.205',
                'floor' => 2,
                'room_type' => 'deluxe',
                'rental_type' => 'month',
                'price' => 4500000,
                'deposit' => 2000000,
                'area' => 28,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Ban công', 'Tủ lạnh'],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'description' => [
                    'description',
                ],
            ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['description']['description']);
    }
}
