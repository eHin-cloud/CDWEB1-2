<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test Suite: TÍNH NĂNG 6 - TRANG CHI TIẾT PHÒNG TRỌ (FEAT_06_ROOM_DETAIL)
 * Thành viên phụ trách: Nguyễn Thanh Hiền
 * Spec: BaoCao/NhomC_baocao_2026102.docx (Table 52 -> Table 57)
 */
class RoomDetailFeature06Test extends TestCase
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
            'price' => 3500000,
            'area' => 28,
            'status' => 'empty',
        ], $attrs));
    }

    /**
     * Kịch bản 1: Truy cập với ID phòng hợp lệ (Table 57 - DoD STT 1)
     * URL: GET /renty/room/{id}
     * Kiểm tra hiển thị đầy đủ thông số phòng, ảnh gallery, giá niêm yết, thông tin chủ trọ, action buttons.
     */
    public function test_room_detail_page_renders_successfully_with_valid_id(): void
    {
        $tenant = Tenant::create([
            'name' => 'Chung cư mini Cầu Giấy',
            'email' => 'landlord_cg@example.com',
            'phone' => '0988000001',
            'listing_badge' => 'verified',
            'boost_score' => 90,
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh Cầu Giấy',
            'address' => 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
        ]);

        $room = $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '402',
            'price' => 3800000,
            'area' => 30,
            'status' => 'empty',
        ]);

        $response = $this->get('/renty/room/' . $room->id);
        $response->assertStatus(200);

        // Kiểm tra UI Elements theo Table 56
        // STT 1: img_slider
        $response->assertSee('img_slider');

        // STT 2: lbl_price
        $response->assertSee('lbl_price');
        $response->assertSee('3.800.000 đ/tháng');

        // STT 3: landlord_info
        $response->assertSee('landlord_info');
        $response->assertSee('Chung cư mini Cầu Giấy');

        // STT 4: btnBooking & Action buttons
        $response->assertSee('btnBooking');
        $response->assertSee('Hẹn xem phòng / Liên hệ');

        // Nút sao chép liên kết (ERR_06_04)
        $response->assertSee('copyRoomLink');
        $response->assertSee('Chia sẻ phòng');
    }

    /**
     * Kịch bản 2: Truy cập với ID phòng không tồn tại (Table 57 - DoD STT 2 & ERR_06_01)
     * Trả về redirect về /renty kèm flash error hoặc JSON 404
     */
    public function test_room_detail_page_handles_non_existent_id(): void
    {
        // 1. Web request: redirect về /renty kèm message lỗi
        $response = $this->get('/renty/room/99999');
        $response->assertRedirect('/renty');
        $response->assertSessionHas('error', 'Phòng trọ này không tồn tại hoặc đã được gỡ bỏ khỏi hệ thống.');

        // 2. Web request với ID không phải số
        $responseInvalid = $this->get('/renty/room/abc_invalid');
        $responseInvalid->assertRedirect('/renty');
        $responseInvalid->assertSessionHas('error', 'Phòng trọ này không tồn tại hoặc đã được gỡ bỏ khỏi hệ thống.');

        // 3. API request: trả về HTTP 404 JSON
        $responseApi = $this->getJson('/renty/room/99999');
        $responseApi->assertStatus(404);
        $responseApi->assertJson([
            'success' => false,
            'error_code' => 'ERR_06_01',
            'message' => 'Phòng trọ này không tồn tại hoặc đã được gỡ bỏ khỏi hệ thống.',
        ]);
    }

    /**
     * Kịch bản 3: Phòng đã có người thuê (ERR_06_02)
     * Kiểm tra hiển thị nhãn ĐÃ THUÊ, vô hiệu hóa nút đặt phòng
     */
    public function test_room_detail_displays_occupied_badge_and_disables_booking(): void
    {
        $tenant = Tenant::create([
            'name' => 'Chủ trọ Hoàng Mai',
            'email' => 'landlord_hm@example.com',
            'phone' => '0988111222',
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà B',
            'address' => 'Hoàng Mai, Hà Nội',
        ]);

        $room = $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '501',
            'status' => 'occupied', // Đã thuê
        ]);

        $response = $this->get('/renty/room/' . $room->id);
        $response->assertStatus(200);

        // Hiển thị nhãn ĐÃ THUÊ màu xám
        $response->assertSee('ĐÃ THUÊ');
        $response->assertSee('Phòng lưu trú này hiện đã có người thuê, tạm ngừng nhận đăng ký.');
        $response->assertSee('disabled');
    }

    /**
     * Kịch bản 4: API Reviews của phòng trọ (Table 53: GET /api/renty/room/{id}/reviews)
     */
    public function test_api_room_reviews_returns_success_and_handles_404(): void
    {
        $tenant = Tenant::create(['name' => 'Chủ trọ C', 'email' => 'tenant_c@example.com']);
        $building = Building::create(['tenant_id' => $tenant->id, 'name' => 'Tòa C', 'address' => 'Hà Nội']);
        $room = $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '303',
        ]);

        Review::create([
            'room_id' => $room->id,
            'author_name' => 'Nguyễn Văn Test',
            'rating' => 5,
            'comment' => 'Phòng rất sạch đẹp, thoáng mát và chủ nhà thân thiện!',
        ]);

        // 1. Gọi endpoint theo spec: /api/renty/room/{id}/reviews
        $response = $this->getJson('/api/renty/room/' . $room->id . '/reviews');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'room_number' => '303',
            'rating_avg' => 5.0,
            'reviews_count' => 1,
        ]);

        // 2. Kiểm tra gọi phòng không tồn tại -> 404 kèm ERR_06_01
        $response404 = $this->getJson('/api/renty/room/99999/reviews');
        $response404->assertStatus(404);
        $response404->assertJson([
            'success' => false,
            'error_code' => 'ERR_06_01',
        ]);
    }
}
