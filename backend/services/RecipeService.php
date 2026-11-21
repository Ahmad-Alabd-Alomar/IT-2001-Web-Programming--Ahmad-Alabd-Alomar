<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RecipeDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class RecipeService extends BaseService
{
    private CategoryDao $categoryDao;

    public function __construct()
    {
        parent::__construct(new RecipeDao());
        $this->categoryDao = new CategoryDao();
    }

    public function get_recipes(?string $search = null, ?int $categoryId = null): array
    {
        if ($search !== null && trim($search) !== '') {
            return $this->dao->search_by_title(trim($search));
        }
        if ($categoryId !== null && $categoryId > 0) {
            return $this->dao->get_by_category_id($categoryId);
        }
        return $this->dao->get_all_with_details();
    }

    public function get_by_user($userId): array
    {
        return $this->dao->get_by_user_id($userId);
    }

    public function create_recipe(array $data, $currentUser): array
    {
        $title = trim($data['title'] ?? '');
        $categoryId = (int)($data['category_id'] ?? 0);

        if ($title === '') {
            throw new Exception("Recipe title is required.", 400);
        }
        if ($categoryId <= 0 || !$this->categoryDao->get_by_id($categoryId)) {
            throw new Exception("A valid category_id is required.", 400);
        }

        $prepTime = max(0, (int)($data['prep_time_minutes'] ?? 0));
        $cookTime = max(0, (int)($data['cook_time_minutes'] ?? 0));
        $calories = max(0, (int)($data['calories'] ?? 0));

        return $this->dao->add([
            'user_id'           => (int)$currentUser->id,
            'category_id'       => $categoryId,
            'title'             => $title,
            'description'       => trim($data['description'] ?? ''),
            'prep_time_minutes' => $prepTime,
            'cook_time_minutes' => $cookTime,
            'calories'          => $calories
        ]);
    }

    public function update_recipe($id, array $data, $currentUser): array
    {
        $recipe = $this->get_by_id($id);

        if ((int)$recipe['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only modify your own recipes.", 403);
        }

        $payload = [];

        if (isset($data['title'])) {
            $title = trim($data['title']);
            if ($title === '') {
                throw new Exception("Recipe title cannot be empty.", 400);
            }
            $payload['title'] = $title;
        }

        if (isset($data['category_id'])) {
            $categoryId = (int)$data['category_id'];
            if ($categoryId <= 0 || !$this->categoryDao->get_by_id($categoryId)) {
                throw new Exception("Invalid category_id provided.", 400);
            }
            $payload['category_id'] = $categoryId;
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = trim((string)$data['description']);
        }
        if (isset($data['prep_time_minutes'])) {
            $payload['prep_time_minutes'] = max(0, (int)$data['prep_time_minutes']);
        }
        if (isset($data['cook_time_minutes'])) {
            $payload['cook_time_minutes'] = max(0, (int)$data['cook_time_minutes']);
        }
        if (isset($data['calories'])) {
            $payload['calories'] = max(0, (int)$data['calories']);
        }

        if (empty($payload)) {
            throw new Exception("No valid fields provided for update.", 400);
        }

        return $this->dao->update($id, $payload);
    }

    public function delete_recipe($id, $currentUser): array
    {
        $recipe = $this->get_by_id($id);

        if ((int)$recipe['user_id'] !== (int)$currentUser->id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only delete your own recipes.", 403);
        }

        $this->dao->delete($id);
        return ['message' => 'Recipe deleted successfully.'];
    }
}
