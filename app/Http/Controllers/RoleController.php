<?php

namespace App\Http\Controllers;

use App\DTO\RoleDTO;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleController extends Controller
{   
    public function showRoleCollection() 
    {
        //
    }


    /**
     * Возвращает экземпляр запрошенной по id роли в формате JSON.
     *
     * @return JsonResponse
     * @param $id
     */
    public function showRole($id) : JsonResponse
    {
        
        $role = Role::findOrFail($id);

        $roleDTO = RoleDTO::fromRole($role);
        

        return response()->json(['roleInfo' => $roleDTO->toArray()]);
    }
    

    /**
     * Возвращает экземпляр запрошенного по id роли в формате JSON.
     *
     * @return JsonResponse
     * @param CreateRoleRequest
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
     * Возвращает обновленный экземпляр запрошенного роли в формате JSON.
     *
     * @return JsonResponse
     * @param UpdateRoleRequest
     * @param $id
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
        $role = Role::findOrFail($id);
        $role->deleted_by = Auth::user()->id;
        $role->delete();
        $role->update();
        return response()->json(['Роль мягко удалена'=> $role], 200);
    }


    /**
     * Жестко удаляет роль по id.
     * Возвращает обновленный экземпляр жестко удаленной роли в формате JSON.
     *
     * @return JsonResponse
     * @param $id
     */
    public function forceDelete($id) : JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->forceDelete();
        return response()->json(['Роль полностью удалена'] , 200);
    }


    /**
     * Восстанавливает мягко удаленную роль по id.
     * Возвращает обновленный экземпляр мягко удаленной роли в формате JSON.
     *
     * @return JsonResponse
     * @param $id
     */
    public function restore($id) : JsonResponse
    {
      
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->deleted_by = NULL;
        $role->update();

        $role->restore();

        return response()->json(['Роль была успешно восстановлена'=> $role], 200);
    }
}
