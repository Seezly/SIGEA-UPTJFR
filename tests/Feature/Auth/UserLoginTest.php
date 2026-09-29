<?php

use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Models\People;

beforeEach(function () {
    $this->people = People::factory()->create();
    $this->user = User::factory()->create([
        'password' => bcrypt('password'),
        'people_id' => $this->people->id,
    ]);
});

test('User can see login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Login'));
});

test('User can login', function () {
    $formData = [
        'email' => $this->user->email,
        'password' => 'password',
    ];

    $response = $this->post('/login', $formData);
    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->user);
});
