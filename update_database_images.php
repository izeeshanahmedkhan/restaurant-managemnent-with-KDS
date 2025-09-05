<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\Category;
use App\Model\Banner;

echo "🍕 Pizza N Gyro - Database Image Update\n";
echo "=====================================\n\n";

// Check current database status
echo "📊 Current Database Status:\n";
echo "Products: " . Product::count() . "\n";
echo "Categories: " . Category::count() . "\n";
echo "Banners: " . Banner::count() . "\n\n";

// Define image mappings
$productImageMappings = [
    2 => 'super-charger-burger.jpg',
    3 => 'beef-spicy-burger.jpg',
    4 => 'grilled-cheese-burger-alt.jpg',
    5 => 'italian-spicy-pizza.jpg',
    6 => 'mozzarella-cheese-pizza.jpg',
    7 => 'set-menu-1.jpg',
    8 => 'chicken-biryani-alt.jpg',
    9 => 'beef-biryani-masala.jpg',
    10 => 'set-menu-2.jpg',
    11 => 'cheese-sandwich-grilled.jpg',
    12 => 'spicy-burger-alt.jpg'
];

$categoryImageMappings = [
    3 => 'fish-and-rice.jpg'
];

$bannerImageMappings = [
    1 => 'restaurant-banner-1.jpg',
    2 => 'food-banner-2.jpg'
];

echo "🔄 Updating Product Images...\n";
$updatedProducts = 0;
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
    }
}

echo "\n🔄 Updating Category Images...\n";
$updatedCategories = 0;
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
    }
}

echo "\n🔄 Updating Banner Images...\n";
$updatedBanners = 0;
foreach ($bannerImageMappings as $bannerId => $imageName) {
    $banner = Banner::find($bannerId);
    if ($banner) {
        $oldImage = $banner->image;
        $banner->image = $imageName;
        $banner->save();
        echo "✅ Banner {$bannerId} ({$banner->title}): {$oldImage} → {$imageName}\n";
        $updatedBanners++;
    } else {
        echo "❌ Banner {$bannerId} not found\n";
    }
}

echo "\n📊 Update Summary:\n";
echo "Updated Products: {$updatedProducts}\n";
echo "Updated Categories: {$updatedCategories}\n";
echo "Updated Banners: {$updatedBanners}\n";

echo "\n✅ Database image update completed!\n";
echo "🌐 Test your images at: http://localhost:8000/verify_image_paths.php\n";

?>
