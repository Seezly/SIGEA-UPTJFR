<?php

use App\Models\JobCategory;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('Guest cannot access job categories page and redirects to login', function () {
    $response = $this->get(route('job-categories.index'));

    $response->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot access job categories page and gets 403 forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('job-categories.index'));

    $response->assertStatus(403);
});

test('Authenticated admin user can access job categories page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('job-categories.index'));

    $response->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/JobCategories'));
});

test('Job categories list includes soft-deleted job categories', function () {
    $admin = User::factory()->admin()->create();

    JobCategory::factory()->create(['name' => 'Active Category', 'slug' => 'active-category']);
    JobCategory::factory()->create(['name' => 'Deleted Category', 'slug' => 'deleted-category'])->delete();

    $response = $this->actingAs($admin)->get(route('job-categories.index'));

    $response->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/JobCategories')
                ->has('jobCategories', 2)
                ->where('jobCategories.1.slug', 'deleted-category')
                ->where('jobCategories.1.deleted_at', fn ($value) => $value !== null)
        );
});
