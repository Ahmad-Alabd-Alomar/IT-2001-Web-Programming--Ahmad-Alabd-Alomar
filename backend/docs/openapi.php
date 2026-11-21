<?php

ini_set('display_errors', '0');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

// Load OpenAPI root specification and all route classes into memory
// so ReflectionAnalyser can inspect their PHP 8 attributes
require_once __DIR__ . '/OpenApiSpec.php';
require_once __DIR__ . '/../routes/AuthRoutes.php';
require_once __DIR__ . '/../routes/UserRoutes.php';
require_once __DIR__ . '/../routes/CategoryRoutes.php';
require_once __DIR__ . '/../routes/RecipeRoutes.php';
require_once __DIR__ . '/../routes/MealPlanRoutes.php';
require_once __DIR__ . '/../routes/ReviewRoutes.php';
require_once __DIR__ . '/../routes/ExternalRoutes.php';

use OpenApi\Generator;
use Psr\Log\NullLogger;

header('Content-Type: application/json; charset=utf-8');

$generator = new Generator(new NullLogger());
$openapi = $generator->generate([
    __DIR__ . '/OpenApiSpec.php',
    __DIR__ . '/../routes'
]);

echo $openapi->toJson();