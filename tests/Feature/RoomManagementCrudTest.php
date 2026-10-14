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
            'verification_status' => 'kyc_verified',
        ]);

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Admin',
            'username' => 'landlord_admin',
            'email' => 'landlord@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);

        \App\Models\LandlordProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'full_name' => 'Chủ Trọ Admin',
            'phone' => '0912345678',
            'property_name' => 'Tòa Nhà Diamond',
            'property_address' => '123 Đường Số 1, Quận 9, TP.HCM',
            'national_id' => '079200001234',
            'business_license' => 'GPKD123456',
            'status' => 'approved',
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

    /**
     * DoD 1: Nhập min_price = 5,000,000 và max_price = 2,000,000 rồi bấm Lọc -> Báo lỗi khoảng giá không hợp lệ
     */
    public function test_dod_1_price_range_validation_when_min_price_exceeds_max_price(): void
    {
        [$admin] = $this->setupTenantAdminAndBuilding();

        $response = $this->actingAs($admin)->get(route('admin.rooms.index', [
            'min_price' => 5000000,
            'max_price' => 2000000,
        ]));

        $response->assertStatus(200);
        $response->assertSessionHas('error', 'Khoảng giá lọc không hợp lệ: Giá tối thiểu phải nhỏ hơn hoặc bằng giá tối đa');
    }

    /**
     * DoD 2: Nhập từ khóa tìm kiếm 'Phòng 201' vào ô txtSearch -> Bảng chỉ hiển thị phòng có số/tên chứa '201'
     */
    public function test_dod_2_search_rooms_by_txt_search_keyword(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();

        $room201 = Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'Phòng 201',
            'floor' => 2,
            'status' => 'empty',
            'room_type' => 'standard',
            'price' => 3500000,
            'deposit' => 1000000,
            'area' => 20,
        ]);

        $room305 = Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'Phòng 305',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'standard',
            'price' => 4000000,
            'deposit' => 1500000,
            'area' => 22,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.rooms.index', [
            'txtSearch' => 'Phòng 201',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Phòng 201');
        $response->assertDontSee('Phòng 305');
    }

    /**
     * Kiểm tra AJAX check-number: Trả về exists = true khi trùng số phòng trong cùng tòa nhà (ERR_03_02)
     */
    public function test_ajax_check_room_number_duplicate(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();

        Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'standard',
            'price' => 3000000,
            'deposit' => 1000000,
            'area' => 18,
        ]);

        // Trùng số phòng 101 trong cùng tòa nhà
        $responseDup = $this->actingAs($admin)->getJson(route('admin.rooms.check_number', [
            'building_id' => $building->id,
            'room_number' => '101',
        ]));
        $responseDup->assertStatus(200)
            ->assertJson([
                'exists' => true,
                'message' => 'Số phòng này đã tồn tại trong cơ sở lưu trú, vui lòng chọn số khác.',
            ]);

        // Không trùng với số phòng 102
        $responseUnique = $this->actingAs($admin)->getJson(route('admin.rooms.check_number', [
            'building_id' => $building->id,
            'room_number' => '102',
        ]));
        $responseUnique->assertStatus(200)
            ->assertJson(['exists' => false]);
    }

    public function test_landlord_with_premium_verified_can_access_create_room(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khu Trọ Premium',
            'email' => 'premium@example.com',
            'verification_status' => 'premium_verified',
            'listing_badge' => 'premium_verified',
        ]);
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Tích Xanh',
            'username' => 'chutro_premium',
            'email' => 'chutro_premium@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);
        \App\Models\LandlordProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'full_name' => 'Chủ Trọ Tích Xanh',
            'phone' => '0912345678',
            'property_name' => 'Khu Trọ Premium',
            'property_address' => 'Hà Nội',
            'status' => 'verified',
            'verification_status' => 'kyc_verified',
            'national_id' => '001203040506',
            'business_license' => 'DKKD-998811',
        ]);
        Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà X',
            'address' => 'Hà Nội',
        ]);

        $response = $this->actingAs($user)->get(route('admin.rooms.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.rooms.create');
    }

    public function test_unverified_landlord_is_redirected_to_profile_section(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khu Trọ Mới',
            'email' => 'unverified@example.com',
            'verification_status' => 'unverified',
        ]);
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Chưa Duyệt',
            'username' => 'chutro_unverified',
            'email' => 'chutro_unverified@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);
        \App\Models\LandlordProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'full_name' => 'Chủ Trọ Chưa Duyệt',
            'phone' => '0987654321',
            'property_name' => 'Khu Trọ Mới',
            'property_address' => 'Hà Nội',
            'status' => 'pending',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('admin.rooms.create'));
        $response->assertRedirect(route('smartroom.admin', ['tab' => 'profile-section']));
        $response->assertSessionHas('warning', 'Bạn cần hoàn thiện hồ sơ (MST/CCCD) và chờ Admin duyệt trước khi đăng tin công khai.');
    }

    public function test_landlord_without_legal_docs_is_redirected_to_profile_section_even_if_status_approved(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khu Trọ Chưa Có CCCD',
            'email' => 'nolaw@example.com',
            'verification_status' => 'kyc_verified',
        ]);
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Chủ Trọ Trống CCCD',
            'username' => 'chutro_nocccd',
            'email' => 'chutro_nocccd@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);
        \App\Models\LandlordProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'full_name' => 'Chủ Trọ Trống CCCD',
            'phone' => '0987654321',
            'property_name' => 'Khu Trọ Chưa Có CCCD',
            'property_address' => 'Hà Nội',
            'national_id' => null,
            'business_license' => null,
            'status' => 'approved',
            'verification_status' => 'kyc_verified',
        ]);

        $response = $this->actingAs($user)->get(route('admin.rooms.create'));
        $response->assertRedirect(route('smartroom.admin', ['tab' => 'profile-section']));
        $response->assertSessionHas('warning', 'Bạn cần hoàn thiện hồ sơ (MST/CCCD) và chờ Admin duyệt trước khi đăng tin công khai.');

        $storeResponse = $this->actingAs($user)->post(route('admin.rooms.store'), [
            'room_number' => 'P101',
            'price' => 3000000,
            'area' => 25,
        ]);
        $storeResponse->assertRedirect(route('smartroom.admin', ['tab' => 'profile-section']));
    }

    public function test_can_delete_individual_images_from_room(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();
        Storage::fake('public');

        $img1 = 'rooms/img1.jpg';
        $img2 = 'rooms/img2.jpg';
        Storage::disk('public')->put($img1, 'fake content 1');
        Storage::disk('public')->put($img2, 'fake content 2');

        $room = Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'P.501',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'deluxe',
            'rental_type' => 'month',
            'price' => 5000000,
            'area' => 30,
            'image' => $img1,
            'images' => [$img1, $img2],
            'version' => 1,
        ]);

        // Yêu cầu xóa $img1, chỉ giữ $img2
        $response = $this->actingAs($admin)->post(route('admin.rooms.update', $room->id), [
            'version' => 1,
            'building_id' => $building->id,
            'room_number' => 'P.501',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'deluxe',
            'rental_type' => 'month',
            'price' => 5000000,
            'area' => 30,
            'deleted_images' => [$img1],
        ]);

        $response->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('success');

        $room->refresh();
        $this->assertFalse(in_array($img1, $room->images ?? [], true));
        $this->assertTrue(in_array($img2, $room->images ?? [], true));
        $this->assertEquals($img2, $room->image);
        Storage::disk('public')->assertMissing($img1);
        Storage::disk('public')->assertExists($img2);
    }

    public function test_can_delete_and_replace_room_video(): void
    {
        [$admin, $tenant, $building] = $this->setupTenantAdminAndBuilding();
        Storage::fake('public');

        $videoPath = 'rooms/videos/sample.mp4';
        Storage::disk('public')->put($videoPath, 'fake video content');

        $room = Room::create([
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
            'room_number' => 'P.502',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'video' => $videoPath,
            'version' => 1,
        ]);

        // 1. Kiểm tra xóa video hiện tại
        $responseDel = $this->actingAs($admin)->post(route('admin.rooms.update', $room->id), [
            'version' => 1,
            'building_id' => $building->id,
            'room_number' => 'P.502',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'delete_video' => '1',
        ]);

        $responseDel->assertRedirect(route('admin.rooms.index'));
        $room->refresh();
        $this->assertNull($room->video);
        Storage::disk('public')->assertMissing($videoPath);

        // 2. Kiểm tra upload thay thế video mới
        $newVideo = UploadedFile::fake()->create('new_tour.mp4', 10000, 'video/mp4');
        $responseNew = $this->actingAs($admin)->post(route('admin.rooms.update', $room->id), [
            'version' => $room->version,
            'building_id' => $building->id,
            'room_number' => 'P.502',
            'floor' => 5,
            'status' => 'empty',
            'room_type' => 'vip',
            'rental_type' => 'month',
            'price' => 6000000,
            'area' => 35,
            'video' => $newVideo,
        ]);

        $responseNew->assertRedirect(route('admin.rooms.index'));
        $room->refresh();
        $this->assertNotNull($room->video);
        Storage::disk('public')->assertExists($room->video);
    }
}
