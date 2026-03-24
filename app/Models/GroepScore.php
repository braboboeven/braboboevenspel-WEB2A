<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroepScore extends Model
{
    /** @use HasFactory<\Database\Factories\GroepScoreFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groep_id',
        'score',
        'big_boss_score',
        'last_submission_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_submission_at' => 'datetime',
        ];
    }

    public function groep(): BelongsTo
    {
        return $this->belongsTo(Groep::class);
    }
}
