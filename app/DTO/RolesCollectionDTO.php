<?php

namespace App\DTO;


class RolesCollectionDTO
{
    private  $roles;

   



    public function __construct($roles)
    {
        $this->roles = $roles;
        
    }

    


    public function getFilteredRoles()
    {
        return $this->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'deleted_at' => ($role->deleted_at!=null) ? 'Soft Deleted at ' . $role->deleted_at : 'Not Deleted',
            ];
        });
    }
}