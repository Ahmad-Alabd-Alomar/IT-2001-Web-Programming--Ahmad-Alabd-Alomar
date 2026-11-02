<?php
require_once __DIR__ . '/BaseDao.php';

class ReviewDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('reviews');
    }

    /**
     * Retrieves all reviews submitted for a specific recipe.
     */
    public function get_by_recipe_id($recipe_id)
    {
        $query = "SELECT rev.*, u.full_name AS author_name
                  FROM reviews rev
                  JOIN users u ON rev.user_id = u.id
                  WHERE rev.recipe_id = :recipe_id
                  ORDER BY rev.created_at DESC";
        return $this->query($query, ['recipe_id' => $recipe_id]);
    }

    /**
     * Calculates the average star rating for a recipe.
     */
    public function get_average_rating($recipe_id)
    {
        $result = $this->query_unique(
            "SELECT AVG(rating) AS average_rating, COUNT(*) AS total_reviews FROM reviews WHERE recipe_id = :recipe_id",
            ['recipe_id' => $recipe_id]
        );
        return [
            'average_rating' => $result['average_rating'] ? round((float)$result['average_rating'], 1) : 0,
            'total_reviews'  => (int)$result['total_reviews']
        ];
    }
}