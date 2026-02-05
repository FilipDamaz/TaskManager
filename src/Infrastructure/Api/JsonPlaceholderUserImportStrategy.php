<?php

namespace App\Infrastructure\Api;

use App\Application\User\UserData;
use App\Application\User\UserImportStrategy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JsonPlaceholderUserImportStrategy implements UserImportStrategy
{
    private HttpClientInterface $client;
    private string $baseUrl;

    public function __construct(HttpClientInterface $client, string $baseUrl = 'https://jsonplaceholder.typicode.com')
    {
        $this->client = $client;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function fetchUsers(): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/users');
        $status = $response->getStatusCode();

        if ($status !== 200) {
            throw new \RuntimeException('JsonPlaceholder returned status ' . $status);
        }

        $payload = $response->toArray();
        $users = [];

        foreach ($payload as $row) {
            $users[] = new UserData(
                (int) ($row['id'] ?? 0),
                (string) ($row['name'] ?? ''),
                (string) ($row['username'] ?? ''),
                (string) ($row['email'] ?? ''),
                (string) ($row['phone'] ?? ''),
                (string) ($row['website'] ?? ''),
                isset($row['address']) ? (array) $row['address'] : null,
                isset($row['company']) ? (array) $row['company'] : null
            );
        }

        return $users;
    }
}
