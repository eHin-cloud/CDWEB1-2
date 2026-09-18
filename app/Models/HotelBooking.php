<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\Aes256GcmEncrypted;
use App\Support\SensitiveData;

class HotelBooking extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'booking_code',
        'guest_name',
        'guest_phone',
        'guest_phone_blind_index',
        'guest_cccd',
        'guest_cccd_blind_index',
        'rental_type',
        'check_in_at',
        'expected_check_out_at',
        'actual_check_out_at',
        'unit_rate',
        'room_amount',
        'service_amount',
        'surcharge_amount',
        'deposit_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'status',
        'note',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'expected_check_out_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'guest_phone' => Aes256GcmEncrypted::class,
        'guest_cccd' => Aes256GcmEncrypted::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $booking): void {
            if ($booking->isDirty('guest_phone')) {
                $booking->guest_phone_blind_index = SensitiveData::blindIndex($booking->guest_phone);
            }
            if ($booking->isDirty('guest_cccd')) {
                $booking->guest_cccd_blind_index = SensitiveData::blindIndex($booking->guest_cccd);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function folioItems(): HasMany
    {
        return $this->hasMany(HotelFolioItem::class, 'booking_id');
    }
}
