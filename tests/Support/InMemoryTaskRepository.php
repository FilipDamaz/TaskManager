<?php

namespace App\Tests\Support;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;

final class InMemoryTaskRepository implements TaskRepository
{
    /**
     * @var array<string, Task>
     */
    private array $items = [];

    public function save(Task $task): void
    {
        $this->items[$task->id()->toString()] = $task;
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function get(TaskId $id): ?Task
    {
        return $this->items[$id->toString()] ?? null;
    }

    public function findByAssignee(string $assigneeId): array
    {
        return array_values(array_filter($this->items, static function (Task $task) use ($assigneeId): bool {
            return $task->assigneeId() === $assigneeId;
        }));
    }

    public function all(): array
    {
        return array_values($this->items);
    }
}
