<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hint extends Model
{
    /** @use HasFactory<\Database\Factories\HintFactory> */
    use HasFactory;

    protected $primaryKey = 'hint_nummer';

    protected $keyType = 'int';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hint_nummer',
        'hint_beschrijving',
        'aantal_rows',
    ];
}
