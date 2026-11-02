<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\InstitutionalSettingsService;

class AboutUsPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, InstitutionalSettingsService $institutionalSetting)
    {

        $institutionalSettings = $institutionalSetting->institution();
        $aboutUs = $institutionalSetting->get([
            'about_us',
            'mission',
            'vision'
        ]);

        $institution = [
            ...$institutionalSettings,
            'about_us' => $aboutUs['about_us'] ?? 'Información no disponible en este momento',
            'mission' => $aboutUs['mission'] ?? 'Información no disponible en este momento',
            'vision' => $aboutUs['vision'] ?? 'Información no disponible en este momento',
        ];

        return Inertia::render('AboutUs', [
            'institution' => $institution
        ]);
    }
}
