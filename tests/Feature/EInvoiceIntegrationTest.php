<?php

namespace Tests\Feature;

use App\Helpers\VietnameseCurrencyHelper;
use App\Models\Bill;
use App\Models\Building;
use App\Models\ElectronicInvoice;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Services\EInvoice\Drivers\MisaMeInvoiceDriver;
use App\Services\EInvoice\Drivers\MockEInvoiceDriver;
use App\Services\EInvoice\Drivers\ViettelSinvoiceDriver;
use App\Services\EInvoice\Drivers\VnptInvoiceDriver;
use App\Services\EInvoice\EInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EInvoiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Queue::fake();
    }

    private function createLandlordUser(array $tenantOverrides = []): array
    {
        $role = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        $tenant = Tenant::create(array_merge([
            'name' => 'Chủ Trọ Test SmartRoom',
            'email' => 'landlord_' . uniqid() . '@test.com',
            'phone' => '0912345678',
            'bank_name' => 'MB',
            'bank_account_no' => '999988889999',
            'bank_account_name' => 'NGUYEN VAN CHU TRO',
            'einvoice_config' => [
                'provider' => 'mock',
                'tax_code' => '0101234567-001',
                'company_name' => 'HỘ KINH DOANH CHO THUÊ NHÀ NGUYEN VAN',
                'address' => '123 Cầu Giấy, Hà Nội',
                'template_symbol' => '1C26TAA',
                'invoice_series' => 'C26TAA',
                'tax_rate' => 8.0,
                'auto_issue_on_payment' => true,
                'auto_send_email' => true,
                'auto_send_zalo' => true,
            ],
        ], $tenantOverrides));

        $user = User::create([
            'name' => 'Chủ Trọ Admin',
            'username' => 'landlord_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
        ]);

        return [$user, $tenant];
    }

    /**
     * Kiểm tra hàm đọc số tiền tiếng Việt chuẩn quy chuẩn tài chính kế toán
     */
    public function test_vietnamese_currency_helper_converts_numbers_accurately(): void
    {
        $this->assertEquals('Không đồng', VietnameseCurrencyHelper::readMoney(0));
        $this->assertEquals('Một triệu đồng', VietnameseCurrencyHelper::readMoney(1000000));
        $this->assertEquals('Ba triệu năm trăm nghìn đồng', VietnameseCurrencyHelper::readMoney(3500000));
        $this->assertEquals('Bốn mươi lăm nghìn đồng', VietnameseCurrencyHelper::readMoney(45000));
        $this->assertEquals('Mười lăm nghìn đồng', VietnameseCurrencyHelper::readMoney(15000));
        $this->assertEquals('Hai mươi mốt nghìn đồng', VietnameseCurrencyHelper::readMoney(21000));
        $this->assertEquals('Một trăm hai mươi lăm nghìn đồng', VietnameseCurrencyHelper::readMoney(125000));
    }

    /**
     * Kiểm tra khởi tạo và xuất hóa đơn điện tử qua Driver chuẩn NĐ 123/2020/NĐ-CP & TT 78
     */
    public function test_einvoice_drivers_and_mock_issue(): void
    {
        $service = new EInvoiceService();

        $this->assertInstanceOf(MockEInvoiceDriver::class, $service->resolveDriver('mock'));
        $this->assertInstanceOf(MisaMeInvoiceDriver::class, $service->resolveDriver('misa'));
        $this->assertInstanceOf(VnptInvoiceDriver::class, $service->resolveDriver('vnpt'));
        $this->assertInstanceOf(ViettelSinvoiceDriver::class, $service->resolveDriver('viettel'));

        $mockDriver = new MockEInvoiceDriver();
        $payload = [
            'invoice_number' => 42,
            'seller' => [
                'name' => 'Nhà Trọ Xanh',
                'tax_code' => '0301234567',
                'address' => 'Quận 7, TP.HCM',
                'phone' => '0901234567',
                'bank_account' => '12345678 (VCB)',
            ],
            'buyer' => [
                'name' => 'Nguyễn Văn Thuê',
                'tax_code' => null,
                'id_card' => '079201000123',
                'address' => 'TP.HCM',
                'phone' => '0987654321',
                'email' => 'thue@test.com',
            ],
            'items' => [
                [
                    'name' => 'Tiền thuê phòng 101 tháng 09/2026',
                    'unit' => 'Tháng',
                    'quantity' => 1,
                    'price' => 3000000,
                    'amount' => 3000000,
                    'vat_rate' => 8,
                    'vat_amount' => 240000,
                ],
            ],
            'subtotal_amount' => 3000000,
            'tax_rate' => 8,
            'tax_amount' => 240000,
            'total_amount' => 3240000,
            'total_amount_in_words' => 'Ba triệu hai trăm bốn mươi nghìn đồng',
        ];

        $result = $mockDriver->issueInvoice($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('00000042', $result['invoice_number']);
        $this->assertEquals('1C26TAA', $result['invoice_symbol']);
        $this->assertEquals('CQT_ACCEPTED', $result['cqt_status']);
        // Mã CQT phải đủ đúng 34 ký tự theo chuẩn Tổng cục Thuế
        $this->assertEquals(34, strlen($result['tax_authority_code']));
        $this->assertStringStartsWith('SRM-', $result['lookup_code']);
        $this->assertNotEmpty($result['digital_signature']);
        $this->assertStringContainsString('<HDon', $result['xml_content']);
        $this->assertStringContainsString('<MSTCQT>' . $result['tax_authority_code'] . '</MSTCQT>', $result['xml_content']);
    }

    /**
     * Kiểm tra tự động xuất HĐĐT có mã CQT khi nhận Webhook thanh toán thành công
     */
    public function test_auto_issue_einvoice_when_payment_webhook_received(): void
    {
        [$user, $tenant] = $this->createLandlordUser();

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
            'address' => '456 Lê Văn Lương, Quận 7, TP.HCM',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '102',
            'floor' => 1,
            'price' => 3500000,
            'status' => 'overdue',
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Trần Văn Khách',
            'email' => 'khach102@test.com',
            'phone' => '0933112233',
            'cccd' => '079201009999',
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $bill = Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-09',
            'room_price' => 3500000,
            'electricity_usage' => 100,
            'electricity_cost' => 350000,
            'water_usage' => 5,
            'water_cost' => 75000,
            'service_cost' => 150000,
            'total_amount' => 4075000,
            'status' => 'pending',
            'vietqr_url' => 'https://img.vietqr.io/image/MB-999988889999-compact.png',
        ]);

        // Giả lập webhook thanh toán PayOS đối soát thành công (Nội dung chuyển khoản chứa SRM + Bill ID)
        $webhookPayload = [
            'code' => '00',
            'desc' => 'success',
            'data' => [
                'orderCode' => 12345,
                'amount' => 4075000,
                'description' => "SRM{$bill->id} Thanh toan tien phong 102",
                'reference' => 'REF' . time(),
            ],
        ];

        $response = $this->postJson('/api/webhooks/payments', $webhookPayload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Kiểm tra Bill đã đổi sang trạng thái paid
        $bill->refresh();
        $this->assertEquals('paid', $bill->status);
        $this->assertNotNull($bill->payment_date);

        // Kiểm tra Hóa đơn điện tử đã tự động được xuất trong database
        $invoice = ElectronicInvoice::where('bill_id', $bill->id)->first();
        $this->assertNotNull($invoice, 'Hóa đơn điện tử phải được tự động sinh ra khi thanh toán thành công');
        $this->assertEquals($tenant->id, $invoice->tenant_id);
        $this->assertEquals($room->id, $invoice->room_id);
        $this->assertEquals($resident->name, $invoice->buyer_name);
        $this->assertEquals('CQT_ACCEPTED', $invoice->cqt_status);
        $this->assertEquals(34, strlen($invoice->tax_authority_code));
        $this->assertNotEmpty($invoice->lookup_code);
        $this->assertNotEmpty($invoice->xml_content);

        // Kiểm tra chi tiết items trong hóa đơn
        $this->assertIsArray($invoice->items);
        $this->assertCount(4, $invoice->items); // Tiền phòng, điện, nước, dịch vụ
    }

    /**
     * Kiểm tra tự động xuất HĐĐT khi chủ trọ xác nhận thanh toán payUtility
     */
    public function test_auto_issue_einvoice_when_landlord_pays_utility(): void
    {
        [$user, $tenant] = $this->createLandlordUser();

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
            'address' => '123 Đường Số 1, Hà Nội',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 4000000,
            'status' => 'overdue',
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Lê Thị Thuê',
            'email' => 'lethithue@test.com',
            'phone' => '0977665544',
            'cccd' => '001201008888',
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $record = UtilityRecord::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-09',
            'old_electricity' => 100,
            'new_electricity' => 150,
            'old_water' => 10,
            'new_water' => 15,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        // Chủ trọ thực hiện xác nhận thu tiền
        $response = $this->actingAs($user)->post(route('smartroom.admin.utility.pay', $record->id));
        $response->assertRedirect();

        $record->refresh();
        $this->assertEquals('paid', $record->status);

        // Kiểm tra HĐĐT đã được tự động phát hành cho record này
        $invoice = ElectronicInvoice::where('utility_record_id', $record->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('CQT_ACCEPTED', $invoice->cqt_status);
        $this->assertEquals(34, strlen($invoice->tax_authority_code));
        $this->assertEquals($tenant->einvoice_config['tax_code'], $invoice->seller_tax_code);
    }

    /**
     * Kiểm tra cổng tra cứu hóa đơn điện tử công khai
     */
    public function test_public_invoice_lookup_portal(): void
    {
        [$user, $tenant] = $this->createLandlordUser();

        $invoice = ElectronicInvoice::create([
            'tenant_id' => $tenant->id,
            'invoice_template' => '1',
            'invoice_series' => 'C26TAA',
            'invoice_symbol' => '1C26TAA',
            'invoice_number' => '00000088',
            'tax_authority_code' => '007926ABCD1234567890ABCDEF12345678',
            'lookup_code' => 'SRM-LOOKUP88',
            'provider' => 'mock',
            'status' => 'issued',
            'cqt_status' => 'CQT_ACCEPTED',
            'seller_tax_code' => '0101234567-001',
            'seller_name' => 'Cơ sở lưu trú SmartRoom',
            'buyer_name' => 'Hoàng Khách Thuê',
            'subtotal_amount' => 3000000,
            'tax_rate' => 8,
            'tax_amount' => 240000,
            'total_amount' => 3240000,
            'total_amount_in_words' => 'Ba triệu hai trăm bốn mươi nghìn đồng',
            'issue_date' => now(),
            'signed_at' => now(),
        ]);

        // 1. Tra cứu bằng mã bí mật lookup_code
        $response = $this->get(route('einvoice.lookup', ['code' => 'SRM-LOOKUP88']));
        $response->assertStatus(200);
        $response->assertSee('00000088');
        $response->assertSee('007926ABCD1234567890ABCDEF12345678');
        $response->assertSee('Hoàng Khách Thuê');

        // 2. Tra cứu bằng mã không tồn tại
        $failResponse = $this->get(route('einvoice.lookup', ['code' => 'INVALID_CODE']));
        $failResponse->assertStatus(200);
        $failResponse->assertSee('Không Tìm Thấy Hóa Đơn');
    }

    /**
     * Kiểm tra xem Bản thể hiện chuẩn NĐ 123 và tải tệp XML ký số
     */
    public function test_view_representation_and_download_xml(): void
    {
        [$user, $tenant] = $this->createLandlordUser();

        $invoice = ElectronicInvoice::create([
            'tenant_id' => $tenant->id,
            'invoice_template' => '1',
            'invoice_series' => 'C26TAA',
            'invoice_symbol' => '1C26TAA',
            'invoice_number' => '00000099',
            'tax_authority_code' => '00792699991234567890ABCDEF12345678',
            'lookup_code' => 'SRM-VIEW99',
            'provider' => 'mock',
            'status' => 'issued',
            'cqt_status' => 'CQT_ACCEPTED',
            'seller_tax_code' => '0101234567-001',
            'seller_name' => 'Cơ sở lưu trú SmartRoom',
            'buyer_name' => 'Nguyễn Thị Hoa',
            'subtotal_amount' => 2000000,
            'tax_rate' => 8,
            'tax_amount' => 160000,
            'total_amount' => 2160000,
            'total_amount_in_words' => 'Hai triệu một trăm sáu mươi nghìn đồng',
            'items' => [
                ['name' => 'Tiền phòng', 'unit' => 'Tháng', 'quantity' => 1, 'price' => 2000000, 'amount' => 2000000, 'vat_rate' => 8, 'vat_amount' => 160000]
            ],
            'issue_date' => now(),
            'signed_at' => now(),
            'xml_content' => '<HDon><DLHDon><SHDon>00000099</SHDon></DLHDon></HDon>',
        ]);

        // Xem bản thể hiện HĐĐT qua route admin
        $viewResponse = $this->actingAs($user)->get(route('smartroom.admin.einvoices.view', $invoice->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('HÓA ĐƠN GIÁ TRỊ GIA TĂNG');
        $viewResponse->assertSee('00792699991234567890ABCDEF12345678');
        $viewResponse->assertSee('ĐÃ KÝ ĐIỆN TỬ HỢP LỆ');

        // Xem bản thể hiện qua route tra cứu công khai (không cần login)
        $publicView = $this->get(route('einvoice.public.view', $invoice->lookup_code));
        $publicView->assertStatus(200);
        $publicView->assertSee('HÓA ĐƠN GIÁ TRỊ GIA TĂNG');

        // Tải XML ký số
        $xmlResponse = $this->actingAs($user)->get(route('smartroom.admin.einvoices.xml', $invoice->id));
        $xmlResponse->assertStatus(200);
        $this->assertEquals('application/xml', $xmlResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('00000099', $xmlResponse->getContent());
    }

    /**
     * Kiểm tra cập nhật cấu hình nhà cung cấp e-Invoice
     */
    public function test_landlord_updates_einvoice_config(): void
    {
        [$user, $tenant] = $this->createLandlordUser();

        $updateData = [
            'provider' => 'misa',
            'tax_code' => '0312345678-002',
            'company_name' => 'CÔNG TY TNHH BẤT ĐỘNG SẢN SMARTROOM',
            'address' => 'Tòa Landmark 81, TP.HCM',
            'template_symbol' => '1C26TBB',
            'invoice_series' => 'C26TBB',
            'api_endpoint' => 'https://api.meinvoice.vn/api/v3',
            'app_id' => 'smartroom-app-id',
            'secret_key' => 'misa_secret_key_123',
            'tax_rate' => 10,
            'auto_issue_on_payment' => true,
            'auto_send_email' => true,
            'auto_send_zalo' => true,
        ];

        $response = $this->actingAs($user)->post(route('smartroom.admin.einvoices.config'), $updateData);
        $response->assertRedirect();

        $tenant->refresh();
        $this->assertEquals('misa', $tenant->einvoice_config['provider']);
        $this->assertEquals('0312345678-002', $tenant->einvoice_config['tax_code']);
        $this->assertEquals('1C26TBB', $tenant->einvoice_config['template_symbol']);
        $this->assertEquals(10, $tenant->einvoice_config['tax_rate']);
    }
}
