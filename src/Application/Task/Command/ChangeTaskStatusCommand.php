<?php

namespace App\Application\Task\Command;

use App\Domain\Task\TaskStatus;

final class ChangeTaskStatusCommand
{
    public readonly string $taskId;
    public readonly TaskStatus $newStatus;

    public function __construct(string $taskId, TaskStatus $newStatus)
    {
        $this->taskId = $taskId;
        $this->newStatus = $newStatus;
    }
}
