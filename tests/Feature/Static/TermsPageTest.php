<?php

use Inertia\Testing\AssertableInertia as Assert;
use App\Models\InstitutionalSetting;
use App\Models\User;

test('Guest user can access terms page', function () {
    $response = $this->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
    );

    $response->assertStatus(200);
});

test('Authenticated user can access terms page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
    );

    $response->assertStatus(200);
});

/**
 * Institutional Information Tests
 */

test('terms page shows the current terms of service information', function () {
    $jsonValue = json_encode([
        [
            'id' => 'terms-of-service',
            'title' => 'Terms of Service',
            'content' => 'Here goes the\n\nmarkdown content'
        ]
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'terms_of_service',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
            ->has('terms', 2)
            ->where('terms.sections.0.id', 'terms-of-service')
            ->where('terms.sections.0.title', 'Terms of Service')
            ->where('terms.sections.0.content', 'Here goes the\n\nmarkdown content')
    );

    $response->assertStatus(200);
});

test('terms page handles correctly the content when it is not available', function () {
    InstitutionalSetting::where('key', 'terms_of_service')->delete();

    $response = $this->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
            ->where('terms.sections', 'Información no disponible en este momento')
    );

    $response->assertStatus(200);
});

test('terms page handles correctly the content when it is corrupt or not valid', function () {
    $jsonValue = json_encode([
        [
            'id' => 'terms-of-service',
            'title' => 'Terms of Service',
            'content' => 'Here goes the\n\nmarkdown content'
        ],
        [
            'id' => 'terms-of-service',
            'content' => 'Here goes the\n\nmarkdown content'
        ],
        [
            'id' => 'terms_of_service',
            'title' => 'json',
        ],
        [
            'title' => 'json',
            'content' => 'Here goes the\n\nmarkdown content'
        ]
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'terms_of_service',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
            ->has('terms', 2)
            ->where('terms.sections.0.id', 'terms-of-service')
            ->where('terms.sections.0.title', 'Terms of Service')
            ->where('terms.sections.0.content', 'Here goes the\n\nmarkdown content')
            ->missing('terms.sections.1')
            ->missing('terms.sections.2')
            ->missing('terms.sections.3')
    );

    $response->assertStatus(200);
});

test('terms page only shows specific information', function () {
    $jsonValue = json_encode([
        [
            'id' => 'terms-of-service',
            'title' => 'Terms of Service',
            'content' => 'Here goes the\n\nmarkdown content',
        ]
    ]);

    $terms = InstitutionalSetting::factory()->create([
        'key' => 'terms_of_service',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('terms'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Terms')
            ->has('terms', 2)
            ->where('terms.sections.0.id', 'terms-of-service')
            ->where('terms.sections.0.title', 'Terms of Service')
            ->where('terms.sections.0.content', 'Here goes the\n\nmarkdown content')
            ->where('terms.updated_at', $terms->updated_at->toIso8601String())
    );

    $response->assertStatus(200);
});
