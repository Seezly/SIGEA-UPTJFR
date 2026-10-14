<?php

use App\Models\JobCategory;
use App\Models\User;

beforeEach(function () {
    $this->jobCategory = JobCategory::factory()->create([
        'name' => 'Original Category',
        'slug' => 'original-category',
        'description' => 'Original description',
        'is_active' => true,
    ]);

    $this->formData = [
        'name' => 'Updated Category',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot edit job category and redirects to login', function () {
    $this->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), $this->formData)
        ->assertRedirect(route('login'));
});

test('Authenticated non-admin user cannot edit job category', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), $this->formData)
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user cannot edit a job category that does not exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('job-categories.update', 999999), $this->formData)
        ->assertStatus(404);
});

test('Admin user cannot edit job category with an empty name', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), ['name' => ''])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseHas('job_categories', ['id' => $this->jobCategory->id, 'name' => 'Original Category']);
});

test('Admin user cannot update job category name to a name already taken by another job category', function () {
    $admin = User::factory()->admin()->create();

    JobCategory::factory()->create([
        'name' => 'Taken Name',
        'slug' => 'taken-name',
    ]);

    $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), ['name' => 'Taken Name'])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('job_categories', ['id' => $this->jobCategory->id, 'name' => 'Taken Name']);
});

test('Admin user cannot rename a job category to a name whose slug is used by another job category', function () {
    $admin = User::factory()->admin()->create();

    JobCategory::factory()->create([
        'name' => 'Taken Name',
        'slug' => 'taken-name',
    ]);

    $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), ['name' => 'TAKEN NAME'])
        ->assertSessionHasErrors(['slug']);

    $this->assertDatabaseHas('job_categories', ['id' => $this->jobCategory->id, 'name' => 'Original Category']);
});

/**
 * Business Rules Tests
 */
test('Admin user can rename a job category and its slug is regenerated', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto actualizada exitosamente.');

    $this->assertDatabaseHas('job_categories', [
        'id' => $this->jobCategory->id,
        'name' => 'Updated Category',
        'slug' => 'updated-category',
    ]);
});

test('Admin user can send an update without the name and the job category keeps its slug', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), []);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto actualizada exitosamente.');

    $this->assertDatabaseHas('job_categories', [
        'id' => $this->jobCategory->id,
        'name' => 'Original Category',
        'slug' => 'original-category',
    ]);
});

test('Admin user can update the description and is_active without touching the name and slug', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put(route('job-categories.update', ['jobCategoryId' => $this->jobCategory->id]), [
            'description' => 'Updated description',
            'is_active' => false,
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('job_categories', [
        'id' => $this->jobCategory->id,
        'name' => 'Original Category',
        'slug' => 'original-category',
        'description' => 'Updated description',
        'is_active' => false,
    ]);
});
