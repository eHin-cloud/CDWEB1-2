<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm thử tính năng 19: Sổ quỹ Thu - Chi tài chính (Financial Ledger - FEAT_19_LEDGER)
 * Bảng 130 – 135 trong báo cáo Phân hệ B (Nguyễn Anh Quý)
 */
class FinancialLedgerFeature19Test extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $landlord;

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
    }

    /**
     * ERR_19_01: Để trống số tiền giao dịch
     */
    public function test_err_19_01_empty_amount_validation()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'income',
                'amount' => '',
                'category' => 'Tiền phòng',
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_19_01',
                'message' => 'Vui lòng nhập số tiền phát sinh thu hoặc chi.',
                'field' => 'amount',
            ]);
    }

    /**
     * ERR_19_02: Nhập số tiền bằng 0 hoặc số âm
     */
    public function test_err_19_02_zero_or_negative_amount_validation()
    {
        // Kiểm tra số tiền bằng 0
        $responseZero = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'income',
                'amount' => 0,
                'category' => 'Tiền phòng',
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

        $responseZero->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_19_02',
                'message' => 'Số tiền giao dịch phải là số nguyên dương lớn hơn 0 đồng.',
                'field' => 'amount',
            ]);

        // Kiểm tra số tiền âm
        $responseNegative = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'expense',
                'amount' => -200000,
                'category' => 'Sửa chữa',
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

        $responseNegative->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_19_02',
                'message' => 'Số tiền giao dịch phải là số nguyên dương lớn hơn 0 đồng.',
                'field' => 'amount',
            ]);
    }

    /**
     * ERR_19_03: Để trống danh mục giao dịch
     */
    public function test_err_19_03_empty_category_validation()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'income',
                'amount' => 5000000,
                'category' => '',
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_19_03',
                'message' => 'Vui lòng chọn danh mục giao dịch (Tiền phòng, Sửa chữa, Vệ sinh...).',
                'field' => 'category',
            ]);
    }

    /**
     * ERR_19_04: Để trống ngày ghi nhận giao dịch
     */
    public function test_err_19_04_empty_date_validation()
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'income',
                'amount' => 5000000,
                'category' => 'Tiền phòng',
                'date' => '',
                'transaction_date' => '',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_19_04',
                'message' => 'Ngày ghi nhận giao dịch không được để trống.',
                'field' => 'date',
            ]);
    }

    /**
     * ERR_19_05: Ghi nhận giao dịch vào sổ quỹ thành công
     */
    public function test_err_19_05_store_transaction_success_and_ledger_update()
    {
        // 1. Tạo khoản thu
        $responseIncome = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'income',
                'amount' => 6000000,
                'category' => 'Tiền phòng',
                'date' => Carbon::now()->format('Y-m-d'),
                'note' => 'Thu tiền phòng 201 tháng 10',
            ]);

        $responseIncome->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_19_05',
                'message' => 'Ghi nhận giao dịch vào sổ quỹ thành công!',
            ]);

        // 2. Tạo khoản chi
        $responseExpense = $this->actingAs($this->landlord)
            ->postJson(route('admin.reports.transaction.store'), [
                'type' => 'expense',
                'amount' => 1500000,
                'category' => 'Sửa chữa',
                'date' => Carbon::now()->format('Y-m-d'),
                'note' => 'Thay vòi sen phòng 302',
            ]);

        $responseExpense->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_19_05',
                'message' => 'Ghi nhận giao dịch vào sổ quỹ thành công!',
            ]);

        // Kiểm tra cơ sở dữ liệu
        $this->assertDatabaseHas('transactions', [
            'tenant_id' => $this->tenant->id,
            'type' => 'income',
            'amount' => 6000000,
            'category' => 'Tiền phòng',
            'description' => 'Thu tiền phòng 201 tháng 10',
        ]);

        $this->assertDatabaseHas('transactions', [
            'tenant_id' => $this->tenant->id,
            'type' => 'expense',
            'amount' => 1500000,
            'category' => 'Sửa chữa',
            'description' => 'Thay vòi sen phòng 302',
        ]);
    }

    /**
     * Kiểm tra endpoint GET /smartroom/admin/reports/cash-flow (Bảng 131)
     */
    public function test_cash_flow_endpoint_returns_json_summary()
    {
        // Tạo trước 2 giao dịch
        Transaction::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'income',
            'amount' => 10000000,
            'category' => 'Tiền phòng',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'expense',
            'amount' => 3000000,
            'category' => 'Sửa chữa',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->landlord)
            ->getJson(route('admin.reports.cash_flow'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_income' => 10000000,
                'total_expense' => 3000000,
                'net_profit' => 7000000,
            ]);
    }

    /**
     * Kiểm tra endpoint GET /smartroom/admin/reports/export-excel (Bảng 131)
     */
    public function test_export_excel_returns_csv_stream_with_utf8_bom()
    {
        Transaction::create([
            'tenant_id' => $this->tenant->id,
            'type' => 'income',
            'amount' => 5000000,
            'category' => 'Tiền phòng',
            'description' => 'Khách phòng 101 đóng tiền mặt',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->landlord)
            ->get(route('admin.reports.export_excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('STT', $content);
        $this->assertStringContainsString('Ngày ghi nhận', $content);
        $this->assertStringContainsString('Phiếu Thu (+)', $content);
        $this->assertStringContainsString('Tiền phòng', $content);
    }

    /**
     * Kiểm tra render UI các thành phần Bảng 134 và route alias ledger
     */
    public function test_ui_elements_render_correctly_table_134()
    {
        $response = $this->actingAs($this->landlord)
            ->get('/smartroom/admin/ledger');

        $response->assertStatus(200);
        $response->assertSee('id="rdoType"', false);
        $response->assertSee('id="amount"', false);
        $response->assertSee('id="category"', false);
        $response->assertSee('id="btnSaveLedger"', false);
        $response->assertSee('id="btnExportExcel"', false);
    }
}
