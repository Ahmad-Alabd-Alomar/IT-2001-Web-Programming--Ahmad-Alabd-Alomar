<?php

require_once __DIR__ . '/Logger.php';

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(string|array $allowedRoles)
    {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    }

    public function before($params): bool
    {
        $user = Flight::get('user');
        $method = Flight::request()->method;
        $url = Flight::request()->url;

        if (!$user || !isset($user->role)) {
            Logger::error("{$method} {$url} 401 user=guest role=none Unauthenticated role check");
            Flight::json(['error' => 'Unauthorized: authentication required.'], 401);
            return false;
        }

        if (!in_array($user->role, $this->allowedRoles, true)) {
            $userId = $user->id ?? 'unknown';
            Logger::error("{$method} {$url} 403 user={$userId} role={$user->role} Insufficient permissions");
            Flight::json(['error' => 'Forbidden: you do not have permission to perform this action.'], 403);
            return false;
        }

        return true;
    }
}