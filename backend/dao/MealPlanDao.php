<?php
require_once __DIR__ . '/BaseDao.php';

class MealPlanDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('meal_plans');
    }

    /**
     * Retrieves all meal plan entries for a specific user, joined with recipe details.
     */
    public function get_by_user_id($user_id)
    {
        $query = "SELECT mp.*, r.title AS recipe_title, r.calories
                  FROM meal_plans mp
                  JOIN recipes r ON mp.recipe_id = r.id
                  WHERE mp.user_id = :user_id
                  ORDER BY mp.plan_date ASC";
        return $this->query($query, ['user_id' => $user_id]);
    }

    /**
     * Retrieves a user's scheduled meals for a specific date.
     */
    public function get_by_user_and_date($user_id, $plan_date)
    {
        $query = "SELECT mp.*, r.title AS recipe_title
                  FROM meal_plans mp
                  JOIN recipes r ON mp.recipe_id = r.id
                  WHERE mp.user_id = :user_id AND mp.plan_date = :plan_date
                  ORDER BY mp.meal_type ASC";
        return $this->query($query, [
            'user_id' => $user_id,
            'plan_date' => $plan_date
        ]);
    }
}
