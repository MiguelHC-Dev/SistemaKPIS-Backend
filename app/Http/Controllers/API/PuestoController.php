<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Puesto;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    public function index()
    {
        $puestos = Puesto::all();
        return response()->json(['data' => $puestos]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:puestos',
            'descripcion' => 'nullable|string'
        ]);

        $puesto = Puesto::create($validated);

        return response()->json([
            'message' => 'Puesto creado exitosamente',
            'data' => $puesto
        ], 201);
    }

    public function show(Puesto $puesto)
    {
        return response()->json(['data' => $puesto]);
    }

    public function update(Request $request, Puesto $puesto)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:50|unique:puestos,nombre,'.$puesto->id,
            'descripcion' => 'nullable|string'
        ]);

        $puesto->update($validated);

        return response()->json([
            'message' => 'Puesto actualizado',
            'data' => $puesto
        ]);
    }

    public function destroy(Puesto $puesto)
    {
        if ($puesto->empleados()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar, hay empleados asociados'
            ], 422);
        }

        $puesto->delete();
        return response()->json(['message' => 'Puesto eliminado']);
    }
}
