<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Verdachte extends Model
{
    /** @use HasFactory<\Database\Factories\VerdachteFactory> */
    use HasFactory;

    protected $table = 'Verdachte';

    protected $primaryKey = 'verdachte_id';

    protected $keyType = 'int';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'verdachte_id',
        'naam',
        'geslacht',
        'leeftijd',
        'lengte',
        'haarkleur',
        'kleur_ogen',
        'gezichtsbeharing',
        'tatoeages',
        'bril',
        'littekens',
        'schoenmaat',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gezichtsbeharing' => 'boolean',
            'tatoeages' => 'boolean',
            'littekens' => 'boolean',
        ];
    }

    public function misdaden(): HasMany
    {
        return $this->hasMany(Misdaad::class, 'verdachte_id');
    }
}
