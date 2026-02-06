<?php

namespace App\Infrastructure\Console;

use App\Application\User\ImportUsersFromExternalService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:users:import', description: 'Import users from JSONPlaceholder')]
final class ImportUsersCommand extends Command
{
    private ImportUsersFromExternalService $importer;

    public function __construct(ImportUsersFromExternalService $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->importer->import();
        $output->writeln('Imported users: '.$count);

        return Command::SUCCESS;
    }
}
