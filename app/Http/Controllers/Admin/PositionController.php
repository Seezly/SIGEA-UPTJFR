<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PositionController extends Controller
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
            'area_id' => 'required|integer|'.Rule::exists('areas', 'id')->where('is_active', true)->whereNull('deleted_at'),
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:positions,code',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        Position::create($validated);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Cargo creado exitosamente.',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $positionId)
    {
        $position = Position::findOrFail($positionId);

        $code = $request->input('code');

        if (is_string($code) && $code !== '') {
            $request->merge(['code' => Str::upper($code)]);
        }

        $validated = $request->validate([
            'area_id' => 'sometimes|integer|'.Rule::exists('areas', 'id')->where('is_active', true)->whereNull('deleted_at'),
            'name' => 'string|max:100',
            'code' => 'string|max:10|unique:positions,code,'.$position->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $position->update($validated);

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Cargo actualizado exitosamente.',
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $positionId)
    {
        $position = Position::findOrFail($positionId);

        abort_if($position->is_active, 403, 'No se puede eliminar un cargo activo.');

        $isInUse = DB::table('available_jobs')
            ->where('position_id', $position->id)
            ->whereNull('deleted_at')
            ->exists()
            || DB::table('employees')
                ->where('current_position_id', $position->id)
                ->whereNull('deleted_at')
                ->exists();

        abort_if($isInUse, 403, 'No se puede eliminar un cargo que está siendo usado.');

        $position->delete();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Cargo eliminado exitosamente.',
            ],
        ]);
    }
}
