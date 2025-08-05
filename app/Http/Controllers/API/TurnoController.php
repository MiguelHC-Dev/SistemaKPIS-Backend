<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::all();
        return response()->json(['data' => $turnos]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:20|unique:turnos',
            'hora_inicio' => 'nullable|date_format:H:i:s',
            'hora_fin' => 'nullable|date_format:H:i:s'
        ]);

        $turno = Turno::create($validated);

        return response()->json([
            'message' => 'Turno creado exitosamente',
            'data' => $turno
        ], 201);
    }

    public function show(Turno $turno)
    {
        return response()->json(['data' => $turno]);
    }

    public function update(Request $request, Turno $turno)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:20|unique:turnos,nombre,'.$turno->id,
            'hora_inicio' => 'nullable|date_format:H:i:s',
            'hora_fin' => 'nullable|date_format:H:i:s'
        ]);

        $turno->update($validated);

        return response()->json([
            'message' => 'Turno actualizado',
            'data' => $turno
        ]);
    }

    public function destroy(Turno $turno)
    {
        if ($turno->ventas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar, hay ventas asociadas'
            ], 422);
        }

        $turno->delete();
        return response()->json(['message' => 'Turno eliminado']);
    }
}
