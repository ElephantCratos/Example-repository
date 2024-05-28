<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Controllers\Controller;

use App\DTO\PermissionDTO;
use App\DTO\PermissionsCollectionDTO;
use App\DTO\ChangeLogDTO;

use Illuminate\Support\Facades\DB;
use App\Models\Permission;

use Carbon\Carbon;


class PermissionController extends Controller
{
    /**
     * Возвращает коллекцию всех разрешений,в том числе мягко удаленных в формате JSON.
     *
     * @return JsonResponse
     */
    public function showPermissionCollection() : JsonResponse
    {
        $user = Auth::user();
        $requiredPermission = 'get-list-permission';

        if(isPermissionExistForUser($user, $requiredPermission))
        {
            $permissions = Permission::withTrashed()->get();
        
            $permissionsCollectionDTO = new PermissionsCollectionDTO($permissions);
            $permissionsCollectionDTO = $permissionsCollectionDTO->getFilteredPermissions();

            return response()->json(['permissionsCollection' => $permissionsCollectionDTO], 200);
        }

        abort(403, $requiredPermission . ' permission required  ');
        
    }


    /**
     * Возвращает экземпляр запрошенного по id разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function showPermission($id) : JsonResponse
    {
            $user = Auth::user();
            $requiredPermission = 'read-permission';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permission = Permission::find($id);
                $permissionDTO = PermissionDTO::fromPermission($permission);
                    
                return response()->json(['permissionInfo' => $permissionDTO->toArray()]);
            }

            abort(403, $requiredPermission . ' permission required  ');
    }
   

    /**
     * Создает новое разрешение. 
     * Возвращает экземпляр созданного разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param CreatePermissionRequest
     */
    public function create(CreatePermissionRequest $request) : JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = Auth::user();
            $requiredPermission = 'create-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permissionDTO = $request -> toPermissionDTO();
                $permissionDTO = $permissionDTO -> toArray();

                $permission = new Permission();



                $permission->name = $permissionDTO["name"];
                $permission->description = $permissionDTO["description"];
                $permission->cipher = $permissionDTO["cipher"];
                $permission->created_by = Auth::user()->id;

                $permission->save();

                $permissionNewValue = json_encode($permission);
                $model = get_class($permission);

                $changeLog = new ChangeLogDTO($model, $permission->id, json_encode(null), $permissionNewValue, Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                return response()->json(['Разрешение успешно создано'=> $permission], 201);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }

    /**
     * Обновляет модель запрошенного по id разрешения.
     * Возвращает обновленный экземпляр запрошенного разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param UpdatePermissionRequest
     * @param int $id 
     */
    public function update(UpdatePermissionRequest $request, $id) : JsonResponse
    {
        return DB::transaction(function () use ($id, $request) {
            $user = Auth::user();
            $requiredPermission = 'update-permission';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permissionDTO = $request -> toPermissionDTO();
                $permissionDTO = $permissionDTO -> toArray();

                $permission = Permission::find($id);

                $permissionOldValue = json_encode($permission);

                $permission->name = $permissionDTO["name"];
                $permission->description = $permissionDTO["description"];
                $permission->cipher = $permissionDTO["cipher"];
                $permission->update();

                $permissionNewValue = json_encode($permission);
                $model = get_class($permission);

                $changeLog = new ChangeLogDTO($model, $id, $permissionOldValue, $permissionNewValue, Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                return response()->json(['Разрешение успешно обновлено'=> $permission], 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


     /**
     * Мягко удаляет разрешение по id.
     * Возвращает обновленный экземпляр мягко удаленного разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function softDelete($id) : JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'soft-delete-permission';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permission = Permission::findOrFail($id);
                $permissionOldValue = json_encode($permission);

                $permission->deleted_by = Auth::user()->id;
                $permission->delete();
                $permission->update();

                $permissionNewValue = json_encode($permission);
                $model = get_class($permission);

                $changeLog = new ChangeLogDTO($model, $id, $permissionOldValue, $permissionNewValue, Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                return response()->json(['Разрешение мягко удалено'=> $permission], 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


    /**
     * Жестко удаляет разрешение по id.
     *
     * @return JsonResponse
     * @param int $id 
     */
    public function forceDelete($id) : JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'delete-permission';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permission = Permission::withTrashed()->findOrFail($id);

                $permissionOldValue = json_encode($permission);
                $model = get_class($permission);

                $changeLog = new ChangeLogDTO($model, $id, $permissionOldValue, json_encode(null), Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                $permission->forceDelete();
                return response()->json('Разрешение полностью удалено', 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


    /**
     * Восстанавливает мягко удаленное разрешение по id.
     * Возвращает обновленный экземпляр мягко удаленного разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function restore($id) : JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'restore-permission';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $permission = Permission::onlyTrashed()->findOrFail($id);

                $permissionOldValue = json_encode($permission);
                $model = get_class($permission);

                $permission->deleted_by = NULL;
                $permission->update();
                $permission->restore();

                $permissionNewValue = json_encode($permission);

                $changeLog = new ChangeLogDTO($model, $id, $permissionOldValue, $permissionNewValue, Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                return response()->json(['Роль была успешно восстановлена'=> $permission], 200);
                
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }
}
