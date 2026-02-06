<?php

namespace App\Application\User;

use App\Domain\User\Address;
use App\Domain\User\Company;

final class UserData
{
    public readonly int $externalId;
    public readonly string $name;
    public readonly string $username;
    public readonly string $email;
    public readonly ?string $phone;
    public readonly ?string $website;
    public readonly ?Address $address;
    public readonly ?Company $company;

    public function __construct(
        int $externalId,
        string $name,
        string $username,
        string $email,
        ?string $phone = null,
        ?string $website = null,
        ?Address $address = null,
        ?Company $company = null,
    ) {
        $this->externalId = $externalId;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->phone = $phone;
        $this->website = $website;
        $this->address = $address;
        $this->company = $company;
    }
}
