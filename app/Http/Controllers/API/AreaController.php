<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return response()->json(['data' => $areas]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:areas',
            'descripcion' => 'nullable|string'
        ]);

        $area = Area::create($validated);

        return response()->json([
            'message' => 'Área creada exitosamente',
            'data' => $area
        ], 201);
    }

    public function show(Area $area)
    {
        return response()->json([
            'data' => $area->load('empleados')
        ]);
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:50|unique:areas,nombre,'.$area->id,
            'descripcion' => 'nullable|string'
        ]);

        $area->update($validated);

        return response()->json([
            'message' => 'Área actualizada',
            'data' => $area
        ]);
    }

    public function destroy(Area $area)
    {
        if ($area->empleados()->exists() || $area->ventas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar, hay registros asociados'
            ], 422);
        }

        $area->delete();
        return response()->json(['message' => 'Área eliminada']);
    }
}
