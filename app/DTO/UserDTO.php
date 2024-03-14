<?php

namespace App\DTO;

class UserDTO
{
    private $username;
    private $email;
    private $birthday;

    public function __construct($username, $email, $birthday)
    {
        $this->username = $username;
        $this->email = $email;
        $this->birthday = $birthday;
    }


    public static function fromUser($user)
    {
        return new self($user->username, $user->email, $user->birthday);
    }

    public function toArray() : array
    {
        return[
            'username' => $this -> username,
            'email' => $this -> email,
            'birthday' => $this -> birthday,
        ];
    }
}
