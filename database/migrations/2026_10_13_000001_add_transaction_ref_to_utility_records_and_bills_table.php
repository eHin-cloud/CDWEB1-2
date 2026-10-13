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
        if (Schema::hasTable('utility_records')) {
            Schema::table('utility_records', function (Blueprint $table) {
                if (!Schema::hasColumn('utility_records', 'transaction_ref')) {
                    $table->string('transaction_ref', 100)->nullable()->after('payment_method');
                }
            });
        }

        if (Schema::hasTable('bills')) {
            Schema::table('bills', function (Blueprint $table) {
                if (!Schema::hasColumn('bills', 'transaction_ref')) {
                    $table->string('transaction_ref', 100)->nullable()->after('payment_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('utility_records') && Schema::hasColumn('utility_records', 'transaction_ref')) {
            Schema::table('utility_records', function (Blueprint $table) {
                $table->dropColumn('transaction_ref');
            });
        }

        if (Schema::hasTable('bills') && Schema::hasColumn('bills', 'transaction_ref')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->dropColumn('transaction_ref');
            });
        }
    }
};
