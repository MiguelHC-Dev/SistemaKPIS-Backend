<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BienController extends Controller
{
    public function index(Request $request)
    {
        $query = Bien::with(['categoria', 'ubicacion', 'estado', 'usuarioRegistro'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->has('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->has('ubicacion_id')) {
            $query->where('ubicacion_id', $request->ubicacion_id);
        }

        if ($request->has('search')) {
            $query->where('nombre', 'like', '%'.$request->search.'%');
        }

        // Paginación
        $perPage = $request->input('per_page', 10);
        $bienes = $query->paginate($perPage);

        return response()->json([
            'data' => $bienes->items(),
            'meta' => [
                'total' => $bienes->total(),
                'per_page' => $bienes->perPage(),
                'current_page' => $bienes->currentPage(),
                'last_page' => $bienes->lastPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria_id' => 'required|exists:categoria_bienes,id',
            'cantidad' => 'required|integer|min:1',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'estado_id' => 'required|exists:estado_bienes,id',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $validated['usuario_registro_id'] = auth()->id();
            $bien = Bien::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Bien registrado exitosamente',
                'data' => $bien->load(['categoria', 'ubicacion', 'estado'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar el bien',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Bien $bien)
    {
        return response()->json([
            'data' => $bien->load(['categoria', 'ubicacion', 'estado', 'usuarioRegistro'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $bien = Bien::find($id);

        if (!$bien) {
            return response()->json([
                'success' => false,
                'message' => 'Bien no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'categoria_id' => 'sometimes|exists:categoria_bienes,id',
            'cantidad' => 'sometimes|integer|min:1',
            'ubicacion_id' => 'sometimes|exists:ubicaciones,id',
            'estado_id' => 'sometimes|exists:estado_bienes,id',
            'observaciones' => 'nullable|string'
        ]);

        $bien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bien actualizado',
            'data' => $bien->fresh(['categoria', 'ubicacion', 'estado'])
        ]);
    }


    public function destroy($id)
    {
        $bien = Bien::find($id);

        if (!$bien) {
            return response()->json([
                'success' => false,
                'message' => 'Bien no encontrado'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $bien->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bien eliminado'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el bien',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function cambiarEstado(Request $request, Bien $bien)
    {
        $validated = $request->validate([
            'estado_id' => 'required|exists:estado_bienes,id',
            'observaciones' => 'nullable|string'
        ]);

        $bien->update([
            'estado_id' => $validated['estado_id'],
            'observaciones' => $validated['observaciones'] ?? null
        ]);

        return response()->json([
            'message' => 'Estado del bien actualizado',
            'data' => $bien->fresh('estado')
        ]);
    }
}
