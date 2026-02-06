<?php

namespace App\Tests\Domain\User;

use App\Domain\User\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserFactoryTest extends TestCase
{
    #[DataProvider('userProvider')]
    public function testCreatesUserAggregate(
        int $externalId,
        string $name,
        string $username,
        string $email,
    ): void {
        $factory = new UserFactory();
        $user = $factory->create($externalId, $name, $username, $email);

        $this->assertSame($externalId, $user->externalId()->toInt());
        $this->assertNotEmpty($user->id()->toString());
        $this->assertSame($name, $user->name());
        $this->assertSame($username, $user->username());
        $this->assertSame($email, $user->email()->toString());
    }

    /**
     * @return array<int, array{0: int, 1: string, 2: string, 3: string}>
     */
    public static function userProvider(): array
    {
        return [
            [1, 'John Doe', 'jdoe', 'john@example.com'],
            [42, 'Jane Roe', 'jroe', 'jane.roe@example.com'],
        ];
    }
}
