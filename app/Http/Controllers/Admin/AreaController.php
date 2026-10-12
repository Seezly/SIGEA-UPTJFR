<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AreaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $code = $request->input('code');

        if (is_string($code) && $code !== '') {
            $request->merge(['code' => Str::upper($code)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:areas,name',
            'code' => 'required|string|max:10|unique:areas,code',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        Area::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Área creada exitosamente.',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $areaId)
    {
        $area = Area::findOrFail($areaId);

        $code = $request->input('code');

        if (is_string($code) && $code !== '') {
            $request->merge(['code' => Str::upper($code)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:areas,name,'.$area->id,
            'code' => 'required|string|max:10|unique:areas,code,'.$area->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $area->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => array_key_exists('description', $validated)
                ? $validated['description']
                : $area->description,
            'is_active' => $validated['is_active'] ?? $area->is_active,
        ]);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Área actualizada exitosamente.',
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $areaId)
    {
        $area = Area::findOrFail($areaId);

        abort_if($area->is_active, 403, 'No se puede eliminar un área activa.');

        $isInUse = DB::table('positions')
            ->where('area_id', $area->id)
            ->whereNull('deleted_at')
            ->exists()
            || DB::table('available_jobs')
                ->where('area_id', $area->id)
                ->whereNull('deleted_at')
                ->exists()
            || DB::table('employees')
                ->where('current_area_id', $area->id)
                ->whereNull('deleted_at')
                ->exists();

        abort_if($isInUse, 403, 'No se puede eliminar un área que está siendo usada.');

        $area->delete();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Área eliminada exitosamente.',
            ],
        ]);
    }
}
