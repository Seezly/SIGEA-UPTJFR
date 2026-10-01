<?php

use App\Models\User;
use App\Models\People;
use App\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access roles page and redirects to login', function () {
    $response = $this->get(route('roles.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access roles page and gets 403 forbidden', function () {
    $people = People::factory()->create();
    $user = User::factory()->create([
        'people_id' => $people->id,
    ]);

    $response = $this->actingAs($user)->get(route('roles.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access roles page', function () {
    $people = People::factory()->create();
    $user = User::factory()->create([
        'people_id' => $people->id,
    ]);

    $adminRole = Role::factory()->create([
        'name' => 'admin',
        'slug' => 'admin',
    ]);

    $user->assignRole($adminRole);

    $response = $this->actingAs($user)->get(route('roles.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn(Assert $page) => $page->component('Admin/Roles'));
});
