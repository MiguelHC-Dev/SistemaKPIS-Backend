<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Evaluacion::with(['empleado', 'usuarioRegistro']);

        if ($request->has('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        if ($request->has('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->has('anio')) {
            $query->where('anio', $request->anio);
        }

        $evaluaciones = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'data' => $evaluaciones->items(),
            'meta' => [
                'total' => $evaluaciones->total(),
                'current_page' => $evaluaciones->currentPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
            'puntuacion' => 'required|integer|between:1,10',
            'comentarios' => 'nullable|string|max:200'
        ], [
            'empleado_id.required' => 'El campo empleado es obligatorio.',
            'empleado_id.exists' => 'El empleado seleccionado no existe.',
            'mes.required' => 'El campo mes es obligatorio.',
            'mes.integer' => 'El mes debe ser un número entero.',
            'mes.between' => 'El mes debe estar entre 1 y 12.',
            'anio.required' => 'El campo año es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.min' => 'El año debe ser mayor o igual a 2000.',
            'puntuacion.required' => 'La puntuación es obligatoria.',
            'puntuacion.integer' => 'La puntuación debe ser un número entero.',
            'puntuacion.between' => 'La puntuación debe estar entre 1 y 10.',
            'comentarios.string' => 'Los comentarios deben ser texto.',
            'comentarios.max' => 'Los comentarios no deben superar los 200 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (Evaluacion::where('empleado_id', $validated['empleado_id'])
            ->where('mes', $validated['mes'])
            ->where('anio', $validated['anio'])
            ->exists()) {
            return response()->json([
                'message' => 'Ya existe una evaluación para este empleado en el periodo seleccionado.'
            ], 422);
        }

        $validated['usuario_registro_id'] = auth()->id();

        $evaluacion = Evaluacion::create($validated);

        return response()->json([
            'message' => 'Evaluación registrada.',
            'data' => $evaluacion
        ], 201);
    }

    public function show(Evaluacion $evaluacion)
    {
        return response()->json([
            'data' => $evaluacion->load(['empleado', 'usuarioRegistro'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $evaluacion = Evaluacion::find($id);

        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'puntuacion' => 'sometimes|integer|between:1,10',
            'comentarios' => 'nullable|string|max:200'
        ], [
            'puntuacion.integer' => 'La puntuación debe ser un número entero.',
            'puntuacion.between' => 'La puntuación debe estar entre 1 y 10.',
            'comentarios.string' => 'Los comentarios deben ser texto.',
            'comentarios.max' => 'Los comentarios no deben superar los 200 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        $evaluacion->update($validator->validated());

        return response()->json([
            'message' => 'Evaluación actualizada.',
            'data' => $evaluacion
        ]);
    }

    public function destroy($id)
    {
        $evaluacion = Evaluacion::find($id);

        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada.'], 404);
        }

        $evaluacion->delete();

        return response()->json(['message' => 'Evaluación eliminada.']);
    }
}
