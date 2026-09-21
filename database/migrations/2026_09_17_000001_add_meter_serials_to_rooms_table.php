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
            if (!Schema::hasColumn('rooms', 'electric_meter_serial')) {
                $table->string('electric_meter_serial', 100)->nullable()->after('area');
            }
            if (!Schema::hasColumn('rooms', 'water_meter_serial')) {
                $table->string('water_meter_serial', 100)->nullable()->after('electric_meter_serial');
            }

            // Index phục vụ tìm kiếm nhanh khi AI quét và match theo số sản xuất
            $table->index(['tenant_id', 'electric_meter_serial'], 'rooms_tenant_electric_serial_idx');
            $table->index(['tenant_id', 'water_meter_serial'], 'rooms_tenant_water_serial_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_tenant_electric_serial_idx');
            $table->dropIndex('rooms_tenant_water_serial_idx');
            $table->dropColumn(['electric_meter_serial', 'water_meter_serial']);
        });
    }
};
