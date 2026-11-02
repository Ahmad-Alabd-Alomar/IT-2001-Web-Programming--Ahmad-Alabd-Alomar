<?php
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/CategoryDao.php';
require_once __DIR__ . '/RecipeDao.php';
require_once __DIR__ . '/MealPlanDao.php';
require_once __DIR__ . '/ReviewDao.php';

echo "=== STARTING DAO LAYER TESTS ===\n\n";

try {
    // 1. TEST CATEGORY DAO (Full CRUD)
    echo "--- Testing CategoryDao (CRUD) ---\n";
    $categoryDao = new CategoryDao();

    // CREATE
    $newCategory = $categoryDao->add([
        'name' => 'Snacks & Desserts',
        'description' => 'Light quick bites and sweet treats'
    ]);
    echo "[CREATE] Inserted Category ID: " . $newCategory['id'] . "\n";

    // READ BY ID
    $fetchedCategory = $categoryDao->get_by_id($newCategory['id']);
    echo "[READ] Fetched Name: " . $fetchedCategory['name'] . "\n";

    // UPDATE
    $updatedCategory = $categoryDao->update($newCategory['id'], [
        'name' => 'Healthy Snacks & Desserts',
        'description' => 'Updated healthy options'
    ]);
    echo "[UPDATE] Updated Name: " . $updatedCategory['name'] . "\n";

    // DELETE
    $categoryDao->delete($newCategory['id']);
    echo "[DELETE] Category ID " . $newCategory['id'] . " deleted successfully.\n\n";


    // 2. TEST USER DAO
    echo "--- Testing UserDao ---\n";
    $userDao = new UserDao();
    
    $adminUser = $userDao->get_by_email('admin@recipetracker.com');
    echo "[READ] Admin Found: " . ($adminUser ? $adminUser['full_name'] : 'None') . "\n";

    $regularUsers = $userDao->get_by_role('user');
    echo "[READ] Regular Users Count: " . count($regularUsers) . "\n\n";


    // 3. TEST RECIPE DAO
    echo "--- Testing RecipeDao ---\n";
    $recipeDao = new RecipeDao();

    $recipesWithDetails = $recipeDao->get_all_with_details();
    echo "[JOIN] Total Recipes with Category/Author Details: " . count($recipesWithDetails) . "\n";
    if (!empty($recipesWithDetails)) {
        echo "       Sample Recipe: " . $recipesWithDetails[0]['title'] . " (Category: " . $recipesWithDetails[0]['category_name'] . ")\n";
    }

    $searchResults = $recipeDao->search_by_title('Salad');
    echo "[SEARCH] Search 'Salad' count: " . count($searchResults) . "\n\n";


    // 4. TEST MEAL PLAN DAO
    echo "--- Testing MealPlanDao ---\n";
    $mealPlanDao = new MealPlanDao();

    $userMealPlans = $mealPlanDao->get_by_user_id(2);
    echo "[READ] Meal Plans for User #2: " . count($userMealPlans) . "\n\n";


    // 5. TEST REVIEW DAO
    echo "--- Testing ReviewDao ---\n";
    $reviewDao = new ReviewDao();

    $ratingStats = $reviewDao->get_average_rating(3);
    echo "[AGGREGATE] Recipe #3 Avg Rating: " . $ratingStats['average_rating'] . " (" . $ratingStats['total_reviews'] . " reviews)\n\n";

    echo "=== ALL DAO TESTS PASSED SUCCESSFULLY ===";

} catch (Exception $e) {
    echo "❌ DAO TEST FAILED: " . $e->getMessage();
}