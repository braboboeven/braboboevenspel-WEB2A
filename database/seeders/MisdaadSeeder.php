<?php

namespace Database\Seeders;

use App\Models\Misdaad;
use Illuminate\Database\Seeder;

class MisdaadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Misdaad::factory(10)->create();
    }
}
