<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\Building;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Kiểm thử tính năng 04: Trang chủ sàn phòng trọ Renty (Landing Page)
 * Mã tính năng: FEAT_04_LANDING
 */
class LandingPageFeature04Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['slug' => 'guest'], ['name' => 'Khách Vãng Lai']);
        Role::firstOrCreate(['slug' => 'tenant'], ['name' => 'Khách Thuê']);
        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ Trọ']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    }

    protected function createRoom(array $attrs = []): Room
    {
        return Room::create(array_merge([
            'floor' => 1,
            'price' => 3000000,
            'area' => 25,
            'status' => 'empty',
        ], $attrs));
    }

    /**
     * Kịch bản 1: Truy cập URL gốc / và /renty thành công mà không yêu cầu đăng nhập (Public Access)
     * DoD STT 1: Truy cập URL gốc / -> Trang chủ tải đầy đủ banner, thanh tìm kiếm và danh sách phòng
     */
    public function test_landing_page_renders_successfully_for_guests(): void
    {
        $tenant = Tenant::create([
            'name' => 'Ký Túc Xá Đại Học Quốc Gia',
            'email' => 'ktx_vnuhcm@example.com',
            'listing_badge' => 'verified',
            'boost_score' => 95,
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh Cầu Giấy',
            'address' => 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
        ]);

        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'price' => 3500000,
        ]);

        // Kiểm tra redirect từ / về /renty
        $responseRoot = $this->get('/');
        $responseRoot->assertRedirect('/renty');

        // Kiểm tra truy cập /renty
        $response = $this->get('/renty');
        $response->assertStatus(200);

        // Kiểm tra các phần tử UI chính theo Spec
        // STT 1: Search Bar (search_box)
        $response->assertSee('search_box');
        $response->assertSee('Nhập quận, huyện, tên đường...');

        // STT 2: Card Grid (room_cards)
        $response->assertSee('room_cards');

        // STT 3: Filter Chips (tags)
        $response->assertSee('Gần ĐH Quốc Gia');
        $response->assertSee('Studio');
        $response->assertSee('Giờ tự do');

        // Bảng thông báo lỗi (ERR_04_01, ERR_04_02, ERR_04_03, ERR_04_04) - Kiểm tra hiển thị câu thông báo lỗi
        $response->assertSee('Vui lòng nhập khu vực, tên đường hoặc trường đại học cần tìm phòng.');
        $response->assertSee('Từ khóa tìm kiếm chứa ký tự không hợp lệ. Hệ thống đã tự động làm sạch.');
        $response->assertSee('Không tìm thấy phòng trọ nào khớp với từ khóa tìm kiếm của bạn.');
        $response->assertSee('Không thể nạp danh sách phòng lúc này. Vui lòng kiểm tra lại kết nối.');
    }

    /**
     * Kịch bản 2: Bấm vào thẻ phòng bất kỳ chuyển hướng chính xác đến trang Chi tiết phòng (DoD STT 2)
     */
    public function test_room_card_links_to_room_detail_page(): void
    {
        $tenant = Tenant::create(['name' => 'Chủ trọ ABC', 'email' => 'tenant_abc@example.com', 'listing_badge' => 'verified']);
        $building = Building::create(['tenant_id' => $tenant->id, 'name' => 'Tòa nhà A', 'address' => 'Hà Nội']);
        $room = $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '202',
            'price' => 4000000,
        ]);

        $response = $this->get('/renty');
        $response->assertStatus(200);

        $expectedDetailUrl = route('renty.room.show', $room->id);
        $response->assertSee($expectedDetailUrl, false);
    }

    /**
     * Kịch bản 3: Sàn Renty ưu tiên tin VIP có tích xanh KYC lên đầu trang
     */
    public function test_landing_page_prioritizes_verified_vip_rooms(): void
    {
        $tenantNormal = Tenant::create(['name' => 'Chủ trọ thường', 'email' => 'normal@example.com', 'listing_badge' => 'unverified', 'boost_score' => 10]);
        $buildingNormal = Building::create(['tenant_id' => $tenantNormal->id, 'name' => 'Tòa thường', 'address' => 'Hà Nội']);
        $roomNormal = $this->createRoom([
            'tenant_id' => $tenantNormal->id,
            'building_id' => $buildingNormal->id,
            'room_number' => 'NORMAL1',
            'price' => 3000000,
        ]);

        $tenantVip = Tenant::create(['name' => 'Chủ trọ VIP', 'email' => 'vip@example.com', 'listing_badge' => 'verified', 'boost_score' => 100]);
        $buildingVip = Building::create(['tenant_id' => $tenantVip->id, 'name' => 'Tòa VIP', 'address' => 'Hà Nội']);
        $roomVip = $this->createRoom([
            'tenant_id' => $tenantVip->id,
            'building_id' => $buildingVip->id,
            'room_number' => 'VIP999',
            'price' => 3500000,
        ]);

        $response = $this->get('/renty');
        $response->assertStatus(200);

        // Kiểm tra thứ tự hiển thị: VIP999 phải xuất hiện trước NORMAL1 trong HTML
        $content = $response->getContent();
        $posVip = strpos($content, 'VIP999');
        $posNormal = strpos($content, 'NORMAL1');

        $this->assertNotFalse($posVip);
        $this->assertNotFalse($posNormal);
        $this->assertLessThan($posNormal, $posVip, 'Phòng VIP có tích xanh phải được ưu tiên xuất hiện trước');
    }

    /**
     * Kịch bản 4: API GET /api/renty/rooms lấy danh sách phòng công khai
     */
    public function test_api_public_rooms_listing(): void
    {
        $tenant = Tenant::create(['name' => 'Ký túc xá Bách Khoa', 'email' => 'ktx_bk@example.com', 'listing_badge' => 'verified', 'boost_score' => 50]);
        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Renty House Bách Khoa',
            'address' => 'Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội',
        ]);

        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '301',
            'price' => 3200000,
        ]);

        // Phòng đã thuê occupied không nên hiển thị mặc định
        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '302',
            'price' => 3200000,
            'status' => 'occupied',
        ]);

        $response = $this->getJson('/api/renty/rooms');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
        ]);
        $response->assertJsonPath('rooms.0.room_number', '301');
        $response->assertJsonPath('rooms.0.trust_badge.is_verified', true);
    }

    /**
     * Kịch bản 5: API GET /api/renty/rooms lọc theo keyword và tự động làm sạch ký tự lạ (ERR_04_02)
     */
    public function test_api_rooms_filter_by_keyword_and_sanitize(): void
    {
        $tenant = Tenant::create(['name' => 'Chủ trọ Sư Phạm', 'email' => 'supham@example.com']);
        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Chung Cư Mini Sư Phạm',
            'address' => 'Đường Xuân Thủy, Cầu Giấy, Hà Nội',
        ]);

        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '505',
        ]);

        // 5.1: Tìm kiếm từ khóa hợp lệ
        $resNormal = $this->getJson('/api/renty/rooms?keyword=Sư+Phạm');
        $resNormal->assertStatus(200);
        $resNormal->assertJson(['success' => true, 'count' => 1]);

        // 5.2: Tìm kiếm từ khóa chứa ký tự lạ/mã độc -> Hệ thống làm sạch và cảnh báo ERR_04_02
        $resXss = $this->getJson('/api/renty/rooms?keyword=<script>alert("hack")</script>Sư+Phạm');
        $resXss->assertStatus(200);
        $resXss->assertJson([
            'success' => true,
            'error_code' => 'ERR_04_02',
        ]);

        // 5.3: Tìm kiếm từ khóa không tồn tại -> Trả về danh sách rỗng kèm mã ERR_04_03
        $resNotFound = $this->getJson('/api/renty/rooms?keyword=KhongTonTai_DiaDiem_XYZ');
        $resNotFound->assertStatus(200);
        $resNotFound->assertJson([
            'success' => true,
            'count' => 0,
            'error_code' => 'ERR_04_03',
        ]);
    }
}
