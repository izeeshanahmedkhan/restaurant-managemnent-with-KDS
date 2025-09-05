<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\Category;
use App\Model\Banner;

echo "🔄 Pizza N Gyro - Updating All Database Images\n";
echo "============================================\n\n";

// Product image mappings
$productImageMappings = [
    1 => 'classic-beef-burger.jpg',
    2 => 'super-charger-burger.jpg', // Already updated
    3 => 'beef-spicy-burger.jpg',    // Already updated
    4 => 'grilled-cheese-burger-alt.jpg', // Already updated
    5 => 'italian-spicy-pizza.jpg',  // Already updated
    6 => 'mozzarella-cheese-pizza.jpg', // Already updated
    7 => 'set-menu-1.jpg',           // Already updated
    8 => 'chicken-biryani-alt.jpg',  // Already updated
    9 => 'beef-biryani-masala.jpg',  // Already updated
    10 => 'set-menu-2.jpg',          // Already updated
    11 => 'cheese-sandwich-grilled.jpg', // Already updated
    12 => 'spicy-burger-alt.jpg',    // Already updated
    13 => 'beef-chow-mein.jpg',
    14 => 'vegetable-stir-fry.jpg',
    15 => 'chocolate-lava-cake.jpg',
    16 => 'tiramisu.jpg',
    17 => 'cheesecake.jpg',
    18 => 'fresh-orange-juice.jpg',
    19 => 'iced-coffee.jpg',
    20 => 'smoothie-bowl.jpg'
];

// Category image mappings
$categoryImageMappings = [
    1 => 'burgers.jpg',
    2 => 'pizza.jpg',
    3 => 'fish-and-rice.jpg', // Already updated
    4 => 'desserts.jpg',
    5 => 'beverages.jpg',
    6 => 'appetizers.jpg',
    7 => 'salads.jpg',
    8 => 'soups.jpg',
    9 => 'beef-burgers.jpg',
    10 => 'chicken-burgers.jpg',
    11 => 'veggie-burgers.jpg',
    12 => 'fish-burgers.jpg',
    13 => 'meat-pizzas.jpg',
    14 => 'vegetarian-pizzas.jpg',
    15 => 'specialty-pizzas.jpg',
    16 => 'calzone-stromboli.jpg',
    17 => 'chinese.jpg',
    18 => 'japanese.jpg',
    19 => 'thai.jpg',
    20 => 'indian.jpg',
    21 => 'cakes.jpg',
    22 => 'ice-cream.jpg',
    23 => 'pastries.jpg',
    24 => 'puddings.jpg',
    25 => 'hot-drinks.jpg',
    26 => 'cold-drinks.jpg',
    27 => 'smoothies.jpg',
    28 => 'fresh-juices.jpg',
    29 => 'wings.jpg',
    30 => 'fries-sides.jpg',
    31 => 'dips-sauces.jpg',
    32 => 'garden-salads.jpg',
    33 => 'protein-salads.jpg',
    34 => 'fruit-salads.jpg',
    35 => 'hot-soups.jpg',
    36 => 'cold-soups.jpg',
    37 => 'bread-bowls.jpg'
];

echo "📦 Updating Product Images...\n";
$updatedProducts = 0;
$failedProducts = 0;

foreach ($productImageMappings as $productId => $imageName) {
    $product = Product::find($productId);
    if ($product) {
        $oldImage = $product->image;
        $product->image = $imageName;
        $product->save();
        echo "✅ Product {$productId} ({$product->name}): {$oldImage} → {$imageName}\n";
        $updatedProducts++;
    } else {
        echo "❌ Product {$productId} not found\n";
        $failedProducts++;
    }
}

echo "\n📂 Updating Category Images...\n";
$updatedCategories = 0;
$failedCategories = 0;

foreach ($categoryImageMappings as $categoryId => $imageName) {
    $category = Category::find($categoryId);
    if ($category) {
        $oldImage = $category->image;
        $category->image = $imageName;
        $category->save();
        echo "✅ Category {$categoryId} ({$category->name}): {$oldImage} → {$imageName}\n";
        $updatedCategories++;
    } else {
        echo "❌ Category {$categoryId} not found\n";
        $failedCategories++;
    }
}

echo "\n📊 Update Summary:\n";
echo "==================\n";
echo "Products Updated: {$updatedProducts}\n";
echo "Products Failed: {$failedProducts}\n";
echo "Categories Updated: {$updatedCategories}\n";
echo "Categories Failed: {$failedCategories}\n";

echo "\n✅ Database update completed!\n";
echo "🌐 Test your images at: http://localhost:8000/verify_image_paths.php\n";

?>
