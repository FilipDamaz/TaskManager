<?php

namespace App\Tests\Functional\Console;

use App\Application\Task\Command\CreateTaskCommand as CreateTaskDto;
use App\Application\Task\Handler\CreateTask;
use App\Domain\Task\TaskStatus;
use App\Tests\Support\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ChangeTaskStatusCommandTest extends KernelTestCase
{
    private InMemoryTaskRepository $repo;
    private CreateTask $createHandler;
    private CommandTester $tester;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $repo = self::getContainer()->get(InMemoryTaskRepository::class);
        assert($repo instanceof InMemoryTaskRepository);
        $this->repo = $repo;
        $this->repo->clear();
        $createHandler = self::getContainer()->get(CreateTask::class);
        assert($createHandler instanceof CreateTask);
        $this->createHandler = $createHandler;
        $kernel = self::$kernel;
        assert(null !== $kernel);
        $application = new Application($kernel);
        $command = $application->find('app:tasks:status');
        $this->tester = new CommandTester($command);
    }

    public function testChangesTaskStatus(): void
    {
        $taskId = ($this->createHandler)(new CreateTaskDto('Task B', 'Desc', 'user-2'));

        $exitCode = $this->tester->execute([
            '--id' => $taskId->toString(),
            '--status' => TaskStatus::InProgress->value,
        ]);

        $this->assertSame(0, $exitCode);

        $task = $this->repo->get($taskId);
        $this->assertNotNull($task);
        $this->assertSame(TaskStatus::InProgress, $task->status());
    }
}
