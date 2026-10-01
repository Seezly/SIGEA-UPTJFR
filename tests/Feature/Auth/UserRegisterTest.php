<?php

use Inertia\Testing\AssertableInertia as Assert;

test('User can register', function () {
    $response = $this->get(route('register.create'));
    $response->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Register'));

    $formData = [
        'first_name' => 'Sergio',
        'second_name' => 'Manuel',
        'middle_name' => 'Gutierrez',
        'last_name' => 'Lopez',
        'id_prefix' => 'V',
        'id_number' => '28273564',
        'address' => 'Barinas, Venezuela',
        'birth_date' => '06/11/2001',
        'gender' => 'M',
        'email' => 'sergiogutierrez0611@gmail.com',
        'phone_number' => '04245412882',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post(route('register.create'), $formData);
    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'sergiogutierrez0611@gmail.com',
    ]);

    $this->assertDatabaseHas('people', [
        'id_number' => '28273564',
    ]);
});
