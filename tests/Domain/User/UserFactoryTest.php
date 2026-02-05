<?php

namespace App\Tests\Domain\User;

use App\Domain\User\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserFactoryTest extends TestCase
{
    #[DataProvider('userProvider')]
    public function testCreatesUserAggregate(
        string $id,
        string $name,
        string $username,
        string $email
    ): void {
        $factory = new UserFactory();
        $user = $factory->create($id, $name, $username, $email);

        $this->assertSame($id, $user->id()->toString());
        $this->assertSame($name, $user->name());
        $this->assertSame($username, $user->username());
        $this->assertSame($email, $user->email()->toString());
    }

    public static function userProvider(): array
    {
        return [
            ['1', 'John Doe', 'jdoe', 'john@example.com'],
            ['42', 'Jane Roe', 'jroe', 'jane.roe@example.com'],
        ];
    }
}
