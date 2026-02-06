<?php

namespace App\Application\Task\Handler;

use App\Application\Task\Query\ListAllTasksQuery;
use App\Domain\Task\TaskRepository;

final class ListAllTasks
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array<int, \App\Domain\Task\Task>
     */
    public function __invoke(ListAllTasksQuery $query): array
    {
        return $this->repository->all();
    }
}
