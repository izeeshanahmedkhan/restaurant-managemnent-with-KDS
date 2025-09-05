<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\Category;
use App\Model\Banner;
use App\Models\Cuisine;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

echo "🔍 Finding All Missing Images\n";
echo "============================\n\n";

$missingImages = [];

// Check Products
echo "📦 Checking Products:\n";
$products = Product::all();
foreach ($products as $product) {
    if (!empty($product->image)) {
        $exists = Storage::disk('public')->exists('product/' . $product->image);
        if (!$exists) {
            $missingImages[] = ['type' => 'product', 'id' => $product->id, 'name' => $product->name, 'image' => $product->image];
            echo "❌ Product {$product->id}: {$product->name} - Missing: {$product->image}\n";
        } else {
            echo "✅ Product {$product->id}: {$product->name} - OK\n";
        }
    } else {
        $missingImages[] = ['type' => 'product', 'id' => $product->id, 'name' => $product->name, 'image' => null];
        echo "❌ Product {$product->id}: {$product->name} - No image set\n";
    }
}

// Check Categories
echo "\n📂 Checking Categories:\n";
$categories = Category::all();
foreach ($categories as $category) {
    if (!empty($category->image)) {
        $exists = Storage::disk('public')->exists('category/' . $category->image);
        if (!$exists) {
            $missingImages[] = ['type' => 'category', 'id' => $category->id, 'name' => $category->name, 'image' => $category->image];
            echo "❌ Category {$category->id}: {$category->name} - Missing: {$category->image}\n";
        } else {
            echo "✅ Category {$category->id}: {$category->name} - OK\n";
        }
    } else {
        $missingImages[] = ['type' => 'category', 'id' => $category->id, 'name' => $category->name, 'image' => null];
        echo "❌ Category {$category->id}: {$category->name} - No image set\n";
    }
}

// Check Banners
echo "\n🎯 Checking Banners:\n";
$banners = Banner::all();
foreach ($banners as $banner) {
    if (!empty($banner->image)) {
        $exists = Storage::disk('public')->exists('banner/' . $banner->image);
        if (!$exists) {
            $missingImages[] = ['type' => 'banner', 'id' => $banner->id, 'name' => $banner->title, 'image' => $banner->image];
            echo "❌ Banner {$banner->id}: {$banner->title} - Missing: {$banner->image}\n";
        } else {
            echo "✅ Banner {$banner->id}: {$banner->title} - OK\n";
        }
    } else {
        $missingImages[] = ['type' => 'banner', 'id' => $banner->id, 'name' => $banner->title, 'image' => null];
        echo "❌ Banner {$banner->id}: {$banner->title} - No image set\n";
    }
}

// Check Cuisines
echo "\n🍽️ Checking Cuisines:\n";
$cuisines = Cuisine::all();
foreach ($cuisines as $cuisine) {
    if (!empty($cuisine->image)) {
        $exists = Storage::disk('public')->exists('cuisine/' . $cuisine->image);
        if (!$exists) {
            $missingImages[] = ['type' => 'cuisine', 'id' => $cuisine->id, 'name' => $cuisine->name, 'image' => $cuisine->image];
            echo "❌ Cuisine {$cuisine->id}: {$cuisine->name} - Missing: {$cuisine->image}\n";
        } else {
            echo "✅ Cuisine {$cuisine->id}: {$cuisine->name} - OK\n";
        }
    } else {
        $missingImages[] = ['type' => 'cuisine', 'id' => $cuisine->id, 'name' => $cuisine->name, 'image' => null];
        echo "❌ Cuisine {$cuisine->id}: {$cuisine->name} - No image set\n";
    }
}

// Check other tables with image fields
echo "\n🔍 Checking Other Tables:\n";
$otherTables = ['admins', 'branches', 'users'];
foreach ($otherTables as $tableName) {
    $records = DB::table($tableName)->get();
    foreach ($records as $record) {
        $recordArray = (array)$record;
        $id = $recordArray['id'] ?? 'unknown';
        $image = $recordArray['image'] ?? null;
        $name = $recordArray['name'] ?? $recordArray['title'] ?? "Record {$id}";
        
        if (!empty($image)) {
            $found = false;
            foreach (['product', 'category', 'banner', 'cuisine', 'admin', 'branch', 'user'] as $folder) {
                if (Storage::disk('public')->exists($folder . '/' . $image)) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $missingImages[] = ['type' => $tableName, 'id' => $id, 'name' => $name, 'image' => $image];
                echo "❌ {$tableName} {$id}: {$name} - Missing: {$image}\n";
            } else {
                echo "✅ {$tableName} {$id}: {$name} - OK\n";
            }
        }
    }
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total missing images: " . count($missingImages) . "\n\n";

if (count($missingImages) > 0) {
    echo "🚨 MISSING IMAGES LIST:\n";
    foreach ($missingImages as $item) {
        echo "{$item['type']} ID {$item['id']}: {$item['name']} - " . ($item['image'] ?? 'No image set') . "\n";
    }
} else {
    echo "✅ NO MISSING IMAGES! All images are working perfectly!\n";
}

?>
