<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Empleado;
use App\Models\Asistencia;
use App\Models\Bien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $range = $request->query('range', 'hoy');
        $now = Carbon::now('America/Mexico_City');
        $hoy = $now->toDateString();

        // Determinar el rango de fechas actual
        if ($range === 'hoy') {
            $startDate = $hoy;
            $endDate = $hoy;
            $previousStartDate = $now->copy()->subDay()->toDateString();
            $previousEndDate = $previousStartDate;
        } elseif ($range === 'semana') {
            $startDate = $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
            $endDate = $now->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
            $previousStartDate = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
            $previousEndDate = $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->toDateString();
        } elseif ($range === 'mes') {
            $startDate = $now->copy()->startOfMonth()->toDateString();
            $endDate = $now->copy()->endOfMonth()->toDateString();
            $previousStartDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
            $previousEndDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
        } else {
            return response()->json([
                'error' => 'Parámetro range inválido. Usa: hoy, semana, mes.'
            ], 400);
        }

        // Datos actuales
        $ventasTotal = Venta::whereBetween('fecha', [$startDate, $endDate])->sum('monto');
        $ventasConteo = Venta::whereBetween('fecha', [$startDate, $endDate])->count();
        $ticketPromedio = $ventasConteo > 0 ? $ventasTotal / $ventasConteo : 0;

        // Datos del período anterior
        $previousVentasTotal = Venta::whereBetween('fecha', [$previousStartDate, $previousEndDate])->sum('monto');
        $previousVentasConteo = Venta::whereBetween('fecha', [$previousStartDate, $previousEndDate])->count();
        $previousTicketPromedio = $previousVentasConteo > 0 ? $previousVentasTotal / $previousVentasConteo : 0;

        // Calcular cambios porcentuales
        $changeTotal = $this->calculatePercentageChange($ventasTotal, $previousVentasTotal);
        $changeConteo = $this->calculatePercentageChange($ventasConteo, $previousVentasConteo);
        $changeTicket = $this->calculatePercentageChange($ticketPromedio, $previousTicketPromedio);

        // Resto de tus consultas...
        $ventasPorDia = Venta::select(
            DB::raw('DATE(fecha) as dia'),
            DB::raw('SUM(monto) as total')
        )
            ->whereBetween('fecha', [$startDate, $endDate])
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();

        $ventasPorArea = Venta::select('area_id', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha', [$startDate, $endDate])
            ->groupBy('area_id')
            ->with('area')
            ->get();

        $ventasPorTurno = Venta::select('turno_id', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha', [$startDate, $endDate])
            ->groupBy('turno_id')
            ->with('turno')
            ->get();

        $empleadosActivos = Empleado::where('activo', true)->count();

        $totalAsistenciasHoy = Asistencia::whereDate('fecha', $hoy)->count();
        $presentesHoy = Asistencia::whereDate('fecha', $hoy)
            ->where('estado', 'Presente')
            ->count();
        $tasaAsistenciaHoy = $totalAsistenciasHoy > 0
            ? round(($presentesHoy / $totalAsistenciasHoy) * 100, 2)
            : 0;

        $bienesPorCategoria = Bien::select('categoria_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();

        $bienesPorEstado = Bien::select('estado_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estado_id')
            ->with('estado')
            ->get();

        return response()->json([
            'ventas' => [
                'total' => $ventasTotal,
                'conteo' => $ventasConteo,
                'ticketPromedio' => $ticketPromedio,
                'porDia' => $ventasPorDia,
                'porArea' => $ventasPorArea,
                'porTurno' => $ventasPorTurno,
                'changes' => [
                    'total' => $changeTotal,
                    'conteo' => $changeConteo,
                    'ticketPromedio' => $changeTicket,
                ],
                'previous_period' => [
                    'start' => $previousStartDate,
                    'end' => $previousEndDate
                ]
            ],
            'empleados' => [
                'activos' => $empleadosActivos,
                'tasaAsistenciaHoy' => $tasaAsistenciaHoy,
            ],
            'bienes' => [
                'porCategoria' => $bienesPorCategoria,
                'porEstado' => $bienesPorEstado,
            ],
        ]);
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}
