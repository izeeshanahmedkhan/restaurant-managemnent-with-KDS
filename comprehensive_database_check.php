<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\Category;
use App\Model\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

echo "🔍 Pizza N Gyro - Comprehensive Database Check\n";
echo "============================================\n\n";

// Check all tables
echo "📊 DATABASE OVERVIEW:\n";
echo "====================\n";

// Get all table names
$tables = DB::select('SHOW TABLES');
$tableCount = count($tables);
echo "Total Tables: {$tableCount}\n\n";

// Check each table
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    $count = DB::table($tableName)->count();
    echo "Table '{$tableName}': {$count} records\n";
}

echo "\n📦 DETAILED PRODUCT ANALYSIS:\n";
echo "=============================\n";
$allProducts = Product::all();
$productsWithImages = 0;
$productsWithoutImages = 0;
$productsWithBrokenImages = 0;

foreach ($allProducts as $product) {
    $hasImage = !empty($product->image);
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('product/' . $product->image);
    }
    
    if (!$hasImage) {
        $productsWithoutImages++;
        echo "❌ Product {$product->id}: {$product->name} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $productsWithBrokenImages++;
        echo "⚠️  Product {$product->id}: {$product->name} - BROKEN IMAGE: {$product->image}\n";
    } else {
        $productsWithImages++;
        echo "✅ Product {$product->id}: {$product->name} - OK: {$product->image}\n";
    }
}

echo "\n📂 DETAILED CATEGORY ANALYSIS:\n";
echo "==============================\n";
$allCategories = Category::all();
$categoriesWithImages = 0;
$categoriesWithoutImages = 0;
$categoriesWithBrokenImages = 0;

foreach ($allCategories as $category) {
    $hasImage = !empty($category->image);
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('category/' . $category->image);
    }
    
    if (!$hasImage) {
        $categoriesWithoutImages++;
        echo "❌ Category {$category->id}: {$category->name} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $categoriesWithBrokenImages++;
        echo "⚠️  Category {$category->id}: {$category->name} - BROKEN IMAGE: {$category->image}\n";
    } else {
        $categoriesWithImages++;
        echo "✅ Category {$category->id}: {$category->name} - OK: {$category->image}\n";
    }
}

echo "\n🎯 DETAILED BANNER ANALYSIS:\n";
echo "============================\n";
$allBanners = Banner::all();
$bannersWithImages = 0;
$bannersWithoutImages = 0;
$bannersWithBrokenImages = 0;

foreach ($allBanners as $banner) {
    $hasImage = !empty($banner->image);
    $imageExists = false;
    
    if ($hasImage) {
        $imageExists = Storage::disk('public')->exists('banner/' . $banner->image);
    }
    
    if (!$hasImage) {
        $bannersWithoutImages++;
        echo "❌ Banner {$banner->id}: {$banner->title} - NO IMAGE\n";
    } elseif (!$imageExists) {
        $bannersWithBrokenImages++;
        echo "⚠️  Banner {$banner->id}: {$banner->title} - BROKEN IMAGE: {$banner->image}\n";
    } else {
        $bannersWithImages++;
        echo "✅ Banner {$banner->id}: {$banner->title} - OK: {$banner->image}\n";
    }
}

// Check for any other tables that might have image fields
echo "\n🔍 CHECKING OTHER TABLES FOR IMAGE FIELDS:\n";
echo "==========================================\n";

$imageTables = [];
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    
    // Get table structure
    $columns = DB::select("DESCRIBE `{$tableName}`");
    $hasImageField = false;
    
    foreach ($columns as $column) {
        if (strpos(strtolower($column->Field), 'image') !== false) {
            $hasImageField = true;
            break;
        }
    }
    
    if ($hasImageField) {
        $imageTables[] = $tableName;
        echo "📷 Table '{$tableName}' has image-related fields\n";
        
        // Check for records with image fields
        $records = DB::table($tableName)->whereNotNull('image')->where('image', '!=', '')->get();
        if ($records->count() > 0) {
            echo "   - {$records->count()} records with image data\n";
            foreach ($records as $record) {
                $recordArray = (array)$record;
                $id = $recordArray['id'] ?? 'unknown';
                $image = $recordArray['image'] ?? 'unknown';
                echo "   - Record {$id}: {$image}\n";
            }
        }
    }
}

// Final summary
echo "\n📊 COMPREHENSIVE SUMMARY:\n";
echo "========================\n";
echo "Products with working images: {$productsWithImages}\n";
echo "Products without images: {$productsWithoutImages}\n";
echo "Products with broken images: {$productsWithBrokenImages}\n";
echo "Categories with working images: {$categoriesWithImages}\n";
echo "Categories without images: {$categoriesWithoutImages}\n";
echo "Categories with broken images: {$categoriesWithBrokenImages}\n";
echo "Banners with working images: {$bannersWithImages}\n";
echo "Banners without images: {$bannersWithoutImages}\n";
echo "Banners with broken images: {$bannersWithBrokenImages}\n";

$totalItems = $allProducts->count() + $allCategories->count() + $allBanners->count();
$totalWorking = $productsWithImages + $categoriesWithImages + $bannersWithImages;
$totalBroken = $productsWithBrokenImages + $categoriesWithBrokenImages + $bannersWithBrokenImages;
$totalMissing = $productsWithoutImages + $categoriesWithoutImages + $bannersWithoutImages;

echo "\nTOTAL SYSTEM STATUS:\n";
echo "Total items: {$totalItems}\n";
echo "Working images: {$totalWorking}\n";
echo "Broken images: {$totalBroken}\n";
echo "Missing images: {$totalMissing}\n";

if ($totalBroken == 0 && $totalMissing == 0) {
    echo "\n✅ PERFECT! No broken or missing images in the entire database!\n";
} else {
    echo "\n⚠️  Issues found: {$totalBroken} broken, {$totalMissing} missing\n";
}

echo "\n🔍 Additional tables with image fields: " . count($imageTables) . "\n";
if (!empty($imageTables)) {
    echo "Tables: " . implode(', ', $imageTables) . "\n";
}

echo "\n✅ Comprehensive database check completed!\n";

?>
