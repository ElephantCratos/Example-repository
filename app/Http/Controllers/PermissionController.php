<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\DTO\PermissionDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function showPermission($id) : JsonResponse
    {
        
        $permission = Permission::findOrFail($id);

        $permissionDTO = PermissionDTO::fromPermission($permission);
        

        return response()->json(['permissionInfo' => $permissionDTO->toArray()]);
    }
   
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

    public function softDelete($id) : JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->deleted_by = Auth::user()->id;
        $permission->delete();
        $permission->update();
        return response()->json(['Разрешение мягко удалено'=> $permission], 200);
    }

    public function forceDelete($id) : JsonResponse
    {
        $permission = Permission::withTrashed()->findOrFail($id);
        $permission->forceDelete();
        return response()->json('Разрешение полностью удалено', 200);
    }


    public function restore($id) : JsonResponse
    {
      
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->deleted_by = NULL;
        $permission->update();

        $permission->restore();

        return response()->json(['Роль была успешно восстановлена'=> $permission], 200);
    }
}
