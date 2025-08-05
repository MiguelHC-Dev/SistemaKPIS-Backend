<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Empleado::with(['puesto', 'area'])
            ->orderBy('nombre_completo');

        // Filtros
        if ($request->has('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->has('puesto_id')) {
            $query->where('puesto_id', $request->puesto_id);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        if ($request->has('search')) {
            $query->where('nombre_completo', 'like', '%'.$request->search.'%');
        }

        // Paginación
        $perPage = $request->input('per_page', 15);
        $empleados = $query->paginate($perPage);

        return response()->json([
            'data' => $empleados->items(),
            'meta' => [
                'total' => $empleados->total(),
                'per_page' => $empleados->perPage(),
                'current_page' => $empleados->currentPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'puesto_id' => 'required|exists:puestos,id',
            'area_id' => 'required|exists:areas,id',
            'fecha_registro' => 'required|date',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:empleados,email',
            'direccion' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $empleado = Empleado::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Empleado registrado exitosamente',
                'data' => $empleado->load(['puesto', 'area'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Empleado $empleado)
    {
        return response()->json([
            'data' => $empleado->load(['puesto', 'area', 'asistencias', 'evaluaciones'])
        ]);
    }

    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:100',
            'puesto_id' => 'sometimes|exists:puestos,id',
            'area_id' => 'sometimes|exists:areas,id',
            'fecha_registro' => 'sometimes|date',
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                Rule::unique('empleados')->ignore($empleado->id)
            ],
            'direccion' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean'
        ]);

        $empleado->update($validated);

        return response()->json([
            'message' => 'Empleado actualizado',
            'data' => $empleado->fresh(['puesto', 'area'])
        ]);
    }

    public function destroy(Empleado $empleado)
    {
        DB::beginTransaction();

        try {
            // Verificar si tiene registros asociados
            if ($empleado->asistencias()->exists() || $empleado->evaluaciones()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar, el empleado tiene registros asociados'
                ], 422);
            }

            $empleado->delete();
            DB::commit();

            return response()->json([
                'message' => 'Empleado eliminado'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Empleado $empleado)
    {
        $empleado->update([
            'activo' => !$empleado->activo
        ]);

        return response()->json([
            'message' => 'Estado del empleado actualizado',
            'data' => [
                'id' => $empleado->id,
                'activo' => $empleado->activo
            ]
        ]);
    }

    public function asistencias(Empleado $empleado, Request $request)
    {
        $query = $empleado->asistencias()
            ->orderBy('fecha', 'desc');

        if ($request->has('mes')) {
            $query->whereMonth('fecha', $request->mes);
        }

        if ($request->has('anio')) {
            $query->whereYear('fecha', $request->anio);
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

    public function getActiveEmployees(Request $request)
    {
        $query = Empleado::with(['puesto', 'area'])
            ->where('activo', true)
            ->orderBy('nombre_completo');

        // Opcional: agregar paginación si es necesario
        $perPage = $request->input('per_page', 100); // Número alto para evitar paginación
        $empleados = $query->paginate($perPage);

        return response()->json([
            'data' => $empleados->items(),
            'meta' => [
                'total' => $empleados->total(),
                'per_page' => $empleados->perPage(),
                'current_page' => $empleados->currentPage()
            ]
        ]);
    }
}
