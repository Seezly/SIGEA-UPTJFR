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
                'id',
                'area_id',
                'position_id',
                'job_category_id',
                'title',
                'description',
                'requirements',
                'responsibilities',
                'employment_type',
                'vacancies_count',
                'published_at',
                'application_deadline'
            )
                ->where('is_active', true)
                ->where('published_at', '<=', now())
                ->where('application_deadline', '>', now())
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
                ]
            );
        }
    }
}
