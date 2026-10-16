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
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'signing_method')) {
                $table->string('signing_method')->default('online')->after('status');
            }
            if (!Schema::hasColumn('contracts', 'offline_contract_scan')) {
                $table->string('offline_contract_scan')->nullable()->after('signing_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'offline_contract_scan')) {
                $table->dropColumn('offline_contract_scan');
            }
            if (Schema::hasColumn('contracts', 'signing_method')) {
                $table->dropColumn('signing_method');
            }
        });
    }
};
