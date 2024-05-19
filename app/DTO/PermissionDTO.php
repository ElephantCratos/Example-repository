<?php

namespace App\DTO;


class PermissionDTO
{
    private string $name;

    private string $description;

    private string $cipher;



    public function __construct(string $name, string $description, string $cipher)
    {
        $this->name = $name;
        $this->description = $description;
        $this->cipher = $cipher;
    }


    public static function fromPermission($permission) : PermissionDTO
    {
        return new self($permission->name, $permission->description, $permission->cipher);
    }

    

    public function toArray() : array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'cipher'      => $this->cipher,
        ];
    }
}