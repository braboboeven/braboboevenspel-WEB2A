<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gebruiker extends Model
{
    /** @use HasFactory<\Database\Factories\GebruikerFactory> */
    use HasFactory;

    protected $table = 'Gebruiker';

    protected $primaryKey = 'Gebruiker_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'naam',
        'Tijd',
        'Score',
        'Klas',
        'geblevenVraag',
        'hintsGebruikt',
    ];
}
