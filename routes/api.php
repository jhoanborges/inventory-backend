<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoteController;
use App\Http\Controllers\Api\MovimientoController;
use App\Http\Controllers\Api\OperacionController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\UbicacionController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::apiResource('productos', ProductoController::class);
    Route::post('/productos/sync', [ProductoController::class, 'sync']);
    Route::post('/productos/import-csv', [ProductoController::class, 'importCsv']);

    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('rutas', RutaController::class);
    Route::post('/rutas/{ruta}/iniciar', [RutaController::class, 'iniciar']);
    Route::post('/rutas/{ruta}/pausar', [RutaController::class, 'pausar']);
    Route::post('/rutas/{ruta}/finalizar', [RutaController::class, 'finalizar']);

    Route::post('/ubicacion', [UbicacionController::class, 'store']);
    Route::get('/ubicaciones', [UbicacionController::class, 'index']);

    Route::get('/movimientos', [MovimientoController::class, 'index']);
    Route::post('/movimientos', [MovimientoController::class, 'store']);

    Route::post('/scan/verify-stock', [ScanController::class, 'verifyStock']);
    Route::get('/scan/{barcode}', [ScanController::class, 'scan']);

    Route::get('/operaciones', [OperacionController::class, 'index']);
    Route::post('/operaciones', [OperacionController::class, 'store']);
    Route::get('/operaciones/{operacion}', [OperacionController::class, 'show']);
});
