<?php

namespace Database\Seeders;

use App\Models\Gebruiker;
use Illuminate\Database\Seeder;

class GebruikerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gebruiker::factory(10)->create();
    }
}
