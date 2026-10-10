<?php

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->permissions = Permission::factory(3)->create();

    $this->formData = [
        'name' => 'Test',
        'slug' => 'test',
        'description' => 'Just a test role',
        'permission_ids' => $this->permissions->pluck('id')->toArray(),
    ];
});

/**
 * Authorization Tests
 */

test('Guest can not add role and redirects to login', function () {
    $response = $this->post(route('roles.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add role', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('roles.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add role with permissions in a single transaction', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('roles.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol creado exitosamente.');

    $this->assertDatabaseHas('roles', ['slug' => 'test']);

    $role = Role::where('slug', 'test')->first();

    foreach ($this->formData['permission_ids'] as $permissionId) {
        $this->assertDatabaseHas('permission_roles', [
            'role_id' => $role->id,
            'permission_id' => $permissionId
        ]);
    };
});

test('Admin user can add role without permissions', function () {
    $admin = User::factory()->admin()->create();

    $roleFormData = [
        'name' => 'WO Permissions',
        'slug' => 'wo-permissions',
        'permission_ids' => [],
    ];

    $response = $this->actingAs($admin)->post(route('roles.store'), $roleFormData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Rol creado exitosamente.');

    $this->assertDatabaseHas('roles', ['slug' => 'wo-permissions']);

    $role = Role::where('slug', 'wo-permissions')->first();

    $this->assertDatabaseMissing('permission_roles', ['role_id' => $role->id]);
});

/**
 * Validation Tests
 */

test('Admin user cannot create a role with missing required fields', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('roles.store'), [
        'name' => '',
        'slug' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'slug']);
    $this->assertDatabaseCount('roles', 1);
});

test('Admin user cannot create a role with an already taken slug', function () {
    $user = User::factory()->admin()->create();

    Role::factory()->create(['slug' => $this->formData['slug']]);

    $response = $this->actingAs($user)->post(route('roles.store'), $this->formData);

    $response->assertSessionHasErrors(['slug']);
});

test('Admin user cannot create a role exceeding maximum field lengths', function () {
    $user = User::factory()->admin()->create();

    $invalidData = array_merge($this->formData, [
        'name' => Str::random(51),
        'slug' => Str::random(51),
    ]);

    $response = $this->actingAs($user)->post(route('roles.store'), $invalidData);

    $response->assertSessionHasErrors(['name', 'slug']);
});

test('Admin user cannot create a role with invalid permission Ids', function () {
    $admin = User::factory()->admin()->create();

    $invalidFormData = [
        'name' => 'Invalid Permission',
        'slug' => 'invalid-permission',
        'permission_ids' => [999],
    ];

    $response = $this->actingAs($admin)->post(route('roles.store'), $invalidFormData);

    $response->assertSessionHasErrors();

    $this->assertDatabaseMissing('roles', ['slug' => 'invalid-permission']);
});
