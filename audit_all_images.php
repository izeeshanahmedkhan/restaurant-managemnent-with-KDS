<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\Category;
use App\Model\Banner;
use Illuminate\Support\Facades\Storage;

echo "🔍 Pizza N Gyro - Complete Image Audit\n";
echo "====================================\n\n";

// Check all products
echo "📦 AUDITING ALL PRODUCTS:\n";
echo "========================\n";
$allProducts = Product::all();
$productsNeedingImages = [];
$productsWithBrokenImages = [];

foreach ($allProducts as $product) {
    $hasImage = !empty($product->image) && $product->image !== 'def.png';
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('product/' . $product->image);
    }
    
    if (!$hasImage) {
        $productsNeedingImages[] = $product;
        echo "❌ Product {$product->id}: {$product->name} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $productsWithBrokenImages[] = $product;
        echo "⚠️  Product {$product->id}: {$product->name} - BROKEN IMAGE: {$product->image}\n";
    } else {
        echo "✅ Product {$product->id}: {$product->name} - OK: {$product->image}\n";
    }
}

echo "\n📂 AUDITING ALL CATEGORIES:\n";
echo "==========================\n";
$allCategories = Category::all();
$categoriesNeedingImages = [];
$categoriesWithBrokenImages = [];

foreach ($allCategories as $category) {
    $hasImage = !empty($category->image);
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('category/' . $category->image);
    }
    
    if (!$hasImage) {
        $categoriesNeedingImages[] = $category;
        echo "❌ Category {$category->id}: {$category->name} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $categoriesWithBrokenImages[] = $category;
        echo "⚠️  Category {$category->id}: {$category->name} - BROKEN IMAGE: {$category->image}\n";
    } else {
        echo "✅ Category {$category->id}: {$category->name} - OK: {$category->image}\n";
    }
}

echo "\n🎯 AUDITING ALL BANNERS:\n";
echo "========================\n";
$allBanners = Banner::all();
$bannersNeedingImages = [];
$bannersWithBrokenImages = [];

foreach ($allBanners as $banner) {
    $hasImage = !empty($banner->image);
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('banner/' . $banner->image);
    }
    
    if (!$hasImage) {
        $bannersNeedingImages[] = $banner;
        echo "❌ Banner {$banner->id}: {$banner->title} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $bannersWithBrokenImages[] = $banner;
        echo "⚠️  Banner {$banner->id}: {$banner->title} - BROKEN IMAGE: {$banner->image}\n";
    } else {
        echo "✅ Banner {$banner->id}: {$banner->title} - OK: {$banner->image}\n";
    }
}

// Summary
echo "\n📊 AUDIT SUMMARY:\n";
echo "================\n";
echo "Products needing images: " . count($productsNeedingImages) . "\n";
echo "Products with broken images: " . count($productsWithBrokenImages) . "\n";
echo "Categories needing images: " . count($categoriesNeedingImages) . "\n";
echo "Categories with broken images: " . count($categoriesWithBrokenImages) . "\n";
echo "Banners needing images: " . count($bannersNeedingImages) . "\n";
echo "Banners with broken images: " . count($bannersWithBrokenImages) . "\n";

// Export lists for fixing
if (!empty($productsNeedingImages) || !empty($productsWithBrokenImages)) {
    echo "\n📝 PRODUCTS TO FIX:\n";
    foreach ($productsNeedingImages as $product) {
        echo "Product {$product->id}: {$product->name}\n";
    }
    foreach ($productsWithBrokenImages as $product) {
        echo "Product {$product->id}: {$product->name} (broken: {$product->image})\n";
    }
}

if (!empty($categoriesNeedingImages) || !empty($categoriesWithBrokenImages)) {
    echo "\n📝 CATEGORIES TO FIX:\n";
    foreach ($categoriesNeedingImages as $category) {
        echo "Category {$category->id}: {$category->name}\n";
    }
    foreach ($categoriesWithBrokenImages as $category) {
        echo "Category {$category->id}: {$category->name} (broken: {$category->image})\n";
    }
}

if (!empty($bannersNeedingImages) || !empty($bannersWithBrokenImages)) {
    echo "\n📝 BANNERS TO FIX:\n";
    foreach ($bannersNeedingImages as $banner) {
        echo "Banner {$banner->id}: {$banner->title}\n";
    }
    foreach ($bannersWithBrokenImages as $banner) {
        echo "Banner {$banner->id}: {$banner->title} (broken: {$banner->image})\n";
    }
}

echo "\n✅ Audit complete!\n";

?>
