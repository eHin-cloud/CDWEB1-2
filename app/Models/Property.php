<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'room_count',
        'status', // draft | pending | approved | rejected
        'reject_reason',
    ];

    protected $casts = [
        'room_count' => 'integer',
    ];

    /**
     * Chủ trọ sở hữu bất động sản này
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }
}
