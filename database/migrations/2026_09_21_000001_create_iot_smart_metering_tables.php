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
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->string('device_code', 100)->unique();
            $table->string('meter_serial', 100)->index();
            $table->enum('meter_type', ['electricity', 'water'])->default('electricity');
            $table->enum('protocol', ['esp32_wifi', 'lorawan', 'modbus_rs485', 'zigbee', 'mqtt'])->default('esp32_wifi');
            $table->string('api_key', 64)->nullable()->index();
            $table->enum('status', ['online', 'offline', 'warning'])->default('offline');
            $table->decimal('last_reading', 12, 2)->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->json('config')->nullable(); // Lưu các ngưỡng cảnh báo quá tải, rò rỉ
            $table->string('firmware_version', 50)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'meter_type', 'status']);
        });

        Schema::create('iot_meter_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iot_device_id')->nullable()->constrained('iot_devices')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
            $table->enum('meter_type', ['electricity', 'water']);
            $table->string('meter_serial', 100)->index();
            $table->decimal('reading', 12, 2); // Chỉ số tích lũy (kWh hoặc m3)
            $table->decimal('voltage', 6, 2)->nullable(); // Điện áp (V)
            $table->decimal('current', 6, 3)->nullable(); // Dòng điện (A)
            $table->decimal('power', 8, 2)->nullable(); // Công suất tức thời (W)
            $table->decimal('flow_rate', 8, 3)->nullable(); // Lưu lượng tức thời (L/min hoặc m3/h)
            $table->integer('signal_quality')->nullable(); // RSSI (dBm) hoặc LQI
            $table->tinyInteger('battery_level')->nullable(); // % pin cho node LoRaWAN
            $table->json('raw_payload')->nullable(); // Lưu payload gốc
            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->index(['room_id', 'meter_type', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iot_meter_telemetries');
        Schema::dropIfExists('iot_devices');
    }
};
