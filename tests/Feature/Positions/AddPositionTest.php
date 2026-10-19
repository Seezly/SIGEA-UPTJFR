<?php

use App\Models\Area;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->area = Area::factory()->create();

    $this->formData = [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot add position and redirects to login', function () {
    $this->post(route('positions.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add position', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('positions.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add position with default values', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo creado exitosamente.');

    $this->assertDatabaseHas('positions', [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'COORD01',
        'description' => null,
        'is_active' => true,
    ]);
});

/**
 * Validation Tests
 */
test('Admin user cannot create a position with missing required name', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => '',
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position with missing required code', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => '',
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position with missing required area', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['area_id']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position with an already taken code', function () {
    $admin = User::factory()->admin()->create();

    Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Docente',
        'code' => 'COORD01',
    ]);

    $response = $this->actingAs($admin)->post(route('positions.store'), $this->formData);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('positions', 1);
});

test('Admin user cannot create a position with a code used by a soft-deleted position', function () {
    $admin = User::factory()->admin()->create();

    Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Docente',
        'code' => 'COORD01',
    ])->delete();

    $response = $this->actingAs($admin)->post(route('positions.store'), $this->formData);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('positions', 1);
});

test('Admin user cannot create a position exceeding maximum name length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => Str::random(101),
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position exceeding maximum code length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => Str::random(11),
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position in an area that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => 999999,
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['area_id']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position in an inactive area', function () {
    $admin = User::factory()->admin()->create();
    $inactiveArea = Area::factory()->notActive()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $inactiveArea->id,
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['area_id']);
    $this->assertDatabaseCount('positions', 0);
});

test('Admin user cannot create a position in a soft-deleted area', function () {
    $admin = User::factory()->admin()->create();
    $deletedArea = Area::factory()->create();
    $deletedArea->delete();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $deletedArea->id,
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ]);

    $response->assertSessionHasErrors(['area_id']);
    $this->assertDatabaseCount('positions', 0);
});

/**
 * Business Rules Tests
 */
test('Position code is stored uppercase', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'coord02',
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('positions', [
        'name' => 'Coordinador',
        'code' => 'COORD02',
    ]);
});

test('Admin user cannot create a position with a lowercase code that matches an existing one', function () {
    $admin = User::factory()->admin()->create();

    Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Docente',
        'code' => 'COORD01',
    ]);

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'coord01',
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseCount('positions', 1);
});

test('Admin user can create an inactive position with a description', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $this->area->id,
        'name' => 'Inspector',
        'code' => 'INSP01',
        'description' => 'Responsable de la inspección interna.',
        'is_active' => false,
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo creado exitosamente.');

    $this->assertDatabaseHas('positions', [
        'area_id' => $this->area->id,
        'name' => 'Inspector',
        'code' => 'INSP01',
        'description' => 'Responsable de la inspección interna.',
        'is_active' => false,
    ]);
});

test('Position name is not unique and can repeat across areas', function () {
    $admin = User::factory()->admin()->create();
    $otherArea = Area::factory()->create();

    Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'COORD01',
    ]);

    $response = $this->actingAs($admin)->post(route('positions.store'), [
        'area_id' => $otherArea->id,
        'name' => 'Coordinador',
        'code' => 'COORD02',
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('positions', 2);
});
