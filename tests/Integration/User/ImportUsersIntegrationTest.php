<?php

namespace App\Tests\Integration\User;

use App\Application\User\ImportUsersFromExternalService;
use App\Domain\User\ExternalUserId;
use App\Domain\User\UserRepository;
use App\Infrastructure\Api\JsonPlaceholderUserImportStrategy;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[Group('integration')]
final class ImportUsersIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel(['environment' => 'integration']);
        $em = self::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $em->getConnection()->executeStatement('DELETE FROM users');
    }

    public function testImportsUsersIntoDatabase(): void
    {
        $strategy = self::getContainer()->get(JsonPlaceholderUserImportStrategy::class);
        assert($strategy instanceof JsonPlaceholderUserImportStrategy);
        $expectedUsers = $strategy->fetchUsers();
        $this->assertNotEmpty($expectedUsers);

        $importer = self::getContainer()->get(ImportUsersFromExternalService::class);
        assert($importer instanceof ImportUsersFromExternalService);
        $count = $importer->import();

        $this->assertSame(count($expectedUsers), $count);

        $repository = self::getContainer()->get(UserRepository::class);
        assert($repository instanceof UserRepository);
        $first = $expectedUsers[0];
        $user = $repository->findByExternalId(ExternalUserId::fromInt($first->externalId));
        $this->assertNotNull($user);
        $this->assertSame($first->email, $user->email()->toString());
    }
}
