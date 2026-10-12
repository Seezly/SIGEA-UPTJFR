<?php

use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->formData = [
        'name' => 'Gestión de Proyectos',
    ];
});

/**
 * Authorization Tests
 */
test('Guest can not add skill and redirects to login', function () {
    $response = $this->post(route('skills.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add skill', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('skills.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add skill with auto-generated slug', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('skills.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Habilidad creada exitosamente.');

    $this->assertDatabaseHas('skills', [
        'name' => 'Gestión de Proyectos',
        'slug' => 'gestion-de-proyectos',
        'is_active' => true,
    ]);
});

/**
 * Validation Tests
 */
test('Admin user cannot create a skill with missing required name', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('skills.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('skills', 0);
});

test('Admin user cannot create a skill with an already taken name', function () {
    $admin = User::factory()->admin()->create();

    Skill::factory()->create([
        'name' => 'Gestión de Proyectos',
        'slug' => 'gestion-de-proyectos',
    ]);

    $response = $this->actingAs($admin)->post(route('skills.store'), $this->formData);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('skills', 1);
});

test('Admin user cannot create a skill exceeding maximum name length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('skills.store'), [
        'name' => Str::random(101),
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('skills', 0);
});
