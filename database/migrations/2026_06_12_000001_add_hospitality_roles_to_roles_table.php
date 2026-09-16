<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            [
                'name' => 'Lễ tân',
                'slug' => 'receptionist',
                'description' => 'Quản lý quầy lễ tân: Check-in, Check-out ngày/giờ, Minibar và thu tiền Folio.'
            ],
            [
                'name' => 'Buồng phòng',
                'slug' => 'housekeeper',
                'description' => 'Nhân viên buồng phòng: Tiếp nhận dọn dẹp và cập nhật trạng thái vệ sinh phòng.'
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->whereIn('slug', ['receptionist', 'housekeeper'])
            ->delete();
    }
};
