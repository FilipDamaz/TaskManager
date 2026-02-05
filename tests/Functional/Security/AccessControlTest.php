<?php

namespace App\Tests\Functional\Security;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryUserProvider;
use App\Application\Task\Command\CreateTaskCommand as CreateTaskDto;
use App\Application\Task\Handler\CreateTask;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AccessControlTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;
    private InMemoryUserProvider $provider;
    private JWTTokenManagerInterface $jwtManager;
    private CreateTask $createTask;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->provider = self::getContainer()->get(InMemoryUserProvider::class);
        $this->provider->clear();
        $this->jwtManager = self::getContainer()->get('test.jwt_manager');
        $this->createTask = self::getContainer()->get(CreateTask::class);
    }

    /**
     * @dataProvider accessProvider
     */
    #[DataProvider('accessProvider')]
    public function testAccess(?array $roles, ?string $userId, string $path, int $expectedStatus, bool $createHistoryTask): void
    {
        $headers = [];

        if ($createHistoryTask) {
            $taskId = ($this->createTask)(new CreateTaskDto('Hist', 'Desc', 'owner-1'));
            $path = str_replace('test-id', $taskId->toString(), $path);
        }

        if ($roles !== null && $userId !== null) {
            $user = new UserEntity(
                $userId,
                999,
                'User',
                'user',
                $userId.'@example.com',
                null,
                null,
                null,
                null
            );
            $user->setRoles($roles);
            $this->provider->addUser($user);
            $token = $this->jwtManager->create($user);
            $headers['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }

        $this->client->request('GET', $path, server: $headers);

        $this->assertSame($expectedStatus, $this->client->getResponse()->getStatusCode());
    }

    public static function accessProvider(): array
    {
        return [
            'anon tasks' => [null, null, '/tasks', 401, false],
            'user tasks' => [['ROLE_USER'], 'user-1', '/tasks', 200, false],
            'admin tasks' => [['ROLE_ADMIN'], 'admin-1', '/tasks', 200, false],

            'anon me' => [null, null, '/me', 401, false],
            'user me' => [['ROLE_USER'], 'user-2', '/me', 200, false],

            'user admin tasks forbidden' => [['ROLE_USER'], 'user-3', '/admin/tasks', 403, false],
            'admin admin tasks ok' => [['ROLE_ADMIN'], 'admin-2', '/admin/tasks', 200, false],

            'owner history ok' => [['ROLE_USER'], 'owner-1', '/tasks/test-id/history', 200, true],
            'user history forbidden' => [['ROLE_USER'], 'not-owner', '/tasks/test-id/history', 403, true],
            'admin history ok' => [['ROLE_ADMIN'], 'admin-3', '/tasks/test-id/history', 200, true],
        ];
    }
}
