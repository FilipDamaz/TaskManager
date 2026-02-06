<?php

namespace App\Infrastructure\Api;

use App\Application\User\UserData;
use App\Application\User\UserImportStrategy;
use App\Domain\User\Address;
use App\Domain\User\Company;
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
        $response = $this->client->request('GET', $this->baseUrl.'/users');
        $status = $response->getStatusCode();

        if (200 !== $status) {
            throw new \RuntimeException('JsonPlaceholder returned status '.$status);
        }

        $payload = $response->toArray();
        $users = [];

        foreach ($payload as $row) {
            $address = isset($row['address']) ? Address::fromArray((array) $row['address']) : null;
            $company = isset($row['company']) ? Company::fromArray((array) $row['company']) : null;
            $users[] = new UserData(
                (int) ($row['id'] ?? 0),
                (string) ($row['name'] ?? ''),
                (string) ($row['username'] ?? ''),
                (string) ($row['email'] ?? ''),
                (string) ($row['phone'] ?? ''),
                (string) ($row['website'] ?? ''),
                $address,
                $company
            );
        }

        return $users;
    }
}
