<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Model\Product;

try {
    echo "Testing variations transformation...\n";
    
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
    echo "Original variations: " . $product->variations . "\n\n";
    
    // Apply the same transformation as in the controller
    $variations = json_decode($product->variations, true) ?? [];
    $transformedVariations = [];
    
    foreach ($variations as $index => $variation) {
        $transformedVariation = [
            'id' => $index + 1,
            'name' => $variation['name'] ?? 'Variation',
            'type' => $variation['type'] ?? 'single',
            'required' => ($variation['required'] ?? false) === 'on' || ($variation['required'] ?? false) === true,
            'min' => $variation['min'] ?? 1,
            'max' => $variation['max'] ?? 1,
            'options' => []
        ];
        
        if (isset($variation['values']) && is_array($variation['values'])) {
            foreach ($variation['values'] as $optionIndex => $option) {
                $transformedVariation['options'][] = [
                    'id' => $optionIndex + 1,
                    'label' => $option['label'] ?? 'Option',
                    'delta' => floatval($option['optionPrice'] ?? $option['price'] ?? 0)
                ];
            }
        }
        
        $transformedVariations[] = $transformedVariation;
    }
    
    echo "Transformed variations:\n";
    echo json_encode($transformedVariations, JSON_PRETTY_PRINT) . "\n";
    
    echo "\nVariation groups that will be created:\n";
    foreach ($transformedVariations as $index => $variation) {
        echo "  " . ($index + 1) . ". " . $variation['name'] . " (type: " . $variation['type'] . ", required: " . ($variation['required'] ? 'yes' : 'no') . ")\n";
        foreach ($variation['options'] as $option) {
            echo "     - " . $option['label'] . " (+$" . $option['delta'] . ")\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}


