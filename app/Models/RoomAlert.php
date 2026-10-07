<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAlert extends Model
{
    use HasFactory;

    protected $table = 'room_alerts';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'room_id',
        'contact_info',
        'target_district',
        'max_budget',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'max_budget' => 'integer',
        'notified_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
