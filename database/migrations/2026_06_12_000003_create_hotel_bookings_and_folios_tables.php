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
        if (!Schema::hasTable('hotel_bookings')) {
            Schema::create('hotel_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('room_id');
                $table->string('booking_code', 30)->unique();
                $table->string('guest_name');
                $table->text('guest_phone')->nullable();
                $table->string('guest_phone_blind_index', 64)->nullable();
                $table->text('guest_cccd')->nullable();
                $table->string('guest_cccd_blind_index', 64)->nullable();
                $table->string('rental_type', 20)->default('day'); // 'day', 'hour'
                $table->timestamp('check_in_at')->useCurrent();
                $table->timestamp('expected_check_out_at')->nullable();
                $table->timestamp('actual_check_out_at')->nullable();
                $table->decimal('unit_rate', 12, 2)->default(0); // đơn giá giờ hoặc ngày áp dụng
                $table->decimal('room_amount', 12, 2)->default(0);
                $table->decimal('service_amount', 12, 2)->default(0);
                $table->decimal('surcharge_amount', 12, 2)->default(0); // phụ thu check-in sớm / check-out trễ
                $table->decimal('deposit_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('payment_status', 20)->default('unpaid'); // 'unpaid', 'paid'
                $table->string('payment_method', 30)->nullable(); // 'cash', 'vietqr', 'transfer'
                $table->string('status', 20)->default('checked_in'); // 'checked_in', 'checked_out', 'cancelled'
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
                $table->index(['tenant_id', 'status']);
                $table->index('room_id');
            });
        }

        if (!Schema::hasTable('hotel_folio_items')) {
            Schema::create('hotel_folio_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('item_name');
                $table->string('item_type', 30)->default('minibar'); // 'minibar', 'service', 'surcharge'
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('hotel_bookings')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_folio_items');
        Schema::dropIfExists('hotel_bookings');
    }
};
