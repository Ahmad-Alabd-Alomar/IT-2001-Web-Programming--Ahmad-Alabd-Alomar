<?php

require_once __DIR__ . '/Logger.php';

class GlobalMiddleware
{
    public static function register(): void
    {
        // Record request start timestamp
        Flight::before('start', function () {
            Flight::set('request_start_time', microtime(true));
        });

        // Log every completed HTTP request to info.log
        Flight::after('start', function () {
            $start = Flight::get('request_start_time') ?? microtime(true);
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $method = Flight::request()->method;
            $url = Flight::request()->url;
            $status = Flight::response()->status() ?: 200;

            $user = Flight::get('user');
            $userId = $user->id ?? 'guest';
            $role = $user->role ?? 'none';

            Logger::info("{$method} {$url} {$status} user={$userId} role={$role} {$durationMs}ms");
        });

        // Catch all unhandled exceptions without exposing stack traces or SQL details
        Flight::map('error', function (\Throwable $ex) {
            $start = Flight::get('request_start_time') ?? microtime(true);
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $method = Flight::request()->method;
            $url = Flight::request()->url;

            $user = Flight::get('user');
            $userId = $user->id ?? 'guest';
            $role = $user->role ?? 'none';

            $code = $ex->getCode();
            $isClientError = is_int($code) && $code >= 400 && $code < 500;
            $httpStatus = $isClientError ? $code : 500;

            $exceptionClass = (new \ReflectionClass($ex))->getShortName();
            $file = basename($ex->getFile());
            $line = $ex->getLine();

            Logger::error("{$method} {$url} {$httpStatus} user={$userId} {$exceptionClass}: {$ex->getMessage()} ({$file}:{$line})");
            Logger::info("{$method} {$url} {$httpStatus} user={$userId} role={$role} {$durationMs}ms");

            $publicMessage = $isClientError
                ? $ex->getMessage()
                : 'An internal server error occurred. Please try again later.';

            Flight::json(['error' => $publicMessage], $httpStatus);
            Flight::response()->send();
        });
    }
}