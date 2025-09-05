<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cuisine;
use Illuminate\Support\Facades\Storage;

echo "🍽️ Downloading Cuisine Images\n";
echo "=============================\n\n";

// Cuisine images mapping
$cuisineImages = [
    'american-cuisine.png' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=400&fit=crop&crop=center',
    'italian-cuisine.png' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=400&h=400&fit=crop&crop=center',
    'asian-cuisine.png' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&h=400&fit=crop&crop=center'
];

$downloaded = 0;
$failed = 0;

foreach ($cuisineImages as $filename => $url) {
    echo "Downloading {$filename}...\n";
    
    $imageData = @file_get_contents($url);
    
    if ($imageData === false) {
        echo "❌ Failed to download {$filename}\n";
        $failed++;
        continue;
    }
    
    $path = 'cuisine/' . $filename;
    $saved = Storage::disk('public')->put($path, $imageData);
    
    if ($saved) {
        echo "✅ Downloaded and saved: {$path}\n";
        $downloaded++;
    } else {
        echo "❌ Failed to save {$filename}\n";
        $failed++;
    }
}

echo "\n📊 Download Summary:\n";
echo "Downloaded: {$downloaded}\n";
echo "Failed: {$failed}\n";

// Verify the images
echo "\n🔍 Verifying images:\n";
$cuisines = Cuisine::all();
foreach ($cuisines as $cuisine) {
    $imagePath = 'cuisine/' . $cuisine->image;
    $exists = Storage::disk('public')->exists($imagePath);
    
    if ($exists) {
        echo "✅ {$cuisine->name}: {$cuisine->image} - OK\n";
    } else {
        echo "❌ {$cuisine->name}: {$cuisine->image} - MISSING\n";
    }
}

echo "\n✅ Cuisine images download completed!\n";

?>
