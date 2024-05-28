<?php

namespace App\Http\Controllers;

use App\DTO\RoleDTO;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\DTO\RolesCollectionDTO;

use App\DTO\ChangeLogDTO;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Carbon\Carbon;

class RoleController extends Controller
{   

    /**
     * Возвращает коллекцию всех ролей,в том числе мягко удаленных в формате JSON.
     *
     * @return JsonResponse
     */
    public function showRoleCollection() 
    {
        $user = Auth::user();
        $requiredPermission = 'get-list-role';

        if(isPermissionExistForUser($user, $requiredPermission))
        {
            $roles = Role::withTrashed()->get();
        
            $rolesCollectionDTO = new RolesCollectionDTO($roles);
            $rolesCollectionDTO = $rolesCollectionDTO->getFilteredRoles();

            return response()->json(['rolesCollection' => $rolesCollectionDTO], 200);
            
        }

        abort(403, $requiredPermission . ' permission required  ');
        
    }


    /**
     * Возвращает экземпляр запрошенной по id роли в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function showRole($id) : JsonResponse
    {
        $user = Auth::user();
        $requiredPermission = 'read-role';

        if(isPermissionExistForUser($user, $requiredPermission))
        {
            $role = Role::findOrFail($id);

            $roleDTO = RoleDTO::fromRole($role);
            $rolePermissions = RoleDTO::getPermissionsOfRole($role);
        
            return response()->json(['roleInfo' => $roleDTO->toArray(), 'rolePermissions' => $rolePermissions]);
            
        }

        abort(403, $requiredPermission . ' permission required  ');
    }
    

    /**
     * Создает новую роль. 
     * Возвращает экземпляр созданной роли в формате JSON.
     *
     * @return JsonResponse
     * @param  CreateRoleRequest
     */
    public function create(CreateRoleRequest $request) : JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = Auth::user();
            $requiredPermission = 'create-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $roleDTO = $request -> toRoleDTO();
                $roleDTO = $roleDTO -> toArray();

                $role = new Role();

                

                $role->name = $roleDTO["name"];
                $role->description = $roleDTO["description"];
                $role->cipher = $roleDTO["cipher"];
                $role->created_by = Auth::user()->id;

                $role->save();


                $roleNewValue = json_encode($role);
                $model = get_class($role);

                $changeLog = new ChangeLogDTO($model, $role->id, json_encode(null), $roleNewValue, Carbon::now(), Auth::user()->id);
                ChangeLogController::createLog($changeLog);

                return response()->json(['Роль успешно создана' => $role], 201);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


    /**
     * Обновляет модель запрошенной по id роли.
     * Возвращает обновленный экземпляр запрошенной роли в формате JSON.
     *
     * @return JsonResponse
     * @param UpdateRoleRequest
     * @param int $id 
     */
    public function update(UpdateRoleRequest $request, $id) : JsonResponse
    {
        return DB::transaction(function () use ($id, $request) {
            $user = Auth::user();
            $requiredPermission = 'update-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                $roleDTO = $request -> toRoleDTO();
                $roleDTO = $roleDTO -> toArray();
                
                $role = Role::find($id);
                
                DB::transaction(function () 
                use ($role, $roleDTO, $id) {

                    $roleOldValue = json_encode($role);

                    $role->name = $roleDTO["name"];
                    $role->description = $roleDTO["description"];
                    $role->cipher = $roleDTO["cipher"];
                    $role->update();


                    $roleNewValue = json_encode($role);
                    $model = get_class($role);

                    
                    $changeLog = new ChangeLogDTO($model, $id, $roleOldValue, $roleNewValue, Carbon::now(), Auth::user()->id);
                    ChangeLogController::createLog($changeLog);

                },5);
                
                return response()->json(['Роль успешно обновлена'=> $role], 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


     /**
     * Мягко удаляет роль по id.
     * Возвращает обновленный экземпляр мягко удаленной роли в формате JSON.
     *
     * @return JsonResponse
     * @param $id
     */
    public function softDelete($id) : JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'soft-delete-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                    $role = Role::findOrFail($id);
                    $roleOldValue = json_encode($role);

                    $role->deleted_by = Auth::user()->id;
                    $role->delete();
                    $role->update();

                    $roleNewValue = json_encode($role);
                    $model = get_class($role);
                    
                    $changeLog = new ChangeLogDTO($model, $id, $roleOldValue, $roleNewValue, Carbon::now(), Auth::user()->id);
                    ChangeLogController::createLog($changeLog);

                    return response()->json(['Роль мягко удалена'=> $role], 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


    /**
     * Жестко удаляет роль по id.
     *
     * @return JsonResponse
     * @param int $id 
     */
    public function forceDelete($id) : JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'delete-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                    $role = Role::withTrashed()->findOrFail($id);

                    $roleOldValue = json_encode($role);
                    $model = get_class($role);

                    $role->forceDelete();

                    $changeLog = new ChangeLogDTO($model, $id, $roleOldValue, json_encode(null), Carbon::now(), Auth::user()->id);
                    ChangeLogController::createLog($changeLog);

                    return response()->json(['Роль полностью удалена'] , 200);
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }


    /**
     * Восстанавливает мягко удаленную роль по id.
     * Возвращает обновленный экземпляр восстановленной роли в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function restore($id) : JsonResponse
    { 
        return DB::transaction(function () use ($id) {
            $user = Auth::user();
            $requiredPermission = 'restore-role';

            if(isPermissionExistForUser($user, $requiredPermission))
            {
                    $role = Role::onlyTrashed()->findOrFail($id);

                    $roleOldValue = json_encode($role);
                    $model = get_class($role);

                    $role->deleted_by = NULL;
                    $role->update();
                    $role->restore();

                    $roleNewValue = json_encode($role);

                    $changeLog = new ChangeLogDTO($model, $id, $roleOldValue, $roleNewValue, Carbon::now(), Auth::user()->id);
                    ChangeLogController::createLog($changeLog);


                    return response()->json(['Роль была успешно восстановлена'=> $role], 200);
                
            }

            abort(403, $requiredPermission . ' permission required  ');
        },5);
    }
}
