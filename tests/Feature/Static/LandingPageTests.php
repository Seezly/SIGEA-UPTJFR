<?php

use App\Models\Area;
use App\Models\User;
use App\Models\JobCategory;
use App\Models\AvailableJob;
use App\Models\Position;
use App\Models\InstitutionalSetting;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->area = Area::factory()->create();
    $this->position = Position::factory()->create(['area_id' => $this->area->id]);
    $this->jobCategory = JobCategory::factory()->create();
    $this->recruiter = User::factory()->create();

    DB::table('employees')->insert([
        'user_id' => $this->recruiter->id,
        'employee_code' => 'EMP001',
        'hire_date' => now(),
        'current_area_id' => $this->area->id,
        'current_position_id' => $this->position->id,
        'status' => 'FULLTIME',
    ]);

    $this->jobData = [
        'area_id' => $this->area->id,
        'position_id' => $this->position->id,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $this->recruiter->id,
        'description' => 'Job description',
        'requirements' => 'Job requirements',
        'responsibilities' => 'Job responsibilities',
        'employment_type' => 'Full-time',
        'vacancies_count' => 3,
        'is_active' => true,
    ];
});

/**
 * Authorization Tests
 */

test('Landing page is the first page shown', function () {
    $response = $this->get('/')->assertInertia(
        fn(Assert $page) => $page
            ->component('Welcome')
    );

    $response->assertStatus(200);
});

test('Guest user can access the landing page', function () {
    $response = $this->get(route('home'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Welcome')
    );

    $response->assertStatus(200);
});

test('Authenticated user can access the landing page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'))->assertInertia(
        fn(Assert $page) => $page
            ->component('Welcome')
    );

    $response->assertStatus(200);
});

/**
 * Institutional Settings Tests
 */

test('Landing page has institution prop built from institutional settings', function () {
    InstitutionalSetting::factory()->create([
        'key' => 'institution_name',
        'value' => 'Universidad Politécnica Territorial "José Félix Ribas"',
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'institution_acronym',
        'value' => 'UPTJFR',
    ]);

    InstitutionalSetting::factory()->create([
        'key' => 'institution_description',
        'value' => 'Universidad Politécnica Territorial "José Félix Ribas"',
    ]);

    $response = $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Welcome')
                ->has('institution')
                ->where('institution.name', 'Universidad Politécnica Territorial "José Félix Ribas"')
                ->where('institution.acronym', 'UPTJFR')
                ->where('institution.description', 'Universidad Politécnica Territorial "José Félix Ribas"')
        );
});

test('Landing page has institution prop with default values when no institutional settings are available', function () {
    InstitutionalSetting::query()->delete();

    $response = $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Welcome')
                ->has('institution')
                ->where('institution.name', 'Universidad Politécnica Territorial "José Félix Ribas"')
                ->where('institution.acronym', 'UPTJFR')
                ->where('institution.description', 'Universidad Politécnica Territorial "José Félix Ribas"')
        );
});

/**
 * Landing Page Jobs Tests
 */

test('Landing page has jobs prop containing only active jobs', function () {
    $activeJob = AvailableJob::factory()->create(array_merge($this->jobData, [
        'published_at' => now()->subDays(1),
        'title' => 'Active Job',
        'application_deadline' => now()->addDays(5),
        'is_active' => true,
    ]));

    AvailableJob::factory()->create(array_merge($this->jobData, [
        'title' => 'Deadline Job',
        'published_at' => now()->subDays(1),
        'application_deadline' => now()->subDay(),
        'is_active' => true,
    ]));

    AvailableJob::factory()->create(array_merge($this->jobData, [
        'title' => 'Inactive Job',
        'published_at' => now()->subDays(1),
        'application_deadline' => now()->addDay(),
        'is_active' => false,
    ]));

    $response = $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Welcome')
                ->has('jobs', 1)
                ->where('jobs.0.title', 'Active Job')
                ->where('jobs.0.id', $activeJob->id)
        );
});

test(
    'Landing page orders recent jobs by published_at in descending order',
    function () {
        $firstJob = AvailableJob::factory()->create(array_merge($this->jobData, [
            'published_at' => now()->subDays(1),
            'title' => 'First Job',
            'application_deadline' => now()->addDays(5),
            'is_active' => true,
        ]));

        $secondJob = AvailableJob::factory()->create(array_merge($this->jobData, [
            'published_at' => now()->subDays(2),
            'title' => 'Second Job',
            'application_deadline' => now()->addDays(5),
            'is_active' => true,
        ]));

        $response = $this->get(route('home'))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Welcome')
                    ->has('jobs', 2)
                    ->where('jobs.0.title', 'First Job')
                    ->where('jobs.1.title', 'Second Job')
            );
    }
);

test(
    'Landing page exposes only necessary public fields for jobs and hides internal applicant data',
    function () {
        $job = AvailableJob::factory()->create(array_merge($this->jobData, [
            'published_at' => now()->subDays(1),
            'title' => 'Public Job',
            'application_deadline' => now()->addDays(5),
            'is_active' => true,
        ]));

        $response = $this->get(route('home'))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Welcome')
                    ->has(
                        'jobs.0',
                        fn(Assert $job) => $job
                            ->has('id')
                            ->has('title')
                            ->has('employment_type')
                            ->has('published_at')
                            ->has('application_deadline')
                            ->has('area')
                            ->has('position')
                            ->has('category')
                            ->missing('created_by')
                            ->missing('created_at')
                            ->missing('updated_at')
                            ->missing('description')
                            ->missing('requirements')
                            ->missing('responsibilities')
                            ->missing('vacancies_count')
                            ->missing('area_id')
                            ->missing('position_id')
                            ->missing('job_category_id')
                    )
            );
    }
);

/**
 * Empty States and Error Handling Tests
 */

test('Landing page shows jobs empty state when no active jobs are available', function () {
    AvailableJob::query()->delete();

    $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Welcome')
                ->has('jobs', 0)
        );
});

test('Landing page shows jobs prop value as null with status 200 if query fails', function () {
    $this->withoutExceptionHandling();

    $this->get(route('home', ['force_error' => true]))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Welcome')
                ->where('jobs', null)
                ->has('errors')
        );
})->skip('This test is skipped because it requires a specific implementation to force a query failure, which is not currently implemented.');
