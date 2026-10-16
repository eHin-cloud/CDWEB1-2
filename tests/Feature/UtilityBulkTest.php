<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilityBulkTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $landlord;
    protected Building $building;
    protected Room $room1;
    protected Room $room2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Chung Cư Mini Thông Minh Cầu Giấy',
            'email' => 'contact@smartroom-caugiay.vn',
            'phone' => '0987654321',
        ]);

        $roleLandlord = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Landlord', 'description' => 'Chủ cơ sở lưu trú']
        );

        $this->landlord = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $roleLandlord->id,
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Tri Thức B',
            'address' => 'Số 10 Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
            'total_floors' => 5,
        ]);

        $this->room1 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 4500000,
            'status' => 'occupied',
        ]);

        $this->room2 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '202',
            'floor' => 2,
            'price' => 4800000,
            'status' => 'occupied',
        ]);

        // Tạo chỉ số tháng trước cho phòng 201: Điện 150 kWh, Nước 30 m3
        UtilityRecord::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room1->id,
            'billing_month' => '2026-09',
            'old_electricity' => 100,
            'new_electricity' => 150,
            'old_water' => 20,
            'new_water' => 30,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'paid',
        ]);
    }

    /**
     * ERR_15_01: Để trống tháng chốt cước
     */
    public function test_err_15_01_empty_billing_month()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.bulk_store'), [
                'billing_month' => '',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => '160',
                        'new_water' => '35',
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_15_01',
                'message' => 'Vui lòng chọn tháng tính cước hóa đơn điện nước.',
            ]);
    }

    /**
     * ERR_15_02: Chỉ số điện mới nhỏ hơn điện cũ
     */
    public function test_err_15_02_new_electricity_lower_than_old()
    {
        // Phòng 201 có chỉ số cũ kỳ trước là 150 kWh. Nhập 120 kWh < 150 kWh.
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.bulk_store'), [
                'billing_month' => '2026-10',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => '120',
                        'new_water' => '35',
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_15_02',
                'message' => 'Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.',
            ]);
    }

    /**
     * ERR_15_03: Chỉ số nước mới nhỏ hơn nước cũ
     */
    public function test_err_15_03_new_water_lower_than_old()
    {
        // Phòng 201 có chỉ số cũ kỳ trước là 30 m3. Nhập 25 m3 < 30 m3.
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.bulk_store'), [
                'billing_month' => '2026-10',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => '170',
                        'new_water' => '25',
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_15_03',
                'message' => 'Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.',
            ]);
    }

    /**
     * ERR_15_04: Nhập ký tự chữ vào ô chỉ số
     */
    public function test_err_15_04_non_numeric_utility_reading()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.bulk_store'), [
                'billing_month' => '2026-10',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => 'abc',
                        'new_water' => '35',
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_15_04',
                'message' => 'Chỉ số công tơ điện nước chỉ được phép nhập số.',
            ]);
    }

    /**
     * ERR_15_05: Lưu chốt số thành công
     */
    public function test_err_15_05_store_bulk_utility_success()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.bulk_store'), [
                'billing_month' => '2026-10',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => '180',
                        'new_water' => '38',
                    ],
                    $this->room2->id => [
                        'new_electricity' => '50',
                        'new_water' => '15',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_15_05',
                'message' => 'Đã lưu chỉ số điện nước thành công cho toàn bộ các phòng!',
                'saved_count' => 2,
            ]);

        $this->assertDatabaseHas('utility_records', [
            'room_id' => $this->room1->id,
            'billing_month' => '2026-10',
            'old_electricity' => 150,
            'new_electricity' => 180,
            'old_water' => 30,
            'new_water' => 38,
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('utility_records', [
            'room_id' => $this->room2->id,
            'billing_month' => '2026-10',
            'old_electricity' => 0,
            'new_electricity' => 50,
            'old_water' => 0,
            'new_water' => 15,
            'status' => 'sent',
        ]);

        $this->assertEquals('overdue', $this->room1->fresh()->status);
        $this->assertEquals('overdue', $this->room2->fresh()->status);
    }

    /**
     * Test GET /smartroom/admin/utility/bulk route and UI elements
     * - cboMonth (STT 1 - Bảng 110)
     * - tblUtility (STT 2 - Bảng 110)
     * - btnSaveAll (STT 3 - Bảng 110)
     */
    public function test_ui_elements_and_bulk_routes_render_correctly()
    {
        // 1. Kiểm tra GET /smartroom/admin/utility/bulk chuyển hướng về tab utility-section
        $getRouteResponse = $this->actingAs($this->landlord)
            ->get('/smartroom/admin/utility/bulk');
        $getRouteResponse->assertRedirect(route('smartroom.admin', ['tab' => 'utility-section']));

        // 2. Kiểm tra GET /smartroom/admin/utility/bulk-store endpoint hỗ trợ POST
        $postAltResponse = $this->actingAs($this->landlord)
            ->postJson('/smartroom/admin/utility/bulk-store', [
                'billing_month' => '2026-11',
                'utilities' => [
                    $this->room1->id => [
                        'new_electricity' => '200',
                        'new_water' => '42',
                    ],
                ],
            ]);
        $postAltResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_15_05',
                'message' => 'Đã lưu chỉ số điện nước thành công cho toàn bộ các phòng!',
            ]);

        // 3. Kiểm tra trang giao diện admin chứa đầy đủ các phần tử Bảng 110
        $dashboardResponse = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin', ['tab' => 'utility-section']));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('id="cboMonth"', false);
        $dashboardResponse->assertSee('Kỳ ghi chỉ số', false);
        $dashboardResponse->assertSee('id="tblUtility"', false);
        $dashboardResponse->assertSee('id="btnSaveAll"', false);
        $dashboardResponse->assertSee('Lưu toàn bộ chỉ số', false);
    }

    /**
     * Test validation cho lưu chỉ số đơn lẻ từng phòng
     */
    public function test_single_room_utility_store_validation()
    {
        // Nhập điện mới nhỏ hơn điện cũ phòng 201 (old = 150)
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.store'), [
                'room_id' => $this->room1->id,
                'billing_month' => '2026-10',
                'new_electricity' => '130',
                'new_water' => '40',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_15_02',
                'message' => 'Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.',
            ]);
    }
}
