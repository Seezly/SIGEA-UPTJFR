<?php

use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Models\InstitutionalSetting;

/**
 * Authentication Tests
 */

test('Guest user can access about us page', function () {
    $response = $this->get(route('about-us'))->assertInertia(
        fn(Assert $page) => $page
            ->component('AboutUs')
    );

    $response->assertStatus(200);
});

test('Authenticated user can access about us page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('about-us'))->assertInertia(
        fn(Assert $page) => $page
            ->component('AboutUs')
    );

    $response->assertStatus(200);
});

/**
 * Institutional Information Tests
 */

test('About us page shows the current institutional information', function () {
    InstitutionalSetting::factory()->create([
        'key' => 'institution_about_us',
        'value' => 'Here goes the about us information',
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'institution_mission',
        'value' => 'Here goes the about us mission',
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'institution_vision',
        'value' => 'Here goes the about us vision',
    ]);

    $response = $this->get(route('about-us'))->assertInertia(
        fn(Assert $page) => $page
            ->component('AboutUs')
            ->has('institution')
            ->where('institution.about_us', 'Here goes the about us information')
            ->where('institution.mission', 'Here goes the about us mission')
            ->where('institution.vision', 'Here goes the about us vision')
    );

    $response->assertStatus(200);
});

test('About us page handles correctly the content when it is not available', function () {
    InstitutionalSetting::where('key', 'institution_about_us')->delete();

    $response = $this->get(route('about-us'))->assertInertia(
        fn(Assert $page) => $page
            ->component('AboutUs')
            ->has('institution')
            ->where('institution.about_us', 'Información no disponible en este momento')
    );

    $response->assertStatus(200);
});
