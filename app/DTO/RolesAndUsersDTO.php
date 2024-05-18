<?php

namespace App\DTO;


class RolesAndUsersDTO
{
    private array $rolesId ;

    public function __construct(array $rolesId)
    {
        $this->rolesId = $rolesId;
    }


    public function toArray() : array
    {
        return [
            'rolesId' => $this->rolesId,
        ];
    }
    
}