<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Khởi tạo các cấu hình toàn sàn mặc định
        $defaultConfigs = [
            [
                'key' => 'commission_rate',
                'value' => '10',
                'group' => 'financial',
                'description' => 'Tỷ lệ chiết khấu / hoa hồng giao dịch sàn Renty (%)'
            ],
            [
                'key' => 'free_listing_quota',
                'value' => '5',
                'group' => 'quota',
                'description' => 'Hạn ngạch số lượng phòng / tin đăng miễn phí cho chủ trọ mới'
            ],
            [
                'key' => 'platform_fee_fixed',
                'value' => '50000',
                'group' => 'financial',
                'description' => 'Phí sàn cố định cho mỗi hợp đồng phát sinh (VNĐ)'
            ],
            [
                'key' => 'auto_approve_landlord',
                'value' => '0',
                'group' => 'policy',
                'description' => 'Tự động duyệt xác minh danh tính KYC chủ trọ (0: Thủ công, 1: Tự động)'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'system',
                'description' => 'Chế độ bảo trì hệ thống toàn diện (0: Bình thường, 1: Bảo trì)'
            ],
            [
                'key' => 'system_hotline',
                'value' => '1900 8888',
                'group' => 'general',
                'description' => 'Hotline hỗ trợ kỹ thuật và khẩn cấp'
            ],
            [
                'key' => 'system_email',
                'value' => 'superadmin@smartroom.local',
                'group' => 'general',
                'description' => 'Email thông báo quản trị viên tối cao'
            ],
        ];

        foreach ($defaultConfigs as $cfg) {
            \Illuminate\Support\Facades\DB::table('system_configs')->updateOrInsert(
                ['key' => $cfg['key']],
                array_merge($cfg, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Đảm bảo vai trò 'superadmin' tồn tại trong bảng roles
        if (Schema::hasTable('roles')) {
            $superadminRole = \Illuminate\Support\Facades\DB::table('roles')->where('slug', 'superadmin')->first();
            if (!$superadminRole) {
                $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
                    'name' => 'Quản trị viên tối cao (Superadmin)',
                    'slug' => 'superadmin',
                    'description' => 'Toàn quyền quản trị cấp cao toàn bộ nền tảng hệ thống Renty & SmartRoom.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $roleId = $superadminRole->id;
            }

            // Gán role superadmin cho user superadmin nếu tồn tại
            if (Schema::hasTable('users')) {
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('username', 'superadmin')
                    ->update([
                        'role' => 'superadmin',
                        'role_id' => $roleId,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
