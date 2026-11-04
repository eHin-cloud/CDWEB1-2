<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Resident;
use App\Models\User;
use App\Models\Role;
use App\Models\LandlordProfile;
use Illuminate\Support\Facades\Hash;

class SystemUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('slug');
        $adminRole = $roles->get('admin');
        $landlordRole = $roles->get('landlord');
        $unverifiedLandlordRole = $roles->get('unverified_landlord');
        $managerRole = $roles->get('manager');
        $receptionistRole = $roles->get('receptionist');
        $housekeeperRole = $roles->get('housekeeper');
        $residentRole = $roles->get('resident');
        $guestRole = $roles->get('guest');

        $passwordHash = Hash::make('123456');

        $tenants = Tenant::all();

        // 1. TẠO HOẶC CẬP NHẬT TÀI KHOẢN ADMIN HỆ THỐNG
        $admins = [
            ['username' => 'admin1', 'name' => 'Admin Hệ Thống 1 (Superadmin)', 'email' => 'admin1@smartroom.local', 'phone' => '0999000001'],
            ['username' => 'admin2', 'name' => 'Admin Hệ Thống 2 (Superadmin)', 'email' => 'admin2@smartroom.local', 'phone' => '0999000002'],
            ['username' => 'superadmin', 'name' => 'Superadmin Mặc Định', 'email' => 'superadmin@smartroom.local', 'phone' => '0999999999'],
            ['username' => 'admin', 'name' => 'Admin Mặc Định', 'email' => 'admin@smartroom.com', 'phone' => '0987654321', 'password' => Hash::make('admin123')],
        ];
        foreach ($admins as $adm) {
            User::updateOrCreate(
                ['username' => $adm['username']],
                [
                    'name' => $adm['name'],
                    'email' => $adm['email'],
                    'phone' => $adm['phone'],
                    'password' => $adm['password'] ?? $passwordHash,
                    'role' => 'admin',
                    'role_id' => $adminRole?->id,
                    'tenant_id' => 1,
                    'status' => 'active',
                    'like' => $adm['name'],
                ]
            );
        }

        // 2. ĐỒNG BỘ TÀI KHOẢN CHỦ TRỌ, QUẢN LÝ, LỄ TÂN, BUỒNG PHÒNG CHO TẤT CẢ TENANTS
        foreach ($tenants as $tenant) {
            $isVer = ($tenant->verification_status === 'kyc_verified');
            $roleSlug = $isVer ? 'landlord' : 'unverified_landlord';
            $tId = $tenant->id;

            // A. Chủ trọ (chutro1 .. chutro40)
            $landlordUser = User::where('username', 'chutro' . $tId)->first()
                ?? User::where('tenant_id', $tId)->whereIn('role', ['landlord', 'unverified_landlord'])->first();

            if ($landlordUser) {
                $landlordUser->role = $roleSlug;
                $landlordUser->role_id = $isVer ? $landlordRole?->id : $unverifiedLandlordRole?->id;
                $landlordUser->save();
            } else {
                $landlordUser = User::create([
                    'username' => 'chutro' . $tId,
                    'name' => 'Chủ trọ cơ sở ' . $tenant->name,
                    'email' => 'chutro' . $tId . '@smartroom.local',
                    'phone' => $tenant->phone ?? ('0988' . str_pad((string) $tId, 6, '0', STR_PAD_LEFT)),
                    'password' => $passwordHash,
                    'role' => $roleSlug,
                    'role_id' => $isVer ? $landlordRole?->id : $unverifiedLandlordRole?->id,
                    'tenant_id' => $tId,
                    'status' => 'active',
                    'like' => 'Chủ trọ cơ sở ' . $tenant->name,
                ]);
            }

            // Đồng bộ hồ sơ LandlordProfile
            LandlordProfile::updateOrCreate(
                ['user_id' => $landlordUser->id],
                [
                    'tenant_id' => $tId,
                    'full_name' => $landlordUser->name,
                    'phone' => $landlordUser->phone,
                    'property_name' => $tenant->name,
                    'property_address' => $tenant->buildings()->value('address') ?? 'Việt Nam',
                    'national_id' => '0' . str_pad((string) (10000000000 + $tId), 11, '0', STR_PAD_LEFT),
                    'business_license' => '0' . str_pad((string) (300000000 + $tId), 9, '0', STR_PAD_LEFT),
                    'status' => $isVer ? 'verified' : ($tenant->verification_status ?? 'pending'),
                    'verification_status' => $tenant->verification_status ?? ($isVer ? 'kyc_verified' : 'pending'),
                ]
            );

            // B. Quản lý cơ sở (quanly1 .. quanly40)
            User::updateOrCreate(
                ['username' => 'quanly' . $tId],
                [
                    'name' => 'Quản Lý ' . $tenant->name,
                    'email' => 'quanly' . $tId . '@smartroom.local',
                    'phone' => '097' . str_pad((string) $tId, 7, '0', STR_PAD_LEFT),
                    'password' => $passwordHash,
                    'role' => 'manager',
                    'role_id' => $managerRole?->id,
                    'tenant_id' => $tId,
                    'status' => 'active',
                    'like' => 'Quản lý ' . $tenant->name,
                ]
            );

            // C. Lễ tân cơ sở (letan1 .. letan40)
            User::updateOrCreate(
                ['username' => 'letan' . $tId],
                [
                    'name' => 'Lễ Tân ' . $tenant->name,
                    'email' => 'letan' . $tId . '@smartroom.local',
                    'phone' => '096' . str_pad((string) $tId, 7, '0', STR_PAD_LEFT),
                    'password' => $passwordHash,
                    'role' => 'receptionist',
                    'role_id' => $receptionistRole?->id,
                    'tenant_id' => $tId,
                    'status' => 'active',
                    'like' => 'Lễ Tân ' . $tenant->name,
                ]
            );

            // D. Buồng phòng cơ sở (buongphong1 .. buongphong40)
            User::updateOrCreate(
                ['username' => 'buongphong' . $tId],
                [
                    'name' => 'Buồng Phòng ' . $tenant->name,
                    'email' => 'buongphong' . $tId . '@smartroom.local',
                    'phone' => '095' . str_pad((string) $tId, 7, '0', STR_PAD_LEFT),
                    'password' => $passwordHash,
                    'role' => 'housekeeper',
                    'role_id' => $housekeeperRole?->id,
                    'tenant_id' => $tId,
                    'status' => 'active',
                    'like' => 'Buồng Phòng ' . $tenant->name,
                ]
            );
        }

        // 3. TẠO TÀI KHOẢN KHÁCH THUÊ DEMO CHUNG
        User::updateOrCreate(
            ['username' => 'tenant'],
            [
                'name' => 'Khách thuê demo chung',
                'email' => 'tenant@smartroom.local',
                'phone' => '0940000000',
                'password' => Hash::make('password'),
                'role' => 'user',
                'role_id' => $residentRole?->id,
                'tenant_id' => 1,
                'status' => 'active',
                'like' => 'Khách thuê demo chung',
            ]
        );

        // 4. TẠO CÁC TÀI KHOẢN KHÁCH VÃNG LAI
        $cityGuests = [
            ['username' => 'khachhanoi', 'name' => 'Khách Tìm Phòng Hà Nội', 'email' => 'khach.hn@smartroom.local', 'phone' => '0931000001'],
            ['username' => 'khachhcm', 'name' => 'Khách Tìm Phòng TP.HCM', 'email' => 'khach.hcm@smartroom.local', 'phone' => '0931000002'],
            ['username' => 'khachdanang', 'name' => 'Khách Tìm Phòng Đà Nẵng', 'email' => 'khach.dn@smartroom.local', 'phone' => '0931000003'],
            ['username' => 'khachdalat', 'name' => 'Khách Tìm Phòng Đà Lạt', 'email' => 'khach.dl@smartroom.local', 'phone' => '0931000004'],
        ];

        foreach ($cityGuests as $cg) {
            User::updateOrCreate(
                ['username' => $cg['username']],
                [
                    'name' => $cg['name'],
                    'email' => $cg['email'],
                    'phone' => $cg['phone'],
                    'password' => $passwordHash,
                    'role' => 'guest',
                    'role_id' => $guestRole?->id,
                    'tenant_id' => null,
                    'status' => 'active',
                    'like' => $cg['name'],
                ]
            );
        }

        for ($g = 1; $g <= 10; $g++) {
            User::updateOrCreate(
                ['username' => 'khach' . $g],
                [
                    'name' => 'Khách Tìm Phòng ' . $g,
                    'email' => 'khach' . $g . '@smartroom.local',
                    'phone' => '093' . str_pad((string) $g, 7, '0', STR_PAD_LEFT),
                    'password' => $passwordHash,
                    'role' => 'guest',
                    'role_id' => $guestRole?->id,
                    'tenant_id' => null,
                    'status' => 'active',
                    'like' => 'Khách Tìm Phòng ' . $g,
                ]
            );
        }

        // 5. DỌN DẸP SẠCH CÁC TÀI KHOẢN RÁC KHÔNG CÒN SỬ DỤNG
        User::where('username', 'like', 'demo-%')->delete();
        User::whereIn('username', ['admin_hanoi', 'unverified-landlord'])->delete();
    }
}
