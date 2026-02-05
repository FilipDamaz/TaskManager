<?php

namespace App\Domain\Task;

use App\Domain\Shared\DomainEvent;

final class TaskStatusUpdatedEvent implements DomainEvent
{
    private TaskId $taskId;
    private TaskStatus $oldStatus;
    private TaskStatus $newStatus;
    private \DateTimeImmutable $occurredAt;

    public function __construct(TaskId $taskId, TaskStatus $oldStatus, TaskStatus $newStatus)
    {
        $this->taskId = $taskId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function eventType(): string
    {
        return 'task.status_updated';
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function toPayload(): array
    {
        return [
            'task_id' => $this->taskId->toString(),
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
        ];
    }
}
