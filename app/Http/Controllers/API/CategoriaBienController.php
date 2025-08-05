<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CategoriaBien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaBienController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $query = CategoriaBien::query();

            if ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            }

            $categorias = $query->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:50|unique:categoria_bienes',
                'descripcion' => 'nullable|string|max:255'
            ]);

            $categoria = CategoriaBien::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $categoria
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $categoria = CategoriaBien::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $categoria
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $categoria = CategoriaBien::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'required|string|max:50|unique:categoria_bienes,nombre,'.$categoria->id,
                'descripcion' => 'nullable|string|max:255'
            ]);

            $categoria->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $categoria
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $categoria = CategoriaBien::findOrFail($id);

            // Verificar si hay bienes asociados
            if ($categoria->bienes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene bienes asociados'
                ], 422);
            }

            $categoria->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
