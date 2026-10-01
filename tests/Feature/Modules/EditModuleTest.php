<?php

use App\Models\User;
use App\Models\Module;

beforeEach(function () {
    $this->module = Module::factory()->create();
    $this->formData = [
        'name' => 'updated',
        'slug' => $this->module->slug,
    ];
});

test('Guest cannot edit modules', function () {
    $response = $this
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData)
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit modules', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can edit modules', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHas('flash.message', 'Módulo actualizado exitosamente.')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('modules', ['slug' => $this->formData['slug']]);
});

test('Admin user cannot edit a module that does not exist', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->put(
            route('modules.update', ['moduleId' => 999]),
            ['name' => 'updated_no_exist', 'slug' => 'updated_no_exist']
        );

    $response->assertStatus(404)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('modules', ['slug' => 'updated_no_exist']);
});
