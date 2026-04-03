<?php

namespace App\Models;

use Database\Factories\OpdrachtFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opdracht extends Model
{
    /** @use HasFactory<OpdrachtFactory> */
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
        'big_boss_query_id',
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

    public function bigBossQuery(): BelongsTo
    {
        return $this->belongsTo(BigBossQuery::class);
    }

    public function resolvedCorrectQuery(): string
    {
        if ($this->is_big_boss && $this->bigBossQuery) {
            return $this->bigBossQuery->correct_query;
        }

        return $this->correct_query;
    }

    public function resolvedRewardCorrect(): int
    {
        if ($this->is_big_boss && $this->bigBossQuery) {
            return (int) $this->bigBossQuery->reward_correct;
        }

        return (int) $this->reward_correct;
    }

    public function resolvedBigBossBadFormatPenalty(): int
    {
        if ($this->is_big_boss && $this->bigBossQuery) {
            return (int) $this->bigBossQuery->bad_format_penalty;
        }

        return 500;
    }
}
