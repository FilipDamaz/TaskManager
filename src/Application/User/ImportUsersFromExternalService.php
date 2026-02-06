<?php

namespace App\Application\User;

use App\Domain\User\ExternalUserId;
use App\Domain\User\UserFactory;
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
            $externalId = ExternalUserId::fromInt($userData->externalId);
            if ($this->repository->findByExternalId($externalId)) {
                continue;
            }

            $user = $this->factory->create(
                $userData->externalId,
                $userData->name,
                $userData->username,
                $userData->email,
                $userData->phone,
                $userData->website,
                $userData->address,
                $userData->company
            );
            $this->repository->save($user);
            ++$count;
        }

        return $count;
    }
}
