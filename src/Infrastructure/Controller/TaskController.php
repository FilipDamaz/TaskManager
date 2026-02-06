<?php

namespace App\Infrastructure\Controller;

use App\Application\Task\Handler\ListAllTasks;
use App\Application\Task\Handler\ListTasksByAssignee;
use App\Application\Task\Query\ListAllTasksQuery;
use App\Application\Task\Query\ListTasksByAssigneeQuery;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;
use App\Application\EventStore\EventStoreInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController
{
    #[Route(path: '/tasks', name: 'tasks_list', methods: ['GET'])]
    public function listForCurrentUser(Security $security, ListTasksByAssignee $handler): JsonResponse
    {
        $user = $security->getUser();
        if (!$user instanceof UserEntity) {
            return new JsonResponse(['error' => 'unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $tasks = ($handler)(new ListTasksByAssigneeQuery($user->id()));

        return new JsonResponse($this->mapTasks($tasks));
    }

    #[Route(path: '/admin/tasks', name: 'tasks_list_admin', methods: ['GET'])]
    public function listAll(ListAllTasks $handler): JsonResponse
    {
        $tasks = ($handler)(new ListAllTasksQuery());

        return new JsonResponse($this->mapTasks($tasks));
    }

    #[Route(path: '/tasks/{id}/history', name: 'tasks_history', methods: ['GET'])]
    public function history(string $id, EventStoreInterface $store, TaskRepository $tasks, Security $security): JsonResponse
    {
        $task = $tasks->get(TaskId::fromString($id));
        if (null === $task) {
            return new JsonResponse(['error' => 'not_found'], JsonResponse::HTTP_NOT_FOUND);
        }
        if (!$security->isGranted('TASK_VIEW', $task)) {
            return new JsonResponse(['error' => 'forbidden'], JsonResponse::HTTP_FORBIDDEN);
        }
        $events = $store->byAggregate('task', $id);

        return new JsonResponse($events);
    }

    /**
     * @param Task[] $tasks
     *
     * @return array<int, array<string, string>>
     */
    private function mapTasks(array $tasks): array
    {
        return array_map(static function (Task $task): array {
            return [
                'id' => $task->id()->toString(),
                'title' => $task->title(),
                'description' => $task->description(),
                'status' => $task->status()->value,
                'assignee_id' => $task->assigneeId(),
            ];
        }, $tasks);
    }
}
