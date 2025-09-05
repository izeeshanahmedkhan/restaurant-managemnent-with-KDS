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

echo "🔍 Deep Database Audit - Pizza N Gyro\n";
echo "====================================\n\n";

// Check all tables with image fields
$tablesToCheck = [
    'products' => 'product',
    'categories' => 'category', 
    'banners' => 'banner',
    'cuisines' => 'cuisine',
    'admins' => 'admin',
    'branches' => 'branch',
    'users' => 'user'
];

$totalIssues = 0;
$issues = [];

foreach ($tablesToCheck as $tableName => $folder) {
    echo "📊 Checking table: {$tableName}\n";
    echo str_repeat("-", 50) . "\n";
    
    $records = DB::table($tableName)->get();
    $tableIssues = 0;
    
    foreach ($records as $record) {
        $recordArray = (array)$record;
        $id = $recordArray['id'] ?? 'unknown';
        $image = $recordArray['image'] ?? null;
        $name = $recordArray['name'] ?? $recordArray['title'] ?? "Record {$id}";
        
        if (empty($image)) {
            echo "❌ {$tableName} ID {$id} ({$name}): NO IMAGE\n";
            $issues[] = [
                'table' => $tableName,
                'id' => $id,
                'name' => $name,
                'issue' => 'no_image',
                'image' => null
            ];
            $tableIssues++;
            $totalIssues++;
        } else {
            // Check if image file exists
            $imagePath = $folder . '/' . $image;
            $exists = Storage::disk('public')->exists($imagePath);
            
            if (!$exists) {
                echo "⚠️  {$tableName} ID {$id} ({$name}): BROKEN IMAGE - {$image}\n";
                $issues[] = [
                    'table' => $tableName,
                    'id' => $id,
                    'name' => $name,
                    'issue' => 'broken_image',
                    'image' => $image
                ];
                $tableIssues++;
                $totalIssues++;
            } else {
                echo "✅ {$tableName} ID {$id} ({$name}): OK - {$image}\n";
            }
        }
    }
    
    echo "Table {$tableName} issues: {$tableIssues}\n\n";
}

// Check for any other tables that might have image fields
echo "🔍 Checking other tables for image fields:\n";
echo str_repeat("-", 50) . "\n";

$allTables = DB::select('SHOW TABLES');
foreach ($allTables as $table) {
    $tableName = array_values((array)$table)[0];
    
    if (!in_array($tableName, array_keys($tablesToCheck))) {
        // Check if this table has image fields
        $columns = DB::select("DESCRIBE `{$tableName}`");
        $hasImageField = false;
        $imageFields = [];
        
        foreach ($columns as $column) {
            if (strpos(strtolower($column->Field), 'image') !== false) {
                $hasImageField = true;
                $imageFields[] = $column->Field;
            }
        }
        
        if ($hasImageField) {
            echo "📷 Table '{$tableName}' has image fields: " . implode(', ', $imageFields) . "\n";
            
            // Check records in this table
            $records = DB::table($tableName)->get();
            foreach ($records as $record) {
                $recordArray = (array)$record;
                $id = $recordArray['id'] ?? 'unknown';
                
                foreach ($imageFields as $field) {
                    $image = $recordArray[$field] ?? null;
                    if (!empty($image)) {
                        echo "   - Record {$id}, {$field}: {$image}\n";
                        
                        // Check if this image exists anywhere
                        $found = false;
                        foreach (['product', 'category', 'banner', 'cuisine', 'admin', 'branch', 'user'] as $checkFolder) {
                            if (Storage::disk('public')->exists($checkFolder . '/' . $image)) {
                                $found = true;
                                break;
                            }
                        }
                        
                        if (!$found) {
                            echo "     ❌ Image not found in any folder!\n";
                            $issues[] = [
                                'table' => $tableName,
                                'id' => $id,
                                'name' => "Record {$id}",
                                'issue' => 'orphaned_image',
                                'image' => $image,
                                'field' => $field
                            ];
                            $totalIssues++;
                        }
                    }
                }
            }
        }
    }
}

echo "\n📊 AUDIT SUMMARY:\n";
echo "================\n";
echo "Total issues found: {$totalIssues}\n\n";

if ($totalIssues > 0) {
    echo "🚨 ISSUES DETAILS:\n";
    echo "==================\n";
    foreach ($issues as $issue) {
        echo "Table: {$issue['table']}, ID: {$issue['id']}, Name: {$issue['name']}\n";
        echo "Issue: {$issue['issue']}, Image: " . ($issue['image'] ?? 'N/A') . "\n";
        if (isset($issue['field'])) {
            echo "Field: {$issue['field']}\n";
        }
        echo "---\n";
    }
} else {
    echo "✅ NO ISSUES FOUND! All images are working perfectly!\n";
}

echo "\n🔍 Checking storage directories:\n";
echo "===============================\n";
$directories = ['product', 'category', 'banner', 'cuisine', 'admin', 'branch', 'user'];
foreach ($directories as $dir) {
    $files = Storage::disk('public')->files($dir);
    echo "{$dir}/: " . count($files) . " files\n";
}

echo "\n✅ Deep audit completed!\n";

?>
