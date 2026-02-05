<?php

namespace App\Application\Task\Handler;

use App\Application\Task\Query\ListTasksByAssigneeQuery;
use App\Domain\Task\TaskRepository;

final class ListTasksByAssignee
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ListTasksByAssigneeQuery $query): array
    {
        return $this->repository->findByAssignee($query->assigneeId);
    }
}
