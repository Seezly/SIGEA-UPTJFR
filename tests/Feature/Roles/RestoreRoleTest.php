<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->role->delete();
});

test('Guest cannot restore a role and redirects to login', function () {
    $response = $this->post(route('roles.restore', ['roleId' => $this->role->id]));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('roles.restore', ['roleId' => $this->role->id]));

    $response->assertStatus(403);
});

test('Admin user receives 404 when restoring a non-existent role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('roles.restore', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when restoring a role that is not deleted', function () {
    $admin = User::factory()->admin()->create();
    $activeRole = Role::factory()->create();

    $this->actingAs($admin)
        ->post(route('roles.restore', ['roleId' => $activeRole->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('roles.restore', ['roleId' => $this->role->id]))
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol restaurado exitosamente.');

    $this->assertDatabaseHas('roles', [
        'id' => $this->role->id,
        'deleted_at' => null,
    ]);
});

test('Restoring a role makes its slug available again', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('roles.store'), [
            'name' => 'Restored Role',
            'slug' => $this->role->slug,
        ])
        ->assertSessionHasErrors(['slug']);

    $this->actingAs($admin)
        ->post(route('roles.restore', ['roleId' => $this->role->id]))
        ->assertRedirect();

    $this->assertDatabaseHas('roles', [
        'id' => $this->role->id,
        'slug' => $this->role->slug,
        'deleted_at' => null,
    ]);
    $this->assertDatabaseCount('roles', 2);
});
