<?php

namespace Tests\Feature;

use App\Models\LandlordVerificationRequest;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KycVerificationFeature09Test extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $landlord;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private_documents');
        Storage::fake('local');

        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ / Quản lý']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin hệ thống']);

        $this->tenant = Tenant::create([
            'name' => 'Nhà Trọ Thanh Hiền SmartRoom',
            'email' => 'thanhhien@example.com',
            'verification_status' => 'unverified',
            'listing_badge' => '',
            'onboarding_step' => 1,
        ]);

        $landlordRole = Role::where('slug', 'landlord')->first();
        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Nguyễn Thanh Hiền',
            'username' => 'thanhhien_landlord',
            'email' => 'thanhhien@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'landlord',
        ]);
    }

    /**
     * ERR_09_01: Để trống số CCCD
     */
    public function test_err_09_01_requires_cccd_number(): void
    {
        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '',
            'cccd_front' => UploadedFile::fake()->create('front.jpg', 500, 'image/jpeg'),
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_number' => 'Vui lòng nhập số Căn cước công dân của chủ cơ sở.']);
    }

    /**
     * ERR_09_02: Số CCCD không đúng 12 chữ số
     */
    public function test_err_09_02_requires_exact_12_digits_cccd(): void
    {
        // Thử với 5 số
        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '12345',
            'cccd_front' => UploadedFile::fake()->create('front.jpg', 500, 'image/jpeg'),
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_number' => 'Số Căn cước công dân phải gồm chính xác 12 chữ số.']);

        // Thử với ký tự chữ
        $response2 = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '07920100abcd',
            'cccd_front' => UploadedFile::fake()->create('front.jpg', 500, 'image/jpeg'),
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response2->assertSessionHasErrors(['cccd_number' => 'Số Căn cước công dân phải gồm chính xác 12 chữ số.']);
    }

    /**
     * ERR_09_03: Chưa tải ảnh mặt trước CCCD
     */
    public function test_err_09_03_requires_front_image(): void
    {
        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '079201001234',
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_front' => 'Vui lòng tải lên hình ảnh chụp mặt trước Căn cước công dân.']);
    }

    /**
     * ERR_09_04: Chưa tải ảnh mặt sau CCCD
     */
    public function test_err_09_04_requires_back_image(): void
    {
        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '079201001234',
            'cccd_front' => UploadedFile::fake()->create('front.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_back' => 'Vui lòng tải lên hình ảnh chụp mặt sau Căn cước công dân.']);
    }

    /**
     * ERR_09_05: Tải tệp không đúng định dạng
     */
    public function test_err_09_05_rejects_invalid_file_extension(): void
    {
        $invalidFile = UploadedFile::fake()->create('malicious.exe', 500, 'application/x-msdownload');

        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '079201001234',
            'cccd_front' => $invalidFile,
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_front' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.']);
    }

    /**
     * ERR_09_06: Dung lượng tệp vượt quá 10MB
     */
    public function test_err_09_06_rejects_file_over_10mb(): void
    {
        $oversizedFile = UploadedFile::fake()->create('huge_front.jpg', 12000, 'image/jpeg');

        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '079201001234',
            'cccd_front' => $oversizedFile,
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors(['cccd_front' => 'Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).']);
    }

    /**
     * ERR_09_07 & DoD Happy Case: Gửi hồ sơ KYC thành công & Zero Trust Checksum
     */
    public function test_err_09_07_happy_case_submits_kyc_successfully(): void
    {
        $frontImage = UploadedFile::fake()->create('cccd_front.jpg', 800, 'image/jpeg');
        $backImage = UploadedFile::fake()->create('cccd_back.jpg', 800, 'image/jpeg');

        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'id_card_number' => '079201001234',
            'front_image' => $frontImage,
            'back_image' => $backImage,
            'bank_name' => 'Vietcombank',
            'bank_account_no' => '9988776655',
            'bank_account_name' => 'NGUYEN THANH HIEN',
        ]);

        $response->assertRedirect(route('smartroom.admin'));
        $response->assertSessionHas('success', 'Hồ sơ xác minh KYC đã được gửi thành công! Vui lòng chờ Admin duyệt.');

        // Kiểm tra cơ sở dữ liệu
        $this->assertEquals('kyc_pending', $this->tenant->fresh()->verification_status);

        $verification = LandlordVerificationRequest::where('tenant_id', $this->tenant->id)
            ->where('type', 'kyc')
            ->first();

        $this->assertNotNull($verification);
        $this->assertEquals('pending', $verification->status);
        $this->assertEquals('079201001234', $verification->cccd_number);

        // Kiểm tra lưu tài liệu bảo mật và checksum SHA-256
        $documents = $verification->documents;
        $this->assertCount(2, $documents);
        foreach ($documents as $doc) {
            $this->assertNotNull($doc->sha256_checksum);
            $this->assertEquals(64, strlen($doc->sha256_checksum));
        }
    }

    /**
     * Kiểm tra tương thích route /smartroom/admin/verification/kyc
     */
    public function test_compatibility_with_existing_verification_route(): void
    {
        $response = $this->actingAs($this->landlord)->post('/smartroom/admin/verification/kyc', [
            'cccd_number' => '079201009999',
            'cccd_front' => UploadedFile::fake()->create('front.png', 500, 'image/png'),
            'cccd_back' => UploadedFile::fake()->create('back.png', 500, 'image/png'),
        ]);

        $response->assertRedirect(route('smartroom.admin'));
        $response->assertSessionHas('success', 'Hồ sơ xác minh KYC đã được gửi thành công! Vui lòng chờ Admin duyệt.');
    }

    /**
     * Kiểm tra endpoint GET /smartroom/admin/kyc/status
     */
    public function test_kyc_status_endpoint(): void
    {
        // Khi chưa gửi KYC
        $response = $this->actingAs($this->landlord)->getJson('/smartroom/admin/kyc/status');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'verification_status' => 'unverified',
                'current_request' => null,
            ],
        ]);

        // Sau khi gửi KYC
        $this->actingAs($this->landlord)->post('/smartroom/admin/kyc/submit', [
            'cccd_number' => '079201008888',
            'cccd_front' => UploadedFile::fake()->create('front.jpg', 500, 'image/jpeg'),
            'cccd_back' => UploadedFile::fake()->create('back.jpg', 500, 'image/jpeg'),
        ]);

        $responseAfter = $this->actingAs($this->landlord)->getJson('/smartroom/admin/kyc/status');
        $responseAfter->assertOk();
        $responseAfter->assertJson([
            'success' => true,
            'data' => [
                'verification_status' => 'kyc_pending',
            ],
        ]);
        $this->assertNotNull($responseAfter->json('data.current_request.id'));
    }

    /**
     * Kiểm tra truy cập trigger /smartroom/admin/kyc
     */
    public function test_kyc_settings_page_redirect(): void
    {
        $response = $this->actingAs($this->landlord)->get('/smartroom/admin/kyc');
        $response->assertRedirect(route('smartroom.admin') . '#verification-section');
    }
}
