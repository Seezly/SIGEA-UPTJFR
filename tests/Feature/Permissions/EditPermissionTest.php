<?php

use App\Models\Permission;
use App\Models\User;

beforeEach(function () {
    $this->permission = Permission::factory()->create();

    $this->formData = [
        'name' => 'Updated name'
    ];
});

test('Guest user cannot edit permissions', function () {
    $response = $this->put(route('permissions.update', ['permissionId' => $this->permission->id]), $this->formData);

    $response->assertRedirect('/login');
});

test('Authenticated non-admin user cannot edit permissions', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->put(route('permissions.update', ['permissionId' => $this->permission->id]), $this->formData);

    $response->assertForbidden();
});

test('Admin user can edit permissions', function () {
    $permission = Permission::factory()->create([
        'name' => 'View reports',
        'slug' => 'view-reports',
    ]);

    $formData = [
        'name' => 'Manage reports',
        'slug' => 'manage-reports'
    ];

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('permissions.update', ['permissionId' => $permission->id]), $formData);

    $response->assertRedirect();

    $permission->refresh();

    expect($permission->name)->toBe('Manage reports')
        ->and($permission->slug)->toBe('manage-reports');
});

test('Admin user cannot edit permissions with missing required fields', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('permissions.update', ['permissionId' => $this->permission->id]), [
            'name' => '',
            'slug' => ''
        ]);

    $response->assertSessionHasErrors(['name', 'slug']);
});

test('Admin user cannot edit permissions with a slug that is already taken by another permission', function () {
    $firstPermission = Permission::factory()->create([
        'slug' => 'manage-users',
    ]);

    $secondPermission = Permission::factory()->create([
        'slug' => 'view-users',
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('permissions.update', ['permissionId' => $firstPermission->id]), ['slug' => 'view-users']);

    $response->assertSessionHasErrors(['slug']);
});

test('Admin user cannot edit permissions that does not exists', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('permissions.update', ['permissionId' => 999]), $this->formData);

    $response->assertNotFound();
});

test('Admin user can edit permissions keeping the same slug', function () {
    $admin = User::factory()->admin()->create();

    $permission = Permission::factory()->create([
        'name' => 'manage accounts',
        'slug' => 'manage-users'
    ]);

    $response = $this->actingAs($admin)
        ->put(
            route('permissions.update', ['permissionId' => $permission->id]),
            [
                'name' => 'Manage accounts',
                'slug' => 'manage-users'
            ]
        )
        ->assertSessionHasNoErrors();

    $response->assertRedirect();

    $this->assertDatabaseHas('permissions', ['slug' => 'manage-users', 'name' => 'Manage accounts']);
});

test('Admin user can edit permissions changing its slug', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('permissions.update', ['permissionId' => $this->permission->id]), [
            'slug' => 'updated-slug'
        ])
        ->assertSessionHasNoErrors();

    $response->assertRedirect();

    $this->assertDatabaseHas('permissions', ['slug' => 'updated-slug']);
});
