<?php

echo "🖼️  Pizza N Gyro - Downloading All Missing Images\n";
echo "===============================================\n\n";

// Product images to download
$productImages = [
    'classic-beef-burger.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&h=500&fit=crop&crop=center',
    'beef-chow-mein.jpg' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&h=500&fit=crop&crop=center',
    'vegetable-stir-fry.jpg' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=500&h=500&fit=crop&crop=center',
    'chocolate-lava-cake.jpg' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500&h=500&fit=crop&crop=center',
    'tiramisu.jpg' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=500&h=500&fit=crop&crop=center',
    'cheesecake.jpg' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=500&h=500&fit=crop&crop=center',
    'fresh-orange-juice.jpg' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500&h=500&fit=crop&crop=center',
    'iced-coffee.jpg' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500&h=500&fit=crop&crop=center',
    'smoothie-bowl.jpg' => 'https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=500&h=500&fit=crop&crop=center'
];

// Category images to download
$categoryImages = [
    'burgers.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&h=500&fit=crop&crop=center',
    'pizza.jpg' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=500&h=500&fit=crop&crop=center',
    'desserts.jpg' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500&h=500&fit=crop&crop=center',
    'beverages.jpg' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500&h=500&fit=crop&crop=center',
    'appetizers.jpg' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=500&h=500&fit=crop&crop=center',
    'salads.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=500&fit=crop&crop=center',
    'soups.jpg' => 'https://images.unsplash.com/photo-1547592166-23ac45744ac2?w=500&h=500&fit=crop&crop=center',
    'beef-burgers.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&h=500&fit=crop&crop=center',
    'chicken-burgers.jpg' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=500&h=500&fit=crop&crop=center',
    'veggie-burgers.jpg' => 'https://images.unsplash.com/photo-1553979459-d2229ba7433a?w=500&h=500&fit=crop&crop=center',
    'fish-burgers.jpg' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&h=500&fit=crop&crop=center',
    'meat-pizzas.jpg' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=500&h=500&fit=crop&crop=center',
    'vegetarian-pizzas.jpg' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=500&h=500&fit=crop&crop=center',
    'specialty-pizzas.jpg' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=500&h=500&fit=crop&crop=center',
    'calzone-stromboli.jpg' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=500&h=500&fit=crop&crop=center',
    'chinese.jpg' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&h=500&fit=crop&crop=center',
    'japanese.jpg' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=500&h=500&fit=crop&crop=center',
    'thai.jpg' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&h=500&fit=crop&crop=center',
    'indian.jpg' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&h=500&fit=crop&crop=center',
    'cakes.jpg' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500&h=500&fit=crop&crop=center',
    'ice-cream.jpg' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=500&h=500&fit=crop&crop=center',
    'pastries.jpg' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=500&h=500&fit=crop&crop=center',
    'puddings.jpg' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=500&h=500&fit=crop&crop=center',
    'hot-drinks.jpg' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&h=500&fit=crop&crop=center',
    'cold-drinks.jpg' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500&h=500&fit=crop&crop=center',
    'smoothies.jpg' => 'https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=500&h=500&fit=crop&crop=center',
    'fresh-juices.jpg' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500&h=500&fit=crop&crop=center',
    'wings.jpg' => 'https://images.unsplash.com/photo-1567620832904-9fe5cf23db13?w=500&h=500&fit=crop&crop=center',
    'fries-sides.jpg' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=500&h=500&fit=crop&crop=center',
    'dips-sauces.jpg' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=500&h=500&fit=crop&crop=center',
    'garden-salads.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=500&fit=crop&crop=center',
    'protein-salads.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=500&fit=crop&crop=center',
    'fruit-salads.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=500&fit=crop&crop=center',
    'hot-soups.jpg' => 'https://images.unsplash.com/photo-1547592166-23ac45744ac2?w=500&h=500&fit=crop&crop=center',
    'cold-soups.jpg' => 'https://images.unsplash.com/photo-1547592166-23ac45744ac2?w=500&h=500&fit=crop&crop=center',
    'bread-bowls.jpg' => 'https://images.unsplash.com/photo-1547592166-23ac45744ac2?w=500&h=500&fit=crop&crop=center'
];

// Download product images
echo "📦 Downloading Product Images...\n";
$productSuccess = 0;
$productFailed = 0;

foreach ($productImages as $filename => $url) {
    $filepath = "storage/app/public/product/" . $filename;
    $result = file_put_contents($filepath, file_get_contents($url));
    
    if ($result !== false) {
        echo "✅ Downloaded: {$filename}\n";
        $productSuccess++;
    } else {
        echo "❌ Failed: {$filename}\n";
        $productFailed++;
    }
}

// Download category images
echo "\n📂 Downloading Category Images...\n";
$categorySuccess = 0;
$categoryFailed = 0;

foreach ($categoryImages as $filename => $url) {
    $filepath = "storage/app/public/category/" . $filename;
    $result = file_put_contents($filepath, file_get_contents($url));
    
    if ($result !== false) {
        echo "✅ Downloaded: {$filename}\n";
        $categorySuccess++;
    } else {
        echo "❌ Failed: {$filename}\n";
        $categoryFailed++;
    }
}

// Summary
echo "\n📊 Download Summary:\n";
echo "===================\n";
echo "Product Images: {$productSuccess} success, {$productFailed} failed\n";
echo "Category Images: {$categorySuccess} success, {$categoryFailed} failed\n";
echo "Total: " . ($productSuccess + $categorySuccess) . " images downloaded\n";

echo "\n✅ Image download completed!\n";

?>
