<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class IotDevice extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'device_code',
        'meter_serial',
        'meter_type',
        'protocol',
        'api_key',
        'status',
        'last_reading',
        'last_seen_at',
        'config',
        'firmware_version',
    ];

    protected $casts = [
        'last_reading' => 'decimal:2',
        'last_seen_at' => 'datetime',
        'config' => 'array',
    ];

    protected $appends = [
        'is_online',
        'status_label',
        'protocol_label',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function telemetries(): HasMany
    {
        return $this->hasMany(IotMeterTelemetry::class, 'iot_device_id');
    }

    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }

        // Nếu thiết bị không gửi dữ liệu trong 35 phút (quá 2 chu kỳ 15 phút) -> coi là offline
        return $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes(35));
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_online) {
            return 'Mất kết nối (Offline)';
        }

        return match ($this->status) {
            'warning' => 'Cảnh báo dị thường',
            default => 'Hoạt động bình thường (Online)',
        };
    }

    public function getProtocolLabelAttribute(): string
    {
        return match ($this->protocol) {
            'esp32_wifi' => 'ESP32 (WiFi / 4G)',
            'lorawan' => 'LoRaWAN Long-Range',
            'modbus_rs485' => 'Modbus RS485 Bus',
            'zigbee' => 'Zigbee 3.0 Wireless',
            'mqtt' => 'MQTT Broker Bridge',
            default => strtoupper((string) $this->protocol),
        };
    }

    public static function generateApiKey(): string
    {
        return 'iot_' . Str::random(32);
    }
}
