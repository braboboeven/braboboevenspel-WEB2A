<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poging extends Model
{
    /** @use HasFactory<\Database\Factories\PogingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groep_id',
        'opdracht_id',
        'user_id',
        'submitted_query',
        'is_correct',
        'is_good_format',
        'earned',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'is_good_format' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function groep(): BelongsTo
    {
        return $this->belongsTo(Groep::class);
    }

    public function opdracht(): BelongsTo
    {
        return $this->belongsTo(Opdracht::class);
    }

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
