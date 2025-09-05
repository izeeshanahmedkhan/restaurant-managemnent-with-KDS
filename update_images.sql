-- Update products with new images
UPDATE `products` SET `image` = 'super-charger-burger.jpg' WHERE `id` = 2;
UPDATE `products` SET `image` = 'beef-spicy-burger.jpg' WHERE `id` = 3;
UPDATE `products` SET `image` = 'grilled-cheese-burger-alt.jpg' WHERE `id` = 4;
UPDATE `products` SET `image` = 'italian-spicy-pizza.jpg' WHERE `id` = 5;
UPDATE `products` SET `image` = 'mozzarella-cheese-pizza.jpg' WHERE `id` = 6;
UPDATE `products` SET `image` = 'set-menu-1.jpg' WHERE `id` = 7;
UPDATE `products` SET `image` = 'chicken-biryani-alt.jpg' WHERE `id` = 8;
UPDATE `products` SET `image` = 'beef-biryani-masala.jpg' WHERE `id` = 9;
UPDATE `products` SET `image` = 'set-menu-2.jpg' WHERE `id` = 10;
UPDATE `products` SET `image` = 'cheese-sandwich-grilled.jpg' WHERE `id` = 11;
UPDATE `products` SET `image` = 'spicy-burger-alt.jpg' WHERE `id` = 12;

-- Update categories with new images
UPDATE `categories` SET `image` = 'fish-and-rice.jpg' WHERE `id` = 3;

-- Update banners with new images
UPDATE `banners` SET `image` = 'restaurant-banner-1.jpg' WHERE `id` = 1;

-- Insert additional banners
INSERT INTO `banners` (`id`, `title`, `image`, `product_id`, `status`, `created_at`, `updated_at`, `category_id`) VALUES
(2, 'Food Banner', 'food-banner-2.jpg', NULL, 1, NOW(), NOW(), NULL);