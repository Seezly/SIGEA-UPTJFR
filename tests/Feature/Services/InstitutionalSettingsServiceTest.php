<?php

use App\Models\InstitutionalSetting;
use App\Services\InstitutionalSettingsService;

test('InstitutionalSettingsService can retrieve institutional settings', function () {
    $institutionalSettings = [
        ['key' => 'name', 'value' => 'Test University'],
        ['key' => 'acronym', 'value' => 'TU'],
        ['key' => 'logo', 'value' => '/test_logo.svg'],
        ['key' => 'facade', 'value' => '/test_facade.jpg'],
    ];

    foreach ($institutionalSettings as $setting) {
        InstitutionalSetting::create($setting);
    }

    $service = new InstitutionalSettingsService();
    $settings = $service->institution();

    expect($settings)->toBeArray();
    expect($settings['name'])->toBe('Test University');
    expect($settings['acronym'])->toBe('TU');
    expect($settings['logo'])->toBe('/test_logo.svg');
    expect($settings['facade'])->toBe('/test_facade.jpg');
});

test('InstitutionalSettingsService can retrieve terms of service', function () {
    $termsData = [
        'id' => 'terms-1',
        'title' => 'Terms Title',
        'content' => 'Terms content here'
    ];

    $term = InstitutionalSetting::create([
        'key' => 'terms_of_service',
        'value' => json_encode([$termsData])
    ]);

    $service = new InstitutionalSettingsService();
    $terms = $service->terms();

    expect($terms)->toBeArray();
    expect($terms['sections'][0]['id'])->toBe('terms-1');
    expect($terms['sections'][0]['title'])->toBe('Terms Title');
    expect($terms['sections'][0]['content'])->toBe('Terms content here');
    expect($terms['updated_at'])->toBeString();
    expect($terms['updated_at'])->toBe($term->updated_at->toIso8601String());
});

test('InstitutionalSettingsService can add terms of service keeping existing ones', function () {
    $initialTerms = [
        'id' => 'terms-1',
        'title' => 'Initial Term',
        'content' => 'Content 1'
    ];

    InstitutionalSetting::create([
        'key' => 'terms_of_service',
        'value' => json_encode([$initialTerms])
    ]);

    $service = new InstitutionalSettingsService();

    $newTerm = ['id' => 'terms-2', 'title' => 'Second Term', 'content' => 'Content 2'];
    $service->addTerms([$newTerm]);

    $stored = json_decode(InstitutionalSetting::where('key', 'terms_of_service')->first()->value, true);

    expect($stored)->toBeArray();
    expect(count($stored))->toBe(2);
});

test('InstitutionalSettingsService can update existing terms of service', function () {
    $termsData = [
        'id' => 'terms-1',
        'title' => 'Old Title',
        'content' => 'Old content'
    ];

    InstitutionalSetting::create([
        'key' => 'terms_of_service',
        'value' => json_encode([$termsData])
    ]);

    $service = new InstitutionalSettingsService();

    $updatedData = [
        'id' => 'terms-1',
        'title' => 'Updated Title',
        'content' => 'Updated content'
    ];
    $service->updateTerms([$updatedData]);

    $stored = json_decode(InstitutionalSetting::where('key', 'terms_of_service')->first()->value, true);

    expect($stored[0]['title'])->toBe('Updated Title');
});

test('InstitutionalSettingsService can retrieve privacy policies', function () {
    $policyData = [
        'id' => 'policy-1',
        'title' => 'Privacy Policy',
        'content' => 'Policy content'
    ];

    InstitutionalSetting::create([
        'key' => 'privacy_policies',
        'value' => json_encode([$policyData])
    ]);

    $service = new InstitutionalSettingsService();
    $policies = $service->privacyPolicies();

    expect($policies)->toBeArray();
    expect($policies['sections'][0]['title'])->toBe('Privacy Policy');
});

test('InstitutionalSettingsService can add privacy policies keeping existing ones', function () {
    InstitutionalSetting::create([
        'key' => 'privacy_policies',
        'value' => json_encode([['id' => 'p-1', 'title' => 'Policy 1', 'content' => 'Content 1']])
    ]);

    $service = new InstitutionalSettingsService();
    $service->addPrivacyPolicy([['id' => 'p-2', 'title' => 'Policy 2', 'content' => 'Content 2']]);

    $stored = json_decode(InstitutionalSetting::where('key', 'privacy_policies')->first()->value, true);

    expect(count($stored))->toBe(2);
});

test('InstitutionalSettingsService can update existing privacy policies', function () {
    InstitutionalSetting::create([
        'key' => 'privacy_policies',
        'value' => json_encode(['id' => 'p-1', 'title' => 'Old Policy', 'content' => 'Old'])
    ]);

    $service = new InstitutionalSettingsService();
    $service->updatePrivacyPolicy([['id' => 'p-1', 'title' => 'New Policy', 'content' => 'New']]);

    $stored = json_decode(InstitutionalSetting::where('key', 'privacy_policies')->first()->value, true);

    expect($stored[0]['title'])->toBe('New Policy');
});

test('InstitutionalSettingsService can retrieve all settings', function () {
    InstitutionalSetting::create(['key' => 'name', 'value' => 'Test University']);
    InstitutionalSetting::create(['key' => 'acronym', 'value' => 'TU']);

    $service = new InstitutionalSettingsService();
    $allSettings = $service->all();

    expect($allSettings)->toBeArray();
    expect($allSettings)->toHaveKey('name');
    expect($allSettings)->toHaveKey('acronym');
});

test('InstitutionalSettingsService can retrieve one key', function () {
    InstitutionalSetting::create(['key' => 'custom_key', 'value' => 'Custom Value']);

    $service = new InstitutionalSettingsService();
    $value = $service->get('custom_key');

    expect($value)->toBe('Custom Value');
});

test('InstitutionalSettingsService can retrieve multiple keys', function () {
    InstitutionalSetting::create(['key' => 'custom_key_1', 'value' => 'Custom Value 1']);
    InstitutionalSetting::create(['key' => 'custom_key_2', 'value' => 'Custom Value 2']);
    InstitutionalSetting::create(['key' => 'custom_key_3', 'value' => 'Custom Value 3']);

    $service = new InstitutionalSettingsService();
    $value = $service->get(['custom_key_1', 'custom_key_2', 'custom_key_3']);

    expect($value)->toBeArray();
    expect($value['custom_key_1'])->toBe('Custom Value 1');
    expect($value['custom_key_2'])->toBe('Custom Value 2');
    expect($value['custom_key_3'])->toBe('Custom Value 3');
});

test('InstitutionalSettingsService can add one key', function () {
    $service = new InstitutionalSettingsService();
    $service->set('new_key', 'New Value');

    expect(InstitutionalSetting::where('key', 'new_key')->first())->not->toBeNull();
    expect(InstitutionalSetting::where('key', 'new_key')->first()->value)->toBe('New Value');
});

test('InstitutionalSettingsService can update one key', function () {
    InstitutionalSetting::create(['key' => 'existing_key', 'value' => 'Old Value']);

    $service = new InstitutionalSettingsService();
    $service->set('existing_key', 'Updated Value');

    $setting = InstitutionalSetting::where('key', 'existing_key')->first();
    expect($setting->value)->toBe('Updated Value');
});
