<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tạo bảng tenant_profiles (hồ sơ khách thuê) nếu chưa có
        if (!Schema::hasTable('tenant_profiles')) {
            Schema::create('tenant_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('national_id', 20)->nullable()->comment('Số CCCD / CMND');
                $table->string('current_address', 255)->nullable()->comment('Địa chỉ hiện tại');
                $table->date('date_of_birth')->nullable()->comment('Ngày sinh');
                $table->timestamps();

                $table->index('user_id');
            });
        }

        // 2. Tạo bảng tenant_preferences (sở thích / khu vực tìm phòng) nếu chưa có
        if (!Schema::hasTable('tenant_preferences')) {
            Schema::create('tenant_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->json('area_tags')->nullable()->comment('Danh sách khu vực / tiện ích tìm kiếm dạng JSON');
                $table->timestamps();

                $table->index('user_id');
            });
        }

        // 3. Đảm bảo role 'tenant' tồn tại trong bảng roles
        if (Schema::hasTable('roles')) {
            $tenantRole = DB::table('roles')->where('slug', 'tenant')->first();
            if (!$tenantRole) {
                DB::table('roles')->insert([
                    'name' => 'Khách thuê phòng',
                    'slug' => 'tenant',
                    'description' => 'Người dùng tìm kiếm và thuê phòng trọ trên Renty & SmartRoom',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_preferences');
        Schema::dropIfExists('tenant_profiles');
    }
};
