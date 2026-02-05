<?php

namespace App\Tests\Infrastructure\GraphQL;

use App\Application\Task\Handler\ListAllTasks;
use App\Application\Task\Handler\ListTasksByAssignee;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Infrastructure\GraphQL\TaskResolver;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryTaskRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class TaskResolverTest extends TestCase
{
    private InMemoryTaskRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new InMemoryTaskRepository();
    }

    public function testListForCurrentThrowsWhenNotAuthenticated(): void
    {
        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn(null);

        $resolver = new TaskResolver(new ListTasksByAssignee($this->repo), new ListAllTasks($this->repo), $security);

        $this->expectException(AccessDeniedException::class);
        $resolver->listForCurrent();
    }

    public function testListForCurrentReturnsTasksForUser(): void
    {
        $user = new UserEntity(
            'user-1',
            1,
            'Test User',
            'testuser',
            'test@example.com',
            null,
            null,
            null,
            null
        );

        $task = Task::create(TaskId::fromString('task-1'), 'Title', 'Desc', $user->id());

        $this->repo->save($task);
        $this->repo->save(Task::create(TaskId::fromString('task-2'), 'Other', 'Desc', 'other-user'));

        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn($user);

        $resolver = new TaskResolver(new ListTasksByAssignee($this->repo), new ListAllTasks($this->repo), $security);

        $result = $resolver->listForCurrent();
        $this->assertSame([$task], $result);
    }

    public function testListAllThrowsWhenNotAdmin(): void
    {
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $resolver = new TaskResolver(new ListTasksByAssignee($this->repo), new ListAllTasks($this->repo), $security);

        $this->expectException(AccessDeniedException::class);
        $resolver->listAll();
    }

    public function testListAllReturnsTasksForAdmin(): void
    {
        $task = Task::create(TaskId::fromString('task-2'), 'Title', 'Desc', 'assignee-1');
        $other = Task::create(TaskId::fromString('task-3'), 'Title 2', 'Desc', 'assignee-2');
        $this->repo->save($task);
        $this->repo->save($other);

        $security = $this->createStub(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $resolver = new TaskResolver(new ListTasksByAssignee($this->repo), new ListAllTasks($this->repo), $security);

        $result = $resolver->listAll();
        $this->assertSame([$task, $other], $result);
    }
}
