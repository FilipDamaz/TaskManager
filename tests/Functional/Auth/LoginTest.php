<?php

namespace App\Tests\Functional\Auth;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryUserProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginTest extends WebTestCase
{
    private InMemoryUserProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = self::getContainer()->get(InMemoryUserProvider::class);
        $this->provider->clear();
    }

    public function testReturnsJwtToken(): void
    {
        $client = self::createClient();

        $hasher = self::getContainer()->get('test.user_password_hasher');
        $user = new UserEntity(
            'uuid-test-1',
            1,
            'Test User',
            'testuser',
            'test@example.com',
            null,
            null,
            null,
            null
        );
        $user->setPasswordHash($hasher->hashPassword($user, 'secret123'));
        $this->provider->addUser($user);

        $client->request('POST', '/login', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode([
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLoginThenFetchMe(): void
    {
        $client = self::createClient();

        $hasher = self::getContainer()->get('test.user_password_hasher');
        $user = new UserEntity(
            'uuid-test-2',
            2,
            'Another User',
            'another',
            'another@example.com',
            null,
            null,
            null,
            null
        );
        $user->setPasswordHash($hasher->hashPassword($user, 'secret456'));
        $this->assertTrue($hasher->isPasswordValid($user, 'secret456'));
        $this->provider->addUser($user);
        $this->assertSame('another@example.com', $this->provider->loadUserByIdentifier('another@example.com')->getUserIdentifier());

        $client->request('POST', '/login', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode([
            'email' => 'another@example.com',
            'password' => 'secret456',
        ]));

        $this->assertSame('another@example.com', $this->provider->lastIdentifier());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $loginData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $loginData);

        $client->request('GET', '/me', server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$loginData['token'],
        ]);

        if ($client->getResponse()->getStatusCode() !== 200) {
            $this->fail('Response: '.$client->getResponse()->getContent());
        }
        $me = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('another@example.com', $me['email']);
        $this->assertSame('Another User', $me['name']);
        $this->assertSame('another', $me['username']);
    }
}
