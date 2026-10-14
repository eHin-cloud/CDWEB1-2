<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Bổ sung các chỉ mục (Indexes) cho các cột được tìm kiếm và lọc với tần suất cao nhất
     */
    public function up(): void
    {
        // 1. Tối ưu bảng rooms (Phục vụ Sàn Renty FEAT_04 và Quản lý phòng FEAT_03)
        Schema::table('rooms', function (Blueprint $table) {
            // Lọc phòng trống công khai trên sàn Renty (WHERE status = 'empty')
            $table->index('status', 'rooms_status_idx');

            // Composite Index: Tối ưu hàng đầu cho sàn Renty (Lọc phòng trống + Lọc khoảng giá min-max)
            $table->index(['status', 'price'], 'rooms_status_price_idx');

            // Lọc theo khoảng giá đơn lẻ
            $table->index('price', 'rooms_price_idx');

            // Lọc theo diện tích phòng
            $table->index('area', 'rooms_area_idx');
        });

        // 2. Tối ưu bảng buildings (Tìm kiếm & Sắp xếp cơ sở lưu trú)
        Schema::table('buildings', function (Blueprint $table) {
            $table->index(['tenant_id', 'name'], 'buildings_tenant_name_idx');
        });

        // 3. Tối ưu bảng contact_requests (Tiếp nhận & lọc yêu cầu hẹn xem phòng FEAT_07)
        Schema::table('contact_requests', function (Blueprint $table) {
            $table->index('status', 'contact_requests_status_idx');
        });

        // 4. Tối ưu bảng contracts (Quản lý hợp đồng FEAT_08 theo trạng thái)
        Schema::table('contracts', function (Blueprint $table) {
            $table->index(['tenant_id', 'status'], 'contracts_tenant_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_status_idx');
            $table->dropIndex('rooms_status_price_idx');
            $table->dropIndex('rooms_price_idx');
            $table->dropIndex('rooms_area_idx');
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropIndex('buildings_tenant_name_idx');
        });

        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropIndex('contact_requests_status_idx');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('contracts_tenant_status_idx');
        });
    }
};
