<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->role = Role::factory()->create();
});

test('Guest cannot delete a role and redirects to login', function () {
    $response = $this->delete(route('roles.destroy', ['roleId' => $this->role->id]));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot delete a role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('roles.destroy', ['roleId' => $this->role->id]));

    $response->assertStatus(403);
});

test('Admin user receives 404 when deleting a non-existent role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('roles.destroy', 999999))
        ->assertStatus(404);
});

test('Admin user can delete an unused role', function () {
    $admin = User::factory()->admin()->create();
    $role = role::factory()->create(['is_active' => false]);

    $this->actingAs($admin)
        ->delete(route('roles.destroy', ['roleId' => $role->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('Admin user cannot delete an active role', function () {
    $admin = User::factory()->admin()->create();
    $role = role::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->delete(route('roles.destroy', ['roleId' => $role->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});
