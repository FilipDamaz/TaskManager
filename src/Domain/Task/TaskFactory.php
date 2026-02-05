<?php

namespace App\Domain\Task;

final class TaskFactory
{
    public function create(string $title, string $description, string $assigneeId, ?string $id = null): Task
    {
        $taskId = $id ? TaskId::fromString($id) : TaskId::new();

        return Task::create($taskId, $title, $description, $assigneeId);
    }
}
