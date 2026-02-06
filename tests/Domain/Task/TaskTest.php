<?php

namespace App\Tests\Domain\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testCreateRecordsEvent(): void
    {
        $task = Task::create(TaskId::new(), 'Title', 'Desc', 'user-1');

        $events = $task->pullEvents();
        $this->assertCount(1, $events);
        $this->assertSame('task.created', $events[0]->eventType());
    }

    #[DataProvider('statusProvider')]
    public function testChangeStatusRecordsEvent(TaskStatus $from, TaskStatus $to, bool $expectEvent): void
    {
        $task = Task::create(TaskId::new(), 'Title', 'Desc', 'user-1');
        $task->pullEvents();
        if (TaskStatus::Todo !== $from) {
            $task->changeStatus($from);
            $task->pullEvents();
        }

        $task->changeStatus($to);
        $events = $task->pullEvents();

        $this->assertSame($expectEvent ? 1 : 0, count($events));
        if ($expectEvent) {
            $this->assertSame('task.status_updated', $events[0]->eventType());
        }
    }

    /**
     * @return array<int, array{0: TaskStatus, 1: TaskStatus, 2: bool}>
     */
    public static function statusProvider(): array
    {
        return [
            [TaskStatus::Todo, TaskStatus::InProgress, true],
            [TaskStatus::InProgress, TaskStatus::Done, true],
            [TaskStatus::Done, TaskStatus::Done, false],
        ];
    }
}
