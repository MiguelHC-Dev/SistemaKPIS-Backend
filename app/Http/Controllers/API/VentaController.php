<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['turno', 'area', 'usuarioRegistro']);

        // Filtros
        if ($request->has('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->has('turno_id')) {
            $query->where('turno_id', $request->turno_id);
        }

        // Paginación
        $ventas = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'data' => $ventas->items(),
            'meta' => [
                'total' => $ventas->total(),
                'current_page' => $ventas->currentPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'turno_id' => 'required|exists:turnos,id',
            'monto' => 'required|numeric|min:0',
            'area_id' => 'required|exists:areas,id'
        ]);

        // Validar si ya existe un registro para esta combinación
        $existing = Venta::where('fecha', $validated['fecha'])
            ->where('turno_id', $validated['turno_id'])
            ->where('area_id', $validated['area_id'])
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Ya existe un registro para esta fecha, turno y área',
                'errors' => [
                    'general' => 'Solo puede haber un registro por día, turno y área'
                ]
            ], 422);
        }

        $validated['usuario_registro_id'] = auth()->id();

        $venta = Venta::create($validated);

        return response()->json([
            'message' => 'Venta registrada',
            'data' => $venta
        ], 201);
    }

    public function show(Venta $venta)
    {
        return response()->json([
            'data' => $venta->load(['turno', 'area', 'usuarioRegistro'])
        ]);
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'fecha' => 'sometimes|date',
            'turno_id' => 'sometimes|exists:turnos,id',
            'monto' => 'sometimes|numeric|min:0',
            'area_id' => 'sometimes|exists:areas,id'
        ]);

        $venta->update($validated);

        return response()->json([
            'message' => 'Venta actualizada',
            'data' => $venta
        ]);
    }

    public function destroy(Venta $venta)
    {
        try {
            $venta->delete();
            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta'
            ], 500);
        }
    }

    // Agrega este nuevo método al VentaController
    public function checkExisting(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'turno_id' => 'required|exists:turnos,id',
            'area_id' => 'required|exists:areas,id'
        ]);

        $venta = Venta::where('fecha', $validated['fecha'])
            ->where('turno_id', $validated['turno_id'])
            ->where('area_id', $validated['area_id'])
            ->first();

        if ($venta) {
            return response()->json([
                'exists' => true,
                'data' => $venta
            ]);
        }

        return response()->json([
            'exists' => false
        ]);
    }

    public function updateVenta(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0'
        ]);

        // Solo permitimos actualizar el monto, no otros campos
        $venta->update(['monto' => $validated['monto']]);

        return response()->json([
            'message' => 'Venta actualizada correctamente',
            'data' => $venta->load(['turno', 'area', 'usuarioRegistro'])
        ]);
    }

    // Agrega este método al VentaController
    public function getTotalesPorDia(Request $request)
    {
        $query = Venta::selectRaw("DATE_FORMAT(fecha, '%Y-%m-%d') as fecha_formateada, SUM(monto) as total")
            ->groupBy('fecha_formateada')
            ->orderBy('fecha_formateada');

        $totales = $query->get();

        return response()->json([
            'totales' => $totales->mapWithKeys(function ($item) {
                return [$item->fecha_formateada => (float)$item->total];
            })
        ]);
    }

    public function getVentasPorDia(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $ventas = Venta::with(['turno', 'area'])
            ->where('fecha', $request->fecha)
            ->get();

        return response()->json([
            'data' => $ventas,
            'total' => $ventas->sum('monto')
        ]);
    }


}
