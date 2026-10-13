<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Building;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilityRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentManagementFeature29Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated payment feature tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    /**
     * Kịch bản 1: Để trống số tiền và bấm Xác nhận -> Báo lỗi đỏ 'Vui lòng nhập số tiền thanh toán'
     */
    public function test_empty_payment_amount_returns_validation_error(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        $response = $this->actingAs($user)->postJson(route('admin.payments.confirm'), [
            'invoice_id' => $record->id,
            'payment_amount' => '',
            'payment_method' => 'Tiền mặt',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'field' => 'payment_amount',
            'message' => 'Vui lòng nhập số tiền thanh toán',
        ]);
    }

    /**
     * Kịch bản Bấm cập nhật nhưng chưa chọn phương thức -> Mã lỗi ERR_29_01
     */
    public function test_missing_payment_method_returns_err_29_01(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        $response = $this->actingAs($user)->postJson(route('admin.payments.confirm'), [
            'invoice_id' => $record->id,
            'payment_amount' => '3500000',
            'payment_method' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'code' => 'ERR_29_01',
            'field' => 'payment_method',
            'message' => 'Vui lòng chọn hình thức nhận tiền (Tiền mặt hoặc Chuyển khoản).',
        ]);
    }

    /**
     * Kịch bản 2: Nhập số tiền 10 triệu trong khi nợ chỉ 2.230.000 đ
     */
    public function test_payment_amount_exceeding_remaining_debt_returns_error(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        $response = $this->actingAs($user)->postJson(route('admin.payments.confirm'), [
            'invoice_id' => $record->id,
            'payment_amount' => '10000000',
            'payment_method' => 'Tiền mặt',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'field' => 'payment_amount',
        ]);
        $this->assertStringContainsString('Số tiền thanh toán không được vượt quá số tiền nợ còn lại', $response->json('message'));
    }

    /**
     * Kịch bản 3: Happy case - Nhập đúng số tiền nợ và xác nhận -> Gạch nợ thành công, chuyển trạng thái sang Đã thanh toán (ERR_29_02), tự động sinh phiếu thu Sổ quỹ
     */
    public function test_valid_payment_confirmation_happy_case(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        // Tính nợ: room 2.000.000 + dien (20-10)*3500=35.000 + nuoc (8-5)*15000=45.000 + dv 150.000 = 2.230.000
        $validAmount = 2230000;

        $response = $this->actingAs($user)->postJson(route('admin.payments.confirm'), [
            'invoice_id' => $record->id,
            'payment_amount' => $validAmount,
            'payment_method' => 'Chuyển khoản VietQR',
            'transaction_ref' => 'FT2610130099',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'code' => 'ERR_29_02',
            'message' => 'Đã ghi nhận thanh toán tiền phòng thành công!',
            'status' => 'paid',
            'remaining_debt' => 0,
        ]);

        // Kiểm tra cơ sở dữ liệu UtilityRecord
        $this->assertDatabaseHas('utility_records', [
            'id' => $record->id,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'transaction_ref' => 'FT2610130099',
        ]);

        // Kiểm tra tự động sinh phiếu thu trong Sổ quỹ (Transaction)
        $this->assertDatabaseHas('transactions', [
            'tenant_id' => $user->tenant_id,
            'type' => 'income',
            'amount' => $validAmount,
            'category' => 'rental',
        ]);

        // Kiểm tra phòng cập nhật trạng thái từ overdue sang occupied
        $this->assertSame('occupied', $room->fresh()->status);
    }

    /**
     * Kịch bản Bấm nút Xuất CSV đối soát -> Tải xuống tệp CSV định dạng UTF-8 thành công
     */
    public function test_export_csv_reconciliation(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        $response = $this->actingAs($user)->get(route('admin.payments.export', [
            'billing_month' => '2026-06',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('P.101', $content);
        $this->assertStringContainsString('Nguyen Van A', $content);
        $this->assertStringContainsString('2230000', $content);
    }

    /**
     * Kiểm tra bảo toàn phân quyền Multi-tenancy (không gạch nợ chéo sang tenant khác)
     */
    public function test_multi_tenancy_cannot_confirm_other_tenant_invoice(): void
    {
        [$user, $room, $record] = $this->setupTestData();

        // Tạo một tenant khác
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'email' => 'other-tenant@example.com',
        ]);

        $otherRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chu tro']);
        $otherUser = User::create([
            'tenant_id' => $otherTenant->id,
            'role_id' => $otherRole->id,
            'name' => 'Other Landlord',
            'username' => 'other-landlord',
            'email' => 'other-landlord@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($otherUser)->postJson(route('admin.payments.confirm'), [
            'invoice_id' => $record->id,
            'payment_amount' => 2230000,
            'payment_method' => 'Tiền mặt',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('utility_records', [
            'id' => $record->id,
            'status' => 'paid',
        ]);
    }

    private function setupTestData(): array
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Cầu Giấy',
            'email' => 'tenant-test-payments@example.com',
            'bank_name' => 'MB',
            'bank_account_no' => '999988880001',
            'bank_account_name' => 'TRAN VAN HOANG',
            'verification_status' => 'kyc_verified',
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chủ trọ',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Chủ trọ Test',
            'username' => 'landlord-feat29',
            'email' => 'landlord-feat29@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
            'address' => 'Số 10 Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'overdue',
            'price' => 2000000,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Nguyen Van A',
            'phone' => '0912345678',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        $record = UtilityRecord::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-06',
            'old_electricity' => 10,
            'new_electricity' => 20,
            'old_water' => 5,
            'new_water' => 8,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        Bill::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-06',
            'room_price' => 2000000,
            'electricity_usage' => 10,
            'electricity_cost' => 35000,
            'water_usage' => 3,
            'water_cost' => 45000,
            'service_cost' => 150000,
            'total_amount' => 2230000,
            'status' => 'pending',
        ]);

        return [$user, $room, $record];
    }
}
