<?php

namespace App\Domain\Shared;

interface DomainEvent
{
    public function eventType(): string;

    public function occurredAt(): \DateTimeImmutable;

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array;
}
