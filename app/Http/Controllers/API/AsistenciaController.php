<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Asistencia::with(['empleado', 'usuarioRegistro']);

        if ($request->has('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->has('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        $asistencias = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'data' => $asistencias->items(),
            'meta' => [
                'total' => $asistencias->total(),
                'current_page' => $asistencias->currentPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:Presente,Ausente con justificación,Ausente injustificado',
            'observaciones' => 'nullable|string'
        ]);

        $validated['usuario_registro_id'] = auth()->id();

        $asistencia = Asistencia::create($validated);

        return response()->json([
            'message' => 'Asistencia registrada',
            'data' => $asistencia
        ], 201);
    }
}
