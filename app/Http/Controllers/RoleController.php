<?php

namespace App\Http\Controllers;

use App\DTO\RoleDTO;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\DTO\RolesCollectionDTO;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleController extends Controller
{   

    /**
     * Возвращает коллекцию всех ролей,в том числе мягко удаленных в формате JSON.
     *
     * @return JsonResponse
     */
    public function showRoleCollection() 
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'get-list-role')) 
            {
                $roles = Role::withTrashed()->get();
        
                $rolesCollectionDTO = new RolesCollectionDTO($roles);
                $rolesCollectionDTO = $rolesCollectionDTO->getFilteredRoles();

                return response()->json(['rolesCollection' => $rolesCollectionDTO], 200);
            }
        }
        abort(403,'get-list-role permission required');
        
    }


    /**
     * Возвращает экземпляр запрошенной по id роли в формате JSON.
     *
     * @return JsonResponse
     * @param int $id
     */
    public function showRole($id) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'read-role')) 
            {
                $role = Role::findOrFail($id);

                $roleDTO = RoleDTO::fromRole($role);
                $rolePermissions = RoleDTO::getPermissionsOfRole($role);
        
                return response()->json(['roleInfo' => $roleDTO->toArray(), 'rolePermissions' => $rolePermissions]);
            }
        }
        abort(403,'get-list-role permission required');
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
        $roleDTO = $request -> toRoleDTO();
        $roleDTO = $roleDTO -> toArray();

        $role = new Role();

        $role->name = $roleDTO["name"];
        $role->description = $roleDTO["description"];
        $role->cipher = $roleDTO["cipher"];
        $role->created_by = Auth::user()->id;

        $role->save();

        return response()->json(['Роль успешно создана' => $role], 201);
    
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
        $roleDTO = $request -> toRoleDTO();
        $roleDTO = $roleDTO -> toArray();

        $role = Role::find($id);

        $role->name = $roleDTO["name"];
        $role->description = $roleDTO["description"];
        $role->cipher = $roleDTO["cipher"];

        $role->update();

        return response()->json(['Роль успешно обновлена'=> $role], 200);
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
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'soft-delete-role')) 
            {
                $role = Role::findOrFail($id);
                $role->deleted_by = Auth::user()->id;
                $role->delete();
                $role->update();
                return response()->json(['Роль мягко удалена'=> $role], 200);
            }
        }
        abort(403,'soft-delete-role permission required');
    }


    /**
     * Жестко удаляет роль по id.
     *
     * @return JsonResponse
     * @param int $id 
     */
    public function forceDelete($id) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'delete-role')) 
            {
                $role = Role::withTrashed()->findOrFail($id);
                $role->forceDelete();
                return response()->json(['Роль полностью удалена'] , 200);
            }
        }
        abort(403,'delete-role permission required');
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
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'restore-role')) 
            {
                $role = Role::onlyTrashed()->findOrFail($id);
                $role->deleted_by = NULL;
                $role->update();
                $role->restore();

                return response()->json(['Роль была успешно восстановлена'=> $role], 200);
            }
        }

        abort(403,'restore-role permission required');
    }
}
