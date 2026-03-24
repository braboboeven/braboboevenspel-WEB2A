<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Groep extends Model
{
    /** @use HasFactory<\Database\Factories\GroepFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'naam',
        'klas',
        'code',
    ];

    public function leden(): HasMany
    {
        return $this->hasMany(GroepLid::class);
    }

    public function gebruikers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'groep_lids')
            ->withPivot('is_leider')
            ->withTimestamps();
    }

    public function pogingen(): HasMany
    {
        return $this->hasMany(Poging::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(GroepScore::class);
    }
}
