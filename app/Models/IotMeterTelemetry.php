<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IotMeterTelemetry extends Model
{
    protected $fillable = [
        'iot_device_id',
        'room_id',
        'meter_type',
        'meter_serial',
        'reading',
        'voltage',
        'current',
        'power',
        'flow_rate',
        'signal_quality',
        'battery_level',
        'raw_payload',
        'recorded_at',
    ];

    protected $casts = [
        'reading' => 'decimal:2',
        'voltage' => 'decimal:2',
        'current' => 'decimal:3',
        'power' => 'decimal:2',
        'flow_rate' => 'decimal:3',
        'raw_payload' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(IotDevice::class, 'iot_device_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
