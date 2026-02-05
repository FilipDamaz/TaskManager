<?php

namespace App\Tests\Application\User;

use App\Application\User\ImportUsersFromExternalService;
use App\Application\User\UserData;
use App\Domain\User\UserFactory;
use App\Tests\Support\FakeUserImportStrategy;
use App\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ImportUsersFromExternalServiceTest extends TestCase
{
    #[DataProvider('importCasesProvider')]
    public function testImportsUsers(array $input, int $expectedImported, int $expectedTotal): void
    {
        $strategy = new FakeUserImportStrategy($input);
        $repository = new InMemoryUserRepository();
        $service = new ImportUsersFromExternalService($strategy, $repository, new UserFactory());

        $count = $service->import();

        $this->assertSame($expectedImported, $count);
        $this->assertCount($expectedTotal, $repository->all());
    }

    public static function importCasesProvider(): array
    {
        return [
            'two new users' => [
                [
                    new UserData('1', 'Alice', 'alice', 'alice@example.com'),
                    new UserData('2', 'Bob', 'bob', 'bob@example.com'),
                ],
                2,
                2,
            ],
            'duplicates are skipped' => [
                [
                    new UserData('1', 'Alice', 'alice', 'alice@example.com'),
                    new UserData('1', 'Alice Dup', 'alice2', 'alice2@example.com'),
                ],
                1,
                1,
            ],
        ];
    }
}
