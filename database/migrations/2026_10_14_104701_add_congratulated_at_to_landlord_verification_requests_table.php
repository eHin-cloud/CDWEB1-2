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
        Schema::table('landlord_verification_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('landlord_verification_requests', 'congratulated_at')) {
                $table->timestamp('congratulated_at')->nullable()->after('reviewed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landlord_verification_requests', function (Blueprint $table) {
            if (Schema::hasColumn('landlord_verification_requests', 'congratulated_at')) {
                $table->dropColumn('congratulated_at');
            }
        });
    }
};
