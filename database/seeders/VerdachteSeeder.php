<?php

namespace Database\Seeders;

use App\Models\Verdachte;
use Illuminate\Database\Seeder;

class VerdachteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Verdachte::factory(10)->create();
    }
}
