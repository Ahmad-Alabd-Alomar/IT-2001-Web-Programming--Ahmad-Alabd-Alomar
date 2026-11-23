# Recipe & Meal Prep Tracker

A responsive single-page web application (SPA) designed for organizing personal recipes, scheduling weekly meal plans, tracking nutritional intake, and discovering new dishes. Developed as part of the Web Programming course (IT 2001).

---

## 📌 Project Description

The **Recipe & Meal Prep Tracker** provides a central platform for users to manage food preparation workflows, browse recipe catalogs, schedule daily meals, and rate recipes. The application features dynamic client-side rendering as a Single-Page Application (SPA) with a FlightPHP RESTful backend architecture, JWT authentication, role-based authorization, and external API integration.

---

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+), jQuery
- **Backend:** PHP 8.2+, FlightPHP Framework (REST API), Firebase PHP-JWT, Swagger-PHP (`zircote/swagger-php`)
- **Database:** MySQL / MariaDB (PHP PDO data access layer)
- **Documentation:** OpenAPI 3.0 / Swagger UI
- **External API:** TheMealDB API (`https://www.themealdb.com/api/json/v1/1`)

---

## 📁 Project Structure

```text
IT-2001-Web-Programming--Ahmad-Alabd-Alomar/
├── .gitignore
├── README.md
├── AGENTS.md
├── database/
│   └── schema.sql
├── docs/
│   └── AI_LOG.md
├── backend/
│   ├── .htaccess
│   ├── composer.json
│   ├── composer.lock
│   ├── index.php
│   ├── config/
│   │   ├── config.example.php
│   │   └── config.php (git-ignored)
│   ├── dao/
│   │   ├── BaseDao.php
│   │   ├── CategoryDao.php
│   │   ├── MealPlanDao.php
│   │   ├── RecipeDao.php
│   │   ├── ReviewDao.php
│   │   ├── UserDao.php
│   │   └── test.php
│   ├── docs/
│   │   ├── index.html
│   │   ├── openapi.php
│   │   └── OpenApiSpec.php
│   ├── logs/
│   │   └── .gitkeep
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GlobalMiddleware.php
│   │   ├── Logger.php
│   │   └── RoleMiddleware.php
│   ├── routes/
│   │   ├── AuthRoutes.php
│   │   ├── CategoryRoutes.php
│   │   ├── ExternalRoutes.php
│   │   ├── MealPlanRoutes.php
│   │   ├── RecipeRoutes.php
│   │   ├── ReviewRoutes.php
│   │   └── UserRoutes.php
│   └── services/
│       ├── BaseService.php
│       ├── CategoryService.php
│       ├── ExternalMealService.php
│       ├── MealPlanService.php
│       ├── RecipeService.php
│       ├── ReviewService.php
│       └── UserService.php
└── frontend/
    ├── css/
    │   └── styles.css
    ├── js/
    │   └── app.js
    ├── views/
    │   ├── dashboard.html
    │   ├── external-api.html
    │   ├── login.html
    │   ├── meal-plans.html
    │   ├── profile.html
    │   ├── recipes.html
    │   └── register.html
    └── index.html