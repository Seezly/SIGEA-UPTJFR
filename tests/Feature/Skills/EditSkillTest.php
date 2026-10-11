<?php

use App\Models\Skill;
use App\Models\User;

beforeEach(function () {
    $this->skill = Skill::factory()->create([
        'name' => 'Original Skill',
        'slug' => 'original-skill',
    ]);

    $this->formData = [
        'name' => 'Updated Skill',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot edit skill and redirects to login', function () {
    $this->put(route('skills.update', ['skillId' => $this->skill->id]), $this->formData)
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit skill', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('skills.update', ['skillId' => $this->skill->id]), $this->formData)
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user cannot edit a skill that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('skills.update', ['skillId' => 9999]), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit skill with missing required name', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('skills.update', ['skillId' => $this->skill->id]), ['name' => ''])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('skills', ['name' => '']);
});

test('Admin user cannot update skill name to a name already taken by another skill', function () {
    $admin = User::factory()->admin()->create();

    Skill::factory()->create([
        'name' => 'Taken Name',
        'slug' => 'taken-name',
    ]);

    $this->actingAs($admin)
        ->put(route('skills.update', ['skillId' => $this->skill->id]), ['name' => 'Taken Name'])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('skills', ['id' => $this->skill->id, 'name' => 'Taken Name']);
});

/**
 * Business Rules Tests
 */
test('Admin user can rename a skill and its slug is regenerated', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('skills.update', ['skillId' => $this->skill->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Habilidad actualizada exitosamente.');

    $this->assertDatabaseHas('skills', [
        'id' => $this->skill->id,
        'name' => 'Updated Skill',
        'slug' => 'updated-skill',
    ]);
});
