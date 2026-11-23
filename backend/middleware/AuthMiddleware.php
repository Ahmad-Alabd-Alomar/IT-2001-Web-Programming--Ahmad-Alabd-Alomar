<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Logger.php';

class AuthMiddleware
{
    public function before($params): bool
    {
        $authHeader = $this->getAuthorizationHeader();
        $method = Flight::request()->method;
        $url = Flight::request()->url;

        if (!$authHeader || !preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)) {
            Logger::error("{$method} {$url} 401 user=guest role=none Missing or malformed Authorization header");
            Flight::json(['error' => 'Unauthorized: missing or invalid token.'], 401);
            return false;
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded);
            return true;
        } catch (ExpiredException $e) {
            Logger::error("{$method} {$url} 401 user=guest role=none Expired JWT token");
            Flight::json(['error' => 'Unauthorized: token has expired.'], 401);
            return false;
        } catch (\Throwable $e) {
            Logger::error("{$method} {$url} 401 user=guest role=none Invalid JWT token");
            Flight::json(['error' => 'Unauthorized: invalid token.'], 401);
            return false;
        }
    }

    private function getAuthorizationHeader(): ?string
    {
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strcasecmp($key, 'Authorization') === 0) {
                    return trim($value);
                }
            }
        }
        return null;
    }
}