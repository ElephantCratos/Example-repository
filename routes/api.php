<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('LogIn');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('Registration');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::post('/out_all', [AuthController::class, 'out_all']);
    });
});
