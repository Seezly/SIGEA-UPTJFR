<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->unverifiedUser = User::factory()->unverified()->create();
});

test('Unverified user can render verification email view', function () {
    $response = $this->actingAs($this->unverifiedUser)->get(route('verification.notice'));

    $response->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Auth/VerifyEmail'));
});

test('Unverified user can request a new verification email', function () {
    Notification::fake();

    $response = $this->actingAs($this->unverifiedUser)->post(route('verification.send'));

    Notification::assertSentTo([$this->unverifiedUser], VerifyEmail::class);

    $response->assertRedirect();
});

test('User is redirected away from verification email view', function () {
    $verifiedUser = User::factory()->create();

    $response = $this->actingAs($verifiedUser)->get(route('verification.notice'));

    $response->assertRedirect(route('dashboard'));
});

test('User can verify email using a valid signed URL', function () {
    Event::fake();

    $verificationUrl = URL::signedRoute(
        'verification.verify',
        [
            'id' => $this->unverifiedUser->id,
            'hash' => sha1($this->unverifiedUser->getEmailForVerification()),
        ]
    );

    $response = $this->actingAs($this->unverifiedUser)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    $this->assertTrue($this->unverifiedUser->fresh()->hasVerifiedEmail());

    $response->assertRedirect(route('dashboard') . '?verified=1');
});

test('User cannot verify email with an invalid or tampered signature', function () {
    $tamperedUrl = URL::signedRoute(
        'verification.verify',
        [
            'id' => $this->unverifiedUser->id,
            'hash' => sha1('invalid-email@example.com'),
        ]
    );

    $this->actingAs($this->unverifiedUser)
        ->get($tamperedUrl)
        ->assertStatus(403);

    $this->assertFalse($this->unverifiedUser->fresh()->hasVerifiedEmail());
});
