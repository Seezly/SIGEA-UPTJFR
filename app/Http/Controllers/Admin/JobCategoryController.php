<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = $request->input('name');

        if (is_string($name) && $name !== '') {
            $request->merge(['slug' => Str::slug($name)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:job_categories,name',
            'slug' => 'required|string|max:100|unique:job_categories,slug',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        JobCategory::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Categoría de puesto creada exitosamente.',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $jobCategoryId)
    {
        $jobCategory = JobCategory::findOrFail($jobCategoryId);

        $name = $request->input('name');

        if (is_string($name) && $name !== '') {
            $request->merge(['slug' => Str::slug($name)]);
        }

        $validated = $request->validate([
            'name' => 'string|max:100|unique:job_categories,name,'.$jobCategory->id,
            'slug' => 'string|max:100|unique:job_categories,slug,'.$jobCategory->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = collect($validated)->except(['name', 'slug'])->all();

        if (array_key_exists('name', $validated)) {
            $data['name'] = $validated['name'];
            $data['slug'] = $validated['slug'];
        }

        $jobCategory->update($data);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Categoría de puesto actualizada exitosamente.',
            ],
        ]);
    }
}
