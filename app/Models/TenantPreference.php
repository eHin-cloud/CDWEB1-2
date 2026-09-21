<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPreference extends Model
{
    use HasFactory;

    protected $table = 'tenant_preferences';

    protected $fillable = [
        'user_id',
        'area_tags',
    ];

    protected $casts = [
        'area_tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
