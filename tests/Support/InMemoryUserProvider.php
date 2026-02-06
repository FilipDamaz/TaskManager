<?php

namespace App\Tests\Support;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<UserEntity>
 */
final class InMemoryUserProvider implements UserProviderInterface
{
    /**
     * @var array<string, UserEntity>
     */
    private static array $users = [];
    private static ?string $lastIdentifier = null;

    public function addUser(UserEntity $user): void
    {
        self::$users[$user->getUserIdentifier()] = $user;
    }

    public function clear(): void
    {
        self::$users = [];
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        self::$lastIdentifier = $identifier;
        $user = self::$users[$identifier] ?? null;
        if (null === $user) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserEntity) {
            throw new \InvalidArgumentException('Unsupported user instance.');
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return is_a($class, UserEntity::class, true);
    }

    public function lastIdentifier(): ?string
    {
        return self::$lastIdentifier;
    }
}
