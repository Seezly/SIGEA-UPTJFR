<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->role = Role::factory()->create();
});

test('Guest cannot delete a role and redirects to login', function () {
    $response = $this->delete(route('roles.destroy', ['role' => $this->role->id]));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot delete a role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('roles.destroy', ['role' => $this->role->id]));

    $response->assertStatus(403);
});

test('Admin user can delete a role', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->delete(route('roles.destroy', ['role' => $this->role->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol eliminado exitosamente.');

    $this->assertDatabaseMissing('roles', ['id' => $this->role->id]);
});
