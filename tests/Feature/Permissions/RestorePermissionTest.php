<?php

use App\Models\Permission;
use App\Models\User;

beforeEach(function () {
    $this->permission = Permission::factory()->create();
    $this->permission->delete();
});

test('Guest cannot restore a permission and redirects to login', function () {
    $response = $this->post(route('permissions.restore', ['permissionId' => $this->permission->id]));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('permissions.restore', ['permissionId' => $this->permission->id]));

    $response->assertStatus(403);
});

test('Admin user receives 404 when restoring a non-existent permission', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('permissions.restore', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when restoring a permission that is not deleted', function () {
    $admin = User::factory()->admin()->create();
    $activePermission = Permission::factory()->create();

    $this->actingAs($admin)
        ->post(route('permissions.restore', ['permissionId' => $activePermission->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted permission', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('permissions.restore', ['permissionId' => $this->permission->id]))
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Permiso restaurado exitosamente.');

    $this->assertDatabaseHas('permissions', [
        'id' => $this->permission->id,
        'deleted_at' => null,
    ]);
});
