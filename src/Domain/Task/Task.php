<?php

namespace App\Domain\Task;

use App\Domain\Shared\DomainEvent;

final class Task
{
    private TaskId $id;
    private string $title;
    private string $description;
    private TaskStatus $status;
    private string $assigneeId;

    /**
     * @var DomainEvent[]
     */
    private array $events = [];

    private function __construct(TaskId $id, string $title, string $description, TaskStatus $status, string $assigneeId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->assigneeId = $assigneeId;
    }

    public static function create(TaskId $id, string $title, string $description, string $assigneeId): self
    {
        $title = trim($title);
        $description = trim($description);

        if ('' === $title) {
            throw new \InvalidArgumentException('Title cannot be empty.');
        }
        if ('' === $assigneeId) {
            throw new \InvalidArgumentException('AssigneeId cannot be empty.');
        }

        $task = new self($id, $title, $description, TaskStatus::Todo, $assigneeId);
        $task->record(new TaskCreatedEvent($id, $title, $description, $assigneeId, TaskStatus::Todo));

        return $task;
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $old = $this->status;
        $this->status = $newStatus;
        $this->record(new TaskStatusUpdatedEvent($this->id, $old, $newStatus));
    }

    /**
     * @return array<int, DomainEvent>
     */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    public function id(): TaskId
    {
        return $this->id;
    }

    public function idAsString(): string
    {
        return $this->id->toString();
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function status(): TaskStatus
    {
        return $this->status;
    }

    public function statusValue(): string
    {
        return $this->status->value;
    }

    public function assigneeId(): string
    {
        return $this->assigneeId;
    }

    private function record(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
