<?php

namespace App\Domain\Task;

use App\Domain\Shared\DomainEvent;

final class TaskCreatedEvent implements DomainEvent
{
    private TaskId $taskId;
    private string $title;
    private string $description;
    private string $assigneeId;
    private TaskStatus $status;
    private \DateTimeImmutable $occurredAt;

    public function __construct(TaskId $taskId, string $title, string $description, string $assigneeId, TaskStatus $status)
    {
        $this->taskId = $taskId;
        $this->title = $title;
        $this->description = $description;
        $this->assigneeId = $assigneeId;
        $this->status = $status;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function eventType(): string
    {
        return 'task.created';
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'task_id' => $this->taskId->toString(),
            'title' => $this->title,
            'description' => $this->description,
            'assignee_id' => $this->assigneeId,
            'status' => $this->status->value,
        ];
    }
}
