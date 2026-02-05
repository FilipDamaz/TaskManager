<?php

namespace App\Application\User;

interface UserImportStrategy
{
    /**
     * @return UserData[]
     */
    public function fetchUsers(): array;
}
