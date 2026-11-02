-- Disable foreign key checks for clean recreation
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS meal_plans;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Create Database
CREATE DATABASE IF NOT EXISTS recipe_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipe_tracker;

-- 1. USERS TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CATEGORIES TABLE
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. RECIPES TABLE
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    prep_time_minutes INT NOT NULL DEFAULT 0,
    cook_time_minutes INT NOT NULL DEFAULT 0,
    calories INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recipes_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_recipes_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. MEAL_PLANS TABLE
CREATE TABLE meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    plan_date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_meal_plans_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_meal_plans_recipe FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. REVIEWS TABLE
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_recipe FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- SAMPLE SEED DATA
-- ========================================================

INSERT INTO users (id, full_name, email, password, role) VALUES
(1, 'System Admin', 'admin@recipetracker.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1aKxKk6J0M1G2H3I4J5K6L7M8N9O0P1', 'admin'),
(2, 'Jane Doe', 'user@recipetracker.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1aKxKk6J0M1G2H3I4J5K6L7M8N9O0P1', 'user');

INSERT INTO categories (id, name, description) VALUES
(1, 'Breakfast', 'Morning meals and breakfast preps'),
(2, 'Lunch', 'Quick midday meals and light bowls'),
(3, 'Dinner', 'Hearty evening preps and main courses'),
(4, 'Vegan', '100% plant-based recipes');

INSERT INTO recipes (id, user_id, category_id, title, description, prep_time_minutes, cook_time_minutes, calories) VALUES
(1, 2, 2, 'Avocado Caesar Salad', 'Fresh romaine, ripe avocado, parmesan, and light Caesar dressing.', 15, 0, 420),
(2, 2, 1, 'Protein Oat Pancakes', 'Fluffy oat pancakes infused with vanilla whey protein and berries.', 10, 10, 380),
(3, 1, 3, 'Baked Salmon & Asparagus', 'Oven-roasted wild salmon with asparagus and garlic butter.', 10, 20, 580);

INSERT INTO meal_plans (id, user_id, recipe_id, plan_date, meal_type) VALUES
(1, 2, 2, '2026-11-02', 'breakfast'),
(2, 2, 1, '2026-11-02', 'lunch'),
(3, 2, 3, '2026-11-02', 'dinner');

INSERT INTO reviews (id, user_id, recipe_id, rating, comment) VALUES
(1, 2, 3, 5, 'Delicious and very easy to meal prep for weeknights!'),
(2, 1, 1, 4, 'Great salad, though I added extra parmesan.');