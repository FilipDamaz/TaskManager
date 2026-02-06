<?php

namespace App\Tests\Domain\Task;

use App\Domain\Task\TaskFactory;
use App\Domain\Task\TaskStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskFactoryTest extends TestCase
{
    private TaskFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new TaskFactory();
    }

    /**
     * @dataProvider validProvider
     */
    #[DataProvider('validProvider')]
    public function testCreatesTask(string $title, string $description, string $assigneeId): void
    {
        $task = $this->factory->create($title, $description, $assigneeId);

        $this->assertSame(trim($title), $task->title());
        $this->assertSame(trim($description), $task->description());
        $this->assertSame(trim($assigneeId), $task->assigneeId());
        $this->assertSame(TaskStatus::Todo, $task->status());
        $this->assertNotEmpty($task->id()->toString());
    }

    /**
     * @dataProvider invalidProvider
     */
    #[DataProvider('invalidProvider')]
    public function testThrowsOnInvalidInput(string $title, string $description, string $assigneeId): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create($title, $description, $assigneeId);
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string}>
     */
    public static function validProvider(): array
    {
        return [
            ['Title', 'Desc', 'user-1'],
            [' Another ', '  ', 'user-2'],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string}>
     */
    public static function invalidProvider(): array
    {
        return [
            ['', 'Desc', 'user-1'],
            ['   ', 'Desc', 'user-1'],
            ['Title', 'Desc', ''],
        ];
    }
}
