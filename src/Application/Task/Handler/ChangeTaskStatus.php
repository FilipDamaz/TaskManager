<?php

namespace App\Application\Task\Handler;

use App\Application\Task\Command\ChangeTaskStatusCommand;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;
use App\Infrastructure\EventStore\EventStoreInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChangeTaskStatus
{
    private TaskRepository $repository;
    private EventStoreInterface $eventStore;
    private MessageBusInterface $bus;

    public function __construct(TaskRepository $repository, EventStoreInterface $eventStore, MessageBusInterface $bus)
    {
        $this->repository = $repository;
        $this->eventStore = $eventStore;
        $this->bus = $bus;
    }

    public function __invoke(ChangeTaskStatusCommand $command): void
    {
        $task = $this->repository->get(TaskId::fromString($command->taskId));
        if (null === $task) {
            throw new \RuntimeException('Task not found.');
        }

        $task->changeStatus($command->newStatus);
        $this->repository->save($task);

        foreach ($task->pullEvents() as $event) {
            $this->eventStore->append('task', $task->id()->toString(), $event);
            $this->bus->dispatch($event);
        }
    }
}
