<?php

namespace Database\Factories;

use App\Models\Verdachte;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Verdachte>
 */
class VerdachteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'verdachte_id' => fake()->unique()->numberBetween(1, 100000),
            'naam' => fake()->name(),
            'geslacht' => fake()->randomElement(['man', 'vrouw']),
            'leeftijd' => fake()->numberBetween(18, 92),
            'lengte' => fake()->randomElement(['klein', 'gemiddeld', 'groot']),
            'haarkleur' => fake()->randomElement(['blond', 'bruin', 'zwart', 'rood', 'grijs']),
            'kleur_ogen' => fake()->randomElement(['blauw', 'groen', 'bruin']),
            'gezichtsbeharing' => fake()->boolean(),
            'tatoeages' => fake()->boolean(),
            'bril' => fake()->randomElement(['ja', 'nee']),
            'littekens' => fake()->optional()->boolean(),
            'schoenmaat' => fake()->randomElement(['klein', 'gemiddeld', 'groot']),
        ];
    }
}
