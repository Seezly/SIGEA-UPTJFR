<?php

use App\Models\People;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->people = People::factory()->create();
    $this->user = User::factory()->create([
        'people_id' => $this->people->id,
        'password' => bcrypt('password'),
    ]);
});

test('Logged user can render change password view', function () {
    $response = $this->actingAs($this->user)->get(route('password.edit'));

    $response->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Settings/Password'));
});

test('Guest can not render change password view', function () {
    $response = $this->get(route('password.edit'));

    $response->assertRedirect(route('login'));
});

test('Logged user can change password and login', function () {
    $formData = [
        'current_password' => 'password',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ];

    $this->actingAs($this->user)
        ->put(route('password.update'), $formData)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertTrue(Hash::check('new_password', $this->user->refresh()->password));

    $this->post(route('logout'));

    $formData = [
        'email' => $this->user->email,
        'password' => 'new_password',
    ];

    $this->post(route('login'), $formData)
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->user);
});

test('User can not change password using incorrect password', function () {
    $formData = [
        'current_password' => 'passsword',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ];

    $this->actingAs($this->user)
        ->put(route('password.update'), $formData)
        ->assertSessionHasErrors(['current_password'])
        ->assertRedirect();

    $this->assertFalse(Hash::check('new_password', $this->user->refresh()->password));
});
