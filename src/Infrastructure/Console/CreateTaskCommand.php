<?php

namespace App\Infrastructure\Console;

use App\Application\Task\Command\CreateTaskCommand as CreateTaskDto;
use App\Application\Task\Handler\CreateTask;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:tasks:create', description: 'Create a task')]
final class CreateTaskCommand extends Command
{
    private CreateTask $handler;

    public function __construct(CreateTask $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('title', null, InputOption::VALUE_REQUIRED)
            ->addOption('description', null, InputOption::VALUE_REQUIRED)
            ->addOption('assignee', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $title = (string) $input->getOption('title');
        $description = (string) $input->getOption('description');
        $assignee = (string) $input->getOption('assignee');

        $taskId = ($this->handler)(new CreateTaskDto($title, $description, $assignee));
        $output->writeln('Task created: '.$taskId->toString());

        return Command::SUCCESS;
    }
}
