<?php

namespace App\Domain\User;

final class User
{
    private UserId $id;
    private ExternalUserId $externalId;
    private string $name;
    private string $username;
    private Email $email;
    private ?string $phone;
    private ?string $website;
    private ?Address $address;
    private ?Company $company;

    private function __construct(
        UserId $id,
        ExternalUserId $externalId,
        string $name,
        string $username,
        Email $email,
        ?string $phone,
        ?string $website,
        ?Address $address,
        ?Company $company
    )
    {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->phone = $phone;
        $this->website = $website;
        $this->address = $address;
        $this->company = $company;
    }

    public static function create(
        UserId $id,
        ExternalUserId $externalId,
        string $name,
        string $username,
        Email $email,
        ?string $phone = null,
        ?string $website = null,
        ?Address $address = null,
        ?Company $company = null
    ): self {
        $name = trim($name);
        $username = trim($username);

        if ($name === '') {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        if ($username === '') {
            throw new \InvalidArgumentException('Username cannot be empty.');
        }

        return new self($id, $externalId, $name, $username, $email, $phone, $website, $address, $company);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function externalId(): ExternalUserId
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

    public function email(): Email
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function website(): ?string
    {
        return $this->website;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    public function company(): ?Company
    {
        return $this->company;
    }
}
