<?php

namespace App\Domain\Shared;

interface DomainEvent
{
    public function eventType(): string;

    public function occurredAt(): \DateTimeImmutable;

    public function toPayload(): array;
}
