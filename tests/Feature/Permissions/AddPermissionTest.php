<?php

use App\Models\Module;
use App\Models\Permission;
use App\Models\User;

beforeEach(function () {
    $this->module = Module::factory()->create();
    $this->formData = [
        'module_id' => $this->module->id,
        'name' => 'Admin Create Permission',
        'slug' => mb_substr($this->module->slug, 0, 40) . '.create',
        'description' => 'Admin Create Permission'
    ];
});

/**
 * Authorization Tests
 */

test('Guest user cannot create a new permission', function () {
    $response = $this->post(route('permissions.store'), $this->formData);

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot create a new permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('permissions.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can create a new permission', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('permissions.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHas('flash.message', 'Permiso para el módulo: ' . $this->module->name . ' creado satisfactoriamente.')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('permissions', ['slug' => $this->formData['slug']]);
});

/**
 * Validation Tests
 */

test('Admin user cannot create a new permission with missing required fields', function () {
    $admin = User::factory()->admin()->create();

    $missingFormData = [
        'description' => 'Missing required fields'
    ];

    $response = $this->actingAs($admin)
        ->post(route('permissions.store'), $missingFormData)
        ->assertSessionHasErrors(['module_id', 'slug', 'name']);

    $response->assertRedirect();
});

test('Admin user cannot create a new permission with an already taken slug', function () {
    $admin = User::factory()->admin()->create();

    $permission = Permission::factory()->create();

    $takenSlugFormData = [
        'module_id' => $permission->module_id,
        'name' => 'Already Admin Create Permission',
        'slug' => $permission->slug,
        'description' => 'Already Admin Create Permission'
    ];

    $response = $this->actingAs($admin)
        ->post(route('permissions.store'), $takenSlugFormData)
        ->assertSessionHasErrors(['slug']);

    $response->assertRedirect();
});

test('Admin user cannot create a new permission exceeding maximum field lengths', function () {
    $admin = User::factory()->admin()->create();

    $exceedingFormData = [
        'module_id' => $this->module->id,
        'name' => 'Exceeding Admin Create Permission',
        'slug' => $this->module->slug . '.create',
        'description' => 'Exceeding Admin Create Permission'
    ];

    $response = $this->actingAs($admin)
        ->post(route('permissions.store'), $exceedingFormData)
        ->assertSessionHasErrors(['slug']);

    $response->assertRedirect();
});
