<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InstitutionalSetting;

class TermsPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $institutionalSettings = InstitutionalSetting::pluck('value', 'key')->toArray();

        $institutionalTerms = InstitutionalSetting::select('value', 'updated_at')->where('key', 'terms_of_service')->first();

        $terms = $institutionalTerms ? json_decode($institutionalTerms->value, true) : [];

        $filteredTerms = collect($terms)->filter(function ($term) {
            return is_array($term)
                && !empty($term['id'])
                && !empty($term['title'])
                && !empty($term['content']);
        })->values()->toArray();

        if (empty($filteredTerms)) {
            $filteredTerms = 'Información no disponible en este momento';
        }

        $institution = [
            'name' => $institutionalSettings['name'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'acronym' => $institutionalSettings['acronym'] ?? 'UPTJFR',
            'description' => $institutionalSettings['description'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'logo' => $institutionalSettings['logo'] ?? '/logo_upt_vector.svg',
            'facade' => $institutionalSettings['facade'] ?? '/fachada_upt.jpg',
        ];

        return Inertia::render('Terms', [
            'terms' => [
                'sections' => $filteredTerms,
                'updated_at' => $institutionalTerms?->updated_at?->toIso8601String() ?? null
            ],
            'institution' => $institution,
        ]);
    }
}
