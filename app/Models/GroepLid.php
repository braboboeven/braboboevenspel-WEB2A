<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroepLid extends Model
{
    /** @use HasFactory<\Database\Factories\GroepLidFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'groep_id',
        'user_id',
        'is_leider',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_leider' => 'boolean',
        ];
    }

    public function groep(): BelongsTo
    {
        return $this->belongsTo(Groep::class);
    }

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
