<?php

namespace App\Application\User;

use App\Domain\User\UserFactory;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class ImportUsersFromExternalService
{
    private UserImportStrategy $strategy;
    private UserRepository $repository;
    private UserFactory $factory;

    public function __construct(UserImportStrategy $strategy, UserRepository $repository, UserFactory $factory)
    {
        $this->strategy = $strategy;
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->strategy->fetchUsers() as $userData) {
            $id = UserId::fromString($userData->id);
            if ($this->repository->findById($id)) {
                continue;
            }

            $user = $this->factory->create(
                $userData->id,
                $userData->name,
                $userData->username,
                $userData->email
            );
            $this->repository->save($user);
            $count++;
        }

        return $count;
    }
}
