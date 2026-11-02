<?php
require_once __DIR__ . '/BaseDao.php';

class RecipeDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('recipes');
    }

    /**
     * Retrieves all recipes with category and creator names joined.
     */
    public function get_all_with_details()
    {
        $query = "SELECT r.*, c.name AS category_name, u.full_name AS author_name 
                  FROM recipes r
                  JOIN categories c ON r.category_id = c.id
                  JOIN users u ON r.user_id = u.id
                  ORDER BY r.created_at DESC";
        return $this->query($query);
    }

    /**
     * Retrieves recipes created by a specific user.
     */
    public function get_by_user_id($user_id)
    {
        return $this->query(
            "SELECT * FROM recipes WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $user_id]
        );
    }

    /**
     * Retrieves recipes belonging to a specific category.
     */
    public function get_by_category_id($category_id)
    {
        return $this->query(
            "SELECT * FROM recipes WHERE category_id = :category_id ORDER BY created_at DESC",
            ['category_id' => $category_id]
        );
    }

    /**
     * Searches recipes by title keyword.
     */
    public function search_by_title($keyword)
    {
        return $this->query(
            "SELECT * FROM recipes WHERE title LIKE :keyword ORDER BY created_at DESC",
            ['keyword' => '%' . $keyword . '%']
        );
    }
}
