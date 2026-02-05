<?php

namespace App\Tests\Functional\Console;

use App\Tests\Support\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateTaskCommandTest extends KernelTestCase
{
    private InMemoryTaskRepository $repo;
    private CommandTester $tester;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->repo = self::getContainer()->get(InMemoryTaskRepository::class);
        $this->repo->clear();
        $application = new Application(self::$kernel);
        $command = $application->find('app:tasks:create');
        $this->tester = new CommandTester($command);
    }

    public function testCreatesTask(): void
    {
        $exitCode = $this->tester->execute([
            '--title' => 'Task A',
            '--description' => 'Desc',
            '--assignee' => 'user-1',
        ]);

        $this->assertSame(0, $exitCode);

        $tasks = $this->repo->findByAssignee('user-1');
        $this->assertCount(1, $tasks);
        $this->assertSame('Task A', $tasks[0]->title());
    }
}
