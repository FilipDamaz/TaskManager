<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\Email;
use App\Domain\User\ExternalUserId;
use App\Domain\User\User;
use App\Domain\User\UserFactory;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepository
{
    private EntityManagerInterface $em;
    private UserFactory $factory;

    public function __construct(EntityManagerInterface $em, UserFactory $factory)
    {
        $this->em = $em;
        $this->factory = $factory;
    }

    public function save(User $user): void
    {
        $repo = $this->em->getRepository(UserEntity::class);
        $entity = $repo->find($user->id()->toString());

        if ($entity === null) {
            $entity = new UserEntity(
                $user->id()->toString(),
                $user->externalId()->toInt(),
                $user->name(),
                $user->username(),
                $user->email()->toString(),
                $user->phone(),
                $user->website(),
                $user->address()?->toArray(),
                $user->company()?->toArray()
            );
            $this->em->persist($entity);
        } else {
            $entity->update(
                $user->name(),
                $user->username(),
                $user->email()->toString(),
                $user->phone(),
                $user->website(),
                $user->address()?->toArray(),
                $user->company()?->toArray()
            );
        }

        $this->em->flush();
    }

    public function findById(UserId $id): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->find($id->toString());
        if ($entity === null) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address(),
            $entity->company(),
            $entity->id()
        );
    }

    public function findByEmail(Email $email): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->findOneBy([
            'email' => $email->toString(),
        ]);
        if ($entity === null) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address(),
            $entity->company(),
            $entity->id()
        );
    }

    public function findByExternalId(ExternalUserId $externalId): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->findOneBy([
            'externalId' => $externalId->toInt(),
        ]);
        if ($entity === null) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address(),
            $entity->company(),
            $entity->id()
        );
    }

    public function all(): array
    {
        $entities = $this->em->getRepository(UserEntity::class)->findAll();
        $users = [];

        foreach ($entities as $entity) {
            $users[] = $this->factory->create(
                $entity->externalId(),
                $entity->name(),
                $entity->username(),
                $entity->email(),
                $entity->phone(),
                $entity->website(),
                $entity->address(),
                $entity->company(),
                $entity->id()
            );
        }

        return $users;
    }
}
