<?php

use App\Http\Controllers\API\KpiController;
use App\Http\Controllers\API\ReporteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PuestoController;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\EmpleadoController;
use App\Http\Controllers\API\TurnoController;
use App\Http\Controllers\API\VentaController;
use App\Http\Controllers\API\CategoriaBienController;
use App\Http\Controllers\API\UbicacionController;
use App\Http\Controllers\API\EstadoBienController;
use App\Http\Controllers\API\BienController;
use App\Http\Controllers\API\AsistenciaController;
use App\Http\Controllers\API\EvaluacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ruta de verificación de salud
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::get('/empleados/activos', [EmpleadoController::class, 'getActiveEmployees']);

Route::get('/reporte-ventas', [ReporteController::class, 'reporteVentas']);

Route::get('/reporte-asistencias', [ReporteController::class, 'reporteAsistencias']);

Route::get('/dashboard/kpis', [KpiController::class, 'getDashboardData']);


// Rutas protegidas
Route::middleware(['auth:sanctum'])->group(function () {
    // Usuarios

    Route::get('/users', [UserController::class, 'index']);

    Route::patch('/users/{user}', [UserController::class, 'update']);

    Route::put('/users/me', [UserController::class, 'updateSelf']); // Ruta para que el usuario edite sus datos
    Route::get('/me', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class)->except(['create', 'edit']);


    // Empleados
    Route::apiResource('empleados', EmpleadoController::class);
    Route::post('/empleados/{empleado}/toggle-status', [EmpleadoController::class, 'toggleStatus']);
    Route::get('/empleados/{empleado}/asistencias', [EmpleadoController::class, 'asistencias']);
    Route::get('/empleados/activos', [EmpleadoController::class, 'getActiveEmployees']);

    // Bienes
    Route::apiResource('bienes', BienController::class)->parameters([
        'bienes' => 'id'
    ]);

    Route::post('/bienes/{bien}/cambiar-estado', [BienController::class, 'cambiarEstado']);

    // Catálogos básicos
    Route::apiResource('puestos', PuestoController::class)->except(['create', 'edit']);
    Route::apiResource('areas', AreaController::class)->except(['create', 'edit']);
    Route::apiResource('turnos', TurnoController::class)->except(['create', 'edit']);
    Route::apiResource('categorias-bienes', CategoriaBienController::class)->except(['create', 'edit']);
    Route::apiResource('ubicaciones', UbicacionController::class)->except(['create', 'edit']);
    Route::apiResource('estados-bienes', EstadoBienController::class)->except(['create', 'edit']);

    // Operaciones
    Route::post('/ventas/check-existing', [VentaController::class, 'checkExisting']);
    Route::put('/ventas/{venta}/update-monto', [VentaController::class, 'updateVenta']);
    Route::get('/ventas/totales-por-dia', [VentaController::class, 'getTotalesPorDia']);
    Route::get('/ventas/por-dia', [VentaController::class, 'getVentasPorDia']);


    Route::apiResource('ventas', VentaController::class);
    Route::apiResource('asistencias', AsistenciaController::class);
    Route::apiResource('evaluaciones', EvaluacionController::class);

    // Rutas adicionales para reportes (puedes implementarlas después)
    Route::prefix('reportes')->group(function () {
        Route::get('/ventas', [VentaController::class, 'generarReporte']);
        Route::get('/asistencias', [AsistenciaController::class, 'generarReporte']);
    });
});

// Ruta fallback para endpoints no existentes
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Endpoint no encontrado'
    ], 404);
});
