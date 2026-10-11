<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:skills,name',
        ]);

        Skill::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Habilidad creada exitosamente.',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $skillId)
    {
        $skill = Skill::findOrFail($skillId);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:skills,name,'.$skill->id,
        ]);

        $skill->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Habilidad actualizada exitosamente.',
            ],
        ]);
    }
}
