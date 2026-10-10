<?php

use App\Models\Module;
use App\Models\User;

beforeEach(function () {
    $this->module = Module::factory()->create();
    $this->module->delete();
});

test('Guest cannot restore a module and redirects to login', function () {
    $response = $this->post(route('modules.restore', ['moduleId' => $this->module->id]));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a module', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('modules.restore', ['moduleId' => $this->module->id]));

    $response->assertStatus(403);
});

test('Admin user receives 404 when restoring a non-existent module', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('modules.restore', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when restoring a module that is not deleted', function () {
    $admin = User::factory()->admin()->create();
    $activeModule = Module::factory()->create();

    $this->actingAs($admin)
        ->post(route('modules.restore', ['moduleId' => $activeModule->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted module', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('modules.restore', ['moduleId' => $this->module->id]))
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Módulo restaurado exitosamente.');

    $this->assertDatabaseHas('modules', [
        'id' => $this->module->id,
        'deleted_at' => null,
    ]);
});
