<?php

namespace Database\Seeders;

use App\Models\LandlordProfile;
use App\Models\LandlordVerificationDocument;
use App\Models\LandlordVerificationRequest;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VerificationSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Chuẩn hóa tên cơ sở Tenant 1
        $t1 = Tenant::find(1);
        if ($t1) {
            $t1->update(['name' => 'Hệ thống Chung cư mini SmartRoom Cầu Giấy']);
        }

        // Xóa dữ liệu verification của các tenant test để tạo bộ dữ liệu đúng chuẩn đặc tả
        $targetTenantIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $targetRequestIds = LandlordVerificationRequest::whereIn('tenant_id', $targetTenantIds)->pluck('id');
        LandlordVerificationDocument::whereIn('verification_request_id', $targetRequestIds)->delete();
        LandlordVerificationRequest::whereIn('tenant_id', $targetTenantIds)->delete();

        $adminId = User::where('role', 'admin')->value('id') ?? 1;

        // Chuẩn bị sẵn file dummy trong private storage nếu cần
        $dummyDisk = 'private_documents';
        $dummyFront = 'landlord-verifications/demo_cccd_front.jpg';
        $dummyBack = 'landlord-verifications/demo_cccd_back.jpg';
        $dummyBiz = 'landlord-verifications/demo_business_registration.pdf';
        $dummyFire = 'landlord-verifications/demo_fire_safety.pdf';
        $dummySec = 'landlord-verifications/demo_security_order.pdf';

        if (!Storage::disk($dummyDisk)->exists($dummyFront)) {
            Storage::disk($dummyDisk)->put($dummyFront, 'DEMO_CCCD_FRONT_DATA');
        }
        if (!Storage::disk($dummyDisk)->exists($dummyBack)) {
            Storage::disk($dummyDisk)->put($dummyBack, 'DEMO_CCCD_BACK_DATA');
        }
        if (!Storage::disk($dummyDisk)->exists($dummyBiz)) {
            Storage::disk($dummyDisk)->put($dummyBiz, 'DEMO_BIZ_LICENSE_DATA');
        }
        if (!Storage::disk($dummyDisk)->exists($dummyFire)) {
            Storage::disk($dummyDisk)->put($dummyFire, 'DEMO_FIRE_SAFETY_DATA');
        }
        if (!Storage::disk($dummyDisk)->exists($dummySec)) {
            Storage::disk($dummyDisk)->put($dummySec, 'DEMO_SECURITY_ORDER_DATA');
        }

        // =========================================================================
        // 1. TRƯỜNG HỢP: ĐÃ CÓ TẤT CẢ (Đã duyệt cả GD1 và GD2 -> Đạt Tích Xanh)
        // =========================================================================
        $tAll = Tenant::find(1);
        $uAll = User::find(4) ?? $tAll?->users->first();
        if ($tAll && $uAll) {
            $tAll->update([
                'verification_status' => 'premium_verified',
                'listing_badge' => 'premium_verified',
                'boost_score' => 150,
                'verified_at' => Carbon::now()->subDays(5),
                'kyc_verified_at' => Carbon::now()->subDays(5),
                'premium_verified_at' => Carbon::now()->subDays(2),
            ]);

            // GD1: KYC đã duyệt
            $reqKyc1 = LandlordVerificationRequest::create([
                'tenant_id' => $tAll->id,
                'user_id' => $uAll->id,
                'type' => 'kyc',
                'cccd_number' => '001098800001',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now()->subDays(7),
                'admin_review_consent_ip' => '127.0.0.1',
                'default_document_access_revoked_at' => Carbon::now()->subDays(5),
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => Carbon::now()->subDays(5),
            ]);
            $this->attachKycDocuments($reqKyc1, 'approved', $dummyFront, $dummyBack);

            // GD2: Premium đã duyệt
            $reqPrem1 = LandlordVerificationRequest::create([
                'tenant_id' => $tAll->id,
                'user_id' => $uAll->id,
                'type' => 'premium',
                'cccd_number' => null,
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now()->subDays(4),
                'admin_review_consent_ip' => '127.0.0.1',
                'default_document_access_revoked_at' => Carbon::now()->subDays(2),
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => Carbon::now()->subDays(2),
            ]);
            $this->attachPremiumDocuments($reqPrem1, 'approved', $dummyBiz, $dummyFire, $dummySec);
        }

        // =========================================================================
        // 2. TRƯỜNG HỢP: MỚI DUYỆT GIAI ĐOẠN 1 (Đã duyệt KYC -> Đã xác minh)
        // =========================================================================
        $tGd1Only = Tenant::find(2);
        $uGd1Only = User::find(33) ?? $tGd1Only?->users->first();
        if ($tGd1Only && $uGd1Only) {
            $tGd1Only->update([
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 100,
                'verified_at' => Carbon::now()->subDays(3),
                'kyc_verified_at' => Carbon::now()->subDays(3),
            ]);

            $reqKyc2 = LandlordVerificationRequest::create([
                'tenant_id' => $tGd1Only->id,
                'user_id' => $uGd1Only->id,
                'type' => 'kyc',
                'cccd_number' => '001098800002',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now()->subDays(4),
                'admin_review_consent_ip' => '127.0.0.1',
                'default_document_access_revoked_at' => Carbon::now()->subDays(3),
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => Carbon::now()->subDays(3),
            ]);
            $this->attachKycDocuments($reqKyc2, 'approved', $dummyFront, $dummyBack);
        }

        // =========================================================================
        // 3. 5 TRƯỜNG HỢP: CHỜ DUYỆT GIAI ĐOẠN 1 (KYC Pending - Nộp CCCD)
        // =========================================================================
        $gd1Tenants = [
            3 => ['uid' => 62, 'cccd' => '001098800003'],
            4 => ['uid' => 91, 'cccd' => '001098800004'],
            5 => ['uid' => 120, 'cccd' => '001098800005'],
            6 => ['uid' => 149, 'cccd' => '001098800006'],
            7 => ['uid' => 178, 'cccd' => '001098800007'],
        ];

        foreach ($gd1Tenants as $tid => $info) {
            $t = Tenant::find($tid);
            $u = User::find($info['uid']) ?? $t?->users->first();
            if ($t && $u) {
                $t->update([
                    'verification_status' => 'pending',
                    'listing_badge' => 'unverified',
                    'boost_score' => 0,
                ]);

                $req = LandlordVerificationRequest::create([
                    'tenant_id' => $t->id,
                    'user_id' => $u->id,
                    'type' => 'kyc',
                    'cccd_number' => $info['cccd'],
                    'admin_review_consent_given' => true,
                    'admin_review_consent_at' => Carbon::now()->subHours(rand(1, 24)),
                    'admin_review_consent_ip' => '127.0.0.1',
                    'default_document_access_revoked_at' => null,
                    'status' => 'pending',
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);
                $this->attachKycDocuments($req, 'pending', $dummyFront, $dummyBack);
            }
        }

        // =========================================================================
        // 4. 5 TRƯỜNG HỢP: CHỜ DUYỆT GIAI ĐOẠN 2 (Premium Pending - Nộp PCCC, ĐKKD, ANTT)
        // =========================================================================
        $gd2Tenants = [
            8 => ['uid' => 207, 'cccd' => '001098800008'],
            9 => ['uid' => 236, 'cccd' => '001098800009'],
            10 => ['uid' => 265, 'cccd' => '001098800010'],
            14 => ['uid' => 294, 'cccd' => '001098800014'],
            15 => ['uid' => 474, 'cccd' => '001098800015'],
        ];

        foreach ($gd2Tenants as $tid => $info) {
            $t = Tenant::find($tid);
            $u = User::find($info['uid']) ?? $t?->users->first();
            if ($t && $u) {
                // Họ đã có KYC được duyệt trước đó
                $t->update([
                    'verification_status' => 'premium_pending',
                    'listing_badge' => 'kyc_verified',
                    'boost_score' => 100,
                    'kyc_verified_at' => Carbon::now()->subDays(10),
                ]);

                // Yêu cầu Premium đang chờ Admin phê duyệt để cấp Tích Xanh
                $req = LandlordVerificationRequest::create([
                    'tenant_id' => $t->id,
                    'user_id' => $u->id,
                    'type' => 'premium',
                    'cccd_number' => $info['cccd'],
                    'admin_review_consent_given' => true,
                    'admin_review_consent_at' => Carbon::now()->subHours(rand(1, 48)),
                    'admin_review_consent_ip' => '127.0.0.1',
                    'default_document_access_revoked_at' => null,
                    'status' => 'pending',
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);
                $this->attachPremiumDocuments($req, 'pending', $dummyBiz, $dummyFire, $dummySec);
            }
        }
    }

    private function attachKycDocuments(LandlordVerificationRequest $req, string $status, string $front, string $back): void
    {
        LandlordVerificationDocument::create([
            'verification_request_id' => $req->id,
            'document_type' => 'cccd_front',
            'disk' => 'private_documents',
            'file_path' => $front,
            'original_filename' => 'CCCD_Mat_Truoc.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 102400,
            'sha256_checksum' => hash('sha256', $req->id . 'front'),
            'status' => $status,
            'notes' => 'CCCD mặt trước',
        ]);

        LandlordVerificationDocument::create([
            'verification_request_id' => $req->id,
            'document_type' => 'cccd_back',
            'disk' => 'private_documents',
            'file_path' => $back,
            'original_filename' => 'CCCD_Mat_Sau.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 102400,
            'sha256_checksum' => hash('sha256', $req->id . 'back'),
            'status' => $status,
            'notes' => 'CCCD mặt sau',
        ]);
    }

    private function attachPremiumDocuments(LandlordVerificationRequest $req, string $status, string $biz, string $fire, string $sec): void
    {
        LandlordVerificationDocument::create([
            'verification_request_id' => $req->id,
            'document_type' => 'business_registration_certificate',
            'disk' => 'private_documents',
            'file_path' => $biz,
            'original_filename' => 'Giay_Phep_Kinh_Doanh.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 204800,
            'sha256_checksum' => hash('sha256', $req->id . 'biz'),
            'status' => $status,
            'notes' => 'Giấy chứng nhận đăng ký hộ kinh doanh',
        ]);

        LandlordVerificationDocument::create([
            'verification_request_id' => $req->id,
            'document_type' => 'fire_safety_certificate',
            'disk' => 'private_documents',
            'file_path' => $fire,
            'original_filename' => 'Bien_Ban_Nghiem_Thu_PCCC.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 307200,
            'sha256_checksum' => hash('sha256', $req->id . 'fire'),
            'status' => $status,
            'notes' => 'Biên bản kiểm tra an toàn PCCC & CNCH',
        ]);

        LandlordVerificationDocument::create([
            'verification_request_id' => $req->id,
            'document_type' => 'security_order_certificate',
            'disk' => 'private_documents',
            'file_path' => $sec,
            'original_filename' => 'Giay_Chung_Nhan_ANTT.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 153600,
            'sha256_checksum' => hash('sha256', $req->id . 'sec'),
            'status' => $status,
            'notes' => 'Giấy chứng nhận đủ điều kiện về an ninh trật tự',
        ]);
    }
}
