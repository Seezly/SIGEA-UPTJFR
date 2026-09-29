<?php

namespace Database\Factories;

use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<People>
 */
class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->name(),
            'second_name' => fake()->name(),
            'middle_name' => fake()->name(),
            'last_name' => fake()->name(),
            'id_prefix' => fake()->randomLetter(),
            'id_number' => fake()->randomNumber(8),
            'address' => fake()->address(),
            'birth_date' => fake()->date('d/m/Y'),
            'gender' => fake()->randomLetter(),
        ];
    }
}
