<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\ProductByBranch;

try {
    echo "Checking product variations...\n";
    
    // Check products with variations in main products table
    $productsWithVariations = Product::whereNotNull('variations')
        ->where('variations', '!=', '[]')
        ->where('variations', '!=', 'null')
        ->count();
    
    echo "Products with variations in main table: " . $productsWithVariations . "\n";
    
    // Check products with variations in product_by_branches table
    $branchProductsWithVariations = ProductByBranch::whereNotNull('variations')
        ->where('variations', '!=', '[]')
        ->where('variations', '!=', 'null')
        ->count();
    
    echo "Products with variations in branch table: " . $branchProductsWithVariations . "\n";
    
    // Get a sample product with variations
    $sampleProduct = Product::whereNotNull('variations')
        ->where('variations', '!=', '[]')
        ->where('variations', '!=', 'null')
        ->first();
    
    if ($sampleProduct) {
        echo "\nSample product: " . $sampleProduct->name . "\n";
        echo "Variations: " . $sampleProduct->variations . "\n";
        
        $variations = json_decode($sampleProduct->variations, true);
        if ($variations) {
            echo "Decoded variations:\n";
            print_r($variations);
        }
    }
    
    // Check branch-specific variations
    $sampleBranchProduct = ProductByBranch::whereNotNull('variations')
        ->where('variations', '!=', '[]')
        ->where('variations', '!=', 'null')
        ->first();
    
    if ($sampleBranchProduct) {
        echo "\nSample branch product variations: " . $sampleBranchProduct->variations . "\n";
        
        $branchVariations = json_decode($sampleBranchProduct->variations, true);
        if ($branchVariations) {
            echo "Decoded branch variations:\n";
            print_r($branchVariations);
        }
    }
    
    // Check if there are any products at all
    $totalProducts = Product::count();
    echo "\nTotal products: " . $totalProducts . "\n";
    
    $totalBranchProducts = ProductByBranch::count();
    echo "Total branch products: " . $totalBranchProducts . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}


