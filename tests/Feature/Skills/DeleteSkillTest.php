<?php

use App\Models\People;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->skill = Skill::factory()->create([
        'name' => 'Test Skill',
        'slug' => 'test-skill',
        'is_active' => false,
    ]);
});

/**
 * Authorization Tests
 */
test('Guest cannot delete a skill and redirects to login', function () {
    $this->delete(route('skills.destroy', ['skillId' => $this->skill->id]))
        ->assertRedirect(route('login'));
});

test('Authorized non-admin user cannot delete a skill', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('skills.destroy', ['skillId' => $this->skill->id]))
        ->assertStatus(403);
});

/**
 * Validation Tests
 */
test('Admin user receives 404 when deleting a non-existent skill', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('skills.destroy', 999999))
        ->assertStatus(404);
});

/**
 * Business Rules Tests
 */
test('Admin user can delete an inactive and unused skill', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->delete(route('skills.destroy', ['skillId' => $this->skill->id]));

    $response->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('flash.message', 'Habilidad eliminada exitosamente.');

    $this->assertSoftDeleted('skills', ['id' => $this->skill->id]);
});

test('Admin user cannot delete an active skill', function () {
    $admin = User::factory()->admin()->create();
    $activeSkill = Skill::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->delete(route('skills.destroy', ['skillId' => $activeSkill->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('skills', ['id' => $activeSkill->id, 'deleted_at' => null]);
});

test('Admin user cannot delete a skill that is in use by an applicant profile', function () {
    $admin = User::factory()->admin()->create();

    $people = People::factory()->create();
    $profileId = DB::table('applicant_profiles')->insertGetId([
        'people_id' => $people->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('applicant_skills')->insert([
        'skill_id' => $this->skill->id,
        'applicant_profile_id' => $profileId,
        'level' => 'Básico',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('skills.destroy', ['skillId' => $this->skill->id]))
        ->assertStatus(403);

    $this->assertDatabaseHas('skills', ['id' => $this->skill->id, 'deleted_at' => null]);
});

test('Admin user receives 404 when deleting an already deleted skill', function () {
    $admin = User::factory()->admin()->create();
    $this->skill->delete();

    $this->actingAs($admin)
        ->delete(route('skills.destroy', ['skillId' => $this->skill->id]))
        ->assertStatus(404);
});
