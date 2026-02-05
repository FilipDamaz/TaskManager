<?php

namespace App\Tests\Support;

use App\Application\User\UserData;
use App\Application\User\UserImportStrategy;

final class FixedUserImportStrategy implements UserImportStrategy
{
    public function fetchUsers(): array
    {
        return [
            new UserData(1, 'Alice', 'alice', 'alice@example.com', '123', 'example.com'),
            new UserData(2, 'Bob', 'bob', 'bob@example.com', '456', 'example.org'),
        ];
    }
}
