<?php

use App\Models\Module;
use App\Models\User;

test('Guest cannot delete a module and redirects to login', function () {
    $module = Module::factory()->create();

    $this->delete(route('modules.destroy', ['moduleId' => $module->id]))
        ->assertRedirect(route('login'));
});

test('Authorized non-admin user cannot delete a module', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create();

    $this->actingAs($user)
        ->delete(route('modules.destroy', ['moduleId' => $module->id]))
        ->assertStatus(403);
});

test('Admin user receives 404 when deleting a non-existent module', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('modules.destroy', 999999))
        ->assertStatus(404);
});

test('Admin user can delete an unused module', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create(['is_active' => false]);

    $this->actingAs($admin)
        ->delete(route('modules.destroy', ['moduleId' => $module->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('modules', ['id' => $module->id]);
});

test('Admin user cannot delete an active module', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->delete(route('modules.destroy', ['moduleId' => $module->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('modules', ['id' => $module->id]);
});
