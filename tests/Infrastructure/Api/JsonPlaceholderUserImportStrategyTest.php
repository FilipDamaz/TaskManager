<?php

namespace App\Tests\Infrastructure\Api;

use App\Infrastructure\Api\JsonPlaceholderUserImportStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class JsonPlaceholderUserImportStrategyTest extends TestCase
{
    /**
     * @param array<int, array<string, mixed>> $payload
     */
    #[DataProvider('usersProvider')]
    public function testMapsUsers(array $payload, int $expectedCount): void
    {
        $json = json_encode($payload);
        if (false === $json) {
            throw new \RuntimeException('Failed to encode payload.');
        }
        $response = new MockResponse($json, ['http_code' => 200]);
        $client = new MockHttpClient($response);
        $strategy = new JsonPlaceholderUserImportStrategy($client, 'https://jsonplaceholder.typicode.com');

        $users = $strategy->fetchUsers();

        $this->assertCount($expectedCount, $users);
        $this->assertSame((int) $payload[0]['id'], $users[0]->externalId);
        $this->assertSame($payload[0]['email'], $users[0]->email);
    }

    public function testThrowsOnNonSuccessResponse(): void
    {
        $response = new MockResponse('error', ['http_code' => 500]);
        $client = new MockHttpClient($response);
        $strategy = new JsonPlaceholderUserImportStrategy($client);

        $this->expectException(\RuntimeException::class);

        $strategy->fetchUsers();
    }

    /**
     * @return array<string, array{0: array<int, array<string, mixed>>, 1: int}>
     */
    public static function usersProvider(): array
    {
        return [
            'single user' => [
                [
                    ['id' => 1, 'name' => 'Alice', 'username' => 'alice', 'email' => 'alice@example.com'],
                ],
                1,
            ],
            'two users' => [
                [
                    ['id' => 1, 'name' => 'Alice', 'username' => 'alice', 'email' => 'alice@example.com'],
                    ['id' => 2, 'name' => 'Bob', 'username' => 'bob', 'email' => 'bob@example.com'],
                ],
                2,
            ],
        ];
    }
}
