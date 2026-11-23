<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/MealPlanService.php';

class MealPlanRoutes
{
    #[OA\Get(
        path: '/meal-plans',
        summary: 'Get authenticated user meal plan schedule (optionally filtered by date)',
        tags: ['Meal Plans'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-11-22'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Scheduled meal plan entries'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function getAll(): void
    {
        $date = Flight::request()->query['date'] ?? null;
        $service = new MealPlanService();
        Flight::json($service->get_user_meal_plans(Flight::get('user'), $date), 200);
    }

    #[OA\Post(
        path: '/meal-plans',
        summary: 'Schedule a recipe onto the meal plan',
        tags: ['Meal Plans'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['recipe_id', 'plan_date', 'meal_type'],
                properties: [
                    new OA\Property(property: 'recipe_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plan_date', type: 'string', format: 'date', example: '2026-11-22'),
                    new OA\Property(property: 'meal_type', type: 'string', enum: ['breakfast', 'lunch', 'dinner', 'snack'], example: 'lunch')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Meal plan entry created'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function create(): void
    {
        $service = new MealPlanService();
        Flight::json($service->create_meal_plan(get_json_body(), Flight::get('user')), 201);
    }

    #[OA\Put(
        path: '/meal-plans/{id}',
        summary: 'Update a meal plan entry (Owner or Admin)',
        tags: ['Meal Plans'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'recipe_id', type: 'integer', example: 2),
                    new OA\Property(property: 'plan_date', type: 'string', format: 'date', example: '2026-11-23'),
                    new OA\Property(property: 'meal_type', type: 'string', enum: ['breakfast', 'lunch', 'dinner', 'snack'], example: 'dinner')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Meal plan entry updated'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function update($id): void
    {
        $service = new MealPlanService();
        Flight::json($service->update_meal_plan($id, get_json_body(), Flight::get('user')), 200);
    }

    #[OA\Delete(
        path: '/meal-plans/{id}',
        summary: 'Remove a meal plan entry (Owner or Admin)',
        tags: ['Meal Plans'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Meal plan entry deleted'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function delete($id): void
    {
        $service = new MealPlanService();
        Flight::json($service->delete_meal_plan($id, Flight::get('user')), 200);
    }
}

Flight::route('GET /meal-plans', [MealPlanRoutes::class, 'getAll'])->addMiddleware(new AuthMiddleware());
Flight::route('POST /meal-plans', [MealPlanRoutes::class, 'create'])->addMiddleware(new AuthMiddleware());
Flight::route('PUT /meal-plans/@id', [MealPlanRoutes::class, 'update'])->addMiddleware(new AuthMiddleware());
Flight::route('DELETE /meal-plans/@id', [MealPlanRoutes::class, 'delete'])->addMiddleware(new AuthMiddleware());
