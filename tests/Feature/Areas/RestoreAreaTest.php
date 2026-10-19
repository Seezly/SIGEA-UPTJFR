<?php

use App\Models\Area;
use App\Models\User;

beforeEach(function () {
    $this->area = Area::factory()->create([
        'name' => 'Test Area',
        'code' => 'TEST01',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot restore an area and redirects to login', function () {
    $this->area->delete();

    $this->post(route('areas.restore', ['areaId' => $this->area->id]))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore an area', function () {
    $user = User::factory()->create();
    $this->area->delete();

    $this->actingAs($user)
        ->post(route('areas.restore', ['areaId' => $this->area->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when restoring a non-existent area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('areas.restore', 999999))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user receives 404 when restoring an area that is not deleted', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('areas.restore', ['areaId' => $this->area->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted area', function () {
    $admin = User::factory()->admin()->create();
    $this->area->delete();

    $response = $this->actingAs($admin)
        ->post(route('areas.restore', ['areaId' => $this->area->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área restaurada exitosamente.');

    $this->assertDatabaseHas('areas', [
        'id' => $this->area->id,
        'deleted_at' => null,
    ]);
});
