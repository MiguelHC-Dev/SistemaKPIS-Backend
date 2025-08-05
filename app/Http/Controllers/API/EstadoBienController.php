<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EstadoBien;
use Illuminate\Http\Request;

class EstadoBienController extends Controller
{
    public function index()
    {
        $estados = EstadoBien::all();
        return response()->json(['data' => $estados]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:20|unique:estado_bienes'
        ]);

        $estado = EstadoBien::create($validated);

        return response()->json([
            'message' => 'Estado creado exitosamente',
            'data' => $estado
        ], 201);
    }

    public function show(EstadoBien $estadoBien)
    {
        return response()->json([
            'data' => $estadoBien->load('bienes')
        ]);
    }

    public function update(Request $request, EstadoBien $estadoBien)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:20|unique:estado_bienes,nombre,'.$estadoBien->id
        ]);

        $estadoBien->update($validated);

        return response()->json([
            'message' => 'Estado actualizado',
            'data' => $estadoBien
        ]);
    }

    public function destroy(EstadoBien $estadoBien)
    {
        if ($estadoBien->bienes()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar, hay bienes asociados'
            ], 422);
        }

        $estadoBien->delete();
        return response()->json(['message' => 'Estado eliminado']);
    }
}
