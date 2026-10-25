<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use App\Models\InstitutionalSetting;
use App\Models\AvailableJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $institutionalSettings = InstitutionalSetting::pluck('value', 'key')->toArray();

        $institution = [
            'name' => $institutionalSettings['name'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'acronym' => $institutionalSettings['acronym'] ?? 'UPTJFR',
            'description' => $institutionalSettings['description'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
        ];

        try {
            $availableJobs = AvailableJob::select(
                'available_jobs.id',
                'areas.name as area',
                'positions.name as position',
                'job_categories.name as category',
                'available_jobs.title as title',
                'available_jobs.employment_type as employment_type',
                'available_jobs.published_at as published_at',
                'available_jobs.application_deadline as application_deadline'
            )
                ->where('available_jobs.is_active', true)
                ->where('published_at', '<=', now())
                ->where('application_deadline', '>', now())
                ->join('job_categories', 'available_jobs.job_category_id', '=', 'job_categories.id')
                ->where('job_categories.is_active', true)
                ->join('positions', 'available_jobs.position_id', '=', 'positions.id')
                ->where('positions.is_active', true)
                ->join('areas', 'available_jobs.area_id', '=', 'areas.id')
                ->where('areas.is_active', true)
                ->offset(0)
                ->limit(6)
                ->orderBy('published_at', 'desc')
                ->get();

            return Inertia::render(
                'Welcome',
                [
                    'institution' => $institution,
                    'jobs' => $availableJobs,
                ]
            );
        } catch (\Throwable $th) {
            $availableJobs = null;

            return Inertia::render(
                'Welcome',
                [
                    'institution' => $institution,
                    'jobs' => $availableJobs,
                    'errors' => [ 'jobs' => $th->getMessage() ]
                ]
            );
        }
    }
}
