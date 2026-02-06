<?php

namespace App\Tests\Functional\Console;

use App\Tests\Support\InMemoryUserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportUsersCommandTest extends KernelTestCase
{
    public function testImportsUsersThroughCommand(): void
    {
        self::bootKernel();

        $kernel = self::$kernel;
        assert(null !== $kernel);
        $application = new Application($kernel);
        $command = $application->find('app:users:import');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Imported users: 2', $tester->getDisplay());

        $repository = self::getContainer()->get(InMemoryUserRepository::class);
        assert($repository instanceof InMemoryUserRepository);
        $this->assertCount(2, $repository->all());
    }
}
