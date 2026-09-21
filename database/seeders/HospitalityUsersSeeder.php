<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class HospitalityUsersSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;

        $receptionistRole = Role::where('slug', 'receptionist')->first();
        $housekeeperRole = Role::where('slug', 'housekeeper')->first();

        // 1. Tạo hoặc cập nhật tài khoản Lễ tân
        $receptionist = User::where('username', 'demo-receptionist')->first();
        if (!$receptionist) {
            $receptionist = new User();
            $receptionist->username = 'demo-receptionist';
            $receptionist->tenant_id = $tenantId;
            $receptionist->name = 'Lễ tân Khách Sạn Demo';
            $receptionist->phone = '0777888111';
            $receptionist->email = 'receptionist@demo.smartroom.local';
            $receptionist->password = Hash::make('password');
            $receptionist->role = 'receptionist';
            $receptionist->role_id = $receptionistRole?->id;
            $receptionist->save();
        }

        // 2. Tạo hoặc cập nhật tài khoản Buồng phòng
        $housekeeper = User::where('username', 'demo-housekeeper')->first();
        if (!$housekeeper) {
            $housekeeper = new User();
            $housekeeper->username = 'demo-housekeeper';
            $housekeeper->tenant_id = $tenantId;
            $housekeeper->name = 'Nhân viên Buồng Phòng Demo';
            $housekeeper->phone = '0777888222';
            $housekeeper->email = 'housekeeper@demo.smartroom.local';
            $housekeeper->password = Hash::make('password');
            $housekeeper->role = 'housekeeper';
            $housekeeper->role_id = $housekeeperRole?->id;
            $housekeeper->save();
        }
    }
}
