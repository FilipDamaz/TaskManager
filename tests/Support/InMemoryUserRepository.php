<?php

namespace App\Tests\Support;

use App\Domain\User\Email;
use App\Domain\User\ExternalUserId;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array<string, User>
     */
    private array $items = [];

    public function save(User $user): void
    {
        $this->items[$user->id()->toString()] = $user;
    }

    public function findById(UserId $id): ?User
    {
        return $this->items[$id->toString()] ?? null;
    }

    public function findByEmail(Email $email): ?User
    {
        foreach ($this->items as $user) {
            if ($user->email()->equals($email)) {
                return $user;
            }
        }

        return null;
    }

    public function findByExternalId(ExternalUserId $externalId): ?User
    {
        foreach ($this->items as $user) {
            if ($user->externalId()->equals($externalId)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return array<int, User>
     */
    public function all(): array
    {
        return array_values($this->items);
    }
}
