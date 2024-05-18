<?php

namespace App\DTO;


class UsersCollectionDTO
{
    private  $users;

   



    public function __construct($users)
    {
        $this->users = $users;
        
    }

    


    public function getFilteredUsers()
    {
        return $this->users->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'birthday' => $user->birthday,
            ];
        });
    }
}