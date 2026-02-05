<?php

namespace App\Infrastructure\Security;

use App\Domain\Task\Task;
use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class TaskVoter extends Voter
{
    public const VIEW = 'TASK_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserEntity) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Task $task */
        $task = $subject;
        return $task->assigneeId() === $user->id();
    }
}
