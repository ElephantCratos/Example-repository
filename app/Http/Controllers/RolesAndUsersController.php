<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\UsersAndRoles;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;

use App\DTO\UserDTO;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Carbon\Carbon;

class RolesAndUsersController extends Controller
{
    /**
     * Проверяет наличие разрешения пользователя и в соответствии с ним
     * Возвращает список ролей пользователя по id/исключение.
     * 
     * 
     * @param int $roleId
     * @param int $permissionId
     * @return JsonResponse
     */
    public function showUserRoles($id)
    {
        $hasPermissionToViewOwnRoles = false;
        $hasPermissionToViewAnyRoles = false;

        $AuthorizedUserId = Auth::user()->id;
        $userRoles = Auth::user()->roles;


        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'read-role-self')) 
            {
                $hasPermissionToViewOwnRoles = true;
            }
            
            if ($roleUser->permissions->contains('name', 'read-role')) 
            {
                $hasPermissionToViewAnyRoles = true;
            }
        }
        
        if (($hasPermissionToViewOwnRoles && $AuthorizedUserId == $id) || $hasPermissionToViewAnyRoles){
            
            $user = User::find($id);
            $userDTO = UserDTO::userRoles($user);
            return response()->json(['userRoles' => $userDTO->toArray()]);
        }
        
        $AuthorizedUserId == $id ? abort(403, 'read-role-self permission required') : abort(403, 'read-role permission required');
    }


    /**
     * Привязывает роль к пользователю.
     *
     * @param int $id
     * @param  AssignRoleRequest 
     * @return JsonResponse
     */
    public function assignRoles(AssignRoleRequest $request, $id)
    {
        $data = $request->toRolesAndUsersDTO();
        $data = $data->toArray();
       
        $user = User::find($id);

      
        if ($user) {

            foreach ($data['rolesId'] as $role) {
                
                $roleFind = Role::find($role);

                if ($roleFind){
                    $user->roles()->syncWithoutDetaching([$roleFind->id => ['created_by' => Auth::user()->id]]);
                }

                else {
                    return response()->json('Роль не найдена', 404);
                }

            }

            return response()->json('Роль успешно назначена пользователю!', 200);

        }

        return response()->json('Пользователь не найден', 404);
    }
    

    /**
     * Отвязывает роль от пользователя (Жестко удаляет).
     *
     * @param int $userId
     * @param int $roleId
     * @return JsonResponse
     */
    public function detachRoleFromUser($userId,$roleId)
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'delete-roles-and-users')) 
            {
                $user = User::find($userId);
                
                $role = Role::withTrashed()->find($roleId);
            
                if ($user && $role) {
                    $user->roles()->detach($role->id);
                    return response()->json('Роль успешно отвязана от пользователя!', 200);
                }
            
                return response()->json('Пользователь или роль не найдены', 404);
            }
        }

        abort(403, 'delete-roles-and-users permission required');
    }


    /**
     * Мягко удаляет роль c пользователя.
     * 
     * @param int $userId
     * @param int $roleId
     * @return JsonResponse
     */
    public function softDelete($userId,$roleId)
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'soft-delete-roles-and-users')) 
            {
                $usersAndRoles = UsersAndRoles::where('user_id', $userId)
                ->where('role_id', $roleId);
                $usersAndRoles->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => Auth::user()->id,
                ]);
           
            return response()->json('Роль была мягко удалена для пользователя!', 200); 
            
            }
        }
        
        abort(403,'soft-delete-roles-and-users permission required');
        
    }


    /**
     * Восстанавливает мягко удаленную связь между ролью и пользователем.
     * 
     * @param int $userId
     * @param int $roleId
     * @return JsonResponse
     */
    public function restore($userId,$roleId)
    {

        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $roleUser)
        {
            if ($roleUser->permissions->contains('name', 'restore-roles-and-users')) 
            {
                $usersAndRoles = UsersAndRoles::withTrashed()->where('user_id', $userId)
                ->where('role_id', $roleId);

                $usersAndRoles->update([
                    'deleted_at' => null,
                    'deleted_by' => null,
                ]);
                
                return response()->json('Роль успешно восстановлена для пользователя', 200);
            }
        }

        abort(403,'restore-roles-and-users permission required');
    }


}
