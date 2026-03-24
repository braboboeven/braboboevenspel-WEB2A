<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opdracht extends Model
{
    /** @use HasFactory<\Database\Factories\OpdrachtFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'titel',
        'prompt',
        'correct_query',
        'source_table',
        'verdachte_nummer',
        'step_nummer',
        'is_big_boss',
        'reward_correct',
        'reward_bad_format',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_big_boss' => 'boolean',
        ];
    }

    public function pogingen(): HasMany
    {
        return $this->hasMany(Poging::class);
    }
}
