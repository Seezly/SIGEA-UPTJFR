<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => \App\Models\User::factory(),
            "employee_code" => fake()->unique()->regexify('[A-Z]{3}[0-9]{5}'),
            "hire_date" => fake()->dateTimeBetween('-5 years', 'now'),
            "status" => fake()->randomElement(['active', 'inactive', 'on_leave']),
            "current_area_id" => \App\Models\Area::factory(),
            "current_position_id" => \App\Models\Position::factory(),
        ];
    }
}
