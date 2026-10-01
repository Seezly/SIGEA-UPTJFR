<?php

use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('User can see login page', function () {
    $response = $this->get(route('login'));
    $response->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Login'));
});

test('User can login', function () {
    $formData = [
        'email' => $this->user->email,
        'password' => 'password',
    ];

    $response = $this->post(route('login'), $formData);
    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->user);
});
