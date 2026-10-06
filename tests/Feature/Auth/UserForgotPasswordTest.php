<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('User can render forgot password view', function () {
    $response = $this->get(route('password.request'))
        ->assertInertia(fn(Assert $page) => $page->component('Auth/ForgotPassword'));

    $response->assertStatus(200);
});

test('User can request new forgot password link', function () {
    Notification::fake();

    $formData = [
        'email' => $this->user->email,
    ];

    $response = $this->post(route('password.email'), $formData)
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

    $response = $this->post(route('password.email'), $formData)
        ->assertRedirect();

    Notification::assertNothingSent();
});

test('User can render change password view from reset link', function () {
    $token = Password::createToken($this->user);

    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $this->user->email]))
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Auth/ResetPassword')
                ->where('token', $token)
                ->where('email', $this->user->email)
        );

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

    $response = $this->post(route('password.store'), $formData)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertTrue(Hash::check('new_password', $this->user->refresh()->password));
});

test('User cannot change password using an invalid reset token', function () {
    $formData = [
        'token' => 'invalid-token-12345',
        'email' => $this->user->email,
        'password' => 'new_secure_password',
        'password_confirmation' => 'new_secure_password',
    ];

    $this->post(route('password.store'), $formData)
        ->assertSessionHasErrors(['email']);

    $this->assertFalse(Hash::check('new_secure_password', $this->user->refresh()->password));
});
