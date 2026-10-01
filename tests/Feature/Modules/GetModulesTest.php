<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access the modules page', function () {
    $response = $this->get(route('modules.index'))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access the modules page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('modules.index'));

    $response->assertStatus(403);
});

test('Admin user can access the modules page', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('modules.index'))
        ->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Modules'));
});
