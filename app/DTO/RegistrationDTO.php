<?php

namespace App\DTO;

class RegistrationDTO
{
    private string $username;
    private string $email;
    private string $password;
    private string $cPassword;
    private string $birthday;

    public function __construct(string $username, string $email, string $password, string $cPassword, string $birthday)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->cPassword = $cPassword;
        $this->birthday = $birthday;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCPassword(): string
    {
        return $this->cPassword;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }

    public function toArray() : array
    {
     return [
           'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'cPassword' => $this->cPassword,
            'birthday' => $this->birthday
     ];
    }


}
