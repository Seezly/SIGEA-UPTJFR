<?php

use App\Models\People;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->formData = [
        'first_name' => 'Sergio',
        'second_name' => 'Manuel',
        'middle_name' => 'Gutierrez',
        'last_name' => 'Lopez',
        'id_prefix' => 'V',
        'id_number' => '28273564',
        'address' => 'Barinas, Venezuela',
        'birth_date' => '06/11/2001',
        'gender' => 'M',
        'email' => 'sergio@example.com',
        'phone_number' => '04245412882',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];
});

test('Guest can render register page', function () {
    $this->get(route('register.create'))
        ->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Register'));
});

test('Authenticated user is redirected away from register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register.create'))
        ->assertRedirect(route('dashboard'));
});

test('User can register successfully with personal details', function () {
    $response = $this->post(route('register.create'), $this->formData);

    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'sergio@example.com',
    ]);

    $this->assertDatabaseHas('people', [
        'id_number' => '28273564',
        'id_prefix' => 'V',
    ]);

    $this->assertAuthenticated();
});

test('User cannot register with missing required fields', function () {
    $this->post(route('register.create'), [
        'first_name' => '',
        'email' => '',
        'password' => '',
    ])
        ->assertSessionHasErrors(['first_name', 'email', 'password']);

    $this->assertGuest();
});

test('User cannot register with an already registered email or id_number', function () {
    User::factory()->create(['email' => 'sergio@example.com']);
    People::factory()->create(['id_number' => '28273564']);

    $response = $this->post(route('register.create'), $this->formData);

    $response->assertSessionHasErrors(['email', 'id_number']);
    $this->assertGuest();
});

test('User cannot register if passwords do not match', function () {
    $invalidData = array_merge($this->formData, [
        'password_confirmation' => 'different_password',
    ]);

    $this->post(route('register.create'), $invalidData)
        ->assertSessionHasErrors(['password']);

    $this->assertGuest();
});
