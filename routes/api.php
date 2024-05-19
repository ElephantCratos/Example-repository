<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesAndUsersController;
use App\Http\Controllers\RolesAndPermissionsController;
use App\Http\Controllers\UserController;


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

Route::get('ref/user', [UserController::class,'showUsersList']);


Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/me', [UserController::class, 'me']);
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::post('/out_all', [AuthController::class, 'out_all']);

    });
    

    
});

Route::group(['middleware' => 'auth:sanctum'], function () {

Route::group(['prefix' => 'ref'], function () {
    
     
     Route::get('/user/{id}/role', [RolesAndUsersController::class,'showUserRoles']);
     Route::post('/user/{id}/role', [RolesAndUsersController::class,'assignRoles']);
     Route::delete('/user/{userId}/role/{roleId}', [RolesAndUsersController::class,'detachRoleFromUser']);
     Route::delete('/user/{userId}/role/{roleId}/soft', [RolesAndUsersController::class,'softDelete']);
     Route::post('/user/{userId}/role/{roleId}/restore', [RolesAndUsersController::class,'restore']);

       Route::group(['prefix' => 'policy'], function () {

                Route::get('/role',  [RoleController::class, 'showRoleCollection']);
                Route::get('/role/{id}',  [RoleController::class, 'showRole']);
                Route::post('/role',  [RoleController::class, 'create']);
                Route::put('/role/{id}',  [RoleController::class, 'update']);
                Route::delete('/role/{id}',  [RoleController::class, 'forceDelete']);
                Route::delete('/role/{id}/soft',  [RoleController::class,'softDelete']);
                Route::post ('role/{id}/restore',  [RoleController::class, 'restore']);

                Route::post('/role/{id}/permission', [RolesAndPermissionsController::class,'assignPermissions']);
                Route::delete('/role/{roleId}/permission/{permissionId}', [RolesAndPermissionsController::class,'detachPermissionFromRole']);
                Route::delete('/role/{roleId}/permission/{permissionId}/soft', [RolesAndPermissionsController::class,'softDelete']);
                Route::post('/role/{roleId}/permission/{permissionId}/restore', [RolesAndPermissionsController::class,'restore']);



                Route::get('/permission',  [PermissionController::class, 'showPermissionCollection']);
                Route::get('/permission/{id}',  [PermissionController::class, 'showPermission']);
                Route::post('/permission',  [PermissionController::class, 'create']);
                Route::put('/permission/{id}',  [PermissionController::class, 'update']);
                Route::delete('/permission/{id}',  [PermissionController::class, 'forceDelete']);
                Route::delete('/permission/{id}/soft',  [PermissionController::class,'softDelete']);
                Route::post ('permission/{id}/restore',  [PermissionController::class, 'restore']);

               
       });

   });
   
});
