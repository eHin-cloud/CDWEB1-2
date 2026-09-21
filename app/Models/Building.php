<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'description',
        'phone',
        'total_floors',
        'status',
        'image',
        'amenities',
        'property_type',
        'checkin_time',
        'checkout_time',
    ];

    protected $casts = [
        'amenities' => 'array',
        'total_floors' => 'integer',
    ];

    public function isHotel(): bool
    {
        return $this->property_type === 'hotel';
    }

    public function isApartment(): bool
    {
        return $this->property_type === 'apartment';
    }

    public function isBoarding(): bool
    {
        return $this->property_type === 'boarding' || empty($this->property_type);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function equipment()
    {
        return $this->hasManyThrough(RoomEquipment::class, Room::class);
    }
}
