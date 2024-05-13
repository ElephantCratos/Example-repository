<?php

namespace App\DTO;


class RoleDTO
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

    

    public function toArray() : array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'cipher'      => $this->cipher,
        ];
    }
}