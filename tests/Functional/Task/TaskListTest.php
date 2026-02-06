<?php

namespace App\Tests\Functional\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskStatus;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryTaskRepository;
use App\Tests\Support\InMemoryUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskListTest extends WebTestCase
{
    private KernelBrowser $client;
    private InMemoryUserProvider $provider;
    private InMemoryTaskRepository $tasks;
    private JWTTokenManagerInterface $jwtManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $provider = self::getContainer()->get(InMemoryUserProvider::class);
        assert($provider instanceof InMemoryUserProvider);
        $this->provider = $provider;
        $tasks = self::getContainer()->get(InMemoryTaskRepository::class);
        assert($tasks instanceof InMemoryTaskRepository);
        $this->tasks = $tasks;
        $jwtManager = self::getContainer()->get('test.jwt_manager');
        assert($jwtManager instanceof JWTTokenManagerInterface);
        $this->jwtManager = $jwtManager;
        $this->provider->clear();
        $this->tasks->clear();
    }

    public function testListForCurrentUserIncludesStatus(): void
    {
        $user = new UserEntity(
            'user-1',
            1,
            'User',
            'user',
            'user@example.com',
            null,
            null,
            null,
            null
        );
        $this->provider->addUser($user);

        $task1 = Task::create(TaskId::fromString('task-1'), 'Task A', 'Desc', $user->id());
        $task2 = Task::create(TaskId::fromString('task-2'), 'Task B', 'Desc', $user->id());
        $task2->changeStatus(TaskStatus::InProgress);
        $other = Task::create(TaskId::fromString('task-3'), 'Other', 'Desc', 'other-user');
        $this->tasks->save($task1);
        $this->tasks->save($task2);
        $this->tasks->save($other);

        $token = $this->jwtManager->create($user);
        $this->client->request('GET', '/tasks', server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        if (false === $content) {
            throw new \RuntimeException('Failed to read response content.');
        }
        $data = json_decode($content, true);
        $this->assertCount(2, $data);

        foreach ($data as $item) {
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('description', $item);
            $this->assertArrayHasKey('status', $item);
            $this->assertArrayHasKey('assignee_id', $item);
            $this->assertContains($item['status'], ['todo', 'in_progress', 'done']);
        }
    }
}
