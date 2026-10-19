<?php

use App\Models\Area;
use App\Models\JobCategory;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->position = Position::factory()->notActive()->create([
        'name' => 'Cargo de Prueba',
        'code' => 'TEST01',
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot delete a position and redirects to login', function () {
    $this->delete(route('positions.destroy', ['positionId' => $this->position->id]))
        ->assertRedirect(route('login'));
});

test('Authorized non-admin user cannot delete a position', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when deleting a non-existent position', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('positions.destroy', 999999))
        ->assertStatus(404);
});

test('Admin user receives 404 when deleting an already deleted position', function () {
    $admin = User::factory()->admin()->create();
    $this->position->delete();

    $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user can delete an inactive and unused position', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo eliminado exitosamente.');

    $this->assertSoftDeleted('positions', ['id' => $this->position->id]);
});

test('Admin user cannot delete an active position', function () {
    $admin = User::factory()->admin()->create();
    $activePosition = Position::factory()->create(['code' => 'ACT001']);

    $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $activePosition->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('positions', ['id' => $activePosition->id, 'deleted_at' => null]);
});

test('Admin user cannot delete a position that is in use by an available job', function () {
    $admin = User::factory()->admin()->create();

    $area = $this->position->area_id;
    $categoryId = JobCategory::factory()->create()->id;
    $otherPositionId = Position::factory()->create(['area_id' => $area])->id;
    $employeeId = createEmployee($area, $otherPositionId);
    DB::table('available_jobs')->insert([
        'area_id' => $area,
        'position_id' => $this->position->id,
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
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'deleted_at' => null]);
});

test('Admin user cannot delete a position that is in use by an employee', function () {
    $admin = User::factory()->admin()->create();

    createEmployee($this->position->area_id, $this->position->id);

    $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('positions', ['id' => $this->position->id, 'deleted_at' => null]);
});

test('Admin user can delete a position whose only available job is soft-deleted', function () {
    $admin = User::factory()->admin()->create();

    $categoryId = JobCategory::factory()->create()->id;
    $otherPositionId = Position::factory()->create(['area_id' => $this->position->area_id])->id;
    $employeeId = createEmployee($this->position->area_id, $otherPositionId);
    $availableJobId = DB::table('available_jobs')->insertGetId([
        'area_id' => $this->position->area_id,
        'position_id' => $this->position->id,
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
    DB::table('available_jobs')->where('id', $availableJobId)->update([
        'deleted_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo eliminado exitosamente.');

    $this->assertSoftDeleted('positions', ['id' => $this->position->id]);
});

test('Admin user can delete a position whose only employee is soft-deleted', function () {
    $admin = User::factory()->admin()->create();

    $employeeId = createEmployee($this->position->area_id, $this->position->id);
    DB::table('employees')->where('id', $employeeId)->update([
        'deleted_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('positions.destroy', ['positionId' => $this->position->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Cargo eliminado exitosamente.');

    $this->assertSoftDeleted('positions', ['id' => $this->position->id]);
});

function createEmployee(int $areaId, int $positionId): int
{
    return DB::table('employees')->insertGetId([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'EMP'.fake()->unique()->numberBetween(1, 9999),
        'hire_date' => now(),
        'status' => 'Activo',
        'current_area_id' => $areaId,
        'current_position_id' => $positionId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
