<?php

namespace App\Application\Task\Command;

final class CreateTaskCommand
{
    public readonly string $title;
    public readonly string $description;
    public readonly string $assigneeId;

    public function __construct(string $title, string $description, string $assigneeId)
    {
        $this->title = $title;
        $this->description = $description;
        $this->assigneeId = $assigneeId;
    }
}
