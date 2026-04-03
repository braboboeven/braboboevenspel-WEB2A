<?php

namespace App\Models;

use Database\Factories\BigBossQueryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BigBossQuery extends Model
{
    /** @use HasFactory<BigBossQueryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'titel',
        'correct_query',
        'reward_correct',
        'bad_format_penalty',
    ];

    public function opdrachten(): HasMany
    {
        return $this->hasMany(Opdracht::class);
    }
}
