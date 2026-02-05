<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskStatus;
use App\Infrastructure\Persistence\Doctrine\Entity\TaskEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTaskRepository implements TaskRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Task $task): void
    {
        $repo = $this->em->getRepository(TaskEntity::class);
        $entity = $repo->find($task->id()->toString());

        if ($entity === null) {
            $entity = new TaskEntity(
                $task->id()->toString(),
                $task->title(),
                $task->description(),
                $task->status(),
                $task->assigneeId()
            );
            $this->em->persist($entity);
        } else {
            $entity->update(
                $task->title(),
                $task->description(),
                $task->status(),
                $task->assigneeId()
            );
        }

        $this->em->flush();
    }

    public function get(TaskId $id): ?Task
    {
        $entity = $this->em->getRepository(TaskEntity::class)->find($id->toString());
        if ($entity === null) {
            return null;
        }

        $task = Task::create(
            TaskId::fromString($entity->id()),
            $entity->title(),
            $entity->description(),
            $entity->assigneeId()
        );
        if ($entity->status() !== TaskStatus::Todo) {
            $task->changeStatus($entity->status());
            $task->pullEvents();
        }

        return $task;
    }

    public function findByAssignee(string $assigneeId): array
    {
        $entities = $this->em->getRepository(TaskEntity::class)->findBy([
            'assigneeId' => $assigneeId,
        ]);

        $tasks = [];
        foreach ($entities as $entity) {
            $task = Task::create(
                TaskId::fromString($entity->id()),
                $entity->title(),
                $entity->description(),
                $entity->assigneeId()
            );
            if ($entity->status() !== TaskStatus::Todo) {
                $task->changeStatus($entity->status());
                $task->pullEvents();
            }
            $tasks[] = $task;
        }

        return $tasks;
    }

    public function all(): array
    {
        $entities = $this->em->getRepository(TaskEntity::class)->findAll();
        $tasks = [];

        foreach ($entities as $entity) {
            $task = Task::create(
                TaskId::fromString($entity->id()),
                $entity->title(),
                $entity->description(),
                $entity->assigneeId()
            );
            if ($entity->status() !== TaskStatus::Todo) {
                $task->changeStatus($entity->status());
                $task->pullEvents();
            }
            $tasks[] = $task;
        }

        return $tasks;
    }
}
