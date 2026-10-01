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
            'modules' => Module::all()
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
    public function destroy(string $id)
    {
        //
    }
}
