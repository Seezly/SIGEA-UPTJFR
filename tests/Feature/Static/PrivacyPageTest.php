<?php

use Inertia\Testing\AssertableInertia as Assert;
use App\Models\InstitutionalSetting;
use App\Models\User;

test('Guest user can access privacy policies page', function () {
    $response = $this->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
    );

    $response->assertStatus(200);
});

test('Authenticated user can access privacy policies page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
    );

    $response->assertStatus(200);
});

/**
 * Institutional Information Tests
 */

test('Privacy policies page shows the current privacy policies information', function () {
    $jsonValue = json_encode([
        [
            'id' => 'privacy-policies',
            'title' => 'Privacy Policies',
            'content' => 'Here goes the\n\nmarkdown content'
        ]
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'privacy_policies',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
            ->has('policies', 2)
            ->where('policies.sections.0.id', 'privacy-policies')
            ->where('policies.sections.0.title', 'Privacy Policies')
            ->where('policies.sections.0.content', 'Here goes the\n\nmarkdown content')
    );

    $response->assertStatus(200);
});

test('Privacy policies page handles correctly the content when it is not available', function () {
    InstitutionalSetting::where('key', 'privacy_policies')->delete();

    $response = $this->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
            ->where('policies.sections', 'Información no disponible en este momento')
    );

    $response->assertStatus(200);
});

test('Privacy policies page handles correctly the content when it is corrupt or not valid', function () {
    $jsonValue = json_encode([
        [
            'id' => 'privacy-policies',
            'title' => 'Privacy Policies',
            'content' => 'Here goes the\n\nmarkdown content'
        ],
        [
            'id' => 'privacy-policies',
            'content' => 'Here goes the\n\nmarkdown content'
        ],
        [
            'id' => 'privacy_policies',
            'title' => 'json',
        ],
        [
            'title' => 'json',
            'content' => 'Here goes the\n\nmarkdown content'
        ]
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'privacy_policies',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
            ->has('policies', 2)
            ->where('policies.sections.0.id', 'privacy-policies')
            ->where('policies.sections.0.title', 'Privacy Policies')
            ->where('policies.sections.0.content', 'Here goes the\n\nmarkdown content')
            ->missing('policies.sections.1')
            ->missing('policies.sections.2')
            ->missing('policies.sections.3')
    );

    $response->assertStatus(200);
});

test('Privacy policies page only shows specific information', function () {
    $jsonValue = json_encode([
        [
            'id' => 'privacy-policies',
            'title' => 'Privacy Policies',
            'content' => 'Here goes the\n\nmarkdown content',
        ]
    ]);

    $policies = InstitutionalSetting::factory()->create([
        'key' => 'privacy_policies',
        'value' => $jsonValue,
        'value_type' => 'json',
    ]);

    $response = $this->get(route('privacy-policies'))->assertInertia(
        fn(Assert $page) => $page
            ->component('PrivacyPolicies')
            ->has('policies', 2)
            ->where('policies.sections.0.id', 'privacy-policies')
            ->where('policies.sections.0.title', 'Privacy Policies')
            ->where('policies.sections.0.content', 'Here goes the\n\nmarkdown content')
            ->where('policies.updated_at', $policies->updated_at->toIso8601String())
    );

    $response->assertStatus(200);
});
