<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            $table->string('verification_method', 50)->nullable()->after('business_license')
                  ->comment('gpkd = Giấy phép kinh doanh, mst = Mã số thuế');
        });
    }

    public function down(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            $table->dropColumn('verification_method');
        });
    }
};
