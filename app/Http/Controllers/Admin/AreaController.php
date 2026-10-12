<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
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
}
