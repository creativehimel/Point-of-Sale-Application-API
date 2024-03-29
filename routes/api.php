<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BrandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::controller(BrandController::class)->group(function(){
        Route::get('/brands', 'index');
        Route::post('/brands', 'store');
        Route::get('/brands/{id}/edit', 'edit');
        Route::put('/brands/{id}', 'update');
        Route::delete('/brands/{id}', 'destroy');
    });
});

