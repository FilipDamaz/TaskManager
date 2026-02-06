<?php

namespace App\Tests\Support;

use App\Domain\Shared\DomainEvent;
use App\Infrastructure\EventStore\EventStoreInterface;

final class InMemoryEventStore implements EventStoreInterface
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $events = [];

    public function append(string $aggregateType, string $aggregateId, DomainEvent $event): void
    {
        $this->events[] = [
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $event->eventType(),
            'payload' => $event->toPayload(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function byAggregate(string $aggregateType, string $aggregateId): array
    {
        return array_values(array_filter($this->events, static function (array $row) use ($aggregateType, $aggregateId): bool {
            return $row['aggregate_type'] === $aggregateType && $row['aggregate_id'] === $aggregateId;
        }));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->events;
    }
}
