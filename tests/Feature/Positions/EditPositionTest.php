<?php

use App\Models\Area;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->area = Area::factory()->create();
    $this->position = Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Cargo Original',
        'code' => 'ORI01',
        'description' => 'Descripción original del cargo.',
    ]);

    $this->formData = [
        'name' => 'Cargo Actualizado',
        'code' => 'ACT01',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot edit position and redirects to login', function () {
    $this->put(route('positions.update', ['positionId' => $this->position->id]), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Authenticated non-admin user cannot edit position', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('positions.update', ['positionId' => $this->position->id]), $this->formData)
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user cannot edit a position that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', 999999), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit position with an empty name', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => '',
            'code' => 'ACT01',
        ])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'name' => 'Cargo Original']);
});

test('Admin user cannot edit position with an empty code', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Actualizado',
            'code' => '',
        ])
        ->assertSessionHasErrors(['code']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'code' => 'ORI01']);
});

test('Admin user cannot update position code to a code already taken by another position', function () {
    $admin = User::factory()->admin()->create();

    Position::factory()->create([
        'area_id' => $this->area->id,
        'name' => 'Otro Cargo',
        'code' => 'TOM01',
    ]);

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Actualizado',
            'code' => 'TOM01',
        ])
        ->assertSessionHasErrors(['code']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'code' => 'ORI01']);
});

test('Admin user cannot update position exceeding maximum field lengths', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => Str::random(101),
            'code' => Str::random(11),
        ])
        ->assertSessionHasErrors(['name', 'code']);
});

test('Admin user cannot change the area of a position to an area that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'area_id' => 999999,
        ])
        ->assertSessionHasErrors(['area_id']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'area_id' => $this->area->id]);
});

test('Admin user cannot change the area of a position to an inactive area', function () {
    $admin = User::factory()->admin()->create();
    $inactiveArea = Area::factory()->notActive()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'area_id' => $inactiveArea->id,
        ])
        ->assertSessionHasErrors(['area_id']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'area_id' => $this->area->id]);
});

test('Admin user cannot change the area of a position to a soft-deleted area', function () {
    $admin = User::factory()->admin()->create();
    $deletedArea = Area::factory()->create();
    $deletedArea->delete();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'area_id' => $deletedArea->id,
        ])
        ->assertSessionHasErrors(['area_id']);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'area_id' => $this->area->id]);
});

/**
 * Business Rules Tests
 */
test('Admin user can update position data', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Actualizado',
            'code' => 'ACT01',
            'description' => 'Descripción actualizada del cargo.',
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo actualizado exitosamente.');

    $this->assertDatabaseHas('positions', [
        'id' => $this->position->id,
        'name' => 'Cargo Actualizado',
        'code' => 'ACT01',
        'description' => 'Descripción actualizada del cargo.',
    ]);
});

test('Admin user can partially update a position sending only its description', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'description' => 'Nueva descripción del cargo.',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo actualizado exitosamente.');

    $this->assertDatabaseHas('positions', [
        'id' => $this->position->id,
        'name' => 'Cargo Original',
        'code' => 'ORI01',
        'description' => 'Nueva descripción del cargo.',
    ]);
});

test('Admin user can update a position keeping its own name and code', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Original',
            'code' => 'ORI01',
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('positions', [
        'id' => $this->position->id,
        'name' => 'Cargo Original',
        'code' => 'ORI01',
        'description' => 'Descripción original del cargo.',
    ]);
});

test('Position code is stored uppercase on update', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Actualizado',
            'code' => 'act02',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'code' => 'ACT02']);
});

test('Admin user can move a position to another active area', function () {
    $admin = User::factory()->admin()->create();
    $otherArea = Area::factory()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'area_id' => $otherArea->id,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'area_id' => $otherArea->id]);
});

test('Admin user can deactivate a position', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('positions.update', ['positionId' => $this->position->id]), [
            'name' => 'Cargo Original',
            'code' => 'ORI01',
            'is_active' => false,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'is_active' => false]);
});
