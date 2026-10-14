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
        Schema::table('contact_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_requests', 'appointment_time')) {
                $table->dateTime('appointment_time')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_requests', function (Blueprint $table) {
            if (Schema::hasColumn('contact_requests', 'appointment_time')) {
                $table->dropColumn('appointment_time');
            }
        });
    }
};
