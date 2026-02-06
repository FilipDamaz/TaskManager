<?php

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    #[DataProvider('validEmailsProvider')]
    public function testValidEmailCreatesValueObject(string $value): void
    {
        $email = Email::fromString($value);

        $this->assertSame($value, $email->toString());
    }

    #[DataProvider('invalidEmailsProvider')]
    public function testInvalidEmailThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Email::fromString($value);
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function validEmailsProvider(): array
    {
        return [
            ['john.doe@example.com'],
            ['user+tag@domain.co'],
        ];
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function invalidEmailsProvider(): array
    {
        return [
            ['invalid-email'],
            [''],
            ['   '],
            ['user@'],
        ];
    }
}
