<?php

namespace App\DTO;


class RolesAndPermissionsDTO
{

    private array $permissionsId;

    public function __construct(array $permissionsId)
    {
        $this->permissionsId = $permissionsId;
    }


    public function toArray() : array
    {
        return [
            'permissionsId' => $this->permissionsId,
        ];
    }

}