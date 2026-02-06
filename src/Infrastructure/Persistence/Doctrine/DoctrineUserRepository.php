<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\Address;
use App\Domain\User\Company;
use App\Domain\User\Email;
use App\Domain\User\ExternalUserId;
use App\Domain\User\User;
use App\Domain\User\UserFactory;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class DoctrineUserRepository implements UserRepository
{
    private EntityManagerInterface $em;
    private UserFactory $factory;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        EntityManagerInterface $em,
        UserFactory $factory,
        UserPasswordHasherInterface $hasher,
    ) {
        $this->em = $em;
        $this->factory = $factory;
        $this->hasher = $hasher;
    }

    public function save(User $user): void
    {
        $repo = $this->em->getRepository(UserEntity::class);
        $entity = $repo->find($user->id()->toString());

        if (null === $entity) {
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
            $this->ensurePassword($entity);
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
            $this->ensurePassword($entity);
        }

        $this->em->flush();
    }

    public function findById(UserId $id): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->find($id->toString());
        if (null === $entity) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address() ? Address::fromArray($entity->address()) : null,
            $entity->company() ? Company::fromArray($entity->company()) : null,
            $entity->id()
        );
    }

    public function findByEmail(Email $email): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->findOneBy([
            'email' => $email->toString(),
        ]);
        if (null === $entity) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address() ? Address::fromArray($entity->address()) : null,
            $entity->company() ? Company::fromArray($entity->company()) : null,
            $entity->id()
        );
    }

    public function findByExternalId(ExternalUserId $externalId): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->findOneBy([
            'externalId' => $externalId->toInt(),
        ]);
        if (null === $entity) {
            return null;
        }

        return $this->factory->create(
            $entity->externalId(),
            $entity->name(),
            $entity->username(),
            $entity->email(),
            $entity->phone(),
            $entity->website(),
            $entity->address() ? Address::fromArray($entity->address()) : null,
            $entity->company() ? Company::fromArray($entity->company()) : null,
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
                $entity->address() ? Address::fromArray($entity->address()) : null,
                $entity->company() ? Company::fromArray($entity->company()) : null,
                $entity->id()
            );
        }

        return $users;
    }

    private function ensurePassword(UserEntity $entity): void
    {
        if ('' !== $entity->getPassword()) {
            return;
        }

        $oneTimePassword = bin2hex(random_bytes(6));
        $entity->setPasswordHash($this->hasher->hashPassword($entity, $oneTimePassword));

        // TODO: Send the one-time password to the user's email address.
    }
}
