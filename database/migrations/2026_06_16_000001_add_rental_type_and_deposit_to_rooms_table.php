<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'rental_type')) {
                $table->string('rental_type', 20)->default('month')->after('room_type');
            }
            if (!Schema::hasColumn('rooms', 'deposit')) {
                $table->unsignedInteger('deposit')->default(0)->after('price');
            }
        });

        // Chuyển đổi dữ liệu cũ: 'normal' -> 'standard'
        DB::table('rooms')->where('room_type', 'normal')->update(['room_type' => 'standard']);
    }

    public function down(): void
    {
        DB::table('rooms')->where('room_type', 'standard')->update(['room_type' => 'normal']);

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['rental_type', 'deposit']);
        });
    }
};
