<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/UserService.php';

class UserRoutes
{
    #[OA\Get(
        path: '/users',
        summary: 'List all registered users (Admin only)',
        tags: ['Users'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Array of users'),
            new OA\Response(response: 403, description: 'Forbidden: Admin role required')
        ]
    )]
    public static function getAll(): void
    {
        $service = new UserService();
        Flight::json($service->get_all_users(), 200);
    }

    #[OA\Get(
        path: '/users/{id}',
        summary: 'Get a single user by ID',
        tags: ['Users'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'User record'),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    public static function getById($id): void
    {
        $service = new UserService();
        Flight::json($service->get_user_by_id($id), 200);
    }

    #[OA\Put(
        path: '/users/{id}',
        summary: 'Update user profile (Owner or Admin)',
        tags: ['Users'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'full_name', type: 'string', example: 'Ahmad Updated'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ahmad.new@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'NewPass123'),
                    new OA\Property(property: 'role', type: 'string', enum: ['user', 'admin'], example: 'user')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated user record'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function update($id): void
    {
        $service = new UserService();
        Flight::json($service->update_user($id, get_json_body(), Flight::get('user')), 200);
    }

    #[OA\Delete(
        path: '/users/{id}',
        summary: 'Delete a user account (Admin only)',
        tags: ['Users'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function delete($id): void
    {
        $service = new UserService();
        Flight::json($service->delete($id), 200);
    }
}

Flight::route('GET /users', [UserRoutes::class, 'getAll'])
    ->addMiddleware(new AuthMiddleware())
    ->addMiddleware(new RoleMiddleware('admin'));

Flight::route('GET /users/@id', [UserRoutes::class, 'getById'])
    ->addMiddleware(new AuthMiddleware());

Flight::route('PUT /users/@id', [UserRoutes::class, 'update'])
    ->addMiddleware(new AuthMiddleware());

Flight::route('DELETE /users/@id', [UserRoutes::class, 'delete'])
    ->addMiddleware(new AuthMiddleware())
    ->addMiddleware(new RoleMiddleware('admin'));