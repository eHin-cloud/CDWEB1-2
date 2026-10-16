<?php

namespace Tests\Feature;

use App\Models\LandlordVerificationDocument;
use App\Models\LandlordVerificationRequest;
use App\Models\NotificationLog;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature Test FEAT_10_ADMIN_KYC:
 * Kiểm duyệt hồ sơ Chủ trọ & Cấp Tích Xanh (Admin Verification)
 * Thành viên phụ trách: Nguyễn Thanh Hiền
 */
class AdminKycVerificationFeature10Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $landlord;
    private Tenant $tenant;
    private LandlordVerificationRequest $verificationRequest;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Quản trị viên Superadmin']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Quản trị viên hệ thống']);
        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);
        Role::firstOrCreate(['slug' => 'unverified_landlord'], ['name' => 'Chủ trọ chưa xác minh']);
        Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);

        $adminRole = Role::where('slug', 'superadmin')->first();
        $this->admin = User::create([
            'name' => 'Superadmin Nguyễn Thanh Hiền',
            'username' => 'superadmin_hien',
            'email' => 'admin_hien@smartroom.vn',
            'password' => Hash::make('secret123'),
            'role' => 'superadmin',
            'role_id' => $adminRole->id,
        ]);

        $this->tenant = Tenant::create([
            'name' => 'Khu Trọ An Cư SmartRoom',
            'email' => 'ancu@example.com',
            'verification_status' => 'pending',
            'listing_badge' => 'unverified',
            'boost_score' => 0,
            'onboarding_step' => 1,
        ]);

        $landlordRole = Role::where('slug', 'unverified_landlord')->first();
        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Trần Văn Chủ Trọ',
            'username' => 'landlord_tran',
            'email' => 'chutro_tran@example.com',
            'phone' => '0912345678',
            'password' => Hash::make('secret123'),
            'role' => 'unverified_landlord',
        ]);

        $this->verificationRequest = LandlordVerificationRequest::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->landlord->id,
            'type' => 'kyc',
            'status' => 'pending',
            'cccd_number' => '079201001234',
            'admin_review_consent_given' => true,
            'admin_review_consent_at' => now(),
        ]);

        LandlordVerificationDocument::create([
            'verification_request_id' => $this->verificationRequest->id,
            'document_type' => 'cccd_front',
            'file_path' => 'landlord-verifications/' . $this->tenant->id . '/front.jpg',
            'status' => 'pending',
        ]);

        LandlordVerificationDocument::create([
            'verification_request_id' => $this->verificationRequest->id,
            'document_type' => 'cccd_back',
            'file_path' => 'landlord-verifications/' . $this->tenant->id . '/back.jpg',
            'status' => 'pending',
        ]);
    }

    /**
     * Kịch bản 1: Phân quyền - Khách hoặc chủ trọ truy cập /admin/kyc/pending hoặc /admin/verifications bị 403 Forbidden
     */
    public function test_non_admin_access_returns_403_forbidden(): void
    {
        // 1.1 Khách chưa đăng nhập
        $guestResponse = $this->get('/admin/kyc/pending');
        $this->assertTrue(in_array($guestResponse->status(), [302, 403], true));

        // 1.2 Chủ trọ cố tình truy cập trang kiểm duyệt KYC
        $landlordResponse = $this->actingAs($this->landlord)->get('/admin/kyc/pending');
        $landlordResponse->assertStatus(403);

        $landlordVerificationsResponse = $this->actingAs($this->landlord)->get('/admin/verifications');
        $landlordVerificationsResponse->assertStatus(403);
    }

    /**
     * Kịch bản 2: Admin truy cập GET /admin/kyc/pending thành công và hiển thị bảng tblKYC
     */
    public function test_admin_can_access_pending_verifications_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/kyc/pending');
        $response->assertStatus(200);
        $response->assertSee('tblKYC');
        $response->assertSee('btnApprove');
        $response->assertSee('btnReject');
        $response->assertSee('Trần Văn Chủ Trọ');
        $response->assertSee('Khu Trọ An Cư SmartRoom');
    }

    /**
     * Kịch bản 3: API GET /admin/kyc/pending trả về JSON danh sách hồ sơ đang chờ thẩm định
     */
    public function test_api_get_pending_verifications_json(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/admin/kyc/pending');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('data.data'));
    }

    /**
     * Kịch bản 4: DoD 1 - Bấm 'Từ chối' rồi nhấn 'Xác nhận' ngay khi ô lý do còn trống -> Báo lỗi ERR_10_01
     */
    public function test_dod_1_reject_with_empty_reason_fails_validation_err_10_01(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/kyc/{$this->verificationRequest->id}/reject", [
            'reject_reason' => '',
        ]);

        $response->assertSessionHasErrors([
            'reject_reason' => 'Vui lòng nhập lý do từ chối để chủ cơ sở nắm rõ thông tin bổ sung.',
        ]);

        // Hồ sơ vẫn phải giữ nguyên trạng thái pending
        $this->verificationRequest->refresh();
        $this->assertEquals('pending', $this->verificationRequest->status);
    }

    /**
     * Kịch bản 5: DoD 1b - Nhập lý do từ chối quá ngắn (< 10 ký tự) -> Báo lỗi ERR_10_02
     */
    public function test_dod_1b_reject_with_too_short_reason_fails_validation_err_10_02(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/kyc/{$this->verificationRequest->id}/reject", [
            'reject_reason' => 'Mờ ảnh', // 6 ký tự < 10
        ]);

        $response->assertSessionHasErrors([
            'reject_reason' => 'Nội dung lý do từ chối phải có độ dài tối thiểu 10 ký tự.',
        ]);

        $this->verificationRequest->refresh();
        $this->assertEquals('pending', $this->verificationRequest->status);
    }

    /**
     * Kịch bản 6: DoD 2 - Happy case Từ chối:
     * Nhập lý do 'Ảnh CCCD mặt sau bị mờ góc' và bấm Xác nhận
     * -> Cập nhật trạng thái 'rejected', tenant cập nhật, gửi thông báo cho chủ trọ
     */
    public function test_dod_2_reject_happy_case_updates_status_and_notifies_landlord(): void
    {
        $reason = 'Ảnh CCCD mặt sau bị mờ góc';

        $response = $this->actingAs($this->admin)->post("/admin/kyc/{$this->verificationRequest->id}/reject", [
            'reject_reason' => $reason,
        ]);

        $response->assertSessionHas('success');

        // Kiểm tra database: verification request
        $this->verificationRequest->refresh();
        $this->assertEquals('rejected', $this->verificationRequest->status);
        $this->assertEquals($reason, $this->verificationRequest->reject_reason);
        $this->assertEquals($this->admin->id, $this->verificationRequest->reviewed_by);
        $this->assertNotNull($this->verificationRequest->reviewed_at);

        // Kiểm tra tenant
        $this->tenant->refresh();
        $this->assertEquals('rejected', $this->tenant->verification_status);
        $this->assertEquals('unverified', $this->tenant->listing_badge);

        // Kiểm tra tài liệu
        foreach ($this->verificationRequest->documents as $doc) {
            $this->assertEquals('rejected', $doc->status);
        }

        // Kiểm tra NotificationLog gửi thông báo cho chủ trọ giải thích lý do
        $this->assertDatabaseHas('notification_logs', [
            'tenant_id' => $this->tenant->id,
            'type' => 'kyc_verification_rejected',
            'target_type' => 'landlord_verification_request',
            'target_id' => $this->verificationRequest->id,
        ]);
    }

    /**
     * Kịch bản 7: DoD 3 - Happy case Phê duyệt:
     * Bấm 'Phê duyệt' cho hồ sơ hợp lệ -> Cập nhật 'approved', cấp tích xanh thành công (ERR_10_04)
     */
    public function test_dod_3_approve_happy_case_grants_blue_badge_and_boost_score(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/kyc/{$this->verificationRequest->id}/approve");

        $response->assertSessionHas('success', 'Đã phê duyệt hồ sơ KYC và cấp Tích Xanh uy tín cho cơ sở thành công!');

        // Kiểm tra verification
        $this->verificationRequest->refresh();
        $this->assertEquals('approved', $this->verificationRequest->status);
        $this->assertNull($this->verificationRequest->reject_reason);
        $this->assertEquals($this->admin->id, $this->verificationRequest->reviewed_by);
        $this->assertNotNull($this->verificationRequest->reviewed_at);

        // Kiểm tra tenant: Đã cấp Tích Xanh & cộng boost_score >= 100
        $this->tenant->refresh();
        $this->assertEquals('kyc_verified', $this->tenant->verification_status);
        $this->assertEquals('kyc_verified', $this->tenant->listing_badge);
        $this->assertGreaterThanOrEqual(100, $this->tenant->boost_score);
        $this->assertNotNull($this->tenant->verified_at);
        $this->assertNotNull($this->tenant->kyc_verified_at);

        // Kiểm tra user nâng cấp từ unverified_landlord sang landlord chính thức
        $this->landlord->refresh();
        $this->assertEquals('landlord', $this->landlord->role);

        // Kiểm tra documents
        foreach ($this->verificationRequest->documents as $doc) {
            $this->assertEquals('approved', $doc->status);
        }

        // Kiểm tra NotificationLog gửi thông báo chúc mừng
        $this->assertDatabaseHas('notification_logs', [
            'tenant_id' => $this->tenant->id,
            'type' => 'kyc_verification_approved',
            'target_type' => 'landlord_verification_request',
            'target_id' => $this->verificationRequest->id,
        ]);
    }

    /**
     * Kịch bản 8: Phê duyệt qua endpoint cũ /admin/verifications/{verification}/approve vẫn tương thích 100%
     */
    public function test_compatibility_with_original_verification_routes(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/verifications/{$this->verificationRequest->id}/approve");

        $response->assertSessionHas('success');
        $this->verificationRequest->refresh();
        $this->assertEquals('approved', $this->verificationRequest->status);
    }
}
