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
        if (!Schema::hasTable('room_alerts')) {
            Schema::create('room_alerts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->string('contact_info', 191);
                $table->string('target_district', 150);
                $table->unsignedBigInteger('max_budget');
                $table->string('status', 30)->default('active'); // active, notified, cancelled
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();

                $table->index(['target_district', 'status']);
                $table->index(['contact_info', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_alerts');
    }
};
