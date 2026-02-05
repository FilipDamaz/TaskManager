<?php

namespace App\Domain\User;

final class UserFactory
{
    public function create(string $id, string $name, string $username, string $email): User
    {
        return User::create(
            UserId::fromString($id),
            $name,
            $username,
            Email::fromString($email)
        );
    }
}
