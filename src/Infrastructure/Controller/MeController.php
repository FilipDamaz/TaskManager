<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

final class MeController
{
    #[Route(path: '/me', name: 'auth_me', methods: ['GET'])]
    public function __invoke(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if ($user === null) {
            return new JsonResponse(['error' => 'unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        if (!$user instanceof UserEntity) {
            return new JsonResponse(['error' => 'unsupported_user'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'id' => $user->id(),
            'external_id' => $user->externalId(),
            'email' => $user->email(),
            'name' => $user->name(),
            'username' => $user->username(),
        ]);
    }
}
