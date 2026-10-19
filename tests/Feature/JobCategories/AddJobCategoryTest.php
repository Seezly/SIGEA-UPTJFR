<?php

use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->formData = [
        'name' => 'Tecnología',
    ];
});

/**
 * Authorization Tests
 */
test('Guest cannot add job category and redirects to login', function () {
    $this->post(route('job-categories.store'), $this->formData)
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();
});

test('Non-admin authenticated user cannot add job category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('job-categories.store'), $this->formData);

    $response->assertStatus(403);
});

test('Admin user can add job category with default values', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('job-categories.store'), $this->formData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto creada exitosamente.');

    $this->assertDatabaseHas('job_categories', [
        'name' => 'Tecnología',
        'slug' => 'tecnologia',
        'description' => null,
        'is_active' => true,
    ]);
});

/**
 * Validation Tests
 */
test('Admin user cannot create a job category with missing required name', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('job-categories.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('job_categories', 0);
});

test('Admin user cannot create a job category with an already taken name', function () {
    $admin = User::factory()->admin()->create();

    JobCategory::factory()->create([
        'name' => 'Tecnología',
        'slug' => 'tecnologia',
    ]);

    $response = $this->actingAs($admin)->post(route('job-categories.store'), $this->formData);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('job_categories', 1);
});

test('Admin user cannot create a job category exceeding maximum name length', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('job-categories.store'), [
        'name' => Str::random(101),
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('job_categories', 0);
});

test('Admin user cannot create a job category whose name generates an already used slug', function () {
    $admin = User::factory()->admin()->create();

    JobCategory::factory()->create([
        'name' => 'Tecnología',
        'slug' => 'tecnologia',
    ]);

    $response = $this->actingAs($admin)->post(route('job-categories.store'), [
        'name' => 'TECNOLOGIA',
    ]);

    $response->assertSessionHasErrors(['slug']);
    $this->assertDatabaseCount('job_categories', 1);
});

/**
 * Business Rules Tests
 */
test('Job category slug is generated from the name', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('job-categories.store'), [
        'name' => 'Tecnología de Software',
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('job_categories', [
        'name' => 'Tecnología de Software',
        'slug' => 'tecnologia-de-software',
    ]);
});

test('Admin user can create an inactive job category with a description', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('job-categories.store'), [
        'name' => 'Infraestructura',
        'description' => 'Categoría para puestos relacionados con infraestructura tecnológica.',
        'is_active' => false,
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto creada exitosamente.');

    $this->assertDatabaseHas('job_categories', [
        'name' => 'Infraestructura',
        'slug' => 'infraestructura',
        'description' => 'Categoría para puestos relacionados con infraestructura tecnológica.',
        'is_active' => false,
    ]);
});
