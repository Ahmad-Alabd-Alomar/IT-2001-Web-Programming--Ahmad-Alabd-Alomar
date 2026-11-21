<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/CategoryService.php';

class CategoryRoutes
{
    #[OA\Get(
        path: '/categories',
        summary: 'List all meal categories',
        tags: ['Categories'],
        responses: [
            new OA\Response(response: 200, description: 'List of categories')
        ]
    )]
    public static function getAll(): void
    {
        $service = new CategoryService();
        Flight::json($service->get_all(), 200);
    }

    #[OA\Get(
        path: '/categories/{id}',
        summary: 'Get a single category by ID',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category details'),
            new OA\Response(response: 404, description: 'Category not found')
        ]
    )]
    public static function getById($id): void
    {
        $service = new CategoryService();
        Flight::json($service->get_by_id($id), 200);
    }

    #[OA\Post(
        path: '/categories',
        summary: 'Create a new category (Admin only)',
        tags: ['Categories'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'High Protein'),
                    new OA\Property(property: 'description', type: 'string', example: 'Meals with 30g+ protein per serving')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created'),
            new OA\Response(response: 403, description: 'Forbidden: Admin role required')
        ]
    )]
    public static function create(): void
    {
        $service = new CategoryService();
        Flight::json($service->create_category(get_json_body()), 201);
    }

    #[OA\Put(
        path: '/categories/{id}',
        summary: 'Update a category (Admin only)',
        tags: ['Categories'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Keto & Low Carb'),
                    new OA\Property(property: 'description', type: 'string', example: 'Low carbohydrate recipes')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated'),
            new OA\Response(response: 403, description: 'Forbidden: Admin role required')
        ]
    )]
    public static function update($id): void
    {
        $service = new CategoryService();
        Flight::json($service->update_category($id, get_json_body()), 200);
    }

    #[OA\Delete(
        path: '/categories/{id}',
        summary: 'Delete a category (Admin only)',
        tags: ['Categories'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted'),
            new OA\Response(response: 400, description: 'Category in use by existing recipes'),
            new OA\Response(response: 403, description: 'Forbidden: Admin role required')
        ]
    )]
    public static function delete($id): void
    {
        $service = new CategoryService();
        Flight::json($service->delete_category($id), 200);
    }
}

Flight::route('GET /categories', [CategoryRoutes::class, 'getAll']);
Flight::route('GET /categories/@id', [CategoryRoutes::class, 'getById']);

Flight::route('POST /categories', [CategoryRoutes::class, 'create'])
    ->addMiddleware(new AuthMiddleware())
    ->addMiddleware(new RoleMiddleware('admin'));

Flight::route('PUT /categories/@id', [CategoryRoutes::class, 'update'])
    ->addMiddleware(new AuthMiddleware())
    ->addMiddleware(new RoleMiddleware('admin'));

Flight::route('DELETE /categories/@id', [CategoryRoutes::class, 'delete'])
    ->addMiddleware(new AuthMiddleware())
    ->addMiddleware(new RoleMiddleware('admin'));