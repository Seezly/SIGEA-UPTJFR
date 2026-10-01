<?php

use App\Models\People;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->people = People::factory()->create();

    $this->unverifiedUser = User::factory()->unverified()->create([
        'password' => bcrypt('password'),
        'people_id' => $this->people->id,
    ]);
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
    $verifiedUser = User::factory()->create([
        'people_id' => $this->people->id,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($verifiedUser)->get(route('verification.notice'));

    $response->assertRedirect(route('dashboard'));
});
