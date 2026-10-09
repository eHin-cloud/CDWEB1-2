<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test FEAT_27_PORTAL: Cổng dịch vụ Cư dân (Resident Portal)
 * Phụ trách: Huỳnh Văn Vĩnh Em
 */
class ResidentPortalFeature27Test extends TestCase
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

    private function createTenant(string $name = 'Renty House'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'renty_' . uniqid() . '@example.test',
            'domain' => 'renty-' . uniqid(),
            'phone' => '0987654321',
            'bank_name' => 'Vietcombank',
            'bank_account_no' => '1051572297',
            'bank_account_name' => 'CHU NHA RENTY HOUSE',
        ]);
    }

    private function createRoom(Tenant $tenant, string $roomNumber = '302', float $price = 4250000): Room
    {
        $building = Building::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa nhà Renty House',
        ], [
            'address' => '123 Đường Sư Vạn Hạnh, Q.10, TP.HCM',
        ]);

        return Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 3,
            'price' => $price,
            'status' => 'occupied',
            'cleaning_status' => 'clean',
        ]);
    }

    private function createResidentUser(Tenant $tenant, Room $room, string $name = 'Huỳnh Văn Vĩnh Em', string $phone = '0940000001'): array
    {
        $residentRole = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);

        $user = User::create([
            'name' => $name,
            'username' => 'cudan_' . uniqid(),
            'email' => 'cudan_' . uniqid() . '@example.com',
            'password' => bcrypt('123456'),
            'role_id' => $residentRole->id,
            'role' => 'user',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => $name,
            'phone' => $phone,
            'email' => $user->email,
            'start_date' => now()->subMonths(2),
            'status' => 'active',
        ]);

        return [$user, $resident];
    }

    private function createContract(Tenant $tenant, Room $room, Resident $resident, string $status = 'active'): Contract
    {
        return Contract::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'contract_code' => 'HD-' . $room->room_number . '-2026',
            'start_date' => now()->subMonths(2)->format('Y-m-d'),
            'end_date' => $status === 'expired' ? now()->subDays(5)->format('Y-m-d') : now()->addMonths(10)->format('Y-m-d'),
            'deposit' => 4000000,
            'status' => $status,
            'terms' => 'Quy định giữ gìn an ninh trật tự, đóng tiền phòng trước ngày 05 hàng tháng.',
        ]);
    }

    private function createBill(Tenant $tenant, Room $room, string $month = '2026-10', string $status = 'sent'): UtilityRecord
    {
        return UtilityRecord::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => $month,
            'old_electricity' => 100,
            'new_electricity' => 220,
            'old_water' => 20,
            'new_water' => 28,
            'electricity_price' => 3500,
            'water_price' => 25000,
            'status' => $status,
            'notes' => 'Hóa đơn tiền phòng và điện nước tháng ' . $month,
        ]);
    }

    /**
     * DoD STT 1: Cư dân hợp lệ đăng nhập vào cổng hiển thị chính xác tiền phòng, số điện, số nước của phòng mình
     */
    public function test_dod_1_valid_resident_can_view_portal_with_room_and_utilities_data()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302', 4250000);
        [$user, $resident] = $this->createResidentUser($tenant, $room);
        $contract = $this->createContract($tenant, $room, $resident, 'active');
        $bill = $this->createBill($tenant, $room, '2026-10', 'sent');

        $response = $this->actingAs($user)->get(route('smartroom.resident.portal'));

        $response->assertStatus(200);
        $response->assertSee('Thông tin phòng thuê');
        $response->assertSee('Phòng 302 - Tòa nhà Renty House');
        $response->assertSee('Hóa đơn kỳ này');
        $response->assertSee('120'); // kWh
        $response->assertSee('8');   // m3
        $response->assertSee('Tải hợp đồng PDF');
        $response->assertSee('Quét mã VietQR');
        $response->assertDontSee('Cổng Cư Dân Tạm Thời Khóa');
    }

    /**
     * DoD STT 2: Bấm nút 'Tải hợp đồng PDF' -> Hệ thống tải xuống file hợp đồng đã ký số (Content-Type: application/pdf)
     */
    public function test_dod_2_can_download_signed_contract_pdf()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302');
        [$user, $resident] = $this->createResidentUser($tenant, $room);
        $contract = $this->createContract($tenant, $room, $resident, 'active');

        $response = $this->actingAs($user)->get(route('smartroom.resident.contract.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('hop_dong_' . $contract->contract_code . '.pdf', $response->headers->get('content-disposition'));
    }

    /**
     * Kịch bản lỗi ERR_27_01: Đăng nhập sai tài khoản hoặc số điện thoại
     * Message cụ thể: "Thông tin phòng hoặc số điện thoại không chính xác trong hệ thống."
     */
    public function test_err_27_01_login_failure_returns_exact_error_message_and_code()
    {
        $response = $this->post(route('user.authUser'), [
            'login' => '0999999999',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect('login');
        $response->assertSessionHas('error_code', 'ERR_27_01');
        $response->assertSessionHas('error', 'Thông tin phòng hoặc số điện thoại không chính xác trong hệ thống.');
    }

    /**
     * Kịch bản lỗi ERR_27_02: Hợp đồng thuê phòng đã hết hạn
     * Message cụ thể: "Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt."
     * Hiển thị màn hình khóa tạm thời kèm hotline liên hệ
     */
    public function test_err_27_02_shows_locked_screen_when_contract_is_expired()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302');
        [$user, $resident] = $this->createResidentUser($tenant, $room);
        $this->createContract($tenant, $room, $resident, 'expired');

        $response = $this->actingAs($user)->get(route('smartroom.resident.portal'));

        $response->assertStatus(200);
        $response->assertSee('ERR_27_02');
        $response->assertSee('Cổng Cư Dân Tạm Thời Khóa');
        $response->assertSee('Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt.');
        $response->assertSee('0987654321'); // Hotline
        $response->assertSee('Hotline Ban Quản Lý / Chủ Trọ');
    }

    /**
     * Kịch bản lỗi ERR_27_03: Bấm mở mã VietQR thanh toán tiền nhà chuẩn Napas 247
     * API /smartroom/resident/bills/{id}/qr-data trả về JSON mã VietQR động chuẩn Napas 247 kèm số tiền chính xác
     */
    public function test_err_27_03_fetches_dynamic_vietqr_napas_data()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302', 4000000);
        [$user, $resident] = $this->createResidentUser($tenant, $room);
        $this->createContract($tenant, $room, $resident, 'active');
        $bill = $this->createBill($tenant, $room, '2026-10', 'sent');

        $response = $this->actingAs($user)->getJson(route('smartroom.resident.bills.qr_data', $bill->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code' => 'ERR_27_03',
            'message' => 'Hiển thị mã VietQR thanh toán tiền nhà chuẩn Napas 247.',
            'bank_name' => 'Vietcombank',
            'bank_account_no' => '1051572297',
        ]);
        $json = $response->json();
        $this->assertStringContainsString('https://img.vietqr.io/image/', $json['qr_url']);
        $this->assertStringContainsString('1051572297', $json['qr_url']);
        $this->assertGreaterThan(0, $json['amount']);
        $this->assertStringContainsString('302', $json['transfer_content']);
    }

    /**
     * Bảo mật hóa đơn & Multi-tenancy Isolation:
     * Cư dân phòng này không được xem hóa đơn phòng bên cạnh
     */
    public function test_multi_tenancy_isolation_cannot_view_neighbor_room_bill_qr()
    {
        $tenant = $this->createTenant();
        $room1 = $this->createRoom($tenant, '301');
        $room2 = $this->createRoom($tenant, '302');

        [$user1, $resident1] = $this->createResidentUser($tenant, $room1, 'Cư dân 301', '0940000001');
        [$user2, $resident2] = $this->createResidentUser($tenant, $room2, 'Cư dân 302', '0940000002');

        $this->createContract($tenant, $room1, $resident1, 'active');
        $this->createContract($tenant, $room2, $resident2, 'active');

        // Hóa đơn của phòng 302
        $bill2 = $this->createBill($tenant, $room2, '2026-10', 'sent');

        // Cư dân phòng 301 cố gắng lấy QR của phòng 302
        $response = $this->actingAs($user1)->getJson(route('smartroom.resident.bills.qr_data', $bill2->id));

        // Phải bị từ chối 404 (Không tìm thấy hóa đơn thuộc phòng của bạn)
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Hóa đơn không tồn tại hoặc không thuộc phòng của bạn.',
        ]);
    }

    /**
     * Endpoint GET /smartroom/resident/invoices xem danh sách hóa đơn của cư dân
     */
    public function test_resident_can_view_invoices_page()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302');
        [$user, $resident] = $this->createResidentUser($tenant, $room);
        $this->createContract($tenant, $room, $resident, 'active');
        $bill = $this->createBill($tenant, $room, '2026-10', 'sent');

        $response = $this->actingAs($user)->get(route('smartroom.resident.invoices'));

        $response->assertStatus(200);
        $response->assertSee('Lịch sử hóa đơn tiền phòng');
        $response->assertSee('2026-10');
        $response->assertSee('Vietcombank');
        $response->assertSee('1051572297');
    }

    /**
     * Alias /portal tự động chuyển hướng đúng vào cổng cư dân
     */
    public function test_portal_route_redirects_resident_to_resident_portal()
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant, '302');
        [$user, $resident] = $this->createResidentUser($tenant, $room);

        $response = $this->actingAs($user)->get('/portal');

        $response->assertRedirect(route('smartroom.resident.portal'));
    }
}
