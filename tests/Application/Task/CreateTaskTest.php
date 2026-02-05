<?php

namespace App\Tests\Application\Task;

use App\Application\Task\Command\CreateTaskCommand;
use App\Application\Task\Handler\CreateTask;
use App\Domain\Task\TaskFactory;
use App\Domain\Task\TaskStatus;
use App\Tests\Support\InMemoryEventStore;
use App\Tests\Support\InMemoryTaskRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateTaskTest extends TestCase
{
    private InMemoryTaskRepository $repo;
    private InMemoryEventStore $store;
    private MessageBusInterface $bus;
    private CreateTask $handler;
    private TaskFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new InMemoryTaskRepository();
        $this->store = new InMemoryEventStore();
        $this->factory = new TaskFactory();
        $this->bus = new class () implements MessageBusInterface {
            public array $messages = [];
            public function dispatch(object $message, array $stamps = []): Envelope
            {
                $this->messages[] = $message;
                return new Envelope($message);
            }
        };
        $this->handler = new CreateTask($this->repo, $this->factory, $this->store, $this->bus);
    }

    public function testCreatesTaskAndStoresEvent(): void
    {
        $taskId = ($this->handler)(new CreateTaskCommand('Title', 'Desc', 'user-1'));

        $task = $this->repo->get($taskId);
        $this->assertNotNull($task);
        $this->assertSame(TaskStatus::Todo, $task->status());
        $this->assertCount(1, $this->store->all());
        $this->assertCount(1, $this->bus->messages);
    }
}
