<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

// Get a product with variations
$product = Product::where('id', 1)->first();

if ($product) {
    echo "Product ID: " . $product->id . "\n";
    echo "Product Name: " . $product->name . "\n";
    echo "Variations JSON:\n";
    echo json_encode(json_decode($product->variations, true), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "No product found with ID 1\n";
}


