<?php

namespace App\Tests\Support;

use App\Application\User\UserData;
use App\Application\User\UserImportStrategy;

final class FakeUserImportStrategy implements UserImportStrategy
{
    /**
     * @var UserData[]
     */
    private array $users;

    /**
     * @param UserData[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function fetchUsers(): array
    {
        return $this->users;
    }
}
