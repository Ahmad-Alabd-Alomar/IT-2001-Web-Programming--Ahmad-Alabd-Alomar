<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/RecipeService.php';

class RecipeRoutes
{
    #[OA\Get(
        path: '/recipes',
        summary: 'Browse all recipes with optional search or category filter',
        tags: ['Recipes'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Filter recipes by title keyword'),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Filter recipes by category ID')
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of recipes')
        ]
    )]
    public static function getAll(): void
    {
        $search = Flight::request()->query['search'] ?? null;
        $categoryId = isset(Flight::request()->query['category_id'])
            ? (int)Flight::request()->query['category_id']
            : null;

        $service = new RecipeService();
        Flight::json($service->get_recipes($search, $categoryId), 200);
    }

    #[OA\Get(
        path: '/recipes/my',
        summary: 'List recipes created by the currently logged-in user',
        tags: ['Recipes'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'User recipes'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function getMyRecipes(): void
    {
        $currentUser = Flight::get('user');
        $service = new RecipeService();
        Flight::json($service->get_by_user($currentUser->id), 200);
    }

    #[OA\Get(
        path: '/recipes/{id}',
        summary: 'Get a single recipe by ID',
        tags: ['Recipes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recipe details'),
            new OA\Response(response: 404, description: 'Recipe not found')
        ]
    )]
    public static function getById($id): void
    {
        $service = new RecipeService();
        Flight::json($service->get_by_id($id), 200);
    }

    #[OA\Post(
        path: '/recipes',
        summary: 'Create a new recipe',
        tags: ['Recipes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['category_id', 'title'],
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 2),
                    new OA\Property(property: 'title', type: 'string', example: 'Grilled Chicken Quinoa Bowl'),
                    new OA\Property(property: 'description', type: 'string', example: 'Seasoned chicken breast over fluffy quinoa and roasted vegetables.'),
                    new OA\Property(property: 'prep_time_minutes', type: 'integer', example: 15),
                    new OA\Property(property: 'cook_time_minutes', type: 'integer', example: 20),
                    new OA\Property(property: 'calories', type: 'integer', example: 510)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Recipe created'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function create(): void
    {
        $service = new RecipeService();
        Flight::json($service->create_recipe(get_json_body(), Flight::get('user')), 201);
    }

    #[OA\Put(
        path: '/recipes/{id}',
        summary: 'Update an existing recipe (Owner or Admin)',
        tags: ['Recipes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 3),
                    new OA\Property(property: 'title', type: 'string', example: 'Updated Chicken Bowl'),
                    new OA\Property(property: 'description', type: 'string', example: 'Updated meal prep notes'),
                    new OA\Property(property: 'prep_time_minutes', type: 'integer', example: 10),
                    new OA\Property(property: 'cook_time_minutes', type: 'integer', example: 25),
                    new OA\Property(property: 'calories', type: 'integer', example: 490)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Recipe updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Recipe not found')
        ]
    )]
    public static function update($id): void
    {
        $service = new RecipeService();
        Flight::json($service->update_recipe($id, get_json_body(), Flight::get('user')), 200);
    }

    #[OA\Delete(
        path: '/recipes/{id}',
        summary: 'Delete a recipe (Owner or Admin)',
        tags: ['Recipes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recipe deleted'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Recipe not found')
        ]
    )]
    public static function delete($id): void
    {
        $service = new RecipeService();
        Flight::json($service->delete_recipe($id, Flight::get('user')), 200);
    }
}

Flight::route('GET /recipes', [RecipeRoutes::class, 'getAll']);
Flight::route('GET /recipes/my', [RecipeRoutes::class, 'getMyRecipes'])->addMiddleware(new AuthMiddleware());
Flight::route('GET /recipes/@id', [RecipeRoutes::class, 'getById']);
Flight::route('POST /recipes', [RecipeRoutes::class, 'create'])->addMiddleware(new AuthMiddleware());
Flight::route('PUT /recipes/@id', [RecipeRoutes::class, 'update'])->addMiddleware(new AuthMiddleware());
Flight::route('DELETE /recipes/@id', [RecipeRoutes::class, 'delete'])->addMiddleware(new AuthMiddleware());