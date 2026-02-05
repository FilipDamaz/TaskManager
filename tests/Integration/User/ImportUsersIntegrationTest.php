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
        $em->getConnection()->executeStatement('DELETE FROM users');
    }

    public function testImportsUsersIntoDatabase(): void
    {
        $strategy = self::getContainer()->get(JsonPlaceholderUserImportStrategy::class);
        $expectedUsers = $strategy->fetchUsers();
        $this->assertNotEmpty($expectedUsers);

        $importer = self::getContainer()->get(ImportUsersFromExternalService::class);
        $count = $importer->import();

        $this->assertSame(count($expectedUsers), $count);

        $repository = self::getContainer()->get(UserRepository::class);
        $first = $expectedUsers[0];
        $user = $repository->findByExternalId(ExternalUserId::fromInt($first->externalId));
        $this->assertNotNull($user);
        $this->assertSame($first->email, $user->email()->toString());
    }
}
