<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ReporteController extends Controller
{
    public function reporteVentas(Request $request)
    {
        $query = Venta::with(['area', 'turno', 'usuarioRegistro'])
            ->orderBy('fecha', 'desc');

        // Aplicar filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('turno_id')) {
            $query->where('turno_id', $request->turno_id);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_registro_id', $request->usuario_id);
        }

        $ventas = $query->get();

        // Procesar datos para gráficas
        $ventasPorDia = $ventas->groupBy(function($item) {
            return Carbon::parse($item->fecha)->format('d/m/Y');
        })->map(function($day) {
            return $day->sum('monto');
        });

        $ventasPorArea = $ventas->groupBy(function($item) {
            return $item->area->nombre ?? 'Sin área';
        })->map(function($area) {
            return $area->sum('monto');
        })->sortDesc();

        $ventasPorTurno = $ventas->groupBy(function($item) {
            return $item->turno->nombre ?? 'Sin turno';
        })->map(function($turno) {
            return $turno->sum('monto');
        })->sortDesc();

        // Generar gráficas y obtener sus rutas locales
        $chartVentasPorDia = $this->generateAndSaveChart(
            'bar',
            $ventasPorDia->keys()->toArray(),
            $ventasPorDia->values()->toArray(),
            'Ventas por Día'
        );

        $chartVentasPorArea = $this->generateAndSaveChart(
            'pie',
            $ventasPorArea->keys()->toArray(),
            $ventasPorArea->values()->toArray(),
            'Ventas por Área'
        );

        $chartVentasPorTurno = $this->generateAndSaveChart(
            'doughnut',
            $ventasPorTurno->keys()->toArray(),
            $ventasPorTurno->values()->toArray(),
            'Ventas por Turno'
        );

        // Generar PDF
        $pdf = PDF::loadView('reportes.ventas', [
            'ventas' => $ventas,
            'montoTotal' => $ventas->sum('monto'),
            'conteoVentas' => $ventas->count(),
            'generadoEl' => Carbon::now('America/Mexico_City')->format('d/m/Y H:i'),
            'chartVentasPorDia' => $chartVentasPorDia,
            'chartVentasPorArea' => $chartVentasPorArea,
            'chartVentasPorTurno' => $chartVentasPorTurno
        ])->setPaper('a4', 'portrait');

        // Limpiar imágenes temporales después de generar el PDF
        if ($chartVentasPorDia) Storage::delete($chartVentasPorDia);
        if ($chartVentasPorArea) Storage::delete($chartVentasPorArea);
        if ($chartVentasPorTurno) Storage::delete($chartVentasPorTurno);

        // Determinar el tipo de respuesta según el parámetro preview
        if ($request->boolean('preview')) {
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="reporte_ventas.pdf"');
        }

        return $pdf->download('reporte_ventas.pdf');
    }




    private function generateAndSaveChart($type, $labels, $data, $title)
    {
        if (empty($data)) {
            return null;
        }

        $chartConfig = [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['display' => true, 'position' => 'top'],
                    'title' => [
                        'display' => true,
                        'text' => $title,
                        'font' => ['size' => 14]
                    ]
                ],
                'responsive' => true
            ]
        ];

        $chartUrl = 'https://quickchart.io/chart?width=600&height=300&c=' . urlencode(json_encode($chartConfig));

        try {
            $imageData = file_get_contents($chartUrl);
            if ($imageData === false) {
                return null;
            }

            $filename = 'charts/chart_' . md5(serialize($chartConfig)) . '.png';
            Storage::put($filename, $imageData);

            return Storage::path($filename);
        } catch (\Exception $e) {
            return null;
        }
    }


    public function reporteAsistencias(Request $request)
    {
        $query = Asistencia::with(['empleado', 'usuarioRegistro'])
            ->orderBy('fecha', 'desc');

        // Aplicar filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $asistencias = $query->get();

        // Validar si hay registros
        if ($asistencias->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron registros con los filtros seleccionados'
            ], 404);
        }

        // Procesar datos para estadísticas
        $asistenciasPorDia = $asistencias->groupBy(function($item) {
            return Carbon::parse($item->fecha)->format('d/m/Y');
        })->map(function($day) {
            return [
                'total' => $day->count(),
                'presentes' => $day->where('estado', 'Presente')->count(),
                'ausentes_justificados' => $day->where('estado', 'Ausente con justificación')->count(),
                'ausentes_injustificados' => $day->where('estado', 'Ausente injustificado')->count()
            ];
        });

        $asistenciasPorEmpleado = $asistencias->groupBy('empleado.nombre_completo')
            ->map(function($empleado) {
                return [
                    'total' => $empleado->count(),
                    'presentes' => $empleado->where('estado', 'Presente')->count(),
                    'ausentes_justificados' => $empleado->where('estado', 'Ausente con justificación')->count(),
                    'ausentes_injustificados' => $empleado->where('estado', 'Ausente injustificado')->count()
                ];
            })->sortByDesc('total');

        $resumenEstados = [
            'Presente' => $asistencias->where('estado', 'Presente')->count(),
            'Ausente con justificación' => $asistencias->where('estado', 'Ausente con justificación')->count(),
            'Ausente injustificado' => $asistencias->where('estado', 'Ausente injustificado')->count()
        ];

        // Generar gráficas
        $chartAsistenciasPorDia = $this->generateAndSaveChart(
            'bar',
            $asistenciasPorDia->keys()->toArray(),
            $asistenciasPorDia->pluck('presentes')->toArray(),
            'Asistencias por Día'
        );

        $chartAsistenciasPorEmpleado = $this->generateAndSaveChart(
            'bar',
            $asistenciasPorEmpleado->keys()->toArray(),
            $asistenciasPorEmpleado->pluck('presentes')->toArray(),
            'Asistencias por Empleado'
        );

        $chartResumenEstados = $this->generateAndSaveChart(
            'pie',
            array_keys($resumenEstados),
            array_values($resumenEstados),
            'Resumen de Estados'
        );

        // Generar PDF
        $pdf = PDF::loadView('reportes.asistencias', [
            'asistencias' => $asistencias,
            'totalRegistros' => $asistencias->count(),
            'resumenEstados' => $resumenEstados,
            'generadoEl' => Carbon::now('America/Mexico_City')->format('d/m/Y H:i'),
            'chartAsistenciasPorDia' => $chartAsistenciasPorDia,
            'chartAsistenciasPorEmpleado' => $chartAsistenciasPorEmpleado,
            'chartResumenEstados' => $chartResumenEstados,
            'filtros' => $request->all()
        ])->setPaper('a4', 'portrait');

        // Limpiar imágenes temporales
        if ($chartAsistenciasPorDia) Storage::delete($chartAsistenciasPorDia);
        if ($chartAsistenciasPorEmpleado) Storage::delete($chartAsistenciasPorEmpleado);
        if ($chartResumenEstados) Storage::delete($chartResumenEstados);

        // Determinar el tipo de respuesta según el parámetro preview
        if ($request->boolean('preview')) {
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="reporte_asistencias.pdf"');
        }

        return $pdf->download('reporte_asistencias.pdf');
    }
}
