<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InstitutionalSetting;

class AboutUsPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $institutionalSettings = InstitutionalSetting::pluck('value', 'key')->toArray();

        $institution = [
            'name' => $institutionalSettings['name'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'acronym' => $institutionalSettings['acronym'] ?? 'UPTJFR',
            'description' => $institutionalSettings['description'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'logo' => $institutionalSettings['logo'] ?? '/logo_upt_vector.svg',
            'facade' => $institutionalSettings['facade'] ?? '/fachada_upt.jpg',
            'about_us' => $institutionalSettings['about_us'] ?? 'Información no disponible en este momento',
            'mission' => $institutionalSettings['mission'] ?? 'Información no disponible en este momento',
            'vision' => $institutionalSettings['vision'] ?? 'Información no disponible en este momento',
        ];

        return Inertia::render('AboutUs', [
            'institution' => $institution
        ]);
    }
}
