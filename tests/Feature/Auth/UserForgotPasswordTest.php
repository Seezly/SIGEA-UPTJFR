<?php

use App\Models\People;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->people = People::factory()->create();
    $this->user = User::factory()->create([
        'people_id' => $this->people->id,
        'password' => bcrypt('password'),
    ]);
});

test('User can render forgot password view', function () {
    $response = $this->get('/forgot-password')
        ->assertInertia(fn(Assert $page) => $page->component('Auth/ForgotPassword'));

    $response->assertStatus(200);
});

test('User can request new forgot password link', function () {
    Notification::fake();

    $formData = [
        'email' => $this->user->email,
    ];

    $response = $this->post('/forgot-password', $formData)
        ->assertRedirect();

    Notification::assertSentTo([
        $this->user,
    ], ResetPassword::class);
});

test('User can not request new forgot password link if not registered', function () {
    Notification::fake();

    $formData = [
        'email' => 'some_email@email.com',
    ];

    $response = $this->post('/forgot-password', $formData)
        ->assertRedirect();

    Notification::assertNothingSentTo($this->user);
});

test('User can render change password view from reset link', function () {
    $token = Password::createToken($this->user);

    $response = $this->get('/reset-password/' . $token)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/ResetPassword'));

    $response->assertStatus(200);
});

test('User can change password from reset link', function () {
    $token = Password::createToken($this->user);

    $formData = [
        'token' => $token,
        'email' => $this->user->email,
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ];

    $response = $this->post('/reset-password', $formData)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertTrue(Hash::check('new_password', $this->user->refresh()->password));
});
