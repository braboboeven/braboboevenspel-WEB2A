<?php

namespace Database\Factories;

use App\Models\Hint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hint>
 */
class HintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hint_nummer' => fake()->unique()->numberBetween(1, 10000),
            'hint_beschrijving' => fake()->sentence(8),
            'aantal_rows' => fake()->numberBetween(0, 1000),
        ];
    }
}
