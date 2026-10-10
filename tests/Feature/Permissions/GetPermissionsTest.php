<?php

use App\Models\Permission;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->permissions = Permission::factory()->createMany([
        ['name' => 'View Users', 'slug' => 'users.view', 'created_at' => now()->subDays(2)],
        ['name' => 'Create Users', 'slug' => 'users.create', 'created_at' => now()->subDay()],
        ['name' => 'Delete Users', 'slug' => 'users.delete', 'created_at' => now()],
    ]);
});

test('Guest user is redirected when trying to view permissions list', function () {
    $response = $this->get(route('permissions.index'));

    $response->assertRedirect(route('login'));
});

test('Unauthorized user receives forbidden response when viewing permissions list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('permissions.index'));

    $response->assertForbidden();
});

test('Admin user can view list of permissions', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->get(route('permissions.index'));

    $response->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Admin/Permissions')
                ->has('permissions', 3)
                ->where('permissions.0.slug', 'users.view')
        );
});

test('Permissions list includes soft-deleted permissions', function () {
    $admin = User::factory()->admin()->create();

    Permission::factory()->create(['name' => 'Deleted Permission', 'slug' => 'users.deleted'])->delete();

    $response = $this->actingAs($admin)
        ->get(route('permissions.index'));

    $response->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Admin/Permissions')
                ->has('permissions', 4)
                ->where('permissions.3.slug', 'users.deleted')
                ->where('permissions.3.deleted_at', fn($value) => $value !== null)
        );
});
