<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Admin/Modules', [
            'modules' => Module::withTrashed()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:modules,slug',
            'description' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $module = Module::create($validated);

        return back()->with('flash', [
            'success' => true,
            'message' => 'Módulo creado exitosamente.',
            'module' => $module
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $moduleId)
    {
        $module = Module::findOrFail($moduleId);

        $validated = $request->validate([
            'name' => 'string|max:50',
            'slug' => 'string|max:50|unique:modules,slug,' . $module->id,
            'description' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $module->update($validated);

        return back()->with('flash', [
            'success' => true,
            'message' => 'Módulo actualizado exitosamente.',
            'module' => $module
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $moduleId)
    {
        $module = Module::findOrFail($moduleId);

        abort_if($module->is_active, 403, 'No se puede eliminar un módulo activo.');

        $module->delete();

        return back()->with('flash', [
            'success' => true,
            'message' => 'Módulo eliminado exitosamente.',
        ]);
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $moduleId)
    {
        $module = Module::withTrashed()->findOrFail($moduleId);

        abort_unless($module->trashed(), 404);

        $module->restore();

        return back()->with('flash', [
            'success' => true,
            'message' => 'Módulo restaurado exitosamente.',
        ]);
    }
}
