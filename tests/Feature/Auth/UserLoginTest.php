<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('Guest user can render login page', function () {
    $this->get(route('login'))
        ->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Login'));
});

test('Authenticated user is redirected when attempting to visit login page', function () {
    $this->actingAs($this->user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('User can login with correct credentials', function () {
    $formData = [
        'email' => $this->user->email,
        'password' => 'password',
    ];

    $this->post(route('login'), $formData)
        ->assertStatus(302)
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->user);
});

test('User cannot login with incorrect password', function () {
    $formData = [
        'email' => $this->user->email,
        'password' => 'wrong-password',
    ];

    $this->post(route('login'), $formData)
        ->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

test('User cannot login with missing fields', function () {
    $this->post(route('login'), [
        'email' => '',
        'password' => '',
    ])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

test('Authenticated user can logout', function () {
    $this->actingAs($this->user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
