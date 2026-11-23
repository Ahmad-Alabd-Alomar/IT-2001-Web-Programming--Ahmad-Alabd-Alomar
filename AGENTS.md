# Agent Context & System Rules

## Project Overview
**Recipe & Meal Prep Tracker** — A single-page web application (SPA) with a FlightPHP REST backend and MySQL database built for the IT 2001 Web Programming course.

## Technology Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+), jQuery.
- **Backend:** PHP 8.2+, FlightPHP Framework, Firebase PHP-JWT, Swagger-PHP (`zircote/swagger-php`), PHP PDO.
- **Database:** MySQL / MariaDB (InnoDB).
- **External API:** TheMealDB REST API (via cURL with timeout and fallback handling).
- **Documentation & Tools:** DataGrip, Mermaid.js, OpenAPI 3.0 / Swagger UI, Composer, Git, XAMPP.

## Architecture & Code Rules
1. **Strict Code Separation:** Backend and frontend code must never be mixed. `frontend/` contains strictly static HTML, CSS, and JS. All server communication occurs via AJAX calls returning JSON.
2. **Layered Backend Architecture:**
   - `backend/routes/` defines FlightPHP HTTP routes and OpenAPI 3.0 attributes only.
   - `backend/services/` enforces input validation, business rules, `PASSWORD_BCRYPT` hashing, JWT issuance, resource ownership checks, and external API fault handling.
   - `backend/dao/` inherits from `BaseDao.php` and executes prepared PDO statements exclusively. Direct SQL queries outside DAO classes are prohibited.
3. **Authentication, RBAC & Middleware:**
   - `AuthMiddleware` validates `Authorization: Bearer <token>` JWTs (`401` on missing, invalid, or expired tokens).
   - `RoleMiddleware` enforces role-based access control (`admin` vs `user`, returning `403` when permissions are insufficient).
   - `GlobalMiddleware` logs all HTTP requests to `backend/logs/info.log` and catches unhandled exceptions into `backend/logs/error.log` without exposing stack traces, SQL errors, or file paths.
4. **Secrets Management:** Database credentials, `JWT_SECRET`, and external API settings live in `backend/config/config.php` (git-ignored) with a committed template in `backend/config/config.example.php`.

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
│   ├── .htaccess
│   ├── composer.json
│   ├── composer.lock
│   ├── index.php
│   ├── config/
│   │   ├── config.example.php
│   │   └── config.php (git-ignored)
│   ├── dao/
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
│   └── services/
└── frontend/
    ├── css/
    ├── js/
    ├── views/
    └── index.html