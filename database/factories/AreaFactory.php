<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = mb_substr(fake()->unique()->word().' '.fake()->unique()->word(), 0, 100);

        return [
            'name' => $name,
            'code' => fake()->unique()->numerify('AREA#####'),
            'description' => mb_substr(fake()->sentence(), 0, 255),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the area should be inactive.
     */
    public function notActive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
