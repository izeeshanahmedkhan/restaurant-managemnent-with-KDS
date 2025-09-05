<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza N Gyro - Images Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #e74c3c;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .image-card:hover {
            transform: translateY(-5px);
        }
        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .image-info {
            padding: 15px;
        }
        .image-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .image-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.success {
            background-color: #d4edda;
            color: #155724;
        }
        .status.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .banner-section {
            margin-bottom: 30px;
        }
        .banner-image {
            width: 100%;
            max-width: 800px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🍕 Pizza N Gyro - Images Test</h1>
        <p>Testing all downloaded images for proper display</p>
    </div>

    <?php
    $baseUrl = 'storage/';
    
    // Test if image exists and is accessible
    function testImage($imagePath) {
        $fullPath = $imagePath;
        if (file_exists($fullPath)) {
            return ['exists' => true, 'size' => filesize($fullPath)];
        }
        return ['exists' => false, 'size' => 0];
    }
    
    // Product images to test
    $products = [
        ['name' => 'Super Charger Burger', 'image' => 'product/super-charger-burger.jpg'],
        ['name' => 'Beef Spicy Burger', 'image' => 'product/beef-spicy-burger.jpg'],
        ['name' => 'Grilled Cheese Burger', 'image' => 'product/grilled-cheese-burger-alt.jpg'],
        ['name' => 'Italian Spicy Pizza', 'image' => 'product/italian-spicy-pizza.jpg'],
        ['name' => 'Mozzarella Cheese Pizza', 'image' => 'product/mozzarella-cheese-pizza.jpg'],
        ['name' => 'Chicken Biryani', 'image' => 'product/chicken-biryani-alt.jpg'],
        ['name' => 'Beef Biryani with Spice Masala', 'image' => 'product/beef-biryani-masala.jpg'],
        ['name' => 'Set Menu 1', 'image' => 'product/set-menu-1.jpg'],
        ['name' => 'Set Menu 2', 'image' => 'product/set-menu-2.jpg'],
        ['name' => 'Cheese Sandwich Grilled', 'image' => 'product/cheese-sandwich-grilled.jpg'],
        ['name' => 'Spicy Burger', 'image' => 'product/spicy-burger-alt.jpg']
    ];
    
    $categories = [
        ['name' => 'Fish and Rice', 'image' => 'category/fish-and-rice.jpg']
    ];
    
    $banners = [
        ['name' => 'Restaurant Banner', 'image' => 'banner/restaurant-banner-1.jpg'],
        ['name' => 'Food Banner', 'image' => 'banner/food-banner-2.jpg']
    ];
    ?>

    <!-- Banners Section -->
    <div class="section banner-section">
        <h2>🎨 Banner Images</h2>
        <?php foreach ($banners as $banner): 
            $test = testImage($banner['image']); ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <h3><?php echo $banner['name']; ?> 
                    <span class="status <?php echo $test['exists'] ? 'success' : 'error'; ?>">
                        <?php echo $test['exists'] ? '✓ Loaded' : '✗ Missing'; ?>
                    </span>
                </h3>
                <img src="<?php echo $baseUrl . $banner['image']; ?>" alt="<?php echo $banner['name']; ?>" class="banner-image" onerror="this.style.display='none'; this.nextSibling.style.display='block';">
                <div style="display:none; padding: 20px; background: #f8d7da; color: #721c24; border-radius: 4px;">
                    Image not found: <?php echo $banner['image']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Products Section -->
    <div class="section">
        <h2>🍔 Product Images</h2>
        <div class="image-grid">
            <?php foreach ($products as $product): 
                $test = testImage($product['image']); ?>
                <div class="image-card">
                    <img src="<?php echo $baseUrl . $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNmOGY5ZmEiLz48dGV4dCB4PSIxNTAiIHk9IjEwMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZSBOb3QgRm91bmQ8L3RleHQ+PC9zdmc+'">
                    <div class="image-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p>Path: <?php echo $product['image']; ?></p>
                        <p>
                            <span class="status <?php echo $test['exists'] ? 'success' : 'error'; ?>">
                                <?php echo $test['exists'] ? '✓ Loaded (' . round($test['size']/1024, 1) . 'KB)' : '✗ Missing'; ?>
                            </span>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="section">
        <h2>📂 Category Images</h2>
        <div class="image-grid">
            <?php foreach ($categories as $category): 
                $test = testImage($category['image']); ?>
                <div class="image-card">
                    <img src="<?php echo $baseUrl . $category['image']; ?>" alt="<?php echo $category['name']; ?>" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNmOGY5ZmEiLz48dGV4dCB4PSIxNTAiIHk9IjEwMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZSBOb3QgRm91bmQ8L3RleHQ+PC9zdmc+'">
                    <div class="image-info">
                        <h3><?php echo $category['name']; ?></h3>
                        <p>Path: <?php echo $category['image']; ?></p>
                        <p>
                            <span class="status <?php echo $test['exists'] ? 'success' : 'error'; ?>">
                                <?php echo $test['exists'] ? '✓ Loaded (' . round($test['size']/1024, 1) . 'KB)' : '✗ Missing'; ?>
                            </span>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section" style="text-align: center; padding: 20px; background: white; border-radius: 8px;">
        <h3>🎯 Summary</h3>
        <p>All images have been downloaded and placed in the correct directories.</p>
        <p>The storage symlink has been created to make images web-accessible.</p>
        <p>The SQL file has been updated with the new image paths.</p>
        <p><strong>Next step:</strong> Import the updated SQL file to see images in your application!</p>
    </div>
</body>
</html>