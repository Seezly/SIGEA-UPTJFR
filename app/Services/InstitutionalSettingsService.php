<?php

namespace App\Services;

use App\Models\InstitutionalSetting;

class InstitutionalSettingsService
{
    /**
     * Retrieve institutional settings such as name, acronym, logo, and facade.
     * @return array
     */
    public function institution(): array
    {
        $settings = InstitutionalSetting::whereIn('key', ['name', 'acronym', 'logo', 'facade'])
            ->pluck('value', 'key')
            ->toArray();

        return [
            'name' => $settings['name'] ?? 'Universidad Politécnica Territorial "José Félix Ribas"',
            'acronym' => $settings['acronym'] ?? 'UPTJFR',
            'logo' => $settings['logo'] ?? '/logo_upt_vector.svg',
            'facade' => $settings['facade'] ?? '/fachada_upt.jpg',
        ];
    }

    /**
     * Retrieve terms of service as an array of sections and updated_at.
     * @return array|string
     */
    public function terms(): array | string
    {
        $termsSetting = InstitutionalSetting::where('key', 'terms_of_service')->first();

        $terms = $termsSetting ? json_decode($termsSetting->value, true) : [];

        $filteredTerms = collect($terms)->filter(function ($term) {
            return is_array($term)
                && !empty($term['id'])
                && !empty($term['title'])
                && !empty($term['content']);
        })->values()->toArray();

        if (empty($filteredTerms)) {
            $filteredTerms = 'Información no disponible en este momento';
        }

        return [
            'sections' => $filteredTerms,
            'updated_at' => $termsSetting?->updated_at?->toIso8601String() ?? null
        ];
    }

    /**
     * Add new terms of service while keeping existing ones.
     * @param array $newTerms
     * @return void
     */
    public function addTerms(array $newTerms): void
    {
        $existingTermsSetting = InstitutionalSetting::where('key', 'terms_of_service')->first();

        $existingTerms = $existingTermsSetting ? json_decode($existingTermsSetting->value, true) : [];

        $mergedTerms = collect($existingTerms)
            ->merge($newTerms)
            ->unique('id')
            ->values()
            ->toArray();

        if ($existingTermsSetting) {
            $existingTermsSetting->update(['value' => json_encode($mergedTerms)]);
        } else {
            InstitutionalSetting::create([
                'key' => 'terms_of_service',
                'value' => json_encode($mergedTerms),
            ]);
        }
    }

    /**
     * Update existing terms of service.
     * @param array $updatedTerms
     * @return void
     */
    public function updateTerms(array $updatedTerms): void
    {
        $existingTermsSetting = InstitutionalSetting::where('key', 'terms_of_service')->first();

        if ($existingTermsSetting) {
            $existingTermsSetting->update(['value' => json_encode($updatedTerms)]);
        } else {
            InstitutionalSetting::create([
                'key' => 'terms_of_service',
                'value' => json_encode($updatedTerms),
            ]);
        }
    }

    /**
     * Retrieve privacy policies as an array of sections and updated_at.
     * @return array|string
     */
    public function privacyPolicies(): array | string
    {
        $privacySetting = InstitutionalSetting::where('key', 'privacy_policies')->first();

        $policies = $privacySetting ? json_decode($privacySetting->value, true) : [];

        $filteredPolicies = collect($policies)->filter(function ($policy) {
            return is_array($policy)
                && !empty($policy['id'])
                && !empty($policy['title'])
                && !empty($policy['content']);
        })->values()->toArray();

        if (empty($filteredPolicies)) {
            $filteredPolicies = 'Información no disponible en este momento';
        }

        return [
            'sections' => $filteredPolicies,
            'updated_at' => $privacySetting?->updated_at?->toIso8601String() ?? null
        ];
    }

    /**
     * Add new privacy policy while keeping existing ones.
     * @param array $newPolicy
     * @return void
     */
    public function addPrivacyPolicy(array $newPolicy): void
    {
        $existingPoliciesSetting = InstitutionalSetting::where('key', 'privacy_policies')->first();

        $existingPolicies = $existingPoliciesSetting ? json_decode($existingPoliciesSetting->value, true) : [];

        $mergedPolicies = collect($existingPolicies)
            ->merge([$newPolicy])
            ->unique('id')
            ->values()
            ->toArray();

        if ($existingPoliciesSetting) {
            $existingPoliciesSetting->update(['value' => json_encode($mergedPolicies)]);
        } else {
            InstitutionalSetting::create([
                'key' => 'privacy_policies',
                'value' => json_encode([$newPolicy]),
            ]);
        }
    }

    /**
     * Update existing privacy policy.
     * @param array $updatedPolicy
     * @return void
     */
    public function updatePrivacyPolicy(array $updatedPolicy): void
    {
        $existingPoliciesSetting = InstitutionalSetting::where('key', 'privacy_policies')->first();

        if ($existingPoliciesSetting) {
            $existingPoliciesSetting->update(['value' => json_encode($updatedPolicy)]);
        } else {
            InstitutionalSetting::create([
                'key' => 'privacy_policies',
                'value' => json_encode($updatedPolicy),
            ]);
        }
    }

    /**
     * Retrieve all institutional settings as an associative array.
     * @return array
     */
    public function all(): array
    {
        return InstitutionalSetting::pluck('value', 'key')->toArray();
    }

    /**
     * Get a specific institutional setting by its key or set of keys.
     * @param string|array $key
     * @return string|array|null
     */
    public function get(string|array $key): string|array|null
    {
        if (is_array($key)) {
            return InstitutionalSetting::whereIn('key', $key)->pluck('value', 'key')->toArray();
        }

        $setting = InstitutionalSetting::where('key', $key)->first();

        return $setting ? $setting->value : null;
    }

    /**
     * Set or update a specific institutional setting by its key.
     * @param string $key
     * @param string $value
     * @return void
     */
    public function set(string $key, string $value): void
    {
        $setting = InstitutionalSetting::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            InstitutionalSetting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
