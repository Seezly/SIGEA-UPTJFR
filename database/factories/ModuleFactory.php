<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => mb_substr(fake()->unique()->word(), 0, 50),
            'slug' => mb_substr($this->faker->unique()->slug(), 0, 50),
            'description' => mb_substr($this->faker->sentence(), 0, 255),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
