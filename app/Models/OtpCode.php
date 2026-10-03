<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';

    protected $fillable = [
        'target',
        'code',
        'type',
        'attempts',
        'max_attempts',
        'last_sent_at',
        'expires_at',
        'is_verified',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];
}
