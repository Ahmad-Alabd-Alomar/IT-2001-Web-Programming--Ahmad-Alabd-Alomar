## Milestone 1: Initial Setup, Static Frontend, and Database Planning

### AI Tools Used
* **Gemini (Google AI)**

### Parts Assisted
1. **Requirements Audit:** Filtering project documents to isolate only Milestone 1 scope without mixing in future milestone deliverables[cite: 1].
2. **Frontend Architecture:** Generating static single-page application (SPA) views (`dashboard.html`, `recipes.html`, `meal-plans.html`, `external-api.html`, `profile.html`, `login.html`, `register.html`) and the jQuery router script (`app.js`)[cite: 1].
3. **Troubleshooting:** Resolving the Apache 403 Forbidden directory access error in XAMPP.
4. **Database Planning:** Structuring 5 core entities (`users`, `categories`, `recipes`, `meal_plans`, `reviews`) and writing a Mermaid ERD diagram[cite: 1].
5. **Documentation:** Generating milestone-compliant `README.md` and context files (`AGENTS.md`, `AI_LOG.md`)[cite: 1].

### Important Prompts Used
* *"im working on a project for my web programing course, my project is a Recipe & Meal Prep Tracker... tell me what to do to finish milestone 1 and make sure i cover every requirment..."*[cite: 1]
* *"work on the static frontend... keep in mind your working on an empty setup so you have to tell me what to do from scratch..."*[cite: 1]
* *"i get this message when i open the page Forbidden You don't have permission to access this resource..."*
* *"for the database planning am required to make a plan... what do you think is better and do it"*[cite: 1]

### AI Mistakes & Corrections



#### Case 1: Generic and Unauthentic AI Log Draft
* **What was wrong:** The initial draft generated for `AI_LOG.md` contained placeholder details (referencing tools like ChatGPT/Claude and fabricated prompts) that did not reflect my actual conversation or workflow.
* **How I noticed:** During code review of the generated documentation, I saw listed AI tools and interactions that were not part of my actual development process.
* **How it was fixed:** I instructed the AI to rewrite `AI_LOG.md` honestly using the actual chat history, documenting real prompts, real troubleshooting steps (e.g., Apache 403 error), and actual corrections made during the session.

---

## Milestone 2: Database Schema & DAO Layer Implementation

### AI Tools Used
* **Gemini (Google AI)**

### Parts Assisted
1. **SQL DDL & Seed Script:** Creating `database/schema.sql` with 5 relational entities (`users`, `categories`, `recipes`, `meal_plans`, `reviews`), foreign key constraints (`ON DELETE CASCADE` / `RESTRICT`), and sample seed data.
2. **BaseDao Implementation:** Implementing `BaseDao.php` with dynamic PDO connection loading from `Config`, SQL injection prevention via prepared statements, and dynamic table column filtering (`only_columns()`).
3. **Entity DAOs:** Writing specialized entity DAOs extending `BaseDao` (`UserDao`, `CategoryDao`, `RecipeDao`, `MealPlanDao`, `ReviewDao`) with custom query methods (e.g., joins, category filters, average rating aggregations).
4. **DAO Verification Script:** Creating `backend/dao/test.php` to run automated CLI/Browser tests for CRUD operations.

### Important Prompts Used
* *"lets start on database, i have datagrip downloaded how do i procced?"*
* *"finished, but how do i make sure the database is created correctly"*
* *"lets create the BaseDao"*


### AI Mistakes & Corrections

#### Case 1: Assumed Direct Execution of Database Creation in DataGrip
* **What was wrong:** The AI assumed I had already executed the database creation commands when I had only opened DataGrip and started XAMPP MySQL.
* **How I noticed:** I had not run any SQL commands yet or set up the `recipe_tracker` schema in MySQL.
* **How it was fixed:** I informed the AI (`"no its not , i just now started mysql in xamp didnt do anything else"`). The AI provided step-by-step instructions to create `database/schema.sql`, set up the data source connection in DataGrip, and run the schema file directly.

#### Case 2: Initial Path Standard Misalignment
* **What was wrong:** The AI initially referenced `backend/rest/dao/` in instructions while the established project repository structure placed DAO files inside `backend/dao/`.
* **How I noticed:** I cross-checked the folder structure established in Milestone 1 and noticed the path discrepancy.
* **How it was fixed:** Aligned all file creations (`BaseDao.php`, Entity DAOs, and `test.php`) to reside directly inside `backend/dao/` to maintain project structure consistency.