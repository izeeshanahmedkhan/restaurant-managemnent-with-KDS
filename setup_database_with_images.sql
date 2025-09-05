-- Create database and setup with images
CREATE DATABASE IF NOT EXISTS hnd804_food;
USE hnd804_food;

-- First create the database structure from the original SQL file
-- Then update with our new images

-- Update products with new images (after the original data is inserted)
UPDATE `products` SET `image` = 'super-charger-burger.jpg' WHERE `id` = 2 AND `name` = 'Super Charger Burger';
UPDATE `products` SET `image` = 'beef-spicy-burger.jpg' WHERE `id` = 3 AND `name` = 'Beef Spicy Burger';
UPDATE `products` SET `image` = 'grilled-cheese-burger-alt.jpg' WHERE `id` = 4 AND `name` = 'Grilled Cheese Burger';
UPDATE `products` SET `image` = 'italian-spicy-pizza.jpg' WHERE `id` = 5 AND `name` = 'Italian Spicy Pizza';
UPDATE `products` SET `image` = 'mozzarella-cheese-pizza.jpg' WHERE `id` = 6 AND `name` = 'Mozzarella Cheese Pizza';
UPDATE `products` SET `image` = 'set-menu-1.jpg' WHERE `id` = 7 AND `name` = 'Set Menu 1';
UPDATE `products` SET `image` = 'chicken-biryani-alt.jpg' WHERE `id` = 8 AND `name` = 'Chicken Biriyani';
UPDATE `products` SET `image` = 'beef-biryani-masala.jpg' WHERE `id` = 9 AND `name` = 'Beef Biriyani With Spice Masala';
UPDATE `products` SET `image` = 'set-menu-2.jpg' WHERE `id` = 10 AND `name` = 'Set Menu 2';
UPDATE `products` SET `image` = 'cheese-sandwich-grilled.jpg' WHERE `id` = 11 AND `name` = 'Cheese Sandwich With Spicy Grilled';
UPDATE `products` SET `image` = 'spicy-burger-alt.jpg' WHERE `id` = 12 AND `name` = 'Spicy Burger';

-- Update categories with new images
UPDATE `categories` SET `image` = 'fish-and-rice.jpg' WHERE `id` = 3 AND `name` = 'Fish and Rice';

-- Update banners with new images
UPDATE `banners` SET `image` = 'restaurant-banner-1.jpg' WHERE `id` = 1;

-- Insert additional banner
INSERT IGNORE INTO `banners` (`id`, `title`, `image`, `product_id`, `status`, `created_at`, `updated_at`, `category_id`) VALUES 
(2, 'Food Banner', 'food-banner-2.jpg', NULL, 1, NOW(), NOW(), NULL);

-- Show updated records
SELECT 'PRODUCTS WITH NEW IMAGES:' as result;
SELECT id, name, image FROM products WHERE image NOT IN ('def.png', '2023-09-06-64f83b1948ac3.png');

SELECT 'CATEGORIES WITH NEW IMAGES:' as result;
SELECT id, name, image FROM categories WHERE image NOT IN ('def.png');

SELECT 'BANNERS:' as result; 
SELECT id, title, image FROM banners;