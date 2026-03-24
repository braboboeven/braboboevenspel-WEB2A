<?php

namespace Database\Factories;

use App\Models\Gebruiker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gebruiker>
 */
class GebruikerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naam' => fake()->name(),
            'Tijd' => fake()->time('H:i:s'),
            'Score' => fake()->numberBetween(0, 1500),
            'Klas' => (string) fake()->numberBetween(1, 6),
            'geblevenVraag' => fake()->numberBetween(0, 10),
            'hintsGebruikt' => fake()->numberBetween(0, 5),
        ];
    }
}
