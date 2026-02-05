<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthcheckController
{
    #[Route(path: '/health', name: 'health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

        if (!$databaseUrl) {
            return new JsonResponse(
                ['status' => 'error', 'db' => 'missing DATABASE_URL'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {
            $parts = parse_url($databaseUrl);
            $host = $parts['host'] ?? 'localhost';
            $port = $parts['port'] ?? 5432;
            $dbName = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
            $user = $parts['user'] ?? '';
            $pass = $parts['pass'] ?? '';

            $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $dbName);
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->query('SELECT 1');

            return new JsonResponse(['status' => 'ok', 'db' => 'connected']);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['status' => 'error', 'db' => 'connection_failed', 'message' => $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
