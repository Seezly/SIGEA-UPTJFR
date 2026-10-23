<?php

namespace Database\Factories;

use App\Models\InstitutionalSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionalSetting>
 */
class InstitutionalSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'value' => $this->faker->sentence(),
            'value_type' => $this->faker->randomElement(['string', 'integer', 'boolean']),
        ];
    }
}
