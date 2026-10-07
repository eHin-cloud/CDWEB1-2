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
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'housekeeping_status')) {
                $table->string('housekeeping_status', 30)->default('clean')->after('cleaning_status');
            }
            if (!Schema::hasColumn('rooms', 'assigned_staff_id')) {
                $table->unsignedBigInteger('assigned_staff_id')->nullable()->after('housekeeping_status');
            }
            if (!Schema::hasColumn('rooms', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('assigned_staff_id');
            }
            if (!Schema::hasColumn('rooms', 'inspection_notes')) {
                $table->string('inspection_notes', 255)->nullable()->after('priority');
            }
            if (!Schema::hasColumn('rooms', 'inspected_by')) {
                $table->unsignedBigInteger('inspected_by')->nullable()->after('inspection_notes');
            }
            if (!Schema::hasColumn('rooms', 'inspected_at')) {
                $table->timestamp('inspected_at')->nullable()->after('inspected_by');
            }
        });

        // Đồng bộ dữ liệu cũ từ cleaning_status sang housekeeping_status
        DB::table('rooms')->whereNull('housekeeping_status')->orWhere('housekeeping_status', '')->update([
            'housekeeping_status' => DB::raw("CASE 
                WHEN cleaning_status = 'dirty' THEN 'dirty'
                WHEN cleaning_status = 'cleaning' THEN 'cleaning'
                WHEN cleaning_status = 'inspected' THEN 'inspected'
                ELSE 'clean'
            END")
        ]);

        // Tạo bảng housekeeping_logs nếu chưa có để theo dõi vết lịch sử phân công và dọn dẹp
        if (!Schema::hasTable('housekeeping_logs')) {
            Schema::create('housekeeping_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->unsignedBigInteger('room_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->comment('Người thực hiện hành động');
                $table->unsignedBigInteger('assigned_staff_id')->nullable()->comment('Nhân viên được giao');
                $table->string('action', 50)->comment('assign, start_cleaning, clean_done, inspect_passed, checkout_dirty');
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30);
                $table->string('priority', 20)->default('normal');
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_logs');

        Schema::table('rooms', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['housekeeping_status', 'assigned_staff_id', 'priority', 'inspection_notes', 'inspected_by', 'inspected_at'] as $col) {
                if (Schema::hasColumn('rooms', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
