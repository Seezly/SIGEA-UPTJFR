<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
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
            'slug' => mb_substr(fake()->unique()->slug(), 0, 50),
            'description' => mb_substr(fake()->sentence(), 0, 255),
            'is_active' => true,
        ];
    }

    public function notActive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }
}
