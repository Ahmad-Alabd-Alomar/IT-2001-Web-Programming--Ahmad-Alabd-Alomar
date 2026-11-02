# Agent Context & System Rules

## Project Overview
**Recipe & Meal Prep Tracker** — A single-page web application (SPA) with a FlightPHP REST backend and MySQL database built for the IT 2001 Web Programming course.

## Technology Stack
* **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+), jQuery.
* **Backend:** PHP 8.2+, FlightPHP Framework, PHP PDO.
* **Database:** MySQL / MariaDB (InnoDB).
* **Documentation & Tools:** DataGrip, Mermaid.js, OpenAPI / Swagger, Git, XAMPP.

## Architecture & Code Rules
1. **Strict Code Separation:** Backend and frontend code must never be mixed. `frontend/` contains strictly static HTML, CSS, and JS. All server communication occurs via AJAX calls returning JSON.
2. **Database Security:** All database interactions must use PHP PDO with prepared statements to prevent SQL injection.
3. **Secrets Management:** Secrets and database credentials live in `backend/config/config.php` (git-ignored) with a committed template in `backend/config/config.example.php`.
4. **DAO Pattern:** All entity data access must inherit from `BaseDao.php` and use prepared statements. Direct PDO queries outside DAO classes are prohibited.

## Directory Structure
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
│   ├── logs/
│   │   └── .gitkeep
│   ├── middleware/
│   ├── routes/
│   └── services/
└── frontend/
    ├── css/
    ├── js/
    ├── views/
    └── index.html