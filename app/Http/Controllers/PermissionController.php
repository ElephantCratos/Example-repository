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

use App\Models\Permission;


class PermissionController extends Controller
{
    /**
     * Возвращает коллекцию всех разрешений,в том числе мягко удаленных в формате JSON.
     *
     * @return JsonResponse
     */
    public function showPermissionCollection() : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'get-list-permission')) 
            {
                $permissions = Permission::withTrashed()->get();
        
                $permissionsCollectionDTO = new PermissionsCollectionDTO($permissions);
                $permissionsCollectionDTO = $permissionsCollectionDTO->getFilteredPermissions();

                return response()->json(['permissionsCollection' => $permissionsCollectionDTO], 200);
            }
        }
        abort(403,'get-list-permission permission required');
        
    }


    /**
     * Возвращает экземпляр запрошенного по id разрешения в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function showPermission($id) : JsonResponse
    {
        
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'read-permission')) 
            {    
                $permission = Permission::find($id);
                $permissionDTO = PermissionDTO::fromPermission($permission);
                
                return response()->json(['permissionInfo' => $permissionDTO->toArray()]);
            }
        }
        abort(403,'read-permission permission required');
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
        $permissionDTO = $request -> toPermissionDTO();
        $permissionDTO = $permissionDTO -> toArray();

        $permission = new Permission();

        $permission->name = $permissionDTO["name"];
        $permission->description = $permissionDTO["description"];
        $permission->cipher = $permissionDTO["cipher"];
        $permission->created_by = Auth::user()->id;

        $permission->save();

        return response()->json(['Разрешение успешно создано'=> $permission], 201);
    
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
        $permissionDTO = $request -> toPermissionDTO();
        $permissionDTO = $permissionDTO -> toArray();

        $permission = Permission::find($id);

        $permission->name = $permissionDTO["name"];
        $permission->description = $permissionDTO["description"];
        $permission->cipher = $permissionDTO["cipher"];

        $permission->update();

        return response()->json(['Разрешение успешно обновлено'=> $permission], 200);
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
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'soft-delete-permission')) 
            {
                $permission = Permission::findOrFail($id);
                $permission->deleted_by = Auth::user()->id;
                $permission->delete();
                $permission->update();
                return response()->json(['Разрешение мягко удалено'=> $permission], 200);
            }
        }
        abort(403,'soft-delete-permission permission required');
    }


    /**
     * Жестко удаляет разрешение по id.
     *
     * @return JsonResponse
     * @param int $id 
     */
    public function forceDelete($id) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'delete-permission')) 
            {
                $permission = Permission::withTrashed()->findOrFail($id);
                $permission->forceDelete();
                return response()->json('Разрешение полностью удалено', 200);
            }
        }
        abort(403, 'delete-permission permission required');
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
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'restore-permission')) 
            {
                $permission = Permission::onlyTrashed()->findOrFail($id);
                $permission->deleted_by = NULL;
                $permission->update();

                $permission->restore();

                return response()->json(['Роль была успешно восстановлена'=> $permission], 200);
            }
        }
        abort(403, 'restore-permission permission required');
    }
}
