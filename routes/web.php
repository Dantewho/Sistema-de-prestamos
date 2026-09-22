<?php

use App\Http\Controllers\AulaController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('prestamos.index')
        : redirect()->route('login');
});

Route::middleware('auth')->get('/prestamos', function () {
    return view('crud.index');
})->name('prestamos.index');

Route::middleware('auth')->get('/inventario', function () {
    return view('crud.inventario');
})->name('inventario.panel');

Route::middleware('auth')->get('/usuarios', function () {
    return view('crud.usuarios');
})->name('usuarios.index');

Route::middleware('auth')->prefix('api')->group(function () {
    Route::apiResource('edificios', EdificioController::class);
    Route::apiResource('aulas', AulaController::class);
    Route::apiResource('inventario', InventarioController::class);
    Route::apiResource('solicitudes', SolicitudController::class);
    Route::apiResource('perfiles', PerfilController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
});

Route::get('/perfiles', function () {
    return view('crud.usuarios', compact('perfiles'));
})->name('perfiles.index');
