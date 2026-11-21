<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/RecipeDao.php';

class ReviewService extends BaseService
{
    private RecipeDao $recipeDao;

    public function __construct()
    {
        parent::__construct(new ReviewDao());
        $this->recipeDao = new RecipeDao();
    }

    public function get_recipe_reviews($recipeId): array
    {
        if (!$this->recipeDao->get_by_id($recipeId)) {
            throw new Exception("Recipe not found.", 404);
        }

        return [
            'stats'   => $this->dao->get_average_rating($recipeId),
            'reviews' => $this->dao->get_by_recipe_id($recipeId)
        ];
    }

    public function create_review(array $data, $currentUser): array
    {
        $recipeId = (int)($data['recipe_id'] ?? 0);
        $rating = (int)($data['rating'] ?? 0);
        $comment = trim($data['comment'] ?? '');

        if ($recipeId <= 0 || !$this->recipeDao->get_by_id($recipeId)) {
            throw new Exception("A valid recipe_id is required.", 400);
        }

        if ($rating < 1 || $rating > 5) {
            throw new Exception("Rating must be an integer between 1 and 5.", 400);
        }

        return $this->dao->add([
            'user_id'   => (int)$currentUser->id,
            'recipe_id' => $recipeId,
            'rating'    => $rating,
            'comment'   => $comment
        ]);
    }

    public function update_review($id, array $data, $currentUser): array
    {
        $review = $this->get_by_id($id);

        if ((int)$review['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only modify your own review.", 403);
        }

        $payload = [];

        if (isset($data['rating'])) {
            $rating = (int)$data['rating'];
            if ($rating < 1 || $rating > 5) {
                throw new Exception("Rating must be an integer between 1 and 5.", 400);
            }
            $payload['rating'] = $rating;
        }

        if (array_key_exists('comment', $data)) {
            $payload['comment'] = trim((string)$data['comment']);
        }

        if (empty($payload)) {
            throw new Exception("No valid fields provided for update.", 400);
        }

        return $this->dao->update($id, $payload);
    }

    public function delete_review($id, $currentUser): array
    {
        $review = $this->get_by_id($id);

        if ((int)$review['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only delete your own review.", 403);
        }

        $this->dao->delete($id);
        return ['message' => 'Review deleted successfully.'];
    }
}