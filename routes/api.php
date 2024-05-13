<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;


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
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::post('/out_all', [AuthController::class, 'out_all']);

    });

    
});

Route::group(['middleware' => 'auth:sanctum'], function () {

Route::group(['prefix' => 'ref'], function () {
    
    //   Route::get('/user',  [, 'getUsersList']);
     //  Route::get('/user/{id}',  [, 'getUsersList']);
     //  Route::post('/user/role',  [, 'getUsersList']);
     //  Route::put('/user/role/{id}',  [, 'getUsersList']);
     //  Route::delete('/user/role/{id}',  [, 'getUsersList']);
     //  Route::delete('/user/role/{id}/soft',  [, 'getUsersList']);

       Route::group(['prefix' => 'policy'], function () {

            //   Route::get('/role',  [, 'getUsersList']);
                Route::get('/role/{id}',  [RoleController::class, 'showRole']);
                Route::post('/role',  [RoleController::class, 'create']);
                Route::put('/role/{id}',  [RoleController::class, 'update']);
                Route::delete('/role/{id}',  [RoleController::class, 'forceDelete']);
                Route::delete('/role/{id}/soft',  [RoleController::class,'softDelete']);
                Route::post ('role/{id}/restore',  [RoleController::class, 'restore']);


            //    Route::get('/permission',  [, 'getUsersList']);
                Route::get('/permission/{id}',  [PermissionController::class, 'showPermission']);
                Route::post('/permission',  [PermissionController::class, 'create']);
                Route::put('/permission/{id}',  [PermissionController::class, 'update']);
                Route::delete('/permission/{id}',  [PermissionController::class, 'forceDelete']);
                Route::delete('/permission/{id}/soft',  [PermissionController::class,'softDelete']);
                Route::post ('permission/{id}/restore',  [PermissionController::class, 'restore']);

               
       });

   });
   
});
