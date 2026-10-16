<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FEAT_30_DASHBOARD: Bảng điều khiển Thống kê tổng quan phòng & Doanh thu (Dashboard)
 * Thành viên phụ trách: Huỳnh Văn Vĩnh Em
 */
class DashboardStatisticsFeature30Test extends TestCase
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

    private function createTenant(string $name = 'Hệ Thống Trọ Vĩnh Em'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'vinhem_' . uniqid() . '@smartroom.vn',
            'domain' => 'vinhem-' . uniqid(),
        ]);
    }

    private function createLandlordUser(Tenant $tenant): User
    {
        $role = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        return User::create([
            'name' => 'Huỳnh Văn Vĩnh Em',
            'username' => 'landlord_' . uniqid(),
            'email' => 'landlord_' . uniqid() . '@smartroom.vn',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'phone' => '098' . rand(1000000, 9999999),
        ]);
    }

    private function createRoom(Tenant $tenant, Building $building, string $roomNumber, string $status = 'empty', int $price = 3500000): Room
    {
        return Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 1,
            'price' => $price,
            'status' => $status,
            'cleaning_status' => 'clean',
        ]);
    }

    /**
     * 1. GET /smartroom/admin/dashboard yêu cầu đăng nhập (Condition check)
     */
    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/smartroom/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    /**
     * 2. Chủ trọ truy cập thành công và thấy giao diện Dashboard
     */
    public function test_landlord_can_access_dashboard_view(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        $response = $this->actingAs($landlord)->get('/smartroom/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('FEAT_30');
        $response->assertSee('Bảng Điều Khiển', false);
    }

    /**
     * 3. Tính toán chính xác 4 thẻ KPI (Tổng doanh thu, Tỷ lệ lấp đầy, Tiền chưa thu, Số lượng cư dân)
     */
    public function test_kpi_metrics_calculation_accuracy(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Chi Nhánh Cầu Giấy',
            'address' => '100 Xuân Thủy, Cầu Giấy, Hà Nội',
        ]);

        // Tạo 4 phòng: 2 đang ở, 1 trống, 1 sửa chữa -> Tỷ lệ lấp đầy = 2/4 = 50%
        $room1 = $this->createRoom($tenant, $building, '101', 'occupied');
        $room2 = $this->createRoom($tenant, $building, '102', 'occupied');
        $room3 = $this->createRoom($tenant, $building, '103', 'empty');
        $room4 = $this->createRoom($tenant, $building, '104', 'maintenance');

        // Tạo 2 cư dân
        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'name' => 'Nguyễn Văn A',
            'phone' => '0912345678',
            'start_date' => '2026-01-01',
            'status' => 'active',
        ]);
        Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room2->id,
            'name' => 'Trần Thị B',
            'phone' => '0987654321',
            'start_date' => '2026-01-01',
            'status' => 'active',
        ]);

        // Tạo hóa đơn đã thanh toán (Doanh thu) và chưa thanh toán (Nợ tồn)
        $currentMonth = date('Y-m');
        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'billing_month' => $currentMonth,
            'room_price' => 3500000,
            'total_amount' => 4000000,
            'status' => 'paid',
        ]);

        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room2->id,
            'billing_month' => $currentMonth,
            'room_price' => 3500000,
            'total_amount' => 3800000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'kpi' => [
                    'total_revenue' => 4000000,
                    'occupancy_rate' => 50,
                    'unpaid_debt' => 3800000,
                    'residents_count' => 2,
                    'total_rooms' => 4,
                    'occupied_rooms' => 2,
                    'empty_rooms' => 1,
                    'maintenance_rooms' => 1,
                ]
            ]
        ]);
    }

    /**
     * 4. Kịch bản kiểm thử DoD 1: Chọn Từ ngày = 31/10/2026 và Đến ngày = 01/10/2026
     * Báo lỗi đỏ 'Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày'
     */
    public function test_invalid_date_range_validation_dod_1(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        // Kiểm tra qua API JSON (trả về lỗi 422 và error message chuẩn)
        $responseJson = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard?time_filter=custom&start_date=2026-10-31&end_date=2026-10-01');
        $responseJson->assertStatus(422);
        $responseJson->assertJson([
            'success' => false,
            'error_code' => 'ERR_30_INVALID_DATE_RANGE',
            'message' => 'Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày',
        ]);

        // Kiểm tra qua Giao diện HTML (hiển thị thông điệp lỗi)
        $responseHtml = $this->actingAs($landlord)->get('/smartroom/admin/dashboard?time_filter=custom&start_date=2026-10-31&end_date=2026-10-01');
        $responseHtml->assertStatus(200);
        $responseHtml->assertSee('Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày');
        $responseHtml->assertSee('is-invalid');
    }

    /**
     * 5. Kịch bản kiểm thử DoD 2: Chọn xem báo cáo 'Năm nay'
     * Biểu đồ tự động cập nhật lại số liệu 12 tháng chính xác
     */
    public function test_revenue_chart_year_filter_dod_2(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cơ Sở 1',
            'address' => 'Hà Nội',
        ]);

        $room = $this->createRoom($tenant, $building, '201', 'occupied');

        // Tạo doanh thu tháng 3 và tháng 6 trong năm hiện tại
        $currentYear = date('Y');
        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => "$currentYear-03",
            'room_price' => 5000000,
            'total_amount' => 5000000,
            'status' => 'paid',
        ]);

        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => "$currentYear-06",
            'room_price' => 7000000,
            'total_amount' => 7000000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard/revenue-chart?time_filter=this_year');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'has_data' => true,
                'total_revenue' => 12000000,
            ]
        ]);

        $data = $response->json('data');
        $this->assertCount(12, $data['labels']);
        $this->assertEquals('Tháng 1', $data['labels'][0]);
        $this->assertEquals('Tháng 12', $data['labels'][11]);
        $this->assertCount(12, $data['series']);
        $this->assertEquals(5000000, $data['series'][2]); // Tháng 3 (index 2)
        $this->assertEquals(7000000, $data['series'][5]); // Tháng 6 (index 5)
    }

    /**
     * 6. Kịch bản lỗi ERR_30_01: Chọn khoảng thời gian không có doanh thu
     * Trả về message 'Chưa có số liệu doanh thu phát sinh trong khoảng thời gian này.'
     */
    public function test_no_revenue_period_shows_err_30_01(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        // Khoảng thời gian không có bất kỳ hóa đơn thanh toán nào
        $response = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard/revenue-chart?time_filter=custom&start_date=2020-01-01&end_date=2020-01-31');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'has_data' => false,
                'error_code' => 'ERR_30_01',
                'message' => 'Chưa có số liệu doanh thu phát sinh trong khoảng thời gian này.',
            ]
        ]);
    }

    /**
     * 7. Bảo toàn phân quyền Multi-tenancy: Số liệu của Tenant B không bị lộ sang Tenant A
     */
    public function test_multi_tenancy_data_isolation(): void
    {
        $tenantA = $this->createTenant('Nhà Trọ A');
        $landlordA = $this->createLandlordUser($tenantA);

        $tenantB = $this->createTenant('Nhà Trọ B');
        $landlordB = $this->createLandlordUser($tenantB);

        $buildingA = Building::create(['tenant_id' => $tenantA->id, 'name' => 'Tòa A', 'address' => 'Địa chỉ A']);
        $buildingB = Building::create(['tenant_id' => $tenantB->id, 'name' => 'Tòa B', 'address' => 'Địa chỉ B']);

        $roomA = $this->createRoom($tenantA, $buildingA, 'A101', 'occupied');
        $roomB = $this->createRoom($tenantB, $buildingB, 'B101', 'occupied');

        Bill::create([
            'tenant_id' => $tenantA->id,
            'room_id' => $roomA->id,
            'billing_month' => date('Y-m'),
            'room_price' => 3000000,
            'total_amount' => 3000000,
            'status' => 'paid',
        ]);

        Bill::create([
            'tenant_id' => $tenantB->id,
            'room_id' => $roomB->id,
            'billing_month' => date('Y-m'),
            'room_price' => 99000000,
            'total_amount' => 99000000,
            'status' => 'paid',
        ]);

        // Dashboard của Landlord A chỉ thấy doanh thu 3 triệu của mình, tuyệt đối không thấy 99 triệu của B
        $responseA = $this->actingAs($landlordA)->getJson('/smartroom/admin/dashboard');
        $responseA->assertStatus(200);
        $this->assertEquals(3000000, $responseA->json('data.kpi.total_revenue'));
        $this->assertEquals(1, $responseA->json('data.kpi.total_rooms'));

        // Dashboard của Landlord B thấy 99 triệu
        $responseB = $this->actingAs($landlordB)->getJson('/smartroom/admin/dashboard');
        $responseB->assertStatus(200);
        $this->assertEquals(99000000, $responseB->json('data.kpi.total_revenue'));
    }

    /**
     * 8. Lọc theo từng tòa nhà (building_filter)
     */
    public function test_building_filter_filters_rooms_and_revenue(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        $building1 = Building::create(['tenant_id' => $tenant->id, 'name' => 'Chi Nhánh 1', 'address' => 'Địa chỉ 1']);
        $building2 = Building::create(['tenant_id' => $tenant->id, 'name' => 'Chi Nhánh 2', 'address' => 'Địa chỉ 2']);

        $room1 = $this->createRoom($tenant, $building1, '101', 'occupied');
        $room2 = $this->createRoom($tenant, $building2, '201', 'empty');

        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'billing_month' => date('Y-m'),
            'room_price' => 4500000,
            'total_amount' => 4500000,
            'status' => 'paid',
        ]);

        // Khi lọc theo Chi Nhánh 1: chỉ có 1 phòng (đang ở), tỷ lệ lấp đầy = 100%, doanh thu = 4.5M
        $response = $this->actingAs($landlord)->getJson("/smartroom/admin/dashboard?building_filter={$building1->id}");
        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.kpi.total_rooms'));
        $this->assertEquals(100, $response->json('data.kpi.occupancy_rate'));
        $this->assertEquals(4500000, $response->json('data.kpi.total_revenue'));

        // Khi lọc theo Chi Nhánh 2: chỉ có 1 phòng (trống), tỷ lệ lấp đầy = 0%, doanh thu = 0
        $response2 = $this->actingAs($landlord)->getJson("/smartroom/admin/dashboard?building_filter={$building2->id}");
        $response2->assertStatus(200);
        $this->assertEquals(1, $response2->json('data.kpi.total_rooms'));
        $this->assertEquals(0, $response2->json('data.kpi.occupancy_rate'));
        $this->assertEquals(0, $response2->json('data.kpi.total_revenue'));
    }

    /**
     * 9. Kiểm tra tính toán cơ cấu doanh thu thực tế (Revenue Breakdown: Tiền phòng, điện, nước, dịch vụ)
     */
    public function test_revenue_breakdown_realtime_calculation(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);
        $building = Building::create(['tenant_id' => $tenant->id, 'name' => 'Tòa Nhà Thử Nghiệm', 'address' => 'Hà Nội']);
        $room = $this->createRoom($tenant, $building, '301', 'occupied');

        $currentMonth = date('Y-m');
        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => $currentMonth,
            'room_price' => 3000000,
            'electricity_cost' => 500000,
            'water_cost' => 200000,
            'service_cost' => 150000,
            'total_amount' => 3850000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'revenue_breakdown' => [
                    'has_data' => true,
                    'total' => 3850000,
                    'data' => [3000000, 500000, 200000, 150000],
                ]
            ]
        ]);

        $breakdown = $response->json('data.revenue_breakdown');
        $this->assertCount(4, $breakdown['labels']);
        $this->assertEquals('Tiền phòng', $breakdown['labels'][0]);
        $this->assertEquals(77.9, $breakdown['percentages'][0]); // 3000000 / 3850000 ~ 77.9%
    }

    /**
     * 10. Kiểm tra hỗ trợ lọc doanh thu tùy chọn theo khoảng ngày bất kỳ (Custom Date Range Picker)
     */
    public function test_custom_date_range_presets(): void
    {
        $tenant = $this->createTenant();
        $landlord = $this->createLandlordUser($tenant);

        // Test preset 7 ngày qua
        $res7Days = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard?time_filter=last_7_days');
        $res7Days->assertStatus(200);
        $this->assertEquals('last_7_days', $res7Days->json('data.filters.time_filter'));
        $this->assertNotNull($res7Days->json('data.filters.start_date'));
        $this->assertNotNull($res7Days->json('data.filters.end_date'));

        // Test custom range hợp lệ
        $resCustom = $this->actingAs($landlord)->getJson('/smartroom/admin/dashboard?time_filter=custom&start_date=2026-06-01&end_date=2026-08-31');
        $resCustom->assertStatus(200);
        $this->assertEquals('2026-06-01', $resCustom->json('data.filters.start_date'));
        $this->assertEquals('2026-08-31', $resCustom->json('data.filters.end_date'));
    }
}

