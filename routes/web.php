<?php

use App\Http\Controllers\AulaController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/aulas/exportar', [AulaController::class, 'exportar'])
        ->name('aulas.exportar');

    Route::get('/perfiles/exportar', [PerfilController::class, 'exportar'])
        ->name('perfiles.exportar');

    Route::post('/perfiles/importar', [PerfilController::class, 'importar'])
        ->name('perfiles.importar');

    Route::get('/solicitudes/exportar', [SolicitudController::class, 'exportar'])
        ->name('SolicitudExport.exportar');
    
    Route::post('/solicitudes/importar', [SolicitudController::class, 'importar'])
        ->name('SolicitudImport.importar');


    Route::get('/inventario/exportar', [InventarioController::class, 'exportar'])
        ->name('inventario.exportar');
    
    Route::post('/inventario/importar', [InventarioController::class, 'importar'])
        ->name('inventario.importar');

    Route::get('/', fn () => redirect()->route('prestamos.index'));

    Route::get('/prestamos', function () {
        return view('crud.index');
    })->name('prestamos.index');

    Route::get('/inventario', function () {
        return view('crud.inventario');
    })->name('inventario.panel');

    Route::get('/usuarios', function () {
        return view('crud.usuarios');
    })->name('usuarios.index');

    Route::prefix('api')->group(function () {
        Route::post('solicitudes/{solicitud}/finalizar', [SolicitudController::class, 'finalizar']);
        Route::apiResource('edificios', EdificioController::class);
        Route::apiResource('aulas', AulaController::class);
        Route::apiResource('inventario', InventarioController::class);
        Route::apiResource('solicitudes', SolicitudController::class)
            ->parameters(['solicitudes' => 'solicitud']);
        Route::apiResource('perfiles', PerfilController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy'])
            ->parameters(['perfiles' => 'perfil']);
    });

    Route::get('/perfiles', fn () => redirect()->route('usuarios.index'))
        ->name('perfiles.index');
});
