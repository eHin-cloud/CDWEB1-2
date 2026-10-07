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
        $response->assertSee('SƠ ĐỒ BUỒNG PHÒNG', false);
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
}
