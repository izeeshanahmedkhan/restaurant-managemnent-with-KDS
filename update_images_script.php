<?php

// Direct PHP script to update image paths in the database
// This script works with the hnd804_food.sql structure

// Database connection configuration (modify as needed)
$host = '127.0.0.1';
$dbname = 'hnd804_food'; // Based on the SQL file name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";
    
    // Update products with new images
    $updates = [
        "UPDATE `products` SET `image` = 'super-charger-burger.jpg' WHERE `id` = 2",
        "UPDATE `products` SET `image` = 'beef-spicy-burger.jpg' WHERE `id` = 3",
        "UPDATE `products` SET `image` = 'grilled-cheese-burger-alt.jpg' WHERE `id` = 4",
        "UPDATE `products` SET `image` = 'italian-spicy-pizza.jpg' WHERE `id` = 5",
        "UPDATE `products` SET `image` = 'mozzarella-cheese-pizza.jpg' WHERE `id` = 6",
        "UPDATE `products` SET `image` = 'set-menu-1.jpg' WHERE `id` = 7",
        "UPDATE `products` SET `image` = 'chicken-biryani-alt.jpg' WHERE `id` = 8",
        "UPDATE `products` SET `image` = 'beef-biryani-masala.jpg' WHERE `id` = 9",
        "UPDATE `products` SET `image` = 'set-menu-2.jpg' WHERE `id` = 10",
        "UPDATE `products` SET `image` = 'cheese-sandwich-grilled.jpg' WHERE `id` = 11",
        "UPDATE `products` SET `image` = 'spicy-burger-alt.jpg' WHERE `id` = 12",
        
        // Update categories
        "UPDATE `categories` SET `image` = 'fish-and-rice.jpg' WHERE `id` = 3",
        
        // Update banners
        "UPDATE `banners` SET `image` = 'restaurant-banner-1.jpg' WHERE `id` = 1"
    ];
    
    foreach ($updates as $sql) {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();
        echo "Executed: " . substr($sql, 0, 50) . "... " . ($result ? "✓" : "✗") . "\n";
    }
    
    // Insert additional banner
    $insertBanner = "INSERT IGNORE INTO `banners` (`id`, `title`, `image`, `product_id`, `status`, `created_at`, `updated_at`, `category_id`) VALUES (2, 'Food Banner', 'food-banner-2.jpg', NULL, 1, NOW(), NOW(), NULL)";
    $stmt = $pdo->prepare($insertBanner);
    $result = $stmt->execute();
    echo "Inserted new banner: " . ($result ? "✓" : "✗") . "\n";
    
    echo "\n🎉 All image updates completed successfully!\n";
    
    // Verify some updates
    $verify = $pdo->query("SELECT id, name, image FROM products WHERE id IN (2,3,4,5) LIMIT 4");
    echo "\nVerification - Updated products:\n";
    while ($row = $verify->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']}: {$row['image']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "\nTrying alternative approach...\n";
    
    // If database connection fails, let's create the database structure
    // and populate it with the SQL file
    echo "Please run the following commands manually:\n";
    echo "1. Create database: CREATE DATABASE hnd804_food;\n";
    echo "2. Import SQL file: mysql -u root -p hnd804_food < hnd804_food.sql\n";
    echo "3. Run this script again\n";
}

?>
