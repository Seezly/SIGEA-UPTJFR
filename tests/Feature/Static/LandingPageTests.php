<?php

use App\Models\Area;
use App\Models\User;
use App\Models\JobCategory;
use App\Models\Position;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->area = Area::factory()->create();
    $this->position = Position::factory()->create(['area_id' => $this->area->id]);
    $this->jobCategory = JobCategory::factory()->create();
    $this->recruiter = User::factory()->admin()->create();
});

/**
 * Authorization Tests
 */

test('Landing page is the first page shown', function () {
    $response = $this->get('/')->assertInertia(
        fn(Assert $page) => $page
            ->component('LandingPage')
            ->hasAny('title', 'description')
    );

    $response->assertStatus(200);
});

test('Guest user can access the landing page', function () {
    $response = $this->get(route('home'))->assertInertia(
        fn(Assert $page) => $page
            ->component('LandingPage')
    );

    $response->assertStatus(200);
});

test('Authenticated user can access the landing page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'))->assertInertia(
        fn(Assert $page) => $page
            ->component('LandingPage')
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
                ->component('LandingPage')
                ->has('institution', 3)
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
                ->component('LandingPage')
                ->has('institution', 3)
                ->where('institution.name', 'Universidad Politécnica Territorial "José Félix Ribas"')
                ->where('institution.acronym', 'UPTJFR')
                ->where('institution.description', 'Universidad Politécnica Territorial "José Félix Ribas"')
        );
});

/**
 * Landing Page Jobs Tests
 */

test('Landing page has jobs prop containing only active jobs', function () {
    $activeJob = Job::factory()->create([
        'position_id' => $this->position->id,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $this->recruiter->id,
        'published_at' => now()->subDays(1),
        'title' => 'Active Job',
        'application_deadline' => now()->addDays(5),
    ]);

    Job::factory()->create([
        'position_id' => $this->position->id,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $this->recruiter->id,
        'title' => 'Deadline Job',
        'published_at' => now()->subDays(1),
        'application_deadline' => now()->subDay(),
    ]);

    Job::factory()->create([
        'position_id' => $this->position->id,
        'job_category_id' => $this->jobCategory->id,
        'created_by' => $this->recruiter->id,
        'title' => 'Inactive Job',
        'published_at' => now()->subDays(1),
        'application_deadline' => now()->addDay(),
        'is_active' => false,
    ]);

    $response = $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('LandingPage')
                ->has('jobs', 1)
                ->where('jobs.0.title', 'Active Job')
                ->where('jobs.0.id', $activeJob->id)
        );
});

test(
    'Landing page orders recent jobs by published_at in descending order',
    function () {
        $firstJob = Job::factory()->create([
            'position_id' => $this->position->id,
            'job_category_id' => $this->jobCategory->id,
            'created_by' => $this->recruiter->id,
            'published_at' => now()->subDays(1),
            'title' => 'First Job',
            'application_deadline' => now()->addDays(5),
        ]);

        $secondJob = Job::factory()->create([
            'position_id' => $this->position->id,
            'job_category_id' => $this->jobCategory->id,
            'created_by' => $this->recruiter->id,
            'published_at' => now()->subDays(2),
            'title' => 'Second Job',
            'application_deadline' => now()->addDays(5),
        ]);

        $response = $this->get(route('home'))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('LandingPage')
                    ->has('jobs', 2)
                    ->where('jobs.0.title', 'First Job')
                    ->where('jobs.1.title', 'Second Job')
            );
    }
);

test(
    'Landing page exposes only necessary public fields for jobs and hides internal applicant data',
    function () {
        $job = Job::factory()->create([
            'position_id' => $this->position->id,
            'job_category_id' => $this->jobCategory->id,
            'created_by' => $this->recruiter->id,
            'published_at' => now()->subDays(1),
            'title' => 'Public Job',
            'application_deadline' => now()->addDays(5),
        ]);

        $response = $this->get(route('home'))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('LandingPage')
                    ->has(
                        'jobs.0',
                        fn(Assert $job) => $job
                            ->has('id')
                            ->has('title')
                            ->has('description')
                            ->has('employment_type')
                            ->has('vacancies_count')
                            ->has('published_at')
                            ->has('area')
                            ->has('position')
                            ->missing('created_by')
                            ->missing('applications')
                            ->missing('applicants')
                    )
            );
    }
);

/**
 * Empty States and Error Handling Tests
 */

test('Landing page shows jobs empty state when no active jobs are available', function () {
    Job::query()->delete();

    $this->get(route('home'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('LandingPage')
                ->has('jobs', 0)
        );
});

test('Landing page shows jobs prop value as null with status 200 if query fails', function () {
    $this->withoutExceptionHandling();

    $this->get(route('home', ['force_error' => true]))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('LandingPage')
                ->where('jobs', null)
                ->has('error')
        );
});
