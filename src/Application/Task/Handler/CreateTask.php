<?php

namespace App\Application\Task\Handler;

use App\Application\Task\Command\CreateTaskCommand;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;
use App\Infrastructure\EventStore\EventStoreInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateTask
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

    public function __invoke(CreateTaskCommand $command): TaskId
    {
        $task = Task::create(TaskId::new(), $command->title, $command->description, $command->assigneeId);
        $this->repository->save($task);

        foreach ($task->pullEvents() as $event) {
            $this->eventStore->append('task', $task->id()->toString(), $event);
            $this->bus->dispatch($event);
        }

        return $task->id();
    }
}
