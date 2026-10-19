<?php

use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->area = Area::factory()->create([
        'name' => 'Área Original',
        'code' => 'ORI01',
        'description' => 'Descripción original del área.',
    ]);

    $this->formData = [
        'name' => 'Área Actualizada',
        'code' => 'ACT01',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot edit area and redirects to login', function () {
    $this->put(route('areas.update', ['areaId' => $this->area->id]), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Authenticated non-admin user cannot edit area', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('areas.update', ['areaId' => $this->area->id]), $this->formData)
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user cannot edit an area that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => 999999]), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit area with an empty name', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => '',
            'code' => 'ACT01',
        ])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'name' => 'Área Original']);
});

test('Admin user cannot edit area with an empty code', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Actualizada',
            'code' => '',
        ])
        ->assertSessionHasErrors(['code']);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'code' => 'ORI01']);
});

test('Admin user cannot update area name to a name already taken by another area', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Nombre Tomado',
        'code' => 'TOM01',
    ]);

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Nombre Tomado',
            'code' => 'ACT01',
        ])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'name' => 'Área Original']);
});

test('Admin user cannot update area code to a code already taken by another area', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create([
        'name' => 'Otra Área',
        'code' => 'TOM01',
    ]);

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Actualizada',
            'code' => 'TOM01',
        ])
        ->assertSessionHasErrors(['code']);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'code' => 'ORI01']);
});

test('Admin user cannot update area exceeding maximum field lengths', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => Str::random(101),
            'code' => Str::random(11),
        ])
        ->assertSessionHasErrors(['name', 'code']);
});

/**
 * Business Rules Tests
 */
test('Admin user can update area data', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Actualizada',
            'code' => 'ACT01',
            'description' => 'Descripción actualizada del área.',
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área actualizada exitosamente.');

    $this->assertDatabaseHas('areas', [
        'id' => $this->area->id,
        'name' => 'Área Actualizada',
        'code' => 'ACT01',
        'description' => 'Descripción actualizada del área.',
    ]);
});

test('Admin user can partially update an area sending only its description', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'description' => 'Nueva descripción del área.',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área actualizada exitosamente.');

    $this->assertDatabaseHas('areas', [
        'id' => $this->area->id,
        'name' => 'Área Original',
        'code' => 'ORI01',
        'description' => 'Nueva descripción del área.',
    ]);
});

test('Admin user can update an area keeping its own name and code', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Original',
            'code' => 'ORI01',
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('areas', [
        'id' => $this->area->id,
        'name' => 'Área Original',
        'code' => 'ORI01',
        'description' => 'Descripción original del área.',
    ]);
});

test('Area code is stored uppercase on update', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Actualizada',
            'code' => 'act02',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'code' => 'ACT02']);
});

test('Admin user can deactivate an area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('areas.update', ['areaId' => $this->area->id]), [
            'name' => 'Área Original',
            'code' => 'ORI01',
            'is_active' => false,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'is_active' => false]);
});
