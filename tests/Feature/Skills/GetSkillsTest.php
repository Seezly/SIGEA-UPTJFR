<?php

use App\Models\Skill;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access skills page and redirects to login', function () {
    $response = $this->get(route('skills.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access skills page and gets 403 forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('skills.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access skills page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('skills.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Skills'));
});

test('Skills list includes soft-deleted skills', function () {
    $admin = User::factory()->admin()->create();

    Skill::factory()->create(['name' => 'Active Skill', 'slug' => 'active-skill']);
    Skill::factory()->create(['name' => 'Deleted Skill', 'slug' => 'deleted-skill'])->delete();

    $response = $this->actingAs($admin)->get(route('skills.index'));

    $response->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Skills')
                ->has('skills', 2)
                ->where('skills.1.slug', 'deleted-skill')
                ->where('skills.1.deleted_at', fn ($value) => $value !== null)
        );
});
