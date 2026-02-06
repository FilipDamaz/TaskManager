<?php

namespace App\Domain\Task;

use Symfony\Component\Uid\Uuid;

final class TaskId
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
            throw new \InvalidArgumentException('TaskId cannot be empty.');
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
}
