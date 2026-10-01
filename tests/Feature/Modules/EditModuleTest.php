<?php

use App\Models\User;
use App\Models\Module;

beforeEach(function () {
    $this->module = Module::factory()->create([
        'name' => 'Original Name',
        'slug' => 'original-slug',
    ]);

    $this->formData = [
        'name' => 'Updated Name',
        'slug' => 'original-slug',
        'description' => 'Updated Description',
        'is_active' => true,
    ];
});

/**
 * Authorization Tests
 */

test('Guest cannot edit modules', function () {
    $this->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData)
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit modules', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData)
        ->assertStatus(403);
});

/**
 * Validation Tests
 */

test('Admin user cannot edit a module that does not exist', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => 9999]), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit module with invalid or missing required fields', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), [
            'name' => '',
            'slug' => '',
        ])
        ->assertSessionHasErrors(['name', 'slug']);
});

test('Admin user cannot update module slug to a slug already taken by another module', function () {
    $user = User::factory()->admin()->create();
    $otherModule = Module::factory()->create(['slug' => 'taken-slug']);

    $data = array_merge($this->formData, ['slug' => 'taken-slug']);

    $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $data)
        ->assertSessionHasErrors(['slug']);
});

test('Admin user can edit a module keeping the same slug', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHas('flash.message', 'Módulo actualizado exitosamente.')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('modules', [
        'id' => $this->module->id,
        'name' => 'Updated Name',
        'slug' => 'original-slug',
    ]);
});

test('Admin user can edit a module changing its slug', function () {
    $user = User::factory()->admin()->create();
    $data = array_merge($this->formData, ['slug' => 'new-unique-slug']);

    $this->actingAs($user)
        ->put(route('modules.update', ['moduleId' => $this->module->id]), $data)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('modules', [
        'id' => $this->module->id,
        'slug' => 'new-unique-slug',
    ]);
});
