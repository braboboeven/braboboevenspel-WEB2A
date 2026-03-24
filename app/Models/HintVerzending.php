<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HintVerzending extends Model
{
    /** @use HasFactory<\Database\Factories\HintVerzendingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groep_id',
        'hint_nummer',
        'big_boss_hint_id',
        'sent_by_user_id',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function groep(): BelongsTo
    {
        return $this->belongsTo(Groep::class);
    }

    public function hint(): BelongsTo
    {
        return $this->belongsTo(Hint::class, 'hint_nummer', 'hint_nummer');
    }

    public function bigBossHint(): BelongsTo
    {
        return $this->belongsTo(BigBossHint::class, 'big_boss_hint_id');
    }

    public function verzender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
