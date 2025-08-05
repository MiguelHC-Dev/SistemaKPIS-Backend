<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hamburguesas Lidany's - Reporte de Asistencias</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1, h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .resumen { margin-top: 30px; }
        .resumen p { font-weight: bold; }
        .grafica { text-align: center; margin-top: 40px; page-break-inside: avoid; }
        .grafica-container { width: 100%; margin: 20px 0; }
        .grafica-row { display: flex; justify-content: center; margin-bottom: 30px; }
        .grafica-item { width: 60%; margin: 0 auto; }
        .chart-img { width: 100%; max-width: 600px; height: auto; }
        .no-data { color: #666; font-style: italic; }
        .filtros { margin-bottom: 20px; }
        .filtros p { margin: 5px 0; }
        .estado-presente { background-color: #d4edda; }
        .estado-justificado { background-color: #fff3cd; }
        .estado-injustificado { background-color: #f8d7da; }
    </style>
</head>
<body>

<h1>Hamburguesas Lidany's</h1>
<h2>Reporte de Asistencias</h2>
<p style="text-align: right;">
    Generado el: {{ $generadoEl }}
</p>

{{-- Mostrar filtros aplicados --}}
<div class="filtros">
    <h3>Filtros aplicados:</h3>
    @if(!empty($filtros['fecha_inicio']) || !empty($filtros['fecha_fin']))
        <p><strong>Rango de fechas:</strong>
            {{ $filtros['fecha_inicio'] ?? 'Desde inicio' }}
            al
            {{ $filtros['fecha_fin'] ?? 'Hoy' }}
        </p>
    @endif
    @if(!empty($filtros['empleado_id']))
        <p><strong>Empleado:</strong> {{ $asistencias->first()->empleado->nombre_completo ?? '' }}</p>
    @endif
    @if(!empty($filtros['estado']))
        <p><strong>Estado:</strong> {{ $filtros['estado'] }}</p>
    @endif
</div>

{{-- Tabla de asistencias --}}
<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Empleado</th>
        <th>Estado</th>
        <th>Observaciones</th>
        <th>Registró</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($asistencias as $asistencia)
        <tr class="estado-{{ strtolower(str_replace(' ', '-', $asistencia->estado)) }}">
            <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
            <td>{{ $asistencia->empleado->nombre_completo ?? '—' }}</td>
            <td>{{ $asistencia->estado }}</td>
            <td>{{ $asistencia->observaciones ?? '—' }}</td>
            <td>{{ $asistencia->usuarioRegistro->nombre_completo ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Estadísticas --}}
<div class="resumen">
    <h3>Resumen General</h3>
    <p>Total de registros: {{ $totalRegistros }}</p>
    <p>Presentes: {{ $resumenEstados['Presente'] }} ({{ round($resumenEstados['Presente'] / $totalRegistros * 100, 2) }}%)</p>
    <p>Ausentes con justificación: {{ $resumenEstados['Ausente con justificación'] }} ({{ round($resumenEstados['Ausente con justificación'] / $totalRegistros * 100, 2) }}%)</p>
    <p>Ausentes injustificados: {{ $resumenEstados['Ausente injustificado'] }} ({{ round($resumenEstados['Ausente injustificado'] / $totalRegistros * 100, 2) }}%)</p>
</div>

{{-- Gráficas Centradas --}}
<div class="grafica-row">
    <div class="grafica-item">
        <div class="grafica">
            <h3>Asistencias por Día</h3>
            <div class="grafica-container">
                @if($chartAsistenciasPorDia && file_exists($chartAsistenciasPorDia))
                    <img class="chart-img" src="{{ $chartAsistenciasPorDia }}" alt="Gráfica de asistencias por día">
                @else
                    <p class="no-data">No hay datos suficientes para mostrar esta gráfica</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="grafica-row">
    <div class="grafica-item">
        <div class="grafica">
            <h3>Asistencias por Empleado</h3>
            <div class="grafica-container">
                @if($chartAsistenciasPorEmpleado && file_exists($chartAsistenciasPorEmpleado))
                    <img class="chart-img" src="{{ $chartAsistenciasPorEmpleado }}" alt="Gráfica de asistencias por empleado">
                @else
                    <p class="no-data">No hay datos suficientes para mostrar esta gráfica</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="grafica-row">
    <div class="grafica-item">
        <div class="grafica">
            <h3>Distribución de Estados</h3>
            <div class="grafica-container">
                @if($chartResumenEstados && file_exists($chartResumenEstados))
                    <img class="chart-img" src="{{ $chartResumenEstados }}" alt="Gráfica de resumen de estados">
                @else
                    <p class="no-data">No hay datos suficientes para mostrar esta gráfica</p>
                @endif
            </div>
        </div>
    </div>
</div>

</body>
</html>
