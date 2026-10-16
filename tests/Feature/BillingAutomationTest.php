<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm thử Tính năng 17: Tính tiền phòng & Xuất Hóa đơn tự động (FEAT_17_BILLING)
 * Đặc tả theo Bảng 118 – 123 tài liệu NhomC_baocao_2026102.docx.
 * 
 * Bảng kịch bản lỗi (Bảng 121):
 * - ERR_17_01: Phòng chưa có cư dân đại diện thuê ("Phòng này chưa có cư dân đại diện thuê, không thể sinh hóa đơn thu tiền.")
 * - ERR_17_02: Đơn giá dịch vụ bị trống hoặc bằng 0 ("Đơn giá điện nước chưa được cấu hình trong bảng giá cơ sở.")
 * - ERR_17_03: Sinh hóa đơn tính tiền thành công ("Hóa đơn tiền nhà tháng đã được tính toán và tạo tự động thành công!")
 * 
 * Bảng 123 Kịch bản kiểm thử:
 * - Để trống hạn thanh toán ("Vui lòng chọn hạn chót thanh toán")
 * - Chọn hạn thanh toán trước ngày lập hóa đơn ("Hạn thanh toán không được trước ngày lập hóa đơn")
 * 
 * Bảng 122 UI Elements:
 * - summary_card, due_date, qr_code, btnPublish
 */
class BillingAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $landlord;
    protected Building $building;
    protected Room $room1;
    protected Room $roomEmpty;
    protected Resident $resident1;
    protected UtilityRecord $utilityRecord1;

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

        // Phòng 201 có cư dân đại diện thuê
        $this->room1 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 4500000,
            'status' => 'occupied',
            'electric_meter_serial' => 'EM-101',
            'water_meter_serial' => 'WM-101',
        ]);

        $this->resident1 = Resident::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room1->id,
            'name' => 'Nguyễn Văn A',
            'phone' => '0912345678',
            'email' => 'nguyenvana@example.com',
            'status' => 'active',
            'start_date' => Carbon::now()->subMonths(3)->toDateString(),
        ]);

        $currentMonth = Carbon::now()->format('Y-m');
        $this->utilityRecord1 = UtilityRecord::create([
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room1->id,
            'billing_month' => $currentMonth,
            'old_electricity' => 100,
            'new_electricity' => 150, // tiêu thụ 50 kWh
            'old_water' => 20,
            'new_water' => 25, // tiêu thụ 5 m3
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        // Phòng 202 trống, chưa có cư dân đại diện
        $this->roomEmpty = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '202',
            'floor' => 2,
            'price' => 4800000,
            'status' => 'empty',
        ]);
    }

    /**
     * Test Bảng 123 STT 1: Để trống hạn thanh toán và bấm Phát hành -> Báo lỗi đỏ.
     */
    public function test_date_required_validation(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->room1->id,
                'invoice_date' => Carbon::now()->toDateString(),
                'due_date' => '',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_17_DATE_REQUIRED',
                'message' => 'Vui lòng chọn hạn chót thanh toán',
            ]);
    }

    /**
     * Test Bảng 123 STT 2: Chọn hạn thanh toán trước ngày lập hóa đơn -> Báo lỗi.
     */
    public function test_date_range_validation(): void
    {
        $invoiceDate = Carbon::now()->toDateString();
        $dueDateBefore = Carbon::now()->subDays(2)->toDateString();

        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->room1->id,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDateBefore,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_17_DATE_RANGE',
                'message' => 'Hạn thanh toán không được trước ngày lập hóa đơn',
            ]);
    }

    /**
     * Test ERR_17_01: Phòng chưa có cư dân thuê đại diện -> Hiển thị cảnh báo vàng chặn xuất hóa đơn.
     */
    public function test_err_17_01_room_without_active_resident(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->roomEmpty->id,
                'invoice_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_17_01',
                'message' => 'Phòng này chưa có cư dân đại diện thuê, không thể sinh hóa đơn thu tiền.',
            ]);
    }

    /**
     * Test ERR_17_02: Đơn giá dịch vụ bị trống hoặc bằng 0 -> Bôi đỏ trường đơn giá.
     */
    public function test_err_17_02_zero_or_empty_service_price(): void
    {
        // 1. Trường hợp đơn giá điện bằng 0
        $responseElec = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->room1->id,
                'invoice_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'electricity_price' => 0,
                'water_price' => 15000,
            ]);

        $responseElec->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_17_02',
                'message' => 'Đơn giá điện nước chưa được cấu hình trong bảng giá cơ sở.',
            ]);

        // 2. Trường hợp đơn giá nước rỗng hoặc <= 0
        $responseWater = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->room1->id,
                'invoice_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'electricity_price' => 3500,
                'water_price' => 0,
            ]);

        $responseWater->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_17_02',
                'message' => 'Đơn giá điện nước chưa được cấu hình trong bảng giá cơ sở.',
            ]);
    }

    /**
     * Test ERR_17_03: Sinh hóa đơn tính tiền thành công -> Chuyển sang 'Chờ thanh toán', sinh VietQR chuẩn Napas247.
     */
    public function test_err_17_03_successful_invoice_calculation(): void
    {
        $invoiceDate = Carbon::now()->toDateString();
        $dueDate = Carbon::now()->addDays(5)->toDateString();

        // Tiêu thụ từ UtilityRecord: điện 50 kWh * 3500 = 175,000đ
        // Nước 5 m3 * 15000 = 75,000đ
        // Phí dịch vụ cố định = 150,000đ
        // Tiền phòng = 4,500,000đ
        // Phí phát sinh (other_fees) = 50,000đ
        // Tổng tiền kỳ vọng: 4,500,000 + 175,000 + 75,000 + 150,000 + 50,000 = 4,950,000đ
        $expectedTotal = 4500000 + (50 * 3500) + (5 * 15000) + 150000 + 50000;

        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.utility.calculate'), [
                'room_id' => $this->room1->id,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'electricity_price' => 3500,
                'water_price' => 15000,
                'other_fees' => 50000,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_17_03',
                'message' => 'Hóa đơn tiền nhà tháng đã được tính toán và tạo tự động thành công!',
                'data' => [
                    'room_id' => $this->room1->id,
                    'room_number' => '201',
                    'resident_name' => 'Nguyễn Văn A',
                    'total_amount' => $expectedTotal,
                    'status' => 'pending',
                    'status_label' => 'Chờ thanh toán',
                ],
            ]);

        // Kiểm tra mã VietQR chứa đúng số tiền
        $vietqrUrl = $response->json('data.vietqr_url');
        $this->assertStringContainsString("amount={$expectedTotal}", $vietqrUrl);
        $this->assertStringContainsString('https://img.vietqr.io/image/VCB-1051572297-compact.png', $vietqrUrl);

        // Kiểm tra cơ sở dữ liệu bảng bills
        $this->assertDatabaseHas('bills', [
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room1->id,
            'total_amount' => $expectedTotal,
            'status' => 'pending',
        ]);

        // Phòng chuyển sang trạng thái overdue (chờ thanh toán nợ)
        $this->room1->refresh();
        $this->assertEquals('overdue', $this->room1->status);
    }

    /**
     * Test Bảng 119: Endpoint VietQR và xuất Hóa đơn PDF.
     */
    public function test_endpoints_vietqr_and_invoice_pdf(): void
    {
        // 1. Endpoint xuất VietQR
        $responseQr = $this->actingAs($this->landlord)
            ->getJson(route('smartroom.admin.utility.vietqr', $this->utilityRecord1->id));

        $responseQr->assertStatus(200)
            ->assertJson([
                'success' => true,
                'bank_name' => 'Vietcombank (VCB)',
                'account_number' => '1051572297',
                'account_holder' => 'CHỦ TRỌ',
            ]);

        // 2. Endpoint xuất Hóa đơn PDF
        $responsePdf = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin.utility.invoice_pdf', $this->utilityRecord1->id));

        $responsePdf->assertStatus(200);

        // 3. Endpoint trang hóa đơn /smartroom/admin/invoices
        $responseInvoices = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin.invoices'));

        $responseInvoices->assertRedirect(route('smartroom.admin', ['tab' => 'utility-section']));
    }

    /**
     * Test Bảng 122: Các thành phần UI Hóa đơn hiển thị chuẩn xác trong trang Admin.
     */
    public function test_ui_elements_render_correctly(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin'));

        $response->assertStatus(200);

        // STT 1: summary_card (Tổng tiền thanh toán)
        $response->assertSee('id="summary_card"', false);

        // STT 2: due_date (DatePicker Hạn thanh toán *)
        $response->assertSee('id="due_date"', false);

        // STT 3: qr_code (Mã VietQR thanh toán)
        $response->assertSee('id="qr_code"', false);

        // STT 4: btnPublish (Nút Phát hành hóa đơn)
        $response->assertSee('id="btnPublish"', false);
    }
}
