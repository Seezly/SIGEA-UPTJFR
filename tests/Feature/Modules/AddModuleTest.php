<?php

use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->formData = [
        'name' => 'Test Module',
        'slug' => 'test-module',
        'description' => 'This is a test module.',
    ];
});

/**
 * Authorization Tests
 */

test('Guest user cannot create a new module', function () {
    $response = $this->post(route('modules.store'), $this->formData);

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot create a new module', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('modules.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can create a new module', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('modules.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHas('flash.message', 'Módulo creado exitosamente.')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('modules', ['slug' => $this->formData['slug']]);
});

/**
 * Validation Tests
 */

test('Admin user cannot create a module with missing required fields', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('modules.store'), [
        'name' => '',
        'slug' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'slug']);
    $this->assertDatabaseEmpty('modules');
});

test('Admin user cannot create a module with an already taken slug', function () {
    $user = User::factory()->admin()->create();

    Module::factory()->create(['slug' => $this->formData['slug']]);

    $response = $this->actingAs($user)->post(route('modules.store'), $this->formData);

    $response->assertSessionHasErrors(['slug']);
});

test('Admin user cannot create a module exceeding maximum field lengths', function () {
    $user = User::factory()->admin()->create();

    $invalidData = array_merge($this->formData, [
        'name' => Str::random(51),
        'slug' => Str::random(51),
    ]);

    $response = $this->actingAs($user)->post(route('modules.store'), $invalidData);

    $response->assertSessionHasErrors(['name', 'slug']);
});
