<?php

namespace Database\Seeders;

use App\Models\BigBossHint;
use Illuminate\Database\Seeder;

class BigBossHintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hints = [
            ['nummer' => 1, 'beschrijving' => 'De Big Boss komt voor in de Misdaad tabel.', 'lesnummer' => 1],
            ['nummer' => 2, 'beschrijving' => 'Het gedrag in de gevangenis is "slecht".', 'lesnummer' => 1],
            ['nummer' => 3, 'beschrijving' => 'Zoek naar misdaden gepleegd na 2018.', 'lesnummer' => 2],
            ['nummer' => 4, 'beschrijving' => 'De Big Boss zit of zat in gevangenis "Vught".', 'lesnummer' => 2],
            ['nummer' => 5, 'beschrijving' => 'Misdaad type bevat het woord "fraude".', 'lesnummer' => 3],
            ['nummer' => 6, 'beschrijving' => 'De verdachte heeft haarkleur "zwart" of "bruin".', 'lesnummer' => 3],
            ['nummer' => 7, 'beschrijving' => 'Er is geen bril zichtbaar.', 'lesnummer' => 4],
            ['nummer' => 8, 'beschrijving' => 'De leeftijd ligt boven de 35.', 'lesnummer' => 4],
            ['nummer' => 9, 'beschrijving' => 'Zoek naar misdaden met datum_gepleegd in het laatste jaar.', 'lesnummer' => 5],
            ['nummer' => 10, 'beschrijving' => 'De Big Boss heeft meerdere misdaden.', 'lesnummer' => 5],
            ['nummer' => 11, 'beschrijving' => 'Combineer Verdachte en Misdaad via verdachte_id.', 'lesnummer' => 6],
            ['nummer' => 12, 'beschrijving' => 'Selecteer unieke verdachte_id waarden.', 'lesnummer' => 6],
            ['nummer' => 13, 'beschrijving' => 'Gebruik DISTINCT om dubbels te voorkomen.', 'lesnummer' => 7],
            ['nummer' => 14, 'beschrijving' => 'De Big Boss is een man.', 'lesnummer' => 7],
            ['nummer' => 15, 'beschrijving' => 'Combineer gevangenis, gedrag en misdaad_type.', 'lesnummer' => 8],
            ['nummer' => 16, 'beschrijving' => 'Filter op geslacht en haarkleur in Verdachte.', 'lesnummer' => 8],
        ];

        BigBossHint::query()->upsert($hints, ['nummer'], ['beschrijving', 'lesnummer']);
    }
}
