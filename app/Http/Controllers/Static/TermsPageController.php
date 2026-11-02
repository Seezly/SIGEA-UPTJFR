<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\InstitutionalSettingsService;

class TermsPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, InstitutionalSettingsService $institutionalSetting)
    {
        $terms = $institutionalSetting->terms();
        $institution = $institutionalSetting->institution();

        return Inertia::render('Terms', [
            'terms' => [
                'sections' => $terms['sections'],
                'updated_at' => $terms['updated_at']
            ],
            'institution' => $institution,
        ]);
    }
}
