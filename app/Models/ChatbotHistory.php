<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotHistory extends Model
{
    use HasFactory;

    protected $table = 'chatbot_histories';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'session_id',
        'message',
        'response',
        'matched_room_ids',
        'error_code',
        'used_ai',
    ];

    protected $casts = [
        'matched_room_ids' => 'array',
        'used_ai' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
