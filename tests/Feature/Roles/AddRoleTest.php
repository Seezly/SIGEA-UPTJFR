<?php

use App\Models\People;
use App\Models\User;
use App\Models\Role;

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
    $people = People::factory()->create();
    $user = User::factory()->create([
        'people_id' => $people->id,
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($user)->post(route('roles.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add role', function () {
    $people = People::factory()->create();
    $admin = User::factory()->create([
        'people_id' => $people->id,
        'password' => bcrypt('password'),
    ]);

    $adminRole = Role::factory()->create([
        'name' => 'admin',
        'slug' => 'admin',
    ]);

    $admin->assignRole($adminRole);

    $response = $this->actingAs($admin)->post(route('roles.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol creado exitosamente.');

    $this->assertDatabaseHas('roles', ['slug' => 'test']);
});
