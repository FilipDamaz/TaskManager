<?php

namespace App\Tests\Functional\GraphQL;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryTaskRepository;
use App\Tests\Support\InMemoryUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskQueryTest extends WebTestCase
{
    private KernelBrowser $client;
    private InMemoryUserProvider $provider;
    private InMemoryTaskRepository $tasks;
    private JWTTokenManagerInterface $jwtManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->provider = self::getContainer()->get(InMemoryUserProvider::class);
        $this->tasks = self::getContainer()->get(InMemoryTaskRepository::class);
        $this->jwtManager = self::getContainer()->get('test.jwt_manager');
        $this->provider->clear();
        $this->tasks->clear();
    }

    public function testTasksQueryReturnsOnlyCurrentUsersTasks(): void
    {
        $user = $this->addUser('uuid-user-1', 1, 'user1@example.com', 'secret1');
        $other = $this->addUser('uuid-user-2', 2, 'user2@example.com', 'secret2');

        $this->addTask('task-1', 'Task A', $user->id());
        $this->addTask('task-2', 'Task B', $user->id());
        $this->addTask('task-3', 'Task C', $other->id());

        $token = $this->jwtManager->create($user);
        $data = $this->graphql('{ tasks { id title description assigneeId status } }', $token);

        $this->assertArrayNotHasKey('errors', $data);
        $tasks = $data['data']['tasks'];
        $this->assertCount(2, $tasks);
        foreach ($tasks as $task) {
            $this->assertSame($user->id(), $task['assigneeId']);
            $this->assertArrayHasKey('title', $task);
            $this->assertArrayHasKey('description', $task);
            $this->assertArrayHasKey('status', $task);
            $this->assertContains($task['status'], ['todo', 'in_progress', 'done']);
        }
    }

    public function testAdminTasksForbiddenForRegularUser(): void
    {
        $user = $this->addUser('uuid-user-1', 1, 'user1@example.com', 'secret1');

        $this->addTask('task-1', 'Task A', $user->id());

        $userToken = $this->jwtManager->create($user);
        $forbidden = $this->graphql('{ adminTasks { id } }', $userToken);
        $this->assertArrayHasKey('errors', $forbidden);
        $this->assertNull($forbidden['data']['adminTasks'] ?? null);
    }

    public function testAdminTasksReturnsAllForAdmin(): void
    {
        $user = $this->addUser('uuid-user-1', 1, 'user1@example.com', 'secret1');
        $admin = $this->addUser('uuid-admin-1', 99, 'admin@example.com', 'secretAdmin', ['ROLE_ADMIN']);

        $this->addTask('task-1', 'Task A', $user->id());
        $this->addTask('task-2', 'Task B', $admin->id());

        $adminToken = $this->jwtManager->create($admin);
        $allowed = $this->graphql('{ adminTasks { id title description assigneeId status } }', $adminToken);
        $this->assertArrayNotHasKey('errors', $allowed);
        $this->assertCount(2, $allowed['data']['adminTasks']);
        foreach ($allowed['data']['adminTasks'] as $task) {
            $this->assertArrayHasKey('title', $task);
            $this->assertArrayHasKey('description', $task);
            $this->assertArrayHasKey('status', $task);
            $this->assertContains($task['status'], ['todo', 'in_progress', 'done']);
        }
    }

    private function addUser(
        string $id,
        int $externalId,
        string $email,
        string $password,
        array $roles = ['ROLE_USER']
    ): UserEntity
    {
        $hasher = self::getContainer()->get('test.user_password_hasher');
        $user = new UserEntity(
            $id,
            $externalId,
            'Test User',
            'testuser',
            $email,
            null,
            null,
            null,
            null,
            '',
            $roles
        );
        $user->setPasswordHash($hasher->hashPassword($user, $password));
        $this->provider->addUser($user);

        return $user;
    }

    private function addTask(string $id, string $title, string $assigneeId): void
    {
        $this->tasks->save(Task::create(TaskId::fromString($id), $title, 'Desc', $assigneeId));
    }

    private function graphql(string $query, string $token): array
    {
        $this->client->request('POST', '/graphql', server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], content: json_encode([
            'query' => $query,
        ]));

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
