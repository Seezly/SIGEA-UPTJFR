<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access roles page and redirects to login', function () {
    $response = $this->get(route('roles.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access roles page and gets 403 forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('roles.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access roles page', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('roles.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn(Assert $page) => $page->component('Admin/Roles'));
});
