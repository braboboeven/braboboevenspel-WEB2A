<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Misdaad extends Model
{
    /** @use HasFactory<\Database\Factories\MisdaadFactory> */
    use HasFactory;

    protected $table = 'Misdaad';

    protected $primaryKey = 'misdaad_id';

    protected $keyType = 'int';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'misdaad_id',
        'verdachte_id',
        'misdaad_type',
        'datum_gepleegd',
        'gevangenis',
        'gedrag',
        'start_datum',
        'eind_datum',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datum_gepleegd' => 'date',
            'start_datum' => 'date',
            'eind_datum' => 'date',
        ];
    }

    public function verdachte(): BelongsTo
    {
        return $this->belongsTo(Verdachte::class, 'verdachte_id');
    }
}
