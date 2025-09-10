<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;
use App\Model\ProductByBranch;

try {
    echo "Testing variations API...\n";
    
    // Get a product with variations
    $product = Product::whereNotNull('variations')
        ->where('variations', '!=', '[]')
        ->where('variations', '!=', 'null')
        ->first();
    
    if (!$product) {
        echo "No product with variations found.\n";
        exit(1);
    }
    
    echo "Testing with product: " . $product->name . "\n";
    echo "Product ID: " . $product->id . "\n";
    
    // Get the branch ID
    $branch = App\Model\Branch::first();
    if (!$branch) {
        echo "No branch found.\n";
        exit(1);
    }
    
    echo "Using branch: " . $branch->name . " (ID: " . $branch->id . ")\n";
    
    // Test the API endpoint
    $url = "http://localhost:8000/api/v1/kiosk/products/{$product->id}?branch_id={$branch->id}";
    echo "API URL: " . $url . "\n";
    
    $response = file_get_contents($url);
    if ($response === false) {
        echo "Failed to call API. Make sure the server is running.\n";
        exit(1);
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['success']) && $data['success']) {
        echo "\nAPI Response successful!\n";
        echo "Product name: " . $data['data']['name'] . "\n";
        echo "Variations count: " . count($data['data']['variations']) . "\n";
        
        if (!empty($data['data']['variations'])) {
            echo "\nVariations:\n";
            foreach ($data['data']['variations'] as $index => $variation) {
                echo "  " . ($index + 1) . ". " . $variation['name'] . " (type: " . $variation['type'] . ", required: " . ($variation['required'] ? 'yes' : 'no') . ")\n";
                foreach ($variation['options'] as $option) {
                    echo "     - " . $option['label'] . " (+$" . $option['delta'] . ")\n";
                }
            }
        }
    } else {
        echo "API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}


