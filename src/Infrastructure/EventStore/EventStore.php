<?php

namespace App\Infrastructure\EventStore;

use App\Domain\Shared\DomainEvent;
use Doctrine\DBAL\Connection;

final class EventStore implements EventStoreInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function append(string $aggregateType, string $aggregateId, DomainEvent $event): void
    {
        $this->connection->insert('event_store', [
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $event->eventType(),
            'payload' => json_encode($event->toPayload()),
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function byAggregate(string $aggregateType, string $aggregateId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT event_type, payload, occurred_at FROM event_store WHERE aggregate_type = :type AND aggregate_id = :id ORDER BY id ASC',
            ['type' => $aggregateType, 'id' => $aggregateId]
        );
    }
}
