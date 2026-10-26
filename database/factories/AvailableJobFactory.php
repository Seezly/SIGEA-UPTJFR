<?php

namespace Database\Factories;

use App\Models\AvailableJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailableJob>
 */
class AvailableJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "area_id" => \App\Models\Area::factory(),
            "position_id" => \App\Models\Position::factory(),
            "job_category_id" => \App\Models\JobCategory::factory(),
            "created_by" => \App\Models\Employee::factory(),
            "title" => fake()->jobTitle(),
            "description" => fake()->paragraph(),
            "requirements" => fake()->paragraph(),
            "responsibilities" => fake()->paragraph(),
            "employment_type" => fake()->randomElement(['full-time', 'part-time', 'contract', 'temporary', 'internship']),
            "vacancies_count" => fake()->numberBetween(1, 10),
            "is_active" => fake()->boolean(),
            "published_at" => fake()->dateTimeBetween('-1 month', 'now'),
            "application_deadline" => fake()->dateTimeBetween('now', '+1 month'),
            "closed_at" => fake()->optional()->dateTimeBetween('now', '2 months'),
        ];
    }
}
