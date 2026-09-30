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

        // Cập nhật trạng thái xác minh cho 10 Tenant
        Tenant::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8])->update([
            'verification_status' => 'kyc_verified',
            'listing_badge' => 'kyc_verified'
        ]);
        Tenant::where('id', 9)->update([
            'verification_status' => 'pending',
            'listing_badge' => 'unverified'
        ]);
        Tenant::where('id', 10)->update([
            'verification_status' => 'unverified',
            'listing_badge' => 'unverified'
        ]);

        $tenants = Tenant::all()->keyBy('id');

        // 1. TẠO 2 TÀI KHOẢN ADMIN HỆ THỐNG
        $admins = [
            ['username' => 'admin1', 'name' => 'Admin Hệ Thống 1 (Superadmin)', 'email' => 'admin1@smartroom.local', 'phone' => '0999000001'],
            ['username' => 'admin2', 'name' => 'Admin Hệ Thống 2 (Superadmin)', 'email' => 'admin2@smartroom.local', 'phone' => '0999000002'],
            ['username' => 'superadmin', 'name' => 'Superadmin Mặc Định', 'email' => 'superadmin@smartroom.local', 'phone' => '0999999999'],
            ['username' => 'admin', 'name' => 'Admin Mặc Định', 'email' => 'admin@smartroom.com', 'phone' => '0987654321'],
        ];
        foreach ($admins as $adm) {
            User::updateOrCreate(
                ['username' => $adm['username']],
                [
                    'name' => $adm['name'],
                    'email' => $adm['email'],
                    'phone' => $adm['phone'],
                    'password' => $passwordHash,
                    'role' => 'admin',
                    'role_id' => $adminRole?->id,
                    'tenant_id' => 1,
                    'status' => 'active',
                    'like' => $adm['name'],
                ]
            );
        }

        // 2. TẠO 10 TÀI KHOẢN CHỦ TRỌ (8 ĐÃ XÁC MINH + 2 CHƯA XÁC MINH)
        $landlordsData = [
            1 => ['username' => 'chutro1', 'name' => 'Trần Văn Hoàng (SmartRoom Cầu Giấy)', 'phone' => '0988000001', 'email' => 'chutro1@smartroom.local', 'verified' => true],
            2 => ['username' => 'chutro2', 'name' => 'Lê Quản Lý (Rentry Home Thanh Xuân)', 'phone' => '0988000002', 'email' => 'chutro2@smartroom.local', 'verified' => true],
            3 => ['username' => 'chutro3', 'name' => 'Phạm Văn Đống Đa (Sen Vàng Đống Đa)', 'phone' => '0988000003', 'email' => 'chutro3@smartroom.local', 'verified' => true],
            4 => ['username' => 'chutro4', 'name' => 'Hoàng Văn Tây Hồ (Hồ Tây Panorama)', 'phone' => '0988000004', 'email' => 'chutro4@smartroom.local', 'verified' => true],
            5 => ['username' => 'chutro5', 'name' => 'Vũ Thị Hai Bà Trưng (Times Light Hai Bà Trưng)', 'phone' => '0988000005', 'email' => 'chutro5@smartroom.local', 'verified' => true],
            6 => ['username' => 'chutro6', 'name' => 'Trần Văn Ba Đình (Liễu Giai Riverside)', 'phone' => '0988000006', 'email' => 'chutro6@smartroom.local', 'verified' => true],
            7 => ['username' => 'chutro7', 'name' => 'Bùi Tiến Đạt (Mỹ Đình Star Nam Từ Liêm)', 'phone' => '0988000007', 'email' => 'chutro7@smartroom.local', 'verified' => true],
            8 => ['username' => 'chutro8', 'name' => 'Hoàng Văn Hùng (Linh Đàm Green Hoàng Mai)', 'phone' => '0988000008', 'email' => 'chutro8@smartroom.local', 'verified' => true],
            9 => ['username' => 'chutro9', 'name' => 'Nguyễn Thị Mai (Cổ Nhuế House Bắc Từ Liêm)', 'phone' => '0988000009', 'email' => 'chutro9@smartroom.local', 'verified' => false, 'status_note' => 'pending'],
            10 => ['username' => 'chutro10', 'name' => 'Trịnh Văn Lâm (Hà Đông Central)', 'phone' => '0988000010', 'email' => 'chutro10@smartroom.local', 'verified' => false, 'status_note' => 'unverified'],
        ];

        foreach ($landlordsData as $tenantId => $l) {
            $isVer = $l['verified'];
            $roleSlug = $isVer ? 'landlord' : 'unverified_landlord';
            
            $user = User::where('username', $l['username'])->first()
                ?? User::where('username', 'demo-landlord-' . $tenantId)->first()
                ?? User::where('tenant_id', $tenantId)->whereIn('role', ['landlord', 'unverified_landlord'])->first();

            if ($user) {
                $user->username = $l['username'];
                $user->name = $l['name'];
                $user->email = $l['email'];
                $user->phone = $l['phone'];
                $user->password = $passwordHash;
                $user->role = $roleSlug;
                $user->role_id = $isVer ? $landlordRole?->id : $unverifiedLandlordRole?->id;
                $user->tenant_id = $tenantId;
                $user->status = 'active';
                $user->like = $l['name'];
                $user->save();
            } else {
                $user = User::create([
                    'username' => $l['username'],
                    'name' => $l['name'],
                    'email' => $l['email'],
                    'phone' => $l['phone'],
                    'password' => $passwordHash,
                    'role' => $roleSlug,
                    'role_id' => $isVer ? $landlordRole?->id : $unverifiedLandlordRole?->id,
                    'tenant_id' => $tenantId,
                    'status' => 'active',
                    'like' => $l['name'],
                ]);
            }

            LandlordProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'tenant_id' => $tenantId,
                    'full_name' => $l['name'],
                    'phone' => $l['phone'],
                    'property_name' => $tenants[$tenantId]->name ?? 'SmartRoom',
                    'property_address' => 'Hà Nội',
                    'status' => $isVer ? 'verified' : ($l['status_note'] ?? 'pending'),
                    'verification_status' => $isVer ? 'kyc_verified' : ($l['status_note'] ?? 'pending'),
                ]
            );
        }

        // 3. TẠO 10 NHÂN VIÊN QUẢN LÝ (CHỈ PHÂN BỔ CHO 8 CHỦ TRỌ ĐÃ XÁC MINH - CHỦ TRỌ 1 & 2 CÓ 2 QUẢN LÝ)
        $managersData = [
            ['username' => 'quanly1_1', 'name' => 'Nguyễn Quản Lý 1 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0971000001', 'email' => 'ql1_1@smartroom.local'],
            ['username' => 'quanly1_2', 'name' => 'Trần Quản Lý 2 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0971000002', 'email' => 'ql1_2@smartroom.local'],
            ['username' => 'quanly2_1', 'name' => 'Lê Quản Lý 1 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0972000001', 'email' => 'ql2_1@smartroom.local'],
            ['username' => 'quanly2_2', 'name' => 'Phạm Quản Lý 2 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0972000002', 'email' => 'ql2_2@smartroom.local'],
            ['username' => 'quanly3', 'name' => 'Đặng Quản Lý (Đống Đa)', 'tenant_id' => 3, 'phone' => '0973000001', 'email' => 'ql3@smartroom.local'],
            ['username' => 'quanly4', 'name' => 'Hoàng Quản Lý (Tây Hồ)', 'tenant_id' => 4, 'phone' => '0974000001', 'email' => 'ql4@smartroom.local'],
            ['username' => 'quanly5', 'name' => 'Vũ Quản Lý (Hai Bà Trưng)', 'tenant_id' => 5, 'phone' => '0975000001', 'email' => 'ql5@smartroom.local'],
            ['username' => 'quanly6', 'name' => 'Bùi Quản Lý (Ba Đình)', 'tenant_id' => 6, 'phone' => '0976000001', 'email' => 'ql6@smartroom.local'],
            ['username' => 'quanly7', 'name' => 'Đỗ Quản Lý (Nam Từ Liêm)', 'tenant_id' => 7, 'phone' => '0977000001', 'email' => 'ql7@smartroom.local'],
            ['username' => 'quanly8', 'name' => 'Ngô Quản Lý (Hoàng Mai)', 'tenant_id' => 8, 'phone' => '0978000001', 'email' => 'ql8@smartroom.local'],
        ];

        foreach ($managersData as $m) {
            User::updateOrCreate(
                ['username' => $m['username']],
                [
                    'name' => $m['name'],
                    'email' => $m['email'],
                    'phone' => $m['phone'],
                    'password' => $passwordHash,
                    'role' => 'manager',
                    'role_id' => $managerRole?->id,
                    'tenant_id' => $m['tenant_id'],
                    'status' => 'active',
                    'like' => $m['name'],
                ]
            );
        }

        // 4. TẠO 10 NHÂN VIÊN LỄ TÂN
        $receptionistsData = [
            ['username' => 'letan1_1', 'name' => 'Lễ Tân 1 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0961000001', 'email' => 'lt1_1@smartroom.local'],
            ['username' => 'letan1_2', 'name' => 'Lễ Tân 2 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0961000002', 'email' => 'lt1_2@smartroom.local'],
            ['username' => 'letan2_1', 'name' => 'Lễ Tân 1 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0962000001', 'email' => 'lt2_1@smartroom.local'],
            ['username' => 'letan2_2', 'name' => 'Lễ Tân 2 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0962000002', 'email' => 'lt2_2@smartroom.local'],
            ['username' => 'letan3', 'name' => 'Lễ Tân (Đống Đa)', 'tenant_id' => 3, 'phone' => '0963000001', 'email' => 'lt3@smartroom.local'],
            ['username' => 'letan4', 'name' => 'Lễ Tân (Tây Hồ)', 'tenant_id' => 4, 'phone' => '0964000001', 'email' => 'lt4@smartroom.local'],
            ['username' => 'letan5', 'name' => 'Lễ Tân (Hai Bà Trưng)', 'tenant_id' => 5, 'phone' => '0965000001', 'email' => 'lt5@smartroom.local'],
            ['username' => 'letan6', 'name' => 'Lễ Tân (Ba Đình)', 'tenant_id' => 6, 'phone' => '0966000001', 'email' => 'lt6@smartroom.local'],
            ['username' => 'letan7', 'name' => 'Lễ Tân (Nam Từ Liêm)', 'tenant_id' => 7, 'phone' => '0967000001', 'email' => 'lt7@smartroom.local'],
            ['username' => 'letan8', 'name' => 'Lễ Tân (Hoàng Mai)', 'tenant_id' => 8, 'phone' => '0968000001', 'email' => 'lt8@smartroom.local'],
        ];

        foreach ($receptionistsData as $r) {
            User::updateOrCreate(
                ['username' => $r['username']],
                [
                    'name' => $r['name'],
                    'email' => $r['email'],
                    'phone' => $r['phone'],
                    'password' => $passwordHash,
                    'role' => 'receptionist',
                    'role_id' => $receptionistRole?->id,
                    'tenant_id' => $r['tenant_id'],
                    'status' => 'active',
                    'like' => $r['name'],
                ]
            );
        }

        // 5. TẠO 10 NHÂN VIÊN BUỒNG PHÒNG
        $housekeepersData = [
            ['username' => 'buongphong1_1', 'name' => 'Buồng Phòng 1 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0951000001', 'email' => 'bp1_1@smartroom.local'],
            ['username' => 'buongphong1_2', 'name' => 'Buồng Phòng 2 (Cầu Giấy)', 'tenant_id' => 1, 'phone' => '0951000002', 'email' => 'bp1_2@smartroom.local'],
            ['username' => 'buongphong2_1', 'name' => 'Buồng Phòng 1 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0952000001', 'email' => 'bp2_1@smartroom.local'],
            ['username' => 'buongphong2_2', 'name' => 'Buồng Phòng 2 (Thanh Xuân)', 'tenant_id' => 2, 'phone' => '0952000002', 'email' => 'bp2_2@smartroom.local'],
            ['username' => 'buongphong3', 'name' => 'Buồng Phòng (Đống Đa)', 'tenant_id' => 3, 'phone' => '0953000001', 'email' => 'bp3@smartroom.local'],
            ['username' => 'buongphong4', 'name' => 'Buồng Phòng (Tây Hồ)', 'tenant_id' => 4, 'phone' => '0954000001', 'email' => 'bp4@smartroom.local'],
            ['username' => 'buongphong5', 'name' => 'Buồng Phòng (Hai Bà Trưng)', 'tenant_id' => 5, 'phone' => '0955000001', 'email' => 'bp5@smartroom.local'],
            ['username' => 'buongphong6', 'name' => 'Buồng Phòng (Ba Đình)', 'tenant_id' => 6, 'phone' => '0956000001', 'email' => 'bp6@smartroom.local'],
            ['username' => 'buongphong7', 'name' => 'Buồng Phòng (Nam Từ Liêm)', 'tenant_id' => 7, 'phone' => '0957000001', 'email' => 'bp7@smartroom.local'],
            ['username' => 'buongphong8', 'name' => 'Buồng Phòng (Hoàng Mai)', 'tenant_id' => 8, 'phone' => '0958000001', 'email' => 'bp8@smartroom.local'],
        ];

        foreach ($housekeepersData as $h) {
            User::updateOrCreate(
                ['username' => $h['username']],
                [
                    'name' => $h['name'],
                    'email' => $h['email'],
                    'phone' => $h['phone'],
                    'password' => $passwordHash,
                    'role' => 'housekeeper',
                    'role_id' => $housekeeperRole?->id,
                    'tenant_id' => $h['tenant_id'],
                    'status' => 'active',
                    'like' => $h['name'],
                ]
            );
        }

        // 6. TẠO TÀI KHOẢN CƯ DÂN CHO TẤT CẢ CÁC PHÒNG ĐƯỢC THUÊ (OCCUPIED ROOMS)
        $occupiedRooms = Room::with('building')->where('status', 'occupied')->orderBy('tenant_id')->orderBy('id')->get();
        $residentIndex = 1;
        foreach ($occupiedRooms as $room) {
            $resident = Resident::where('room_id', $room->id)->where('status', 'active')->first();
            $residentName = $resident ? $resident->name : ('Cư Dân Phòng ' . $room->room_number);

            $username = 'cudan_' . $residentIndex;
            $phone = '094' . str_pad((string) $residentIndex, 7, '0', STR_PAD_LEFT);
            $email = 'cudan' . $residentIndex . '@smartroom.local';

            $user = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $residentName . ' (P.' . $room->room_number . ' - ' . ($room->building->name ?? 'Cơ sở ' . $room->tenant_id) . ')',
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'role' => 'user',
                    'role_id' => $residentRole?->id,
                    'tenant_id' => $room->tenant_id,
                    'status' => 'active',
                    'like' => $residentName,
                ]
            );

            if ($resident) {
                $resident->update([
                    'user_id' => $user->id,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            }

            $residentIndex++;
        }

        // Tạo thêm tài khoản tenant mặc định trỏ về cudan_1
        User::updateOrCreate(
            ['username' => 'tenant'],
            [
                'name' => 'Khách thuê demo chung',
                'email' => 'tenant@smartroom.local',
                'phone' => '0940000000',
                'password' => $passwordHash,
                'role' => 'user',
                'role_id' => $residentRole?->id,
                'tenant_id' => 1,
                'status' => 'active',
                'like' => 'Khách thuê demo chung',
            ]
        );

        // 7. TẠO 10 TÀI KHOẢN KHÁCH VÃNG LAI (CHƯA GẮN TRỌ)
        for ($g = 1; $g <= 10; $g++) {
            $guestUsername = 'khach' . $g;
            $guestName = 'Khách Tìm Phòng ' . $g;
            $guestPhone = '093' . str_pad((string) $g, 7, '0', STR_PAD_LEFT);
            $guestEmail = 'khach' . $g . '@smartroom.local';

            User::updateOrCreate(
                ['username' => $guestUsername],
                [
                    'name' => $guestName,
                    'email' => $guestEmail,
                    'phone' => $guestPhone,
                    'password' => $passwordHash,
                    'role' => 'guest',
                    'role_id' => $guestRole?->id,
                    'tenant_id' => null,
                    'status' => 'active',
                    'like' => $guestName,
                ]
            );
        }

        // 8. DỌN DẸP SẠCH CÁC TÀI KHOẢN RÁC KHÔNG CÒN SỬ DỤNG
        User::where('username', 'like', 'demo-%')->delete();
        User::whereIn('username', ['admin_hanoi', 'unverified-landlord'])->delete();
    }
}
