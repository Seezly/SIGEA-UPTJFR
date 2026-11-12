<?php

use App\Models\People;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Hash;

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
        'terms_of_service' => true,
    ];
});

test('Guest can render register page', function () {
    $this->get(route('register'))
        ->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/Register'));
});

test('Authenticated user is redirected away from register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('dashboard'));
});

test('User can register successfully with personal details', function () {
    $response = $this->post(route('register'), $this->formData);

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
    $this->post(route('register'), [
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

    $response = $this->post(route('register'), $this->formData);

    $response->assertSessionHasErrors(['email', 'id_number']);
    $this->assertGuest();
});

test('User cannot register if passwords do not match', function () {
    $invalidData = array_merge($this->formData, [
        'password_confirmation' => 'different_password',
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['password']);

    $this->assertGuest();
});

test('User cannot register with invalid email format', function () {
    $invalidData = array_merge($this->formData, [
        'email' => 'invalid-email-format',
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

test('User cannot register with invalid birth date format', function () {
    $invalidData = array_merge($this->formData, [
        'birth_date' => 'invalid-date-format',
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['birth_date']);

    $this->assertGuest();
});

test('User cannot register with invalid gender value', function () {
    $invalidData = array_merge($this->formData, [
        'gender' => 'X',
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['gender']);

    $this->assertGuest();
});

test('User cannot register with invalid phone number format', function () {
    $invalidData = array_merge($this->formData, [
        'phone_number' => 'invalid-phone-number',
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['phone_number']);

    $this->assertGuest();
});

test('User registration succeeds and user is not verified by default', function () {
    $response = $this->post(route('register'), $this->formData);

    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $user = User::where('email', 'sergio@example.com')->first();

    $this->assertNotNull($user);
    $this->assertNull($user->email_verified_at);
});

test('User registration succeeds and user is assigned postulante role', function () {
    $response = $this->post(route('register'), $this->formData);

    $response->assertStatus(302)->assertRedirect(route('dashboard'));

    $user = User::where('email', 'sergio@example.com')->first();
    $this->assertNotNull($user);
    $this->assertEquals('postulante', $user->role()?->slug);
});

test('User registration fails if birth date is 18 years ago or less', function () {
    $pastDate = now()->subYears(17)->format('d/m/Y');
    $invalidData = array_merge($this->formData, [
        'birth_date' => $pastDate,
    ]);

    $this->post(route('register'), $invalidData)
        ->assertSessionHasErrors(['birth_date']);

    $this->assertGuest();
});

test('User registration fails if user do not accept terms of service', function () {
    $formData = array_merge($this->formData, [
        'terms_of_service' => false
    ]);

    $this->post(route('register'), $formData)
        ->assertSessionHasErrors(['terms_of_service']);

    $this->assertGuest();
});

test('Password guarded by secure hashing', function () {
    $this->post(route('register'), $this->formData);

    $user = User::where('email', $this->formData['email'])->first();

    expect($user->password)->not()->toBe($this->formData['password']);
    expect(Hash::check($this->formData['password'], $user->password))->toBeTrue();
});

test('Rate limiting on registration attempts', function () {
    for ($i = 0; $i < 6; $i++) {
        $response = $this->post(route('register'), [
            'first_name' => 'Carlos',
            'middle_name' => 'Pérez',
            'id_prefix' => 'V',
            'id_number' => '123456' . $i,
            'address' => 'Av. Principal, Urbanización El Centro, Casa Nro 12',
            'birth_date' => '01/01/2000',
            'gender' => 'M',
            'email' => 'carlos.postulante@sigea.test',
            'phone_number' => '041412345' . $i,
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
            'terms_of_service' => true,
        ]);
    }

    $response->assertRedirect()
        ->assertSessionHas(
            'flash.error',
            'Has excedido el número de intentos. Por favor, inténtalo de nuevo más tarde.'
        );
});
