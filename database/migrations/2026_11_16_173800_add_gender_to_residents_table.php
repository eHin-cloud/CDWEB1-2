<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('residents', 'gender')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->string('gender', 20)->default('Nam')->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('residents', 'gender')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
