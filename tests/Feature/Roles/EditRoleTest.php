<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->role = Role::factory()->create([
        'slug' => 'original-slug',
    ]);
    $this->formData = [
        'name' => 'Updated Name',
    ];
});

/**
 * Authorization Tests
 */

test('Guest cannot edit a role and redirects to login', function () {
    $response = $this->put(route('roles.update', ['roleId' => $this->role->id]), $this->formData);

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit a role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('roles.update', ['roleId' => $this->role->id]), $this->formData);

    $response->assertStatus(403);
});

/**
 * Validation Tests
 */

test('Admin user cannot edit a role that does not exist', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => 9999]), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit role with invalid or missing required fields', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => $this->role->id]), [
            'name' => '',
            'slug' => '',
        ])
        ->assertSessionHasErrors(['name', 'slug']);
});

test('Admin user cannot update role slug to a slug already taken by another role', function () {
    $user = User::factory()->admin()->create();
    $otherrole = role::factory()->create(['slug' => 'taken-slug']);

    $data = array_merge($this->formData, ['slug' => 'taken-slug']);

    $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => $this->role->id]), $data)
        ->assertSessionHasErrors(['slug']);
});

test('Admin user can edit a role keeping the same slug', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => $this->role->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHas('flash.message', 'Rol actualizado exitosamente.')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', [
        'id' => $this->role->id,
        'name' => 'Updated Name',
        'slug' => 'original-slug',
    ]);
});

test('Admin user can edit a role changing its slug', function () {
    $user = User::factory()->admin()->create();
    $data = array_merge($this->formData, ['slug' => 'new-unique-slug']);

    $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => $this->role->id]), $data)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', [
        'id' => $this->role->id,
        'slug' => 'new-unique-slug',
    ]);
});

test('Admin user cannot deactivate a role if it is being used', function () {
    $adminUser = User::factory()->admin()->create();
    $user = User::factory()->create();
    $role = Role::factory()->create([
        'is_active' => true,
    ]);

    $user->assignRole($role);

    $this->actingAs($adminUser)
        ->put(route('roles.update', ['roleId' => $role->id]), [
            'is_active' => false,
        ])
        ->assertSessionHasNoErrors()
        ->assertStatus(403);

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'is_active' => true,
    ]);
});

test('Admin user cannot deactivate admin role', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('roles.update', ['roleId' => $user->role()->id]), [
            'name' => 'Admin',
            'slug' => 'admin',
            'is_active' => false,
        ])
        ->assertSessionHasNoErrors()
        ->assertStatus(403);

    $this->assertDatabaseHas('roles', [
        'id' => $user->role()->id,
        'is_active' => true,
    ]);
});
