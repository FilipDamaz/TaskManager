<?php

namespace App\Infrastructure\EventStore;

use App\Domain\Shared\DomainEvent;

interface EventStoreInterface
{
    public function append(string $aggregateType, string $aggregateId, DomainEvent $event): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function byAggregate(string $aggregateType, string $aggregateId): array;
}
