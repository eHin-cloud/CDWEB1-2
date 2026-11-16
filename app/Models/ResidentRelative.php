<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentRelative extends Model
{
    use MasksSensitiveAttributes;

    protected $table = 'resident_relatives';

    protected array $sensitiveMaskedAttributes = [];

    protected $fillable = [
        'resident_id',
        'name',
        'dob',
        'cccd',
        'phone',
        'hometown',
        'relationship',
        'temporary_residence_status',
        'start_date',
        'end_date',
        'version'
    ];

    protected $casts = [
        'dob' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function setDobAttribute($value): void
    {
        $this->attributes['dob'] = !empty($value) ? \Carbon\Carbon::parse($value)->toDateString() : null;
    }

    public function setStartDateAttribute($value): void
    {
        $this->attributes['start_date'] = !empty($value) ? \Carbon\Carbon::parse($value)->toDateString() : null;
    }

    public function setEndDateAttribute($value): void
    {
        $this->attributes['end_date'] = !empty($value) ? \Carbon\Carbon::parse($value)->toDateString() : null;
    }

    protected static function booted(): void
    {
        static::saving(function (self $relative): void {
            if ($relative->isDirty('phone')) {
                $relative->phone_blind_index = SensitiveData::blindIndex($relative->phone);
            }

            if ($relative->isDirty('cccd')) {
                $relative->cccd_blind_index = SensitiveData::blindIndex($relative->cccd);
            }
        });
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
