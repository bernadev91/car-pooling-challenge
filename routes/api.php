<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('status', [\App\Http\Controllers\PoolController::class, 'status']);
Route::put('cars', [\App\Http\Controllers\PoolController::class, 'cars']);
Route::post('journey', [\App\Http\Controllers\PoolController::class, 'journey']);
Route::post('dropoff', [\App\Http\Controllers\PoolController::class, 'dropoff']);
Route::post('locate', [\App\Http\Controllers\PoolController::class, 'locate']);