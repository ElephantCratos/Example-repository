<?php

namespace App\DTO;


class PermissionsCollectionDTO
{
    private  $permissions;

   



    public function __construct($permissions)
    {
        $this->permissions = $permissions;
        
    }

    


    public function getFilteredPermissions()
    {
        return $this->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
                'deleted_at' => ($permission->deleted_at!=null) ? 'Soft Deleted at ' . $permission->deleted_at : 'Not Deleted',
            ];
        });
    }
}