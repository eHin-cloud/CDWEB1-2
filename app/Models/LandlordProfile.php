<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordProfile extends Model
{
    use MasksSensitiveAttributes;

    protected array $sensitiveMaskedAttributes = [
        'phone' => 'phone',
        'national_id' => 'national_id',
        'bank_account_number' => 'bank_account',
    ];

    protected $fillable = [
        'user_id',
        'tenant_id',
        'full_name',
        'username',
        'phone',
        'email',
        'password',
        'national_id',
        'permanent_address',
        'bank_account_number',
        'bank_name',
        'business_license',
        'verification_method',
        'property_name',
        'property_address',
        'status',
        'verification_status',
        'reject_reason',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'phone' => Aes256GcmEncrypted::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $profile): void {
            if ($profile->isDirty('phone')) {
                $profile->phone_blind_index = SensitiveData::blindIndex($profile->phone);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
