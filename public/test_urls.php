<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza N Gyro - URL Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 30px; color: #333; }
        .section { margin-bottom: 40px; }
        .section-title { color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 5px; margin-bottom: 20px; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .image-item { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; padding: 10px; }
        .image-item img { max-width: 100%; height: 150px; object-fit: contain; border-bottom: 1px solid #eee; margin-bottom: 10px; }
        .image-item p { font-size: 0.9em; color: #555; margin: 5px 0; }
        .status-ok { color: green; font-weight: bold; }
        .status-fail { color: red; font-weight: bold; }
        .url-info { font-size: 0.8em; color: #666; word-break: break-all; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pizza N Gyro - Image URL Test</h1>
        <p>Testing Laravel storage URLs and image accessibility</p>
    </div>

    <?php
    $baseUrl = 'http://localhost:8000/storage/';
    
    $imageCategories = [
        'product' => [
            'super-charger-burger.jpg', 'beef-spicy-burger.jpg', 'grilled-cheese-burger-alt.jpg',
            'italian-spicy-pizza.jpg', 'mozzarella-cheese-pizza.jpg', 'chicken-biryani-alt.jpg',
            'beef-biryani-masala.jpg', 'set-menu-1.jpg', 'set-menu-2.jpg',
            'cheese-sandwich-grilled.jpg', 'spicy-burger-alt.jpg'
        ],
        'category' => [
            'fish-and-rice.jpg'
        ],
        'banner' => [
            'restaurant-banner-1.jpg', 'food-banner-2.jpg'
        ]
    ];

    foreach ($imageCategories as $category => $images):
        if (!empty($images)):
    ?>
        <div class="section">
            <h2 class="section-title"><?= ucfirst($category) ?> Images</h2>
            <div class="image-grid">
                <?php foreach ($images as $imageName):
                    $fileUrl = $baseUrl . $category . '/' . $imageName;
                    $filePath = __DIR__ . '/storage/' . $category . '/' . $imageName;
                    $exists = file_exists($filePath);
                    $fileSize = $exists ? round(filesize($filePath) / 1024, 2) . ' KB' : 'N/A';
                ?>
                    <div class="image-item">
                        <img src="<?= $fileUrl ?>" alt="<?= $imageName ?>" onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=Image+Not+Found';">
                        <p><strong><?= $imageName ?></strong></p>
                        <p>Status: <span class="<?= $exists ? 'status-ok' : 'status-fail' ?>"><?= $exists ? 'OK' : 'MISSING' ?></span></p>
                        <p>Size: <?= $fileSize ?></p>
                        <p class="url-info">URL: <?= $fileUrl ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
        endif;
    endforeach;
    ?>
</body>
</html>
