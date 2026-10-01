<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
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
