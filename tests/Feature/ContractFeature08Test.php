<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test FEAT_08_CONTRACT: Hợp đồng thuê phòng Online (Ký số Canvas & Xuất PDF)
 * Thành viên phụ trách: Nguyễn Thanh Hiền
 */
class ContractFeature08Test extends TestCase
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

    private function createLandlord(): array
    {
        $tenant = Tenant::create([
            'name' => 'Chung Cư Xanh SmartRoom',
            'email' => 'landlord_' . uniqid() . '@example.test',
            'domain' => 'green-' . uniqid(),
            'phone' => '0912345678',
        ]);

        $roleLandlord = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $user = User::factory()->create([
            'name' => 'Nguyễn Thanh Hiền',
            'email' => 'hien_' . uniqid() . '@example.test',
            'phone' => '0912345678',
            'role' => 'landlord',
            'role_id' => $roleLandlord->id,
            'tenant_id' => $tenant->id,
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh A',
            'address' => 'Số 123 Đường Nguyễn Huệ, Quận 1, TP.HCM',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '301',
            'floor' => 3,
            'price' => 4500000,
            'area' => 30,
            'status' => 'empty',
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Trần Văn Thuê',
            'phone' => '0988776655',
            'email' => 'thue_' . uniqid() . '@example.test',
            'cccd' => '079201001234',
            'status' => 'active',
            'start_date' => now()->toDateString(),
        ]);

        return compact('tenant', 'user', 'building', 'room', 'resident');
    }

    /**
     * Tạo chuỗi Data URL Base64 giả lập nét vẽ chữ ký hợp lệ (SVG hoặc PNG)
     */
    private function generateValidSignatureBase64(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="150" viewBox="0 0 300 150">
            <path d="M20,75 Q60,20 100,75 T180,75 T260,75" fill="none" stroke="#0f172a" stroke-width="4"/>
            <path d="M50,110 L250,110" fill="none" stroke="#0f172a" stroke-width="2"/>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Tạo chuỗi Data URL Base64 trắng rỗng không có nét vẽ
     */
    private function generateEmptySignatureBase64(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="150" viewBox="0 0 300 150"></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Test 1: Validation tạo hợp đồng (ERR_08_01, ERR_08_02, ERR_08_03, ERR_08_04)
     */
    public function test_contract_store_validations(): void
    {
        $ctx = $this->createLandlord();

        // 1.1 Chưa chọn phòng -> ERR_08_01
        $resRoom = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', [
                'resident_id' => $ctx['resident']->id,
                'start_date' => '2026-11-01',
                'end_date' => '2027-05-01',
                'deposit' => 4500000,
            ]);
        $resRoom->assertStatus(422);
        $resRoom->assertJsonFragment(['code' => 'ERR_08_01']);
        $this->assertEquals('Vui lòng chọn phòng lưu trú để thiết lập hợp đồng.', $resRoom->json('message'));

        // 1.2 Chưa chọn cư dân đại diện -> ERR_08_02
        $resResident = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', [
                'room_id' => $ctx['room']->id,
                'start_date' => '2026-11-01',
                'end_date' => '2027-05-01',
                'deposit' => 4500000,
            ]);
        $resResident->assertStatus(422);
        $resResident->assertJsonFragment(['code' => 'ERR_08_02']);
        $this->assertEquals('Vui lòng chọn cư dân đại diện đứng tên trên hợp đồng.', $resResident->json('message'));

        // 1.3 Ngày kết thúc trước/bằng ngày bắt đầu -> ERR_08_03
        $resDate = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', [
                'room_id' => $ctx['room']->id,
                'resident_id' => $ctx['resident']->id,
                'start_date' => '2026-11-01',
                'end_date' => '2026-10-01', // trước start_date
                'deposit' => 4500000,
            ]);
        $resDate->assertStatus(422);
        $resDate->assertJsonFragment(['code' => 'ERR_08_03']);
        $this->assertEquals('Thời hạn kết thúc hợp đồng phải sau ngày bắt đầu hiệu lực.', $resDate->json('message'));

        // 1.4 Tiền cọc âm -> ERR_08_04
        $resDeposit = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', [
                'room_id' => $ctx['room']->id,
                'resident_id' => $ctx['resident']->id,
                'start_date' => '2026-11-01',
                'end_date' => '2027-05-01',
                'deposit' => -500000,
            ]);
        $resDeposit->assertStatus(422);
        $resDeposit->assertJsonFragment(['code' => 'ERR_08_04']);
        $this->assertEquals('Số tiền đặt cọc hợp đồng phải lớn hơn hoặc bằng 0 đồng.', $resDeposit->json('message'));
    }

    /**
     * Test 2: Tạo hợp đồng thành công và tự động tính ngày kết thúc từ duration_months
     */
    public function test_contract_store_success_with_duration_months(): void
    {
        $ctx = $this->createLandlord();

        $response = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', [
                'room_id' => $ctx['room']->id,
                'resident_id' => $ctx['resident']->id,
                'start_date' => '2026-11-01',
                'duration_months' => 6,
                'deposit' => 4500000,
                'rent_price' => 4500000,
                'payment_cycle_months' => 1,
            ]);

        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('download_url'));

        // Kiểm tra hợp đồng trong database
        $contract = Contract::where('room_id', $ctx['room']->id)->first();
        $this->assertNotNull($contract);
        $this->assertEquals('pending', $contract->status);
        $this->assertEquals('2026-11-01', $contract->start_date);
        $this->assertEquals('2027-05-01', $contract->end_date); // Tự động tính +6 tháng
        $this->assertEquals(4500000, $contract->deposit);
    }

    /**
     * Test 3: Ký Canvas với chữ ký rỗng -> báo lỗi ERR_08_05
     */
    public function test_sign_canvas_empty_rejected(): void
    {
        $ctx = $this->createLandlord();

        $contract = Contract::create([
            'tenant_id' => $ctx['tenant']->id,
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'contract_code' => 'HD-TEST-EMPTY',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'deposit' => 4500000,
            'status' => 'pending',
            'terms' => 'Điều khoản mẫu',
        ]);

        // Gửi chữ ký rỗng
        $emptySig = $this->generateEmptySignatureBase64();
        $response = $this->actingAs($ctx['user'])
            ->postJson("/smartroom/contract/{$contract->id}/sign-canvas", [
                'signature_data' => $emptySig,
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['code' => 'ERR_08_05']);
        $this->assertEquals('Vui lòng vẽ chữ ký tay vào khung ký số trước khi bấm xác nhận.', $response->json('message'));
    }

    /**
     * Test 4: Xác thực OTP khi ký số (ERR_08_06, ERR_08_07)
     */
    public function test_sign_canvas_otp_verification(): void
    {
        $ctx = $this->createLandlord();

        $contract = Contract::create([
            'tenant_id' => $ctx['tenant']->id,
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'contract_code' => 'HD-TEST-OTP',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'deposit' => 4500000,
            'status' => 'pending',
            'terms' => 'Điều khoản mẫu',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $validSig = $this->generateValidSignatureBase64();

        // 4.1 Chưa nhập OTP -> ERR_08_06
        $resNoOtp = $this->actingAs($ctx['user'])
            ->postJson("/smartroom/contract/{$contract->id}/sign-canvas", [
                'signature_data' => $validSig,
                'otp_code' => '',
            ]);
        $resNoOtp->assertStatus(422);
        $resNoOtp->assertJsonFragment(['code' => 'ERR_08_06']);
        $this->assertEquals('Vui lòng nhập mã OTP 6 số được gửi về Gmail cư dân để ký kết.', $resNoOtp->json('message'));

        // 4.2 Nhập sai OTP -> ERR_08_07
        $resWrongOtp = $this->actingAs($ctx['user'])
            ->postJson("/smartroom/contract/{$contract->id}/sign-canvas", [
                'signature_data' => $validSig,
                'otp_code' => '999999',
            ]);
        $resWrongOtp->assertStatus(422);
        $resWrongOtp->assertJsonFragment(['code' => 'ERR_08_07']);
        $this->assertEquals('Mã xác thực OTP ký hợp đồng không chính xác hoặc đã hết hạn.', $resWrongOtp->json('message'));
    }

    /**
     * Test 5: Ký Canvas thành công, phòng chuyển sang occupied, trả về ERR_08_08 (DoD Happy Case)
     */
    public function test_sign_canvas_success_and_room_becomes_occupied(): void
    {
        $ctx = $this->createLandlord();

        $contract = Contract::create([
            'tenant_id' => $ctx['tenant']->id,
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'contract_code' => 'HD-TEST-SUCCESS',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'deposit' => 4500000,
            'status' => 'pending',
            'terms' => 'Điều khoản mẫu',
            'otp_code' => '654321',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $validSig = $this->generateValidSignatureBase64();

        $response = $this->actingAs($ctx['user'])
            ->postJson("/smartroom/contract/{$contract->id}/sign-canvas", [
                'signature_data' => $validSig,
                'otp_code' => '654321',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['code' => 'ERR_08_08']);
        $this->assertEquals('Hợp đồng đã được ký số thành công và chuyển sang trạng thái Hiệu lực!', $response->json('message'));
        $this->assertNotEmpty($response->json('download_url'));

        // Đối soát trạng thái Hợp đồng
        $contract->refresh();
        $this->assertEquals('active', $contract->status);
        $this->assertTrue((bool) $contract->is_signed);
        $this->assertNotNull($contract->signed_at);
        $this->assertNotNull($contract->signature);

        // Đối soát trạng thái Phòng chuyển sang occupied (Đã cho thuê)
        $ctx['room']->refresh();
        $this->assertEquals('occupied', $ctx['room']->status);
    }

    /**
     * Test 6: Xuất hợp đồng PDF (Multi-tenancy & Quyền xem)
     */
    public function test_export_pdf_authorization(): void
    {
        $ctx = $this->createLandlord();

        $contract = Contract::create([
            'tenant_id' => $ctx['tenant']->id,
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'contract_code' => 'HD-TEST-PDF',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'deposit' => 4500000,
            'status' => 'active',
            'terms' => 'Điều khoản hợp đồng pháp lý chuẩn',
        ]);

        // Chủ trọ thuộc đúng tenant tải PDF thành công
        $resLandlord = $this->actingAs($ctx['user'])
            ->get("/smartroom/contract/{$contract->id}/export-pdf");
        $resLandlord->assertStatus(200);
        $this->assertEquals('application/pdf', $resLandlord->headers->get('content-type'));

        // Người dùng thuộc cơ sở khác không có quyền xem
        $otherTenant = Tenant::create([
            'name' => 'Khu trọ khác',
            'email' => 'other_' . uniqid() . '@example.test',
            'domain' => 'other-' . uniqid(),
        ]);
        $otherUser = User::factory()->create([
            'role' => 'landlord',
            'tenant_id' => $otherTenant->id,
        ]);

        $resOther = $this->actingAs($otherUser)
            ->get("/smartroom/contract/{$contract->id}/export-pdf");
        $resOther->assertStatus(403);
    }

    /**
     * Test 7: Khởi tạo hợp đồng với phương thức ký trực tiếp (signing_method = offline)
     */
    public function test_contract_store_with_offline_signing_method(): void
    {
        $ctx = $this->createLandlord();

        $payload = [
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'start_date' => '2026-11-01',
            'duration_months' => 6,
            'deposit' => 3000000,
            'signing_method' => 'offline',
            'lessor_name' => 'Chủ Trọ Test Offline',
        ];

        $response = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'success' => true,
            'code' => 'CONTRACT_CREATED',
        ]);

        $contract = Contract::where('room_id', $ctx['room']->id)->first();
        $this->assertNotNull($contract);
        $this->assertEquals('offline', $contract->signing_method);
        $this->assertEquals('pending', $contract->status);
        $this->assertFalse((bool) $contract->is_signed);
    }

    /**
     * Test 8: Xác nhận ký hợp đồng trực tiếp (confirmOfflineSign) kích hoạt phòng thành occupied
     */
    public function test_confirm_offline_sign_success_and_activates_room(): void
    {
        $ctx = $this->createLandlord();

        $contract = Contract::create([
            'tenant_id' => $ctx['tenant']->id,
            'room_id' => $ctx['room']->id,
            'resident_id' => $ctx['resident']->id,
            'contract_code' => 'HD-TEST-OFFLINE',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'deposit' => 3000000,
            'status' => 'pending',
            'signing_method' => 'offline',
            'terms' => 'Điều khoản mẫu',
        ]);

        $this->assertEquals('empty', $ctx['room']->fresh()->status);

        $response = $this->actingAs($ctx['user'])
            ->postJson("/smartroom/admin/contract/{$contract->id}/confirm-offline-sign", [
                'offline_note' => 'Hai bên đã ký trực tiếp bản giấy và nhận bàn giao chìa khóa.',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        $contract->refresh();
        $this->assertEquals('active', $contract->status);
        $this->assertTrue((bool) $contract->is_signed);
        $this->assertEquals('offline', $contract->signing_method);
        $this->assertNotNull($contract->signed_at);

        // Phòng chuyển sang occupied
        $this->assertEquals('occupied', $ctx['room']->fresh()->status);
    }

    /**
     * Test 10: Tạo hợp đồng online tự động gửi email và xuất hiện trong quả chuông thông báo của Bên B
     */
    public function test_contract_creation_dispatches_email_and_bell_notification_to_tenant(): void
    {
        $ctx = $this->createLandlord();

        // Tạo tài khoản User cho Bên B (khách thuê)
        $roleResident = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);
        $tenantUser = User::factory()->create([
            'name' => 'Nguyễn Văn B',
            'email' => 'tenant_b@example.test',
            'phone' => '0988665544',
            'role' => 'resident',
            'role_id' => $roleResident->id,
            'tenant_id' => $ctx['tenant']->id,
        ]);

        $payload = [
            'room_id' => $ctx['room']->id,
            'lessee_name' => 'Nguyễn Văn B',
            'lessee_phone' => '0988665544',
            'lessee_email' => 'tenant_b@example.test',
            'lessee_id_number' => '079201009999',
            'lessee_permanent_address' => 'Hà Nội',
            'lessee_current_address' => 'Hà Nội',
            'start_date' => '2026-11-01',
            'duration_months' => 6,
            'deposit' => 3000000,
            'signing_method' => 'online',
        ];

        $response = $this->actingAs($ctx['user'])
            ->postJson('/smartroom/admin/contracts/store', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['success' => true]);

        // 1. Kiểm tra NotificationLog đã ghi nhận việc gửi email thông báo link ký
        $this->assertDatabaseHas('notification_logs', [
            'tenant_id' => $ctx['tenant']->id,
            'type' => 'contract_invite',
            'channel' => 'email',
            'recipient_contact' => 'tenant_b@example.test',
            'status' => 'sent',
        ]);

        // 2. Kiểm tra khi Bên B đăng nhập và mở quả chuông thông báo /renty/notifications
        $bellResponse = $this->actingAs($tenantUser)
            ->getJson('/renty/notifications');

        $bellResponse->assertStatus(200);
        $this->assertTrue($bellResponse->json('success'));
        $notifications = $bellResponse->json('notifications');

        // Phải có thông báo yêu cầu ký hợp đồng cho phòng tương ứng
        $hasContractNotification = collect($notifications)->contains(function ($item) {
            return str_contains($item['title'], 'Hợp đồng mới cần ký kết')
                && str_contains($item['message'], 'ký điện tử');
        });

        $this->assertTrue($hasContractNotification, 'Bên B phải nhận được thông báo yêu cầu ký hợp đồng trên quả chuông!');
    }

    /**
     * Test 11: Tra cứu nhận dạng tài khoản khách thuê theo số điện thoại (Auto-detect & Auto-fill)
     */
    public function test_lookup_customer_by_phone_returns_existing_user_data(): void
    {
        $ctx = $this->createLandlord();

        // 1. Tạo một User khách thuê đã có tài khoản
        $guestUser = User::factory()->create([
            'name' => 'Lê Thị Thuê',
            'phone' => '0933112233',
            'email' => 'lethithue@example.test',
        ]);

        // Tạo hồ sơ cư dân cũ để có CCCD và HKTT
        Resident::create([
            'tenant_id' => $ctx['tenant']->id,
            'user_id' => $guestUser->id,
            'name' => 'Lê Thị Thuê',
            'phone' => '0933112233',
            'cccd' => '012345678901',
            'hometown' => 'Đà Nẵng',
            'status' => 'active',
            'start_date' => now()->toDateString(),
        ]);

        // 2. Tra cứu số điện thoại tồn tại -> Phải tìm thấy và trả về dữ liệu tự điền
        $responseFound = $this->actingAs($ctx['user'])
            ->getJson('/smartroom/admin/lookup-customer?phone=0933112233');

        $responseFound->assertStatus(200);
        $responseFound->assertJson([
            'found' => true,
            'customer' => [
                'name' => 'Lê Thị Thuê',
                'phone' => '0933112233',
                'email' => 'lethithue@example.test',
                'cccd' => '012345678901',
                'permanent_address' => 'Đà Nẵng',
            ],
        ]);

        // 3. Tra cứu số điện thoại chưa từng tồn tại -> found = false
        $responseNotFound = $this->actingAs($ctx['user'])
            ->getJson('/smartroom/admin/lookup-customer?phone=0999999999');

        $responseNotFound->assertStatus(200);
        $responseNotFound->assertJson(['found' => false]);
    }

    /**
     * Test: Modal Tạo Hợp Đồng Mới chỉ hiển thị các phòng còn trống (status = empty),
     * không hiển thị các phòng đã có người ở (status = occupied/overdue) để tránh chọn nhầm.
     */
    public function test_contract_creation_modal_only_shows_empty_rooms_and_hides_occupied_rooms(): void
    {
        $ctx = $this->createLandlord();

        // Phòng 301 đã được tạo ở createLandlord() với status = empty
        // Tạo thêm 1 phòng đã có người thuê: Phòng 401 với status = occupied
        $occupiedRoom = Room::create([
            'tenant_id' => $ctx['tenant']->id,
            'building_id' => $ctx['building']->id,
            'room_number' => '401',
            'floor' => 4,
            'price' => 5000000,
            'area' => 35,
            'status' => 'occupied',
        ]);

        $response = $this->actingAs($ctx['user'])->get(route('smartroom.admin'));
        $response->assertStatus(200);

        // Phòng 301 trống -> Có trong danh sách chọn phòng hợp đồng
        $response->assertSee('data-id="' . $ctx['room']->id . '"', false);
        $response->assertSee('Phòng 301', false);

        // Phòng 401 đã có người thuê -> Tuyệt đối KHÔNG xuất hiện trong danh sách chọn phòng tạo hợp đồng
        $response->assertDontSee('data-id="' . $occupiedRoom->id . '"', false);
    }
}

