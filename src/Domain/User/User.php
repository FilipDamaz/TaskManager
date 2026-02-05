<?php

namespace App\Domain\User;

final class User
{
    private UserId $id;
    private string $name;
    private string $username;
    private Email $email;

    private function __construct(UserId $id, string $name, string $username, Email $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
    }

    public static function create(UserId $id, string $name, string $username, Email $email): self
    {
        $name = trim($name);
        $username = trim($username);

        if ($name === '') {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        if ($username === '') {
            throw new \InvalidArgumentException('Username cannot be empty.');
        }

        return new self($id, $name, $username, $email);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
