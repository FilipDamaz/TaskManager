<?php

namespace App\Domain\User;

interface UserRepository
{
    public function save(User $user): void;

    public function findById(UserId $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function findByExternalId(ExternalUserId $externalId): ?User;

    /**
     * @return User[]
     */
    public function all(): array;
}
