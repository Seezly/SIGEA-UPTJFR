<?php

use App\Models\User;

beforeEach(function () {
    $this->formData = [
        'name' => 'Test',
        'slug' => 'test',
        'description' => 'Just a test role'
    ];
});

test('Guest can not add role and redirects to login', function () {
    $response = $this->post(route('roles.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('roles.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add role', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('roles.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol creado exitosamente.');

    $this->assertDatabaseHas('roles', ['slug' => 'test']);
});
