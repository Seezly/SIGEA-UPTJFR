<?php

use App\Models\Area;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access areas page and redirects to login', function () {
    $response = $this->get(route('areas.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access areas page and gets 403 forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('areas.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access areas page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('areas.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Areas'));
});

test('Areas list includes soft-deleted areas', function () {
    $admin = User::factory()->admin()->create();

    Area::factory()->create(['name' => 'Active Area', 'code' => 'ACT001']);
    Area::factory()->create(['name' => 'Deleted Area', 'code' => 'DEL001'])->delete();

    $response = $this->actingAs($admin)->get(route('areas.index'));

    $response->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Areas')
                ->has('areas', 2)
                ->where('areas.1.code', 'DEL001')
                ->where('areas.1.deleted_at', fn ($value) => $value !== null)
        );
});
