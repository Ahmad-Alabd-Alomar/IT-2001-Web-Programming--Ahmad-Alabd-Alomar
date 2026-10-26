# Agent Context & System Rules

## Project Overview
**Recipe & Meal Prep Tracker** — A single-page web application (SPA) with a FlightPHP REST backend and MySQL database built for the IT 2001 Web Programming course[cite: 1].

## Technology Stack
* **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+), jQuery[cite: 1].
* **Backend:** PHP 8.2+, FlightPHP Framework, PHP PDO[cite: 1].
* **Database:** MySQL / MariaDB[cite: 1].
* **Documentation & Tools:** Mermaid.js (ERD), OpenAPI / Swagger, Git, XAMPP[cite: 1, 2].

## Architecture & Code Rules
1. **Strict Code Separation:** Backend and frontend code must never be mixed[cite: 1]. `frontend/` contains strictly static HTML, CSS, and JS[cite: 1]. All server communication will occur via AJAX calls returning JSON[cite: 1].
2. **Database Security:** All database interactions must use PHP PDO with prepared statements to prevent SQL injection[cite: 2].
3. **Secrets Management:** Secrets, database credentials, and API keys must live in `backend/config/config.php` (git-ignored) with a committed template in `backend/config/config.example.php`[cite: 1, 2].
4. **AI Assistant Constraints:** AI tools must not assume direct local file creation access. All generated output must be provided in explicit copy-pasteable code blocks.

## Directory Structure
```text
IT-2001-Web-Programming--Ahmad-Alabd-Alomar/
├── .gitignore
├── README.md
├── AGENTS.md
├── docs/
│   └── AI_LOG.md
├── backend/
│   ├── config/
│   │   └── config.example.php
│   ├── dao/
│   ├── logs/
│   ├── middleware/
│   ├── routes/
│   └── services/
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