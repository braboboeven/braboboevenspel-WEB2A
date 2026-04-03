<?php

namespace App\Models;

use Database\Factories\GroepVerdachteBankFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroepVerdachteBank extends Model
{
    /** @use HasFactory<GroepVerdachteBankFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groep_id',
        'verdachte_nummer',
        'banked_amount',
        'confiscated_amount',
        'last_banked_at',
        'confiscated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_banked_at' => 'datetime',
            'confiscated_at' => 'datetime',
        ];
    }

    public function groep(): BelongsTo
    {
        return $this->belongsTo(Groep::class);
    }
}
