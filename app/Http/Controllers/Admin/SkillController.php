<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $skillId)
    {
        $skill = Skill::findOrFail($skillId);

        abort_if($skill->is_active, 403, 'No se puede eliminar una habilidad activa.');

        $isInUse = DB::table('applicant_skills')
            ->where('skill_id', $skill->id)
            ->whereNull('deleted_at')
            ->exists();

        abort_if($isInUse, 403, 'No se puede eliminar una habilidad que está siendo usada.');

        $skill->delete();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Habilidad eliminada exitosamente.',
            ],
        ]);
    }
}
