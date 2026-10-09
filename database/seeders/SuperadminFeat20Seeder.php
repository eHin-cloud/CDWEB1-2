<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminFeat20Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Đảm bảo vai trò superadmin tồn tại
        $superadminRole = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'name' => 'Quản trị viên tối cao (Superadmin)',
                'description' => 'Toàn quyền kiểm soát và điều hành toàn bộ nền tảng hệ thống Renty & SmartRoom.',
            ]
        );

        // 2. Cập nhật tài khoản superadmin mặc định
        $superadmin = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Quản Trị Viên Tối Cao (Superadmin)',
                'email' => 'superadmin@smartroom.local',
                'password' => Hash::make('123456'),
                'status' => 'active',
            ]
        );
        $superadmin->role = 'superadmin';
        $superadmin->role_id = $superadminRole->id;
        $superadmin->phone = '0999999999';
        $superadmin->save();

        // 3. Tạo tài khoản mẫu có số điện thoại '0901234567' phục vụ DoD 2
        $testUserPhone = User::firstOrCreate(
            ['username' => 'test_phone_0901234567'],
            [
                'name' => 'Nguyễn Kiểm Thử SĐT',
                'email' => 'phone0901234567@smartroom.test',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'status' => 'active',
            ]
        );
        $testUserPhone->phone = '0901234567';
        $testUserPhone->save();
    }
}
