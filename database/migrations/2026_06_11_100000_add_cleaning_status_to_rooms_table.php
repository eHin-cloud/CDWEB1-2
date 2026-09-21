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
        Schema::table('rooms', function (Blueprint $table) {
            // Chuyển sang VARCHAR(30) để hỗ trợ đầy đủ các trạng thái lưu trú:
            // empty (trống), occupied (đang ở), overdue (nợ tiền), cleaning (đang dọn dẹp), maintenance (bảo trì)
            $table->string('status', 30)->default('empty')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('status', 20)->default('empty')->change();
        });
    }
};
