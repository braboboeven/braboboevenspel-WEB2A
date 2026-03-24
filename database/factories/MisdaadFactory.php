<?php

namespace Database\Factories;

use App\Models\Misdaad;
use App\Models\Verdachte;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Misdaad>
 */
class MisdaadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'misdaad_id' => fake()->unique()->numberBetween(1, 100000),
            'verdachte_id' => Verdachte::factory(),
            'misdaad_type' => fake()->randomElement([
                'autodiefstal',
                'openbare geweldpleging',
                'bankfraude',
                'vandalisme',
                'overval',
                'beledigen van ambtenaar in functie',
                'zakkenrollen',
                'openbare dronkenschap',
            ]),
            'datum_gepleegd' => fake()->date('Y-m-d'),
            'gevangenis' => fake()->randomElement(['Bijlmer', 'Alcatraz', 'Grave', 'Vught']),
            'gedrag' => fake()->optional()->randomElement(['goed', 'gemiddeld', 'slecht']),
            'start_datum' => fake()->date('Y-m-d'),
            'eind_datum' => fake()->optional()->date('Y-m-d'),
        ];
    }
}
