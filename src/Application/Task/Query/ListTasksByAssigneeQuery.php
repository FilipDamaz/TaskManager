<?php

namespace App\Application\Task\Query;

final class ListTasksByAssigneeQuery
{
    public readonly string $assigneeId;

    public function __construct(string $assigneeId)
    {
        $this->assigneeId = $assigneeId;
    }
}
