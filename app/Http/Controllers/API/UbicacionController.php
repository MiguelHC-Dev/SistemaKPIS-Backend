<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UbicacionController extends Controller
{
    public function index()
    {
        $ubicaciones = Ubicacion::all();
        return response()->json(['data' => $ubicaciones]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:ubicaciones',
            'descripcion' => 'nullable|string'
        ]);

        $ubicacion = Ubicacion::create($validated);

        return response()->json([
            'message' => 'Ubicación creada exitosamente',
            'data' => $ubicacion
        ], 201);
    }

    public function show(Ubicacion $ubicacion)
    {
        return response()->json([
            'data' => $ubicacion->load('bienes')
        ]);
    }

    public function update(Request $request, $id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json([
                'success' => false,
                'message' => "Ubicación con ID $id no encontrada."
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:ubicaciones,nombre,' . $ubicacion->id,
            'descripcion' => 'nullable|string'
        ]);

        $ubicacion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ubicación actualizada',
            'data' => $ubicacion
        ]);
    }

    public function destroy($id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json([
                'success' => false,
                'message' => "Ubicación con ID $id no encontrada."
            ], 404);
        }

        $count = $ubicacion->bienes()->whereNull('deleted_at')->count();

        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar la ubicación porque tiene $count bien(es) asociado(s) activos.",
                'bienes_count' => $count
            ], 422);
        }

        try {
            $ubicacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ubicación eliminada exitosamente (soft delete)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la ubicación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
