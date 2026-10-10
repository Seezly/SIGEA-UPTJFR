<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Admin/Permissions', [
            'permissions' => Permission::withTrashed()->get()
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
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:50|unique:permissions,name',
            'slug' => 'required|string|max:50|unique:permissions,slug',
            'description' => 'string|max:255'
        ]);

        $permission = Permission::create($validated);

        return back()->with('flash', [
            'success' => true,
            'message' => 'Permiso para el módulo: ' . $permission->moduleName() . ' creado satisfactoriamente.'
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
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:50|unique:permissions,name,' . $permission->id,
            'slug' => 'string|max:50|unique:permissions,slug,' . $permission->id,
            'description' => 'string|max:255'
        ]);

        $permission->update($validated);

        return back()->with('flash', [
            'success' => true,
            'message' => 'Permiso actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return back()->with('flash', [
            'success' => true,
            'message' => 'Permiso eliminado correctamente.'
        ]);
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $permissionId)
    {
        $permission = Permission::withTrashed()->findOrFail($permissionId);

        abort_unless($permission->trashed(), 404);

        $permission->restore();

        return back()->with('flash', [
            'success' => true,
            'message' => 'Permiso restaurado exitosamente.'
        ]);
    }
}
