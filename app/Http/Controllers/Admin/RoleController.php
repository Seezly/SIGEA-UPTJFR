<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Admin/Roles', [
            'roles' => Role::withTrashed()->get()
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
            'slug' => 'required|string|max:50|unique:roles,slug',
            'description' => 'string|max:255',
            'is_active' => 'boolean',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated) {
            $role = Role::create(Arr::except($validated, 'permission_ids'));

            if (!empty($validated['permission_ids'])) {
                $role->permissions()->attach($validated['permission_ids']);
            }
        });

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol creado exitosamente.',
            ]
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
    public function update(Request $request, string $roleId)
    {
        $role = Role::findOrFail($roleId);
        $hasActiveUsers = $role->users()->where('is_active', true)->exists();

        $validated = $request->validate([
            'name' => 'string|max:50',
            'slug' => 'string|max:50|unique:roles,slug,' . $role->id,
            'description' => 'string|max:255',
            'is_active' => 'boolean',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if (
            isset($validated['is_active']) &&
            $validated['is_active'] === false &&
            $hasActiveUsers
        ) {
            abort(403, 'No se puede desactivar un rol que está siendo usado.');
        }

        if ($role->slug === 'admin') {
            abort(403, 'No se puede desactivar el rol \'Admin\'.');
        }

        DB::transaction(function () use ($validated, $role) {
            $role->update(Arr::except($validated, 'permission_ids'));

            $role->permissions()->sync($validated['permission_ids'] ?? []);
        });

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol actualizado exitosamente.',
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $roleId)
    {
        $role = Role::findOrFail($roleId);

        abort_if($role->is_active, 403, 'No se puede eliminar un módulo activo.');

        $role->delete();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol eliminado exitosamente.',
            ]
        ]);
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $roleId)
    {
        $role = Role::withTrashed()->findOrFail($roleId);

        abort_unless($role->trashed(), 404);

        $role->restore();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol restaurado exitosamente.',
            ]
        ]);
    }
}
