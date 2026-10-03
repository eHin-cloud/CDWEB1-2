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
        // 1. Tạo bảng properties nếu chưa có
        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->string('address');
                $table->integer('room_count')->default(1);
                $table->string('status', 20)->default('pending'); // draft | pending | approved | rejected
                $table->text('reject_reason')->nullable();
                $table->timestamps();
            });
        }

        // 2. Thêm verification_status và reject_reason vào landlord_profiles
        if (Schema::hasTable('landlord_profiles')) {
            Schema::table('landlord_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('landlord_profiles', 'verification_status')) {
                    $table->string('verification_status', 30)->default('unverified')->after('status');
                }
                if (!Schema::hasColumn('landlord_profiles', 'reject_reason')) {
                    $table->text('reject_reason')->nullable()->after('verification_status');
                }
            });
        }

        // 3. Thêm status (active | locked) vào bảng users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status', 20)->default('active')->after('role');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('landlord_profiles')) {
            Schema::table('landlord_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('landlord_profiles', 'verification_status')) {
                    $table->dropColumn('verification_status');
                }
                if (Schema::hasColumn('landlord_profiles', 'reject_reason')) {
                    $table->dropColumn('reject_reason');
                }
            });
        }

        Schema::dropIfExists('properties');
    }
};
