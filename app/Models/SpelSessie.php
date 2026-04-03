<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\SpelSessieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpelSessie extends Model
{
    /** @use HasFactory<SpelSessieFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'status',
        'started_at',
        'paused_at',
        'ended_at',
        'total_paused_seconds',
        'created_by_user_id',
        'winner_group_name',
        'winner_total_score',
        'winner_declared_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'ended_at' => 'datetime',
            'winner_declared_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function elapsedSeconds(?CarbonInterface $referenceTime = null): int
    {
        if (! $this->started_at) {
            return 0;
        }

        $endTime = $referenceTime ?? match ($this->status) {
            'running' => now(),
            'paused' => $this->paused_at ?? now(),
            default => $this->ended_at ?? now(),
        };

        $elapsed = $this->started_at->diffInSeconds($endTime) - $this->total_paused_seconds;

        return max(0, (int) $elapsed);
    }

    public function elapsedFormatted(?CarbonInterface $referenceTime = null): string
    {
        $elapsed = $this->elapsedSeconds($referenceTime);

        $minutes = str_pad((string) intdiv($elapsed, 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad((string) ($elapsed % 60), 2, '0', STR_PAD_LEFT);

        return $minutes.':'.$seconds;
    }

    public function pause(): void
    {
        if ($this->status !== 'running') {
            return;
        }

        $this->forceFill([
            'status' => 'paused',
            'paused_at' => now(),
        ])->save();
    }

    public function resume(): void
    {
        if ($this->status !== 'paused') {
            return;
        }

        $pausedSeconds = $this->paused_at
            ? $this->paused_at->diffInSeconds(now())
            : 0;

        $this->forceFill([
            'status' => 'running',
            'paused_at' => null,
            'total_paused_seconds' => (int) $this->total_paused_seconds + $pausedSeconds,
        ])->save();
    }

    public function endGame(): void
    {
        $totalPausedSeconds = (int) $this->total_paused_seconds;

        if ($this->status === 'paused' && $this->paused_at) {
            $totalPausedSeconds += $this->paused_at->diffInSeconds(now());
        }

        $this->forceFill([
            'status' => 'stopped',
            'ended_at' => now(),
            'paused_at' => null,
            'total_paused_seconds' => $totalPausedSeconds,
        ])->save();
    }
}
