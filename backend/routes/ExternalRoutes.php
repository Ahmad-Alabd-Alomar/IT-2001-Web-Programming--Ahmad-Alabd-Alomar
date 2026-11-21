<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/ExternalMealService.php';

class ExternalRoutes
{
    #[OA\Get(
        path: '/external/meals',
        summary: 'Search meal inspiration from TheMealDB external API (with timeout & fallback)',
        tags: ['External API'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Chicken'),
                description: 'Meal search keyword (returns a random meal if omitted)'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'External meal suggestions or graceful fallback response')
        ]
    )]
    public static function searchMeals(): void
    {
        $query = Flight::request()->query['q'] ?? null;
        $service = new ExternalMealService();
        Flight::json($service->search_meals($query), 200);
    }
}

Flight::route('GET /external/meals', [ExternalRoutes::class, 'searchMeals']);
