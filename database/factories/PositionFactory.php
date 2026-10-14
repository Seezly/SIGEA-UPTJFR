<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id' => Area::factory(),
            'name' => mb_substr(fake()->unique()->word().' '.fake()->unique()->word(), 0, 100),
            'code' => fake()->unique()->numerify('CAR#####'),
            'description' => mb_substr(fake()->sentence(), 0, 255),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the position should be inactive.
     */
    public function notActive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
