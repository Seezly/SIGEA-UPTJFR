<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->formData = [
        'name' => 'test',
        'slug' => 'test',
    ];
});

test('Guest cannot edit a role and redirects to login', function () {
    $response = $this->put(route('roles.update', ['role' => $this->role->id]), $this->formData);

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit a role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('roles.update', ['role' => $this->role->id]), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can edit a role', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->put(route('roles.update', ['role' => $this->role->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol actualizado exitosamente.');

    $this->assertDatabaseHas('roles', ['slug' => $this->formData['slug']]);
});
