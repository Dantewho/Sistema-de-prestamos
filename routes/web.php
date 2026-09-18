<?php

use App\Http\Controllers\AulaController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::apiResource('edificios', EdificioController::class);
    Route::apiResource('aulas', AulaController::class);
    Route::apiResource('inventario', InventarioController::class);
    Route::apiResource('solicitudes', SolicitudController::class);
    Route::apiResource('perfiles', PerfilController::class)->only(['index', 'show', 'update']);
});


Route::get('/usuarios', function () {
    return view('crud.usuarios', compact('usuarios'));
})->name('usuarios.index');