<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolesAndPermissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionRequest;

use Carbon\Carbon;

class RolesAndPermissionsController extends Controller
{
    /**
     * Привязывает разрешения к роли.
     *
     * @param int $id
     * @param  AssignPermissionRequest 
     * @return JsonResponse
     */
    public function assignPermissions(AssignPermissionRequest $request, $id) : JsonResponse
    {
        
        $data = $request->toRolesAndPermissionsDTO();
        $data = $data->toArray();

        $role = Role::find($id);
        
        if ($role) {

            foreach ($data['permissionsId'] as $permission) {
                
            $permissionFind = Permission::find($permission);

                if ($permissionFind){
                    $role->permissions()->syncWithoutDetaching([$permissionFind->id => ['created_by' => Auth::user()->id]]);
                }

                else {
                    return response()->json('Разрешение не найдено', 404);
                }

            }
            
            return response()->json('Разрешения успешно назначены роли!', 200);

        }

        return response()->json('Роль не найдена не найден', 404);
    }
    
    
    /**
     * Отвязывает разрешение от роли (Жестко удаляет).
     *
     * @param int $roleId
     * @param int $permissionId
     * @return JsonResponse
     */
    public function detachPermissionFromRole($roleId,$permissionId) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'delete-roles-and-permissions')) 
            {
                $role = Role::withTrashed()->find($roleId);
                
                $permission = Permission::withTrashed()->find($permissionId);
            
                if ($role && $permission) {
                    $role->permissions()->detach($permission->id);
                    return response()->json('Разрешение успешно отвязано от роли!', 200);
                }
            
                return response()->json('Пользователь или роль не найдены', 404);
            }
        }
        abort(403, 'delete-roles-and-permissions permission required');
    }

    /**
     * Мягко удаляет разрешение c роли.
     * 
     * @param int $roleId
     * @param int $permissionId
     * @return JsonResponse
     */
    public function softDelete($roleId,$permissionId) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'soft-delete-roles-and-permissions')) 
            {
                $rolesAndPermissions = RolesAndPermissions::where('role_id', $roleId)
                ->where('permission_id', $permissionId);

                $rolesAndPermissions->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => Auth::user()->id,
                ]);
                    return response()->json('Разрешение была мягко удалена для роли!', 200);
            }
        }
        abort(403, 'soft-delete-roles-and-permissions permission required');
    }

    
    /**
     * Восстанавливает мягко удаленную связь между ролью и разрешением.
     * 
     * @param int $roleId
     * @param int $permissionId
     * @return JsonResponse
     */
    public function restore($roleId,$permissionId) : JsonResponse
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'restore-roles-and-permissions')) 
            {

                $rolesAndPermissions = RolesAndPermissions::withTrashed()->where('role_id', $roleId)
                ->where('permission_id', $permissionId);

                $rolesAndPermissions->update([
                    'deleted_at' => null,
                    'deleted_by' => null,
                ]);

                return response()->json('Разрешение успешно восстановлена для роли', 200);
            }
        }
        abort(403, 'restore-roles-and-permissions permission required');
    }
}
