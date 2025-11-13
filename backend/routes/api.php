<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ConstanciaController;

// =========================
// AUTH
// =========================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/validate-token', [AuthController::class, 'validateToken'])->middleware('auth:sanctum');
});

// =========================
// RUTAS PROTEGIDAS
// =========================
Route::middleware(['auth:sanctum'])->group(function () {

    // USERS
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create');
        Route::put('editar/{user}', [UserController::class, 'update'])->middleware('permission:users.edit');
        Route::put('activar/{user}', [UserController::class, 'activar'])->middleware('permission:users.edit');
        Route::put('desactivar/{user}', [UserController::class, 'desactivar'])->middleware('permission:users.delete');
    });

    // ROLES
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create');
        Route::put('/{rol}', [RoleController::class, 'update'])->middleware('permission:roles.edit');
        Route::delete('/{rol}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete');
    });

    // CONSTANCIAS
    Route::prefix('constancias')->group(function () {
        Route::get('/', [ConstanciaController::class, 'index'])->middleware('permission:constancias.view');
        Route::post('/', [ConstanciaController::class, 'store'])->middleware('permission:constancias.create');
        Route::put('editar/{constancia}', [ConstanciaController::class, 'update'])->middleware('permission:constancia.edit');
        Route::get('pdf/{id}', [ConstanciaController::class, 'generarPDF'])->middleware('permission:constancias.view');
    });

    // CONDUCTORES
    Route::prefix('conductores')->group(function () {
        Route::get('/', [ConductorController::class, 'index'])->middleware('permission:conductores.view');
        Route::post('/', [ConductorController::class, 'store'])->middleware('permission:conductores.create');
        Route::put('editar/{conductor}', [ConductorController::class, 'update'])->middleware('permission:conductores.edit');
        Route::put('activar/{conductor}', [ConductorController::class, 'activar'])->middleware('permission:conductores.edit');
        Route::put('desactivar/{conductor}', [ConductorController::class, 'desactivar'])->middleware('permission:conductores.delete');
    });

});