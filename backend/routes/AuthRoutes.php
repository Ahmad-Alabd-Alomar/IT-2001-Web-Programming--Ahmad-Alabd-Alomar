<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/UserService.php';

class AuthRoutes
{
    #[OA\Post(
        path: '/auth/register',
        summary: 'Register a new user account',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['full_name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string', example: 'Ahmad Alomar'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ahmad@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Secret123')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User registered and JWT issued'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 409, description: 'Email already registered')
        ]
    )]
    public static function register(): void
    {
        $service = new UserService();
        $result = $service->register(get_json_body());
        Flight::json($result, 201);
    }

    #[OA\Post(
        path: '/auth/login',
        summary: 'Authenticate user and issue JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ahmad@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Secret123')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public static function login(): void
    {
        $service = new UserService();
        $result = $service->login(get_json_body());
        Flight::json($result, 200);
    }

    #[OA\Get(
        path: '/auth/me',
        summary: 'Get currently authenticated user profile',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user details'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function me(): void
    {
        $currentUser = Flight::get('user');
        $service = new UserService();
        Flight::json($service->get_user_by_id($currentUser->id), 200);
    }
}

Flight::route('POST /auth/register', [AuthRoutes::class, 'register']);
Flight::route('POST /auth/login', [AuthRoutes::class, 'login']);
Flight::route('GET /auth/me', [AuthRoutes::class, 'me'])->addMiddleware(new AuthMiddleware());