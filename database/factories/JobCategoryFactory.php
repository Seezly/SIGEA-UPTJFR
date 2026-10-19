<?php

namespace Database\Factories;

use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobCategory>
 */
class JobCategoryFactory extends Factory
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
            'slug' => Str::slug($name),
            'description' => mb_substr(fake()->sentence(), 0, 255),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the job category should be inactive.
     */
    public function notActive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
