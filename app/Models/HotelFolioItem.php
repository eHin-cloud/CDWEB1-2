<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelFolioItem extends Model
{
    protected $fillable = [
        'booking_id',
        'item_name',
        'item_type',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'booking_id');
    }
}
