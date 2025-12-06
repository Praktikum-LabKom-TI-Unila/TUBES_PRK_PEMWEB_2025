<?php
declare(strict_types=1);

namespace Middleware;

use Core\HttpException;
use Core\MiddlewareInterface;
use Core\Request;

final class RoleMiddleware implements MiddlewareInterface
{
    /**
     * @param array<int, string> $allowedRoles
     */
    public function __construct(private readonly array $allowedRoles)
    {
    }

    public function handle(Request $request): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;
        $role = is_array($user) ? ($user['role'] ?? null) : null;

        if ($role === null) {
            throw new HttpException('Unauthenticated', 401);
        }

        if (!in_array($role, $this->allowedRoles, true)) {
            throw new HttpException('Forbidden', 403);
        }
    }
}
