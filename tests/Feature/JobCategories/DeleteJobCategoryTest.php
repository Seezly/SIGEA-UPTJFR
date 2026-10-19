<?php

use App\Models\Area;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->jobCategory = JobCategory::factory()->notActive()->create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot delete a job category and redirects to login', function () {
    $this->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertRedirect(route('login'));
});

test('Authorized non-admin user cannot delete a job category', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when deleting a non-existent job category', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('job-categories.destroy', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when deleting an already deleted job category', function () {
    $admin = User::factory()->admin()->create();
    $this->jobCategory->delete();

    $this->actingAs($admin)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user can delete an inactive and unused job category', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto eliminada exitosamente.');

    $this->assertSoftDeleted('job_categories', ['id' => $this->jobCategory->id]);
});

test('Admin user cannot delete an active job category', function () {
    $admin = User::factory()->admin()->create();
    $activeCategory = JobCategory::factory()->create(['slug' => 'active-category']);

    $this->actingAs($admin)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $activeCategory->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('job_categories', ['id' => $activeCategory->id, 'deleted_at' => null]);
});

test('Admin user cannot delete a job category that is in use by an available job', function () {
    $admin = User::factory()->admin()->create();

    $area = Area::factory()->create();
    $positionId = DB::table('positions')->insertGetId([
        'area_id' => $area->id,
        'name' => 'Coordinador',
        'code' => 'POS01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $employeeId = DB::table('employees')->insertGetId([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'EMP001',
        'hire_date' => now(),
        'status' => 'Activo',
        'current_area_id' => $area->id,
        'current_position_id' => $positionId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('available_jobs')->insert([
        'area_id' => $area->id,
        'position_id' => $positionId,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $employeeId,
        'title' => 'Desarrollador de Software',
        'description' => 'Descripción de la vacante.',
        'requirements' => 'Requisitos de la vacante.',
        'responsibilities' => 'Responsabilidades de la vacante.',
        'employment_type' => 'Tiempo completo',
        'vacancies_count' => 1,
        'is_active' => true,
        'application_deadline' => now()->addDays(30),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('job_categories', ['id' => $this->jobCategory->id, 'deleted_at' => null]);
});

test('Admin user can delete a job category whose only available job is soft-deleted', function () {
    $admin = User::factory()->admin()->create();

    $area = Area::factory()->create();
    $positionId = DB::table('positions')->insertGetId([
        'area_id' => $area->id,
        'name' => 'Coordinador',
        'code' => 'POS01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $employeeId = DB::table('employees')->insertGetId([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'EMP001',
        'hire_date' => now(),
        'status' => 'Activo',
        'current_area_id' => $area->id,
        'current_position_id' => $positionId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $availableJobId = DB::table('available_jobs')->insertGetId([
        'area_id' => $area->id,
        'position_id' => $positionId,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $employeeId,
        'title' => 'Desarrollador de Software',
        'description' => 'Descripción de la vacante.',
        'requirements' => 'Requisitos de la vacante.',
        'responsibilities' => 'Responsabilidades de la vacante.',
        'employment_type' => 'Tiempo completo',
        'vacancies_count' => 1,
        'is_active' => true,
        'application_deadline' => now()->addDays(30),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('available_jobs')->where('id', $availableJobId)->update([
        'deleted_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('job-categories.destroy', ['jobCategoryId' => $this->jobCategory->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Categoría de puesto eliminada exitosamente.');

    $this->assertSoftDeleted('job_categories', ['id' => $this->jobCategory->id]);
});
