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
        if (!Schema::hasTable('otp_codes')) {
            Schema::create('otp_codes', function (Blueprint $table) {
                $table->id();
                $table->string('target', 150)->comment('SĐT hoặc Email nhận mã');
                $table->string('code', 6)->comment('Mã OTP 6 số');
                $table->string('type', 30)->default('register')->comment('Loại OTP: guest_register, landlord_register, etc');
                $table->unsignedTinyInteger('attempts')->default(0)->comment('Số lần đã nhập sai');
                $table->unsignedTinyInteger('max_attempts')->default(5)->comment('Số lần nhập sai tối đa');
                $table->timestamp('last_sent_at')->nullable()->comment('Thời điểm gửi gần nhất (dùng cho cooldown 60s)');
                $table->timestamp('expires_at')->nullable()->comment('Thời điểm hết hạn mã (5 phút)');
                $table->boolean('is_verified')->default(false)->comment('Trạng thái đã xác minh thành công');
                $table->timestamps();

                $table->index(['target', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
