<?php

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uniq_users_email', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'uniq_users_external_id', columns: ['external_id'])]
final class UserEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $externalId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $passwordHash;

    /** @var array<int, string> */
    #[ORM\Column(type: 'json')]
    private array $roles;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $website;

    /** @var array{street: string, suite: string, city: string, zipcode: string, geo: array{lat: string, lng: string}}|null */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $address;

    /** @var array{name: string, catchPhrase: string, bs: string}|null */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $company;

    /**
     * @param array{street: string, suite: string, city: string, zipcode: string, geo: array{lat: string, lng: string}}|null $address
     * @param array{name: string, catchPhrase: string, bs: string}|null                                                      $company
     * @param array<int, string>                                                                                             $roles
     */
    public function __construct(
        string $id,
        int $externalId,
        string $name,
        string $username,
        string $email,
        ?string $phone,
        ?string $website,
        ?array $address,
        ?array $company,
        string $passwordHash = '',
        array $roles = ['ROLE_USER'],
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->phone = $phone;
        $this->website = $website;
        $this->address = $address;
        $this->company = $company;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function externalId(): int
    {
        return $this->externalId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function userIdentifier(): string
    {
        if ('' === $this->email) {
            throw new \LogicException('User email is empty.');
        }

        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->email) {
            throw new \LogicException('User email is empty.');
        }

        return $this->email;
    }

    /**
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_values(array_unique($roles));
    }

    /**
     * @param array<int, string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function eraseCredentials(): void
    {
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function website(): ?string
    {
        return $this->website;
    }

    /**
     * @return array{street: string, suite: string, city: string, zipcode: string, geo: array{lat: string, lng: string}}|null
     */
    public function address(): ?array
    {
        return $this->address;
    }

    /**
     * @return array{name: string, catchPhrase: string, bs: string}|null
     */
    public function company(): ?array
    {
        return $this->company;
    }

    /**
     * @param array{street: string, suite: string, city: string, zipcode: string, geo: array{lat: string, lng: string}}|null $address
     * @param array{name: string, catchPhrase: string, bs: string}|null                                                      $company
     */
    public function update(
        string $name,
        string $username,
        string $email,
        ?string $phone,
        ?string $website,
        ?array $address,
        ?array $company,
    ): void {
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->phone = $phone;
        $this->website = $website;
        $this->address = $address;
        $this->company = $company;
    }
}
