<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Building;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\HotelBooking;
use App\Models\HousekeepingLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HousekeepingFrontdeskFeature18Test extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Building $building;
    protected Room $room;
    protected User $receptionist;
    protected User $housekeeper;
    protected User $landlord;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['name' => 'Cơ sở Lưu trú Test FEAT 18'],
            ['address' => 'Hà Nội', 'phone' => '0981118888', 'email' => 'feat18@smartroom.test']
        );

        $this->building = Building::firstOrCreate(
            ['name' => 'Tòa Nhà Test Buồng Phòng', 'tenant_id' => $this->tenant->id],
            [
                'address' => '123 Cầu Giấy, Hà Nội',
                'property_type' => 'hotel',
                'total_floors' => 5,
            ]
        );

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => 'TEST-1801',
            'floor' => 1,
            'price' => 5000000,
            'price_per_day' => 350000,
            'price_per_hour' => 120000,
            'rental_type' => 'day',
            'room_type' => 'standard',
            'status' => 'empty',
            'cleaning_status' => 'dirty',
            'housekeeping_status' => 'dirty',
            'priority' => 'normal',
            'area' => 25,
        ]);

        $recRole = Role::firstOrCreate(['slug' => 'receptionist'], ['name' => 'Lễ tân']);
        $hkRole = Role::firstOrCreate(['slug' => 'housekeeper'], ['name' => 'Buồng phòng']);
        $llRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $this->receptionist = User::firstOrCreate(
            ['email' => 'letan_test18@smartroom.test'],
            [
                'name' => 'Nguyễn Lễ Tân 18',
                'username' => 'letan_test18',
                'phone' => '0961818181',
                'password' => bcrypt('password'),
                'tenant_id' => $this->tenant->id,
                'role_id' => $recRole->id,
                'role' => 'receptionist',
                'status' => 'active',
            ]
        );

        $this->housekeeper = User::firstOrCreate(
            ['email' => 'buongphong_test18@smartroom.test'],
            [
                'name' => 'Trần Buồng Phòng 18',
                'username' => 'buongphong_test18',
                'phone' => '0951818181',
                'password' => bcrypt('password'),
                'tenant_id' => $this->tenant->id,
                'role_id' => $hkRole->id,
                'role' => 'housekeeper',
                'status' => 'active',
            ]
        );

        $this->landlord = User::firstOrCreate(
            ['email' => 'chutro_test18@smartroom.test'],
            [
                'name' => 'Lê Chủ Trọ 18',
                'username' => 'chutro_test18',
                'phone' => '0911818181',
                'password' => bcrypt('password'),
                'tenant_id' => $this->tenant->id,
                'role_id' => $llRole->id,
                'role' => 'landlord',
                'status' => 'active',
            ]
        );
    }

    /**
     * Test 1: Sơ đồ buồng phòng thời gian thực (GET /smartroom/admin/housekeeping/matrix)
     */
    public function test_get_housekeeping_matrix_screen_and_api(): void
    {
        $response = $this->actingAs($this->receptionist)
            ->get(route('smartroom.admin.housekeeping.matrix'));

        $response->assertStatus(200);
        $response->assertSee('FEAT_18_HOUSEKEEPING_FRONTDESK');
        $response->assertSee('TEST-1801');

        // Test API JSON
        $jsonResponse = $this->actingAs($this->receptionist)
            ->getJson(route('smartroom.admin.housekeeping.matrix'));

        $jsonResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'stats' => ['total', 'dirty', 'cleaning', 'clean', 'inspected', 'out_of_service'],
                'rooms',
                'housekeepers',
            ]);
    }

    /**
     * Test 2 (DoD 1): Bấm phân công khi chưa chọn nhân viên buồng phòng -> ERR_18_03
     */
    public function test_assign_fails_without_staff_id_returns_err_18_03(): void
    {
        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => null,
                'priority' => 'urgent',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_18_03',
                'message' => 'Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.',
                'field' => 'assigned_staff_id',
            ]);
    }

    /**
     * Test 2.1: Bấm phân công khi chưa chọn phòng -> ERR_18_01
     */
    public function test_assign_fails_without_room_id_returns_err_18_01(): void
    {
        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => null,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'normal',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_18_01',
                'message' => 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.',
                'field' => 'room_id',
            ]);
    }

    /**
     * Test 2.2: Phân công hợp lệ thành công
     */
    public function test_assign_success(): void
    {
        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'urgent',
                'inspection_notes' => 'Cần dọn gấp đón khách VIP',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->room->refresh();
        $this->assertEquals($this->housekeeper->id, $this->room->assigned_staff_id);
        $this->assertEquals('urgent', $this->room->priority);
        $this->assertEquals('Cần dọn gấp đón khách VIP', $this->room->inspection_notes);
    }

    /**
     * Test 3 (DoD 2): Guard Check chặn Check-in đón khách khi phòng đang ở trạng thái Dirty -> ERR_18_02
     */
    public function test_checkin_guard_blocks_dirty_room_returns_err_18_02(): void
    {
        $this->room->update([
            'status' => 'empty',
            'housekeeping_status' => 'dirty',
            'cleaning_status' => 'dirty',
        ]);

        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.frontdesk.checkin'), [
                'room_id' => $this->room->id,
                'guest_name' => 'Trần Văn Khách Mới',
                'guest_phone' => '0912345678',
                'rental_type' => 'day',
                'deposit_amount' => 500000,
            ]);

        $expectedMessage = "Phòng {$this->room->room_number} đang ở trạng thái Cần dọn (Dirty). Không thể thực hiện Check-in đón khách!";

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_18_02',
                'message' => $expectedMessage,
                'room_number' => $this->room->room_number,
            ]);
    }

    /**
     * Test 4 (DoD 3 & FSM Lifecycle):
     * Dirty -> Bắt đầu dọn (Cleaning) -> Báo dọn xong (Clean) -> Lễ tân Nghiệm thu (Inspected - ERR_18_06)
     */
    public function test_happy_path_housekeeping_fsm_lifecycle(): void
    {
        // 1. Phòng đang dirty -> Nhân viên bấm Bắt đầu dọn (Cleaning)
        $this->room->update(['housekeeping_status' => 'dirty']);

        $res1 = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.status'), [
                'room_id' => $this->room->id,
                'housekeeping_status' => 'cleaning',
            ]);

        $res1->assertStatus(200)->assertJsonPath('success', true);
        $this->room->refresh();
        $this->assertEquals('cleaning', $this->room->housekeeping_status);

        // 2. Nhân viên dọn xong -> Bấm Báo dọn xong (Clean)
        $res2 = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.status'), [
                'room_id' => $this->room->id,
                'housekeeping_status' => 'clean',
            ]);

        $res2->assertStatus(200)->assertJsonPath('success', true);
        $this->room->refresh();
        $this->assertEquals('clean', $this->room->housekeeping_status);

        // 3. Lễ tân bấm Nghiệm thu đạt chuẩn -> Chuyển sang Inspected (ERR_18_06)
        $res3 = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.inspect'), [
                'room_id' => $this->room->id,
                'inspection_notes' => 'Kiểm tra đạt chuẩn 5 sao, đã niêm phong chìa khóa',
            ]);

        $res3->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_18_06',
                'message' => 'Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.',
            ]);

        $this->room->refresh();
        $this->assertEquals('inspected', $this->room->housekeeping_status);
        $this->assertEquals('inspected', $this->room->cleaning_status);
        $this->assertEquals($this->receptionist->id, $this->room->inspected_by);
        $this->assertNotNull($this->room->inspected_at);

        // 4. Khi phòng đã Inspected -> Check-in đón khách thành công!
        $resCheckin = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.frontdesk.checkin'), [
                'room_id' => $this->room->id,
                'guest_name' => 'Nguyễn Khách VIP',
                'rental_type' => 'day',
                'deposit_amount' => 1000000,
            ]);

        $resCheckin->assertStatus(200)->assertJsonPath('success', true);
        $this->room->refresh();
        $this->assertEquals('occupied', $this->room->status);
    }

    /**
     * Test 5: Quy tắc FSM nghiêm ngặt - Chuyển đổi trạng thái sai quy trình (nhảy cóc) -> ERR_18_04
     */
    public function test_fsm_invalid_transition_returns_err_18_04(): void
    {
        // Phòng đang ở dirty mà cố tình nghiệm thu (inspect)
        $this->room->update(['housekeeping_status' => 'dirty']);

        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.inspect'), [
                'room_id' => $this->room->id,
                'inspection_notes' => 'Cố tình nghiệm thu khi phòng còn dirty',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_18_04',
                'message' => 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).',
            ]);
    }

    /**
     * Test 6: Check-out trả phòng, đối soát minibar (ERR_18_05) và tự động chuyển phòng sang Dirty
     */
    public function test_checkout_minibar_validation_and_auto_dirty(): void
    {
        // Tạo một booking đang ở (checked_in)
        $booking = HotelBooking::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'booking_code' => 'HB-TEST18',
            'guest_name' => 'Lưu Khách Trả Phòng',
            'rental_type' => 'day',
            'check_in_at' => now()->subDay(),
            'unit_rate' => 350000,
            'status' => 'checked_in',
            'payment_status' => 'unpaid',
        ]);
        $this->room->update(['status' => 'occupied']);

        // 6.1: Nhập số lượng minibar không hợp lệ (số âm) -> ERR_18_05
        $resInvalidMinibar = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.frontdesk.checkout'), [
                'room_id' => $this->room->id,
                'minibar_items' => [
                    ['item_name' => 'Nước khoáng Lavie', 'quantity' => -2, 'unit_price' => 10000],
                ],
            ]);

        $resInvalidMinibar->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_18_05',
                'message' => 'Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0.',
            ]);

        // 6.2: Check-out hợp lệ -> tự động chuyển phòng sang Dirty
        $resValid = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.frontdesk.checkout'), [
                'room_id' => $this->room->id,
                'minibar_items' => [
                    ['item_name' => 'Nước khoáng Lavie 500ml', 'quantity' => 2, 'unit_price' => 10000],
                    ['item_name' => 'Bia Tiger lon 330ml', 'quantity' => 1, 'unit_price' => 25000],
                ],
                'payment_method' => 'vietqr',
            ]);

        $resValid->assertStatus(200)->assertJsonPath('success', true);

        // Kiểm tra phòng đã tự động chuyển sang Dirty
        $this->room->refresh();
        $this->assertEquals('dirty', $this->room->housekeeping_status);
        $this->assertEquals('dirty', $this->room->cleaning_status);
        $this->assertEquals('cleaning', $this->room->status);

        // Kiểm tra booking đã trả phòng
        $booking->refresh();
        $this->assertEquals('checked_out', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals(45000, $booking->service_amount); // 2*10000 + 1*25000 = 45000đ minibar
    }

    /**
     * Test 7: Multi-tenancy isolation - Không thể thao tác phòng thuộc cơ sở khác
     */
    public function test_multi_tenancy_isolation(): void
    {
        $otherTenant = Tenant::firstOrCreate(
            ['name' => 'Cơ sở Khác Quận 10'],
            ['address' => 'TP.HCM', 'email' => 'other_tenant@smartroom.test']
        );

        $otherRoom = Room::create([
            'tenant_id' => $otherTenant->id,
            'building_id' => $this->building->id,
            'room_number' => 'OTHER-999',
            'floor' => 1,
            'price' => 4000000,
            'status' => 'empty',
            'housekeeping_status' => 'dirty',
            'cleaning_status' => 'dirty',
            'area' => 20,
        ]);

        // Lễ tân của tenant 1 cố tình phân công hoặc cập nhật phòng của tenant 2
        $response = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $otherRoom->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'normal',
            ]);

        // Phải bị từ chối
        $this->assertTrue(in_array($response->status(), [403, 404, 422], true));
    }

    /**
     * Test 8: Đăng nhập tài khoản Buồng phòng tự động chuyển hướng tới trang Sơ đồ Buồng phòng
     */
    public function test_housekeeper_login_redirects_to_housekeeping_matrix(): void
    {
        $response = $this->post(route('user.authUser'), [
            'login' => $this->housekeeper->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('smartroom.admin.housekeeping.matrix'));
    }

    /**
     * Test 9: Tài khoản Buồng phòng có nút điều hướng "Buồng phòng" trên Header trang chủ
     */
    public function test_housekeeper_sees_housekeeping_button_in_header(): void
    {
        $response = $this->actingAs($this->housekeeper)->get(route('renty.user'));

        $response->assertStatus(200);
        $response->assertSee(route('smartroom.admin.housekeeping.matrix'), false);
        $response->assertSee('Buồng phòng');
    }

    /**
     * Test 10: Phân quyền buồng phòng - Không có quyền phân công, check-in, check-out nhưng được chọn dọn và nghiệm thu
     */
    public function test_housekeeper_has_restricted_permissions_and_can_take_dirty_room(): void
    {
        // 1. Buồng phòng không được quyền gọi API phân công (assign) -> 403
        $assignRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'normal',
            ]);
        $assignRes->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'ERR_18_07');

        // 2. Buồng phòng không được quyền gọi API Check-in -> 403
        $this->room->update(['housekeeping_status' => 'inspected', 'cleaning_status' => 'inspected']);
        $checkinRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.frontdesk.checkin'), [
                'room_id' => $this->room->id,
                'guest_name' => 'Khách Vãng Lai',
                'rental_type' => 'day',
            ]);
        $checkinRes->assertStatus(403);

        // 3. Buồng phòng không được quyền gọi API Check-out -> 403
        $checkoutRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.frontdesk.checkout'), [
                'room_id' => $this->room->id,
            ]);
        $checkoutRes->assertStatus(403);

        // 4. Buồng phòng nhìn thấy phòng dirty chưa ai nhận và bấm "Bắt đầu dọn" -> Tự động gán phân công
        $this->room->update([
            'housekeeping_status' => 'dirty',
            'cleaning_status' => 'dirty',
            'assigned_staff_id' => null,
        ]);

        $takeRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.status'), [
                'room_id' => $this->room->id,
                'housekeeping_status' => 'cleaning',
            ]);

        $takeRes->assertStatus(200)->assertJsonPath('success', true);
        $this->room->refresh();
        $this->assertEquals('cleaning', $this->room->housekeeping_status);
        $this->assertEquals($this->housekeeper->id, $this->room->assigned_staff_id);

        // 5. Buồng phòng xác nhận đã dọn xong -> clean
        $cleanRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.status'), [
                'room_id' => $this->room->id,
                'housekeeping_status' => 'clean',
            ]);
        $cleanRes->assertStatus(200);
        $this->room->refresh();
        $this->assertEquals('clean', $this->room->housekeeping_status);

        // 6. Buồng phòng nghiệm thu xác nhận hoàn tất -> inspected
        $inspectRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.inspect'), [
                'room_id' => $this->room->id,
                'inspection_notes' => 'Buồng phòng đã nghiệm thu sạch sẽ đạt chuẩn',
            ]);
        $inspectRes->assertStatus(200)->assertJsonPath('code', 'ERR_18_06');
        $this->room->refresh();
        $this->assertEquals('inspected', $this->room->housekeeping_status);
    }

    /**
     * Test Optimistic Locking: Chặn ghi đè dữ liệu khi version bị lệch (HTTP 409)
     */
    public function test_optimistic_locking_prevents_concurrent_overwrites(): void
    {
        $this->room->update([
            'housekeeping_status' => 'dirty',
            'version' => 5,
        ]);

        // Gửi version cũ (4) khi phân công
        $assignRes = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'urgent',
                'version' => 4, // Version cũ
            ]);

        $assignRes->assertStatus(409)
            ->assertJsonPath('code', 'ERR_OPTIMISTIC_LOCK')
            ->assertJsonPath('room.id', $this->room->id)
            ->assertJsonPath('room.version', 5);

        // Gửi version đúng (5) -> Thành công
        $validAssignRes = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'urgent',
                'version' => 5, // Version đúng
            ]);

        $validAssignRes->assertStatus(200)->assertJsonPath('success', true);
        $this->room->refresh();
        $this->assertEquals(6, $this->room->version);

        // Kiểm tra Optimistic Locking khi update status với version cũ
        $conflictStatusRes = $this->actingAs($this->housekeeper)
            ->postJson(route('smartroom.admin.housekeeping.status'), [
                'room_id' => $this->room->id,
                'housekeeping_status' => 'cleaning',
                'version' => 5, // Phòng hiện tại đã là 6
            ]);

        $conflictStatusRes->assertStatus(409)->assertJsonPath('code', 'ERR_OPTIMISTIC_LOCK');
    }

    /**
     * Test Smart Polling endpoint trả về dữ liệu đồng bộ khi có thay đổi
     */
    public function test_smart_polling_endpoint_returns_realtime_updates(): void
    {
        $since = time() - 10;

        // Phân công phòng để kích hoạt broadcastAndCacheRoomUpdate
        $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.housekeeping.assign'), [
                'room_id' => $this->room->id,
                'assigned_staff_id' => $this->housekeeper->id,
                'priority' => 'high',
                'version' => $this->room->version,
            ]);

        // Gọi endpoint poll
        $pollRes = $this->actingAs($this->housekeeper)
            ->getJson(route('smartroom.admin.housekeeping.poll', ['since' => $since]));

        $pollRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('has_update', true)
            ->assertJsonPath('room.id', $this->room->id)
            ->assertJsonStructure([
                'success',
                'has_update',
                'room' => ['id', 'room_number', 'housekeeping_status', 'assigned_staff_name', 'version'],
                'stats' => ['total', 'dirty', 'cleaning', 'clean', 'inspected'],
            ]);
    }

    /**
     * Test Checkout hỗ trợ thêm mặt hàng phát sinh/mua hộ (VD: mua hộ 1 thùng nước, giặt ủi...)
     */
    public function test_checkout_with_custom_requested_items(): void
    {
        $this->room->update([
            'status' => 'occupied',
            'housekeeping_status' => 'inspected',
        ]);

        $booking = HotelBooking::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'booking_code' => 'HB-CUSTOM-TEST',
            'guest_name' => 'Nguyễn Khách VIP',
            'rental_type' => 'day',
            'unit_rate' => 350000,
            'check_in_at' => now()->subDay(),
            'status' => 'checked_in',
            'payment_status' => 'unpaid',
        ]);

        $res = $this->actingAs($this->receptionist)
            ->postJson(route('smartroom.admin.frontdesk.checkout'), [
                'room_id' => $this->room->id,
                'minibar_items' => [
                    // Minibar catalog có sẵn
                    ['item_name' => 'Nước khoáng Lavie 500ml', 'quantity' => 2, 'unit_price' => 10000, 'item_type' => 'minibar'],
                    // Mặt hàng phát sinh / khách yêu cầu mua hộ
                    ['item_name' => 'Mua hộ 1 thùng nước suối Lavie', 'quantity' => 1, 'unit_price' => 120000, 'item_type' => 'service'],
                    ['item_name' => 'Dịch vụ giặt ủi đồ cao cấp', 'quantity' => 3, 'unit_price' => 30000, 'item_type' => 'service'],
                ],
                'payment_method' => 'vietqr',
            ]);

        $res->assertStatus(200)->assertJsonPath('success', true);

        // Kiểm tra Folio items được lưu đúng
        $this->assertDatabaseHas('hotel_folio_items', [
            'booking_id' => $booking->id,
            'item_name' => 'Mua hộ 1 thùng nước suối Lavie',
            'item_type' => 'service',
            'quantity' => 1,
            'unit_price' => 120000,
            'subtotal' => 120000,
        ]);

        $this->assertDatabaseHas('hotel_folio_items', [
            'booking_id' => $booking->id,
            'item_name' => 'Dịch vụ giặt ủi đồ cao cấp',
            'item_type' => 'service',
            'quantity' => 3,
            'unit_price' => 30000,
            'subtotal' => 90000,
        ]);

        // Tổng tiền service_amount: 2*10000 (20k minibar) + 120k (mua hộ) + 90k (giặt ủi) = 230,000đ
        $booking->refresh();
        $this->assertEquals(230000, $booking->service_amount);
        $this->assertEquals('checked_out', $booking->status);

        // Phòng tự động chuyển sang Cần dọn (Dirty)
        $this->room->refresh();
        $this->assertEquals('dirty', $this->room->housekeeping_status);
    }
}
