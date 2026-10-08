<?php

use App\Models\Permission;
use App\Models\User;

beforeEach(function () {
    $this->permission = Permission::factory()->create([
        'name' => 'Delete Users',
        'slug' => 'users.delete',
    ]);
});

test('Guest user cannot delete a permission', function () {
    $response = $this->delete(route('permissions.destroy', $this->permission->id));

    $response->assertRedirect(route('login'));
});

test('Unauthorized user cannot delete a permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('permissions.destroy', $this->permission->id));

    $response->assertStatus(403);
});

test('Admin user can delete a permission', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('permissions.destroy', $this->permission->id));

    $response->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseMissing('permissions', [
        'id' => $this->permission->id,
    ]);
});

test('Returns 404 when attempting to delete a non-existent permission', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('permissions.destroy', 999999));

    $response->assertStatus(404);
});
