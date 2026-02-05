<?php

namespace App\Infrastructure\GraphQL;

use App\Application\Task\Handler\ListAllTasks;
use App\Application\Task\Handler\ListTasksByAssignee;
use App\Application\Task\Query\ListAllTasksQuery;
use App\Application\Task\Query\ListTasksByAssigneeQuery;
use App\Domain\Task\Task;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class TaskResolver
{
    private ListTasksByAssignee $listByAssignee;
    private ListAllTasks $listAll;
    private Security $security;

    public function __construct(ListTasksByAssignee $listByAssignee, ListAllTasks $listAll, Security $security)
    {
        $this->listByAssignee = $listByAssignee;
        $this->listAll = $listAll;
        $this->security = $security;
    }

    /**
     * @return Task[]
     */
    public function listForCurrent(): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof UserEntity) {
            throw new AccessDeniedException('Unauthorized');
        }

        return ($this->listByAssignee)(new ListTasksByAssigneeQuery($user->id()));
    }

    /**
     * @return Task[]
     */
    public function listAll(): array
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Forbidden');
        }

        return ($this->listAll)(new ListAllTasksQuery());
    }
}
