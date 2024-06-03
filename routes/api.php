<?php

use App\Http\Controllers\TwoFactorAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesAndUsersController;
use App\Http\Controllers\RolesAndPermissionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\ServerUpdateController;
use App\Http\Controllers\ServerRequestLogController;
use App\Http\Controllers\ReportController;

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


Route::post('testRoute', [ReportController::class,'addGenerateReportWork']);
Route::post('/hooks/git', [ServerUpdateController::class,'updateServerToMainBranch']);
Route::get('ref/user', [UserController::class,'showUsersList']);


Route::post('auth/code', [TwoFactorAuthController::class, 'sendEmailVerification']) -> middleware('auth:sanctum','abilities:confirm-2auth');
Route::post('auth/2fa', [AuthController::class,'confirmTwoAuth']) -> middleware('auth:sanctum','abilities:confirm-2auth');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

    Route::group(['middleware' => (['auth:sanctum', 'abilities:use-website'])], function () {
        Route::get('/me', [UserController::class, 'me']);
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::post('/out_all', [AuthController::class, 'out_all']);

    });
    

    
});

Route::group(['middleware' => (['auth:sanctum', 'abilities:use-website'])], function () {

Route::group(['prefix' => 'ref'], function () {
    
    
    //Route::get('/user/{id}/story', [RolesAndUsersController::class,'showUserRoles']);
     Route::get('/user/{id}/role', [RolesAndUsersController::class,'showUserRoles']);
     Route::post('/user/{id}/role', [RolesAndUsersController::class,'assignRoles']);
     Route::delete('/user/{userId}/role/{roleId}', [RolesAndUsersController::class,'detachRoleFromUser']);
     Route::delete('/user/{userId}/role/{roleId}/soft', [RolesAndUsersController::class,'softDelete']);
     Route::post('/user/{userId}/role/{roleId}/restore', [RolesAndUsersController::class,'restore']);

       Route::group(['prefix' => 'policy'], function () {

                Route::get('/role',  [RoleController::class, 'showRoleCollection']);
                Route::get('/role/{id}',  [RoleController::class, 'showRole']);
                Route::get('/role/{id}/story',  [ChangeLogController::class, 'showRoleLogs']);
                Route::post('/role',  [RoleController::class, 'create']);
                Route::put('/role/{id}',  [RoleController::class, 'update']);
                Route::delete('/role/{id}',  [RoleController::class, 'forceDelete']);
                Route::delete('/role/{id}/soft',  [RoleController::class,'softDelete']);
                Route::post ('role/{id}/restore',  [RoleController::class, 'restore']);
                

                Route::post('/role/{id}/permission', [RolesAndPermissionsController::class,'assignPermissions']);
                Route::delete('/role/{roleId}/permission/{permissionId}', [RolesAndPermissionsController::class,'detachPermissionFromRole']);
                Route::delete('/role/{roleId}/permission/{permissionId}/soft', [RolesAndPermissionsController::class,'softDelete']);
                Route::post('/role/{roleId}/permission/{permissionId}/restore', [RolesAndPermissionsController::class,'restore']);

                Route::post('/restoreFromChangelog/{id}', [ChangeLogController::class, 'restoreEntityFromLog']);
                Route::post('/restoreFromChangelog/{id}/extra', [ChangeLogController::class, 'restoreEntityFromLogExtraVariable']);

                Route::get('/permission',  [PermissionController::class, 'showPermissionCollection']);
                Route::get('/permission/{id}',  [PermissionController::class, 'showPermission']);
                Route::get('/permission/{id}/story',  [ChangeLogController::class, 'showPermissionLogs']);
                Route::post('/permission',  [PermissionController::class, 'create']);
                Route::put('/permission/{id}',  [PermissionController::class, 'update']);
                Route::delete('/permission/{id}',  [PermissionController::class, 'forceDelete']);
                Route::delete('/permission/{id}/soft',  [PermissionController::class,'softDelete']);
                Route::post ('permission/{id}/restore',  [PermissionController::class, 'restore']);       
       });

        Route::group(['prefix'=> 'log'], function () {
                Route::get('request', [ServerRequestLogController::class, 'showLogCollection']);
                Route::get('request/{id}', [ServerRequestLogController::class, 'showServerRequestLog']);
                Route::delete('request/{id}', [ServerRequestLogController::class, 'deleteServerRequestLog']);
       
        });

   });
   
});


