<?php

use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->area = Area::factory()->notActive()->create([
        'name' => 'Test Area',
        'code' => 'TEST01',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot delete an area and redirects to login', function () {
    $this->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertRedirect(route('login'));
});

test('Authorized non-admin user cannot delete an area', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when deleting a non-existent area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('areas.destroy', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when deleting an already deleted area', function () {
    $admin = User::factory()->admin()->create();
    $this->area->delete();

    $this->actingAs($admin)
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user can delete an inactive and unused area', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Área eliminada exitosamente.');

    $this->assertSoftDeleted('areas', ['id' => $this->area->id]);
});

test('Admin user cannot delete an active area', function () {
    $admin = User::factory()->admin()->create();
    $activeArea = Area::factory()->create(['code' => 'ACT001']);

    $this->actingAs($admin)
        ->delete(route('areas.destroy', ['areaId' => $activeArea->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('areas', ['id' => $activeArea->id, 'deleted_at' => null]);
});

test('Admin user cannot delete an area that is in use by a position', function () {
    $admin = User::factory()->admin()->create();

    DB::table('positions')->insert([
        'area_id' => $this->area->id,
        'name' => 'Coordinador',
        'code' => 'POS01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'deleted_at' => null]);
});

test('Admin user cannot delete an area that is in use by an available job', function () {
    $admin = User::factory()->admin()->create();

    $otherArea = Area::factory()->create();
    $positionId = DB::table('positions')->insertGetId([
        'area_id' => $otherArea->id,
        'name' => 'Coordinador',
        'code' => 'POS01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $categoryId = DB::table('job_categories')->insertGetId([
        'name' => 'Tecnología',
        'slug' => 'tecnologia',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $employeeId = DB::table('employees')->insertGetId([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'EMP001',
        'hire_date' => now(),
        'status' => 'Activo',
        'current_area_id' => $otherArea->id,
        'current_position_id' => $positionId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('available_jobs')->insert([
        'area_id' => $this->area->id,
        'position_id' => $positionId,
        'job_category_id' => $categoryId,
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
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'deleted_at' => null]);
});

test('Admin user cannot delete an area that is in use by an employee', function () {
    $admin = User::factory()->admin()->create();

    $otherArea = Area::factory()->create();
    $positionId = DB::table('positions')->insertGetId([
        'area_id' => $otherArea->id,
        'name' => 'Coordinador',
        'code' => 'POS01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('employees')->insert([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'EMP001',
        'hire_date' => now(),
        'status' => 'Activo',
        'current_area_id' => $this->area->id,
        'current_position_id' => $positionId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('areas.destroy', ['areaId' => $this->area->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('areas', ['id' => $this->area->id, 'deleted_at' => null]);
});
