<?php

use App\Http\Controllers\Admin\{AuthController};
use Illuminate\Http\Request;

Route::post('/login', [AuthController::class, 'loginApi']);
// Para cerrar sesión, exige token válido
Route::post('/logout', [AuthController::class, 'logoutApi'])->middleware('auth:sanctum');
// Grupo de rutas que requieren estar logueado con token
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', function (Request $request) {
        return $request->user(); // usuario autenticado por Sanctum
    });

});
