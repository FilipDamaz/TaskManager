<?php

namespace App\Domain\User;

final class UserFactory
{
    public function create(
        int $externalId,
        string $name,
        string $username,
        string $email,
        ?string $phone = null,
        ?string $website = null,
        ?array $address = null,
        ?array $company = null,
        ?string $id = null
    ): User
    {
        return User::create(
            $id ? UserId::fromString($id) : UserId::new(),
            ExternalUserId::fromInt($externalId),
            $name,
            $username,
            Email::fromString($email),
            $phone,
            $website,
            $address ? Address::fromArray($address) : null,
            $company ? Company::fromArray($company) : null
        );
    }
}
