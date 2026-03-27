<?php

namespace Database\Seeders;

use App\Models\Hint;
use Illuminate\Database\Seeder;

class HintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hints = [
            ['hint_nummer' => 1, 'hint_beschrijving' => 'Let op het geslacht van de verdachte.', 'aantal_rows' => null],
            ['hint_nummer' => 2, 'hint_beschrijving' => 'Gebruik een filter op leeftijd met < of >.', 'aantal_rows' => null],
            ['hint_nummer' => 3, 'hint_beschrijving' => 'De verdachte draagt geen bril.', 'aantal_rows' => null],
            ['hint_nummer' => 4, 'hint_beschrijving' => 'Kijk naar haarkleur in de tabel Verdachte.', 'aantal_rows' => null],
            ['hint_nummer' => 5, 'hint_beschrijving' => 'Filter op littekens met 0 of 1.', 'aantal_rows' => null],
            ['hint_nummer' => 6, 'hint_beschrijving' => 'Schoenmaat kan helpen bij uitsluiten.', 'aantal_rows' => null],
            ['hint_nummer' => 7, 'hint_beschrijving' => 'Gebruik LIKE met % voor een naam hint.', 'aantal_rows' => null],
            ['hint_nummer' => 8, 'hint_beschrijving' => 'Check misdaad_type in tabel Misdaad.', 'aantal_rows' => null],
            ['hint_nummer' => 9, 'hint_beschrijving' => 'Gedrag in de gevangenis staat in Misdaad.gedrag.', 'aantal_rows' => null],
            ['hint_nummer' => 10, 'hint_beschrijving' => 'Gebruik JOIN tussen Verdachte en Misdaad.', 'aantal_rows' => null],
        ];

        Hint::query()->upsert($hints, ['hint_nummer'], ['hint_beschrijving', 'aantal_rows']);
    }
}
