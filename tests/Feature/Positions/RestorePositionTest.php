<?php

use App\Models\Position;
use App\Models\User;

beforeEach(function () {
    $this->position = Position::factory()->create([
        'name' => 'Cargo de Prueba',
        'code' => 'TEST01',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot restore a position and redirects to login', function () {
    $this->position->delete();

    $this->post(route('positions.restore', ['positionId' => $this->position->id]))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a position', function () {
    $user = User::factory()->create();
    $this->position->delete();

    $this->actingAs($user)
        ->post(route('positions.restore', ['positionId' => $this->position->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when restoring a non-existent position', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('positions.restore', 999999))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user receives 404 when restoring a position that is not deleted', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('positions.restore', ['positionId' => $this->position->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted position', function () {
    $admin = User::factory()->admin()->create();
    $this->position->delete();

    $response = $this->actingAs($admin)
        ->post(route('positions.restore', ['positionId' => $this->position->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo restaurado exitosamente.');

    $this->assertDatabaseHas('positions', [
        'id' => $this->position->id,
        'deleted_at' => null,
    ]);
});
