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
        Hint::factory(10)->create();
    }
}
