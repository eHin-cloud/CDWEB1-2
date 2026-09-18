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
        // 1. Thêm loại cơ sở cho bảng buildings
        Schema::table('buildings', function (Blueprint $table) {
            if (!Schema::hasColumn('buildings', 'property_type')) {
                $table->string('property_type', 30)->default('boarding')->after('description');
            }
            if (!Schema::hasColumn('buildings', 'checkin_time')) {
                $table->time('checkin_time')->default('14:00:00')->after('property_type');
            }
            if (!Schema::hasColumn('buildings', 'checkout_time')) {
                $table->time('checkout_time')->default('12:00:00')->after('checkin_time');
            }
        });

        // 2. Thêm các trường giá & hình thức cho thuê đa khung cho rooms
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'rental_type')) {
                $table->string('rental_type', 20)->default('month')->after('room_type');
            }
            if (!Schema::hasColumn('rooms', 'price_per_day')) {
                $table->decimal('price_per_day', 12, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('rooms', 'price_per_hour')) {
                $table->decimal('price_per_hour', 12, 2)->nullable()->after('price_per_day');
            }
            if (!Schema::hasColumn('rooms', 'price_extra_hour')) {
                $table->decimal('price_extra_hour', 12, 2)->nullable()->after('price_per_hour');
            }
            if (!Schema::hasColumn('rooms', 'cleaning_status')) {
                $table->string('cleaning_status', 30)->default('clean')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'rental_type',
                'price_per_day',
                'price_per_hour',
                'price_extra_hour',
                'cleaning_status',
            ]);
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn([
                'property_type',
                'checkin_time',
                'checkout_time',
            ]);
        });
    }
};
