<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BigBossHint extends Model
{
    /** @use HasFactory<\Database\Factories\BigBossHintFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nummer',
        'beschrijving',
        'lesnummer',
    ];

    public function verzondenHints(): HasMany
    {
        return $this->hasMany(HintVerzending::class, 'big_boss_hint_id');
    }
}
