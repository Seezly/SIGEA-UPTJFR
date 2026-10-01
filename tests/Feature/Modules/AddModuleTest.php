<?php

use App\Models\User;

beforeEach(function () {
    $this->formData = [
        'name' => 'Test Module',
        'slug' => 'test-module',
        'description' => 'This is a test module.',
    ];
});

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
