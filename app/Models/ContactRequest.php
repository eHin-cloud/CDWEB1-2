<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'phone',
        'appointment_time',
        'message',
        'status',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->name;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->phone;
    }

    public function getNoteAttribute(): ?string
    {
        return $this->message;
    }
}
