<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_profiles', 'username')) {
                $table->string('username', 100)->nullable()->after('full_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('landlord_profiles', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
