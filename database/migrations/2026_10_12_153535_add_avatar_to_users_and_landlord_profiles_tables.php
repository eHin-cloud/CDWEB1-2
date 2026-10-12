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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar', 500)->nullable()->after('email');
            });
        }

        if (Schema::hasTable('landlord_profiles') && !Schema::hasColumn('landlord_profiles', 'avatar')) {
            Schema::table('landlord_profiles', function (Blueprint $table) {
                $table->string('avatar', 500)->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }

        if (Schema::hasTable('landlord_profiles') && Schema::hasColumn('landlord_profiles', 'avatar')) {
            Schema::table('landlord_profiles', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }
};
