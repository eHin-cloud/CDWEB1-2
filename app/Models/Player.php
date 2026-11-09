<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Player extends Model
{
    use HasFactory, Searchable;

    protected $table = 'players';

    protected $fillable = [
        'name',
        'full_name',
        'nickname',
        'date_of_birth',
        'birth_place',
        'nationality',
        'position',
        'jersey_number',
        'current_club',
        'former_clubs',
        'height',
        'preferred_foot',
        'biography',
        'career_stats',
        'achievements',
        'famous_matches',
        'avatar_url',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'former_clubs' => 'array',
        'career_stats' => 'array',
        'achievements' => 'array',
        'famous_matches' => 'array',
        'jersey_number' => 'integer',
    ];

    /**
     * Dữ liệu index cho Laravel Scout Search
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'full_name' => (string) $this->full_name,
            'nickname' => (string) ($this->nickname ?? ''),
            'nationality' => (string) ($this->nationality ?? ''),
            'position' => (string) ($this->position ?? ''),
            'current_club' => (string) ($this->current_club ?? ''),
            'biography' => (string) ($this->biography ?? ''),
        ];
    }
}

