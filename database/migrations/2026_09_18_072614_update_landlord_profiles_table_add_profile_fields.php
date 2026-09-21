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
        Schema::table('landlord_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_profiles', 'email')) {
                $table->string('email', 150)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('landlord_profiles', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('landlord_profiles', 'national_id')) {
                $table->string('national_id', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('landlord_profiles', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('landlord_profiles', 'bank_account_number')) {
                $table->string('bank_account_number', 60)->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('landlord_profiles', 'bank_name')) {
                $table->string('bank_name', 120)->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('landlord_profiles', 'business_license')) {
                $table->string('business_license', 150)->nullable()->after('bank_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            $columns = [
                'email',
                'password',
                'national_id',
                'permanent_address',
                'bank_account_number',
                'bank_name',
                'business_license',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('landlord_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
