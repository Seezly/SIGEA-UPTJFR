<?php

use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->formData = [
        'name' => 'Recursos Humanos',
        'code' => 'RRHH01',
    ];
});

/**
 * Authorization Tests
 */
test('Guest can not add area and redirects to login', function () {
    $this->post(route('areas.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add area', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('areas.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add area with default values', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área creada exitosamente.');

    $this->assertDatabaseHas('areas', [
        'name' => 'Recursos Humanos',
        'code' => 'RRHH01',
        'description' => null,
        'is_active' => true,
    ]);
});

/**
 * Validation Tests
 */
test('Admin user cannot create an area with missing required name', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => '',
        'code' => 'RRHH01',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('areas', 0);
});

test('Admin user cannot create an area with an already taken name', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Recursos Humanos',
        'code' => 'RRHH01',
    ]);

    $response = $this->actingAs($admin)->post(route('areas.store'), $this->formData);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('areas', 1);
});

test('Admin user cannot create an area exceeding maximum name length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => Str::random(101),
        'code' => 'RRHH01',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('areas', 0);
});

test('Admin user cannot create an area with missing required code', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => 'Recursos Humanos',
        'code' => '',
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('areas', 0);
});

test('Admin user cannot create an area with an already taken code', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Talento Humano',
        'code' => 'RRHH01',
    ]);

    $response = $this->actingAs($admin)->post(route('areas.store'), $this->formData);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('areas', 1);
});

test('Admin user cannot create an area with a code used by a soft-deleted area', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Talento Humano',
        'code' => 'RRHH01',
    ])->delete();

    $response = $this->actingAs($admin)->post(route('areas.store'), $this->formData);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('areas', 1);
});

test('Admin user cannot create an area exceeding maximum code length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => 'Recursos Humanos',
        'code' => Str::random(11),
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('areas', 0);
});

/**
 * Business Rules Tests
 */
test('Admin user can create an inactive area with a description', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => 'Seguridad Industrial',
        'code' => 'SG01',
        'description' => 'Responsable de la seguridad y salud en el trabajo.',
        'is_active' => false,
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área creada exitosamente.');

    $this->assertDatabaseHas('areas', [
        'name' => 'Seguridad Industrial',
        'code' => 'SG01',
        'description' => 'Responsable de la seguridad y salud en el trabajo.',
        'is_active' => false,
    ]);
});

test('Area code is stored uppercase', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => 'Recursos Humanos',
        'code' => 'rrhh02',
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('areas', [
        'name' => 'Recursos Humanos',
        'code' => 'RRHH02',
    ]);
});

test('Admin user cannot create an area with a lowercase code that matches an existing one', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Talento Humano',
        'code' => 'RRHH01',
    ]);

    $response = $this->actingAs($admin)->post(route('areas.store'), [
        'name' => 'Recursos Humanos',
        'code' => 'rrhh01',
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('areas', 1);
});
