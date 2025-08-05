<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hamburguesas Lidany's - Reporte de Ventas</title>
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
    </style>
</head>
<body>

<h1>Hamburguesas Lidany's</h1>
<h2>Reporte de Ventas</h2>
<p style="text-align: right;">
    Generado el: {{ $generadoEl }}
</p>

{{-- Tabla de ventas --}}
<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Área</th>
        <th>Turno</th>
        <th>Registró</th>
        <th>Monto</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($ventas as $venta)
        <tr>
            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
            <td>{{ $venta->area->nombre ?? '—' }}</td>
            <td>{{ $venta->turno->nombre ?? '—' }}</td>
            <td>{{ $venta->usuarioRegistro->nombre_completo ?? '—' }}</td>
            <td>${{ number_format($venta->monto, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Estadísticas --}}
<div class="resumen">
    <p>Total de ventas registradas: {{ $conteoVentas }}</p>
    <p>Monto total acumulado: ${{ number_format($montoTotal, 2) }}</p>
</div>

{{-- Gráficas Centradas --}}
<div class="grafica-row">
    <div class="grafica-item">
        <div class="grafica">
            <h3>Ventas por Día</h3>
            <div class="grafica-container">
                @if($chartVentasPorDia && file_exists($chartVentasPorDia))
                    <img class="chart-img" src="{{ $chartVentasPorDia }}" alt="Gráfica de ventas por día">
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
            <h3>Ventas por Turno</h3>
            <div class="grafica-container">
                @if($chartVentasPorTurno && file_exists($chartVentasPorTurno))
                    <img class="chart-img" src="{{ $chartVentasPorTurno }}" alt="Gráfica de ventas por turno">
                @else
                    <p class="no-data">No hay datos suficientes para mostrar esta gráfica</p>
                @endif
            </div>
        </div>
    </div>
</div>

</body>
</html>
