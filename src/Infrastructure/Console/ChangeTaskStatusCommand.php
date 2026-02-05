<?php

namespace App\Infrastructure\Console;

use App\Application\Task\Command\ChangeTaskStatusCommand as ChangeTaskStatusDto;
use App\Application\Task\Handler\ChangeTaskStatus;
use App\Domain\Task\TaskStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:tasks:status', description: 'Change task status')]
final class ChangeTaskStatusCommand extends Command
{
    private ChangeTaskStatus $handler;

    public function __construct(ChangeTaskStatus $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED)
            ->addOption('status', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (string) $input->getOption('id');
        $status = TaskStatus::from((string) $input->getOption('status'));

        ($this->handler)(new ChangeTaskStatusDto($id, $status));
        $output->writeln('Task status updated');

        return Command::SUCCESS;
    }
}
