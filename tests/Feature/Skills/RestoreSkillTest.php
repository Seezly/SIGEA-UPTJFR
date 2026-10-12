<?php

use App\Models\Skill;
use App\Models\User;

beforeEach(function () {
    $this->skill = Skill::factory()->create([
        'name' => 'Test Skill',
        'slug' => 'test-skill',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot restore a skill and redirects to login', function () {
    $this->skill->delete();

    $this->post(route('skills.restore', ['skillId' => $this->skill->id]))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a skill', function () {
    $user = User::factory()->create();
    $this->skill->delete();

    $this->actingAs($user)
        ->post(route('skills.restore', ['skillId' => $this->skill->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when restoring a non-existent skill', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('skills.restore', 999999))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user receives 404 when restoring a skill that is not deleted', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('skills.restore', ['skillId' => $this->skill->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted skill', function () {
    $admin = User::factory()->admin()->create();
    $this->skill->delete();

    $response = $this->actingAs($admin)
        ->post(route('skills.restore', ['skillId' => $this->skill->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Habilidad restaurada exitosamente.');

    $this->assertDatabaseHas('skills', [
        'id' => $this->skill->id,
        'deleted_at' => null,
    ]);
});
