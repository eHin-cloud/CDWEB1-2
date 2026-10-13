<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('rating_security', 3, 1)->nullable()->after('version');
            $table->decimal('rating_landlord', 3, 1)->nullable()->after('rating_security');
            $table->decimal('rating_pricing', 3, 1)->nullable()->after('rating_landlord');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['rating_security', 'rating_landlord', 'rating_pricing']);
        });
    }
};
