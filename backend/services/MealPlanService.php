<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MealPlanDao.php';
require_once __DIR__ . '/../dao/RecipeDao.php';

class MealPlanService extends BaseService
{
    private RecipeDao $recipeDao;
    private array $validMealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];

    public function __construct()
    {
        parent::__construct(new MealPlanDao());
        $this->recipeDao = new RecipeDao();
    }

    public function get_user_meal_plans($currentUser, ?string $date = null): array
    {
        $userId = (int)$currentUser->id;
        if ($date !== null && trim($date) !== '') {
            $this->validate_date($date);
            return $this->dao->get_by_user_and_date($userId, $date);
        }
        return $this->dao->get_by_user_id($userId);
    }

    public function create_meal_plan(array $data, $currentUser): array
    {
        $recipeId = (int)($data['recipe_id'] ?? 0);
        $planDate = trim($data['plan_date'] ?? '');
        $mealType = strtolower(trim($data['meal_type'] ?? ''));

        if ($recipeId <= 0 || !$this->recipeDao->get_by_id($recipeId)) {
            throw new Exception("A valid recipe_id is required.", 400);
        }

        $this->validate_date($planDate);

        if (!in_array($mealType, $this->validMealTypes, true)) {
            throw new Exception("meal_type must be one of: breakfast, lunch, dinner, snack.", 400);
        }

        return $this->dao->add([
            'user_id'   => (int)$currentUser->id,
            'recipe_id' => $recipeId,
            'plan_date' => $planDate,
            'meal_type' => $mealType
        ]);
    }

    public function update_meal_plan($id, array $data, $currentUser): array
    {
        $entry = $this->get_by_id($id);

        if ((int)$entry['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only modify your own meal plan.", 403);
        }

        $payload = [];

        if (isset($data['recipe_id'])) {
            $recipeId = (int)$data['recipe_id'];
            if ($recipeId <= 0 || !$this->recipeDao->get_by_id($recipeId)) {
                throw new Exception("Invalid recipe_id provided.", 400);
            }
            $payload['recipe_id'] = $recipeId;
        }

        if (isset($data['plan_date'])) {
            $planDate = trim($data['plan_date']);
            $this->validate_date($planDate);
            $payload['plan_date'] = $planDate;
        }

        if (isset($data['meal_type'])) {
            $mealType = strtolower(trim($data['meal_type']));
            if (!in_array($mealType, $this->validMealTypes, true)) {
                throw new Exception("meal_type must be one of: breakfast, lunch, dinner, snack.", 400);
            }
            $payload['meal_type'] = $mealType;
        }

        if (empty($payload)) {
            throw new Exception("No valid fields provided for update.", 400);
        }

        return $this->dao->update($id, $payload);
    }

    public function delete_meal_plan($id, $currentUser): array
    {
        $entry = $this->get_by_id($id);

        if ((int)$entry['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only delete your own meal plan entries.", 403);
        }

        $this->dao->delete($id);
        return ['message' => 'Meal plan entry deleted successfully.'];
    }

    private function validate_date(string $date): void
    {
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            throw new Exception("plan_date must be a valid date in YYYY-MM-DD format.", 400);
        }
    }
}