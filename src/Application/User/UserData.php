<?php

namespace App\Application\User;

final class UserData
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $username;
    public readonly string $email;

    public function __construct(string $id, string $name, string $username, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
    }
}
