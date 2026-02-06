<?php

namespace App\Domain\User;

use Symfony\Component\Uid\Uuid;

final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);
        if ('' === $value) {
            throw new \InvalidArgumentException('UserId cannot be empty.');
        }

        return new self($value);
    }

    public static function new(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
