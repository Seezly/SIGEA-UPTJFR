<?php

use App\Models\Module;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access the modules page', function () {
    $this->get(route('modules.index'))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access the modules page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('modules.index'))
        ->assertStatus(403);
});

test('Admin user can access the modules page with listed data', function () {
    $user = User::factory()->admin()->create();

    Module::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('modules.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Admin/Modules')
                ->has('modules', 3)
                ->has(
                    'modules.0',
                    fn(Assert $page) => $page
                        ->has('id')
                        ->has('name')
                        ->has('slug')
                        ->etc()
                )
        );
});

test('Modules list includes soft-deleted modules', function () {
    $user = User::factory()->admin()->create();

    Module::factory()->create(['name' => 'Active Module', 'slug' => 'active-module']);
    Module::factory()->create(['name' => 'Deleted Module', 'slug' => 'deleted-module'])->delete();

    $this->actingAs($user)
        ->get(route('modules.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Admin/Modules')
                ->has('modules', 2)
                ->where('modules.1.slug', 'deleted-module')
                ->where('modules.1.deleted_at', fn($value) => $value !== null)
        );
});
