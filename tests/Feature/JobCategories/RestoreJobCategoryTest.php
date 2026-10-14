<?php

use App\Models\JobCategory;
use App\Models\User;

beforeEach(function () {
    $this->jobCategory = JobCategory::factory()->create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot restore a job category and redirects to login', function () {
    $this->jobCategory->delete();

    $this->post(route('job-categories.restore', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot restore a job category', function () {
    $user = User::factory()->create();
    $this->jobCategory->delete();

    $this->actingAs($user)
        ->post(route('job-categories.restore', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when restoring a non-existent job category', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('job-categories.restore', 999999))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user receives 404 when restoring a job category that is not deleted', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('job-categories.restore', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertStatus(404);
});

test('Admin user can restore a deleted job category', function () {
    $admin = User::factory()->admin()->create();
    $this->jobCategory->delete();

    $response = $this->actingAs($admin)
        ->post(route('job-categories.restore', ['jobCategoryId' => $this->jobCategory->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto restaurada exitosamente.');

    $this->assertDatabaseHas('job_categories', [
        'id' => $this->jobCategory->id,
        'deleted_at' => null,
    ]);
});
