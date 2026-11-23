<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/middleware/Logger.php';
require_once __DIR__ . '/middleware/GlobalMiddleware.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';

// CORS headers for SPA frontend and Swagger UI
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Helper to parse JSON or form request payloads safely
function get_json_body(): array
{
    $raw = Flight::request()->getBody();
    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    return Flight::request()->data->getData() ?? [];
}

// Register global request logger and centralized exception handler
GlobalMiddleware::register();

// Health check endpoint
Flight::route('GET /', function () {
    Flight::json([
        'status'  => 'ok',
        'service' => 'Recipe & Meal Prep Tracker REST API',
        'docs'    => './docs/'
    ]);
});

// Load REST route definitions
require_once __DIR__ . '/routes/AuthRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/RecipeRoutes.php';
require_once __DIR__ . '/routes/MealPlanRoutes.php';
require_once __DIR__ . '/routes/ReviewRoutes.php';
require_once __DIR__ . '/routes/ExternalRoutes.php';

Flight::start();