<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resident extends Model
{
    use MasksSensitiveAttributes;

    protected array $sensitiveMaskedAttributes = [];

    protected $fillable = [
        'tenant_id',
        'room_id',
        'user_id',
        'name',
        'gender',
        'dob',
        'phone',
        'email',
        'cccd',
        'hometown',
        'start_date',
        'status',
        'temporary_residence_status',
        'version'
    ];

    protected $casts = [
        'dob' => 'date',
        'start_date' => 'date',
    ];

    public function setDobAttribute($value): void
    {
        $this->attributes['dob'] = !empty($value) ? \Carbon\Carbon::parse($value)->toDateString() : null;
    }

    public function setStartDateAttribute($value): void
    {
        $this->attributes['start_date'] = !empty($value) ? \Carbon\Carbon::parse($value)->toDateString() : now()->toDateString();
    }

    protected static function booted(): void
    {
        static::saving(function (self $resident): void {
            if ($resident->isDirty('phone')) {
                $resident->phone_blind_index = SensitiveData::blindIndex($resident->phone);
            }

            if ($resident->isDirty('cccd')) {
                $resident->cccd_blind_index = SensitiveData::blindIndex($resident->cccd);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(ResidentRelative::class);
    }
}
