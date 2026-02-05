<?php

namespace App\Domain\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    public function get(TaskId $id): ?Task;

    /**
     * @return Task[]
     */
    public function findByAssignee(string $assigneeId): array;

    /**
     * @return Task[]
     */
    public function all(): array;
}
