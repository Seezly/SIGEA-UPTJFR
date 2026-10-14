<?php

use App\Models\Position;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access positions page and redirects to login', function () {
    $response = $this->get(route('positions.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access positions page and gets 403 forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('positions.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access positions page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('positions.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Positions'));
});

test('Positions list includes soft-deleted positions', function () {
    $admin = User::factory()->admin()->create();

    Position::factory()->create(['name' => 'Active Position', 'code' => 'ACT001']);
    Position::factory()->create(['name' => 'Deleted Position', 'code' => 'DEL001'])->delete();

    $response = $this->actingAs($admin)->get(route('positions.index'));

    $response->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Positions')
                ->has('positions', 2)
                ->where('positions.1.code', 'DEL001')
                ->where('positions.1.deleted_at', fn ($value) => $value !== null)
        );
});
