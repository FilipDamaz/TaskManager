<?php

namespace App\Tests\Functional\Auth;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use App\Tests\Support\InMemoryUserProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginTest extends WebTestCase
{
    private InMemoryUserProvider $provider;
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $provider = self::getContainer()->get(InMemoryUserProvider::class);
        assert($provider instanceof InMemoryUserProvider);
        $this->provider = $provider;
        $this->provider->clear();
    }

    public function testReturnsJwtToken(): void
    {
        /** @var \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $hasher */
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

        $payload = json_encode([
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]);
        if (false === $payload) {
            throw new \RuntimeException('Failed to encode login payload.');
        }

        $this->client->request('POST', '/login', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        if (false === $content) {
            throw new \RuntimeException('Failed to read response content.');
        }
        $data = json_decode($content, true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLoginThenFetchMe(): void
    {
        /** @var \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $hasher */
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

        $payload = json_encode([
            'email' => 'another@example.com',
            'password' => 'secret456',
        ]);
        if (false === $payload) {
            throw new \RuntimeException('Failed to encode login payload.');
        }

        $this->client->request('POST', '/login', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);

        $this->assertSame('another@example.com', $this->provider->lastIdentifier());
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $loginContent = $this->client->getResponse()->getContent();
        if (false === $loginContent) {
            throw new \RuntimeException('Failed to read login response.');
        }
        $loginData = json_decode($loginContent, true);
        $this->assertArrayHasKey('token', $loginData);

        $this->client->request('GET', '/me', server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$loginData['token'],
        ]);

        if (200 !== $this->client->getResponse()->getStatusCode()) {
            $this->fail('Response: '.$this->client->getResponse()->getContent());
        }
        $meContent = $this->client->getResponse()->getContent();
        if (false === $meContent) {
            throw new \RuntimeException('Failed to read me response.');
        }
        $me = json_decode($meContent, true);
        $this->assertSame('another@example.com', $me['email']);
        $this->assertSame('Another User', $me['name']);
        $this->assertSame('another', $me['username']);
    }
}
