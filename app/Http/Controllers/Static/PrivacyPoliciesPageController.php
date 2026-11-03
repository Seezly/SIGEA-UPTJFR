<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\InstitutionalSettingsService;

class PrivacyPoliciesPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, InstitutionalSettingsService $institutionalSetting)
    {
        $institution = $institutionalSetting->institution();
        $policies = $institutionalSetting->privacyPolicies();

        return Inertia::render('PrivacyPolicies', [
            'institution' => $institution,
            'policies' => $policies,
        ]);
    }
}
