<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('address');
            $table->unsignedInteger('total_floors')->default(1)->after('phone');
            $table->string('status', 30)->default('active')->after('total_floors');
            $table->string('image')->nullable()->after('status');
            $table->json('amenities')->nullable()->after('image');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['phone', 'total_floors', 'status', 'image', 'amenities']);
        });
    }
};
