<?php

use OpenApi\Attributes as OA;

require_once __DIR__ . '/../services/ReviewService.php';

class ReviewRoutes
{
    #[OA\Get(
        path: '/recipes/{id}/reviews',
        summary: 'Get all reviews and average star rating for a recipe',
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rating statistics and review list'),
            new OA\Response(response: 404, description: 'Recipe not found')
        ]
    )]
    public static function getByRecipe($id): void
    {
        $service = new ReviewService();
        Flight::json($service->get_recipe_reviews($id), 200);
    }

    #[OA\Post(
        path: '/reviews',
        summary: 'Submit a rating and review for a recipe',
        tags: ['Reviews'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['recipe_id', 'rating'],
                properties: [
                    new OA\Property(property: 'recipe_id', type: 'integer', example: 1),
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: 'comment', type: 'string', example: 'Super fresh and easy to prep ahead!')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Review created'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public static function create(): void
    {
        $service = new ReviewService();
        Flight::json($service->create_review(get_json_body(), Flight::get('user')), 201);
    }

    #[OA\Put(
        path: '/reviews/{id}',
        summary: 'Update an existing review (Owner or Admin)',
        tags: ['Reviews'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 4),
                    new OA\Property(property: 'comment', type: 'string', example: 'Updated comment after making it a second time.')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Review updated'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function update($id): void
    {
        $service = new ReviewService();
        Flight::json($service->update_review($id, get_json_body(), Flight::get('user')), 200);
    }

    #[OA\Delete(
        path: '/reviews/{id}',
        summary: 'Delete a review (Owner or Admin)',
        tags: ['Reviews'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Review deleted'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public static function delete($id): void
    {
        $service = new ReviewService();
        Flight::json($service->delete_review($id, Flight::get('user')), 200);
    }
}

Flight::route('GET /recipes/@id/reviews', [ReviewRoutes::class, 'getByRecipe']);
Flight::route('POST /reviews', [ReviewRoutes::class, 'create'])->addMiddleware(new AuthMiddleware());
Flight::route('PUT /reviews/@id', [ReviewRoutes::class, 'update'])->addMiddleware(new AuthMiddleware());
Flight::route('DELETE /reviews/@id', [ReviewRoutes::class, 'delete'])->addMiddleware(new AuthMiddleware());