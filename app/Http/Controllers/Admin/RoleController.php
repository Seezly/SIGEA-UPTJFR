<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Admin/Roles', [
            'roles' => Role::all()
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
        ]);

        $role = Role::create($validated);

        if (!$role) {
            return back()->with([
                'flash' => [
                    'success' => false,
                    'message' => 'Fallo al crear el rol.',
                ]
            ]);
        }

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol creado exitosamente.',
                'role' => $role
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
    public function update(Request $request, string $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles,slug',
            'description' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $role = Role::where('id', $role)->update($validated);

        if (!$role) {
            return back()->with([
                'flash' => [
                    'success' => false,
                    'message' => 'Fallo al actualizar el rol.',
                ]
            ]);
        }

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol actualizado exitosamente.',
                'role' => $role
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $role)
    {
        $role = Role::find($role);

        if (!$role) {
            return back()->with([
                'flash' => [
                    'success' => false,
                    'message' => 'El rol que quieres eliminar no existe.',
                ]
            ]);
        }

        $role->delete();

        return back()->with([
            'flash' => [
                'success' => true,
                'message' => 'Rol eliminado exitosamente.',
            ]
        ]);
    }
}
