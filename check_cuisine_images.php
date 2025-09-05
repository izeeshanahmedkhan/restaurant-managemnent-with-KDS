<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cuisine;
use Illuminate\Support\Facades\Storage;

echo "🍽️ Cuisine Images Check\n";
echo "======================\n\n";

$cuisines = Cuisine::all();

foreach ($cuisines as $cuisine) {
    echo "Cuisine ID: {$cuisine->id}\n";
    echo "Name: {$cuisine->name}\n";
    echo "Image: {$cuisine->image}\n";
    
    if (empty($cuisine->image)) {
        echo "❌ NO IMAGE SET\n";
    } else {
        $imagePath = 'cuisine/' . $cuisine->image;
        $exists = Storage::disk('public')->exists($imagePath);
        
        if ($exists) {
            echo "✅ Image exists: {$imagePath}\n";
        } else {
            echo "❌ Image NOT FOUND: {$imagePath}\n";
        }
    }
    
    echo "Full URL: " . $cuisine->image_full_path . "\n";
    echo "---\n";
}

echo "\n📁 Checking cuisine directory:\n";
$cuisineFiles = Storage::disk('public')->files('cuisine');
foreach ($cuisineFiles as $file) {
    echo "File: {$file}\n";
}

?>
