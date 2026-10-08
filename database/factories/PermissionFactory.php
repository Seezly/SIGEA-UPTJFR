<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Module;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create();

        return [
            'module_id' => $module->id,
            'name' => mb_substr(fake()->unique()->name(), 0, 50),
            'slug' => mb_substr(fake()->unique()->slug(), 0, 50),
            'description' => mb_substr(fake()->sentence(), 0, 255)
        ];
    }
}
