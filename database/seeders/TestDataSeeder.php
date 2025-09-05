<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🍕 Setting up Pizza N Gyro test data...');

        // Clear existing data
        DB::table('products')->truncate();
        DB::table('add_ons')->truncate();
        DB::table('categories')->truncate();
        DB::table('cuisines')->truncate();

        // Insert Categories
        $categories = [
            ['name' => 'Burgers', 'parent_id' => 0, 'position' => 1, 'status' => 1, 'priority' => 10, 'image' => 'burger-cat.png', 'banner_image' => 'burger-banner.png', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pizza', 'parent_id' => 0, 'position' => 2, 'status' => 1, 'priority' => 9, 'image' => 'pizza-cat.png', 'banner_image' => 'pizza-banner.png', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asian Food', 'parent_id' => 0, 'position' => 3, 'status' => 1, 'priority' => 8, 'image' => 'asian-cat.png', 'banner_image' => 'asian-banner.png', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desserts', 'parent_id' => 0, 'position' => 4, 'status' => 1, 'priority' => 7, 'image' => 'dessert-cat.png', 'banner_image' => 'dessert-banner.png', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beverages', 'parent_id' => 0, 'position' => 5, 'status' => 1, 'priority' => 6, 'image' => 'beverage-cat.png', 'banner_image' => 'beverage-banner.png', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }

        // Insert Cuisines
        $cuisines = [
            ['name' => 'American', 'image' => 'american-cuisine.png', 'is_active' => 1, 'priority' => 10, 'sub_title' => 'Classic American flavors', 'is_featured' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Italian', 'image' => 'italian-cuisine.png', 'is_active' => 1, 'priority' => 9, 'sub_title' => 'Authentic Italian taste', 'is_featured' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asian', 'image' => 'asian-cuisine.png', 'is_active' => 1, 'priority' => 8, 'sub_title' => 'Traditional Asian cuisine', 'is_featured' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($cuisines as $cuisine) {
            DB::table('cuisines')->insert($cuisine);
        }

        // Insert Addons
        $addons = [
            ['name' => 'Extra Cheese', 'price' => 15.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Bacon', 'price' => 25.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Pickles', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Onions', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Lettuce', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Tomato', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Mushrooms', 'price' => 10.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Jalapeños', 'price' => 8.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Olives', 'price' => 8.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Pineapple', 'price' => 10.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Side Fries', 'price' => 20.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Side Onion Rings', 'price' => 25.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Side Coleslaw', 'price' => 15.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Side Salad', 'price' => 18.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Sauce', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Spicy Sauce', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BBQ Sauce', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ranch Dressing', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Caesar Dressing', 'price' => 5.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Extra Crust', 'price' => 10.00, 'tax' => 0.00, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($addons as $addon) {
            DB::table('add_ons')->insert($addon);
        }

        // Insert Products with variations and addons
        $products = [
            [
                'name' => 'Classic Beef Burger',
                'description' => 'Juicy beef patty with lettuce, tomato, onion, and our special sauce',
                'image' => 'classic-beef-burger.png',
                'price' => 120.00,
                'variations' => '[{"name":"Size","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Regular","optionPrice":"0"},{"label":"Large","optionPrice":"30"},{"label":"Extra Large","optionPrice":"50"}]}]',
                'add_ons' => '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]',
                'tax' => 5.00,
                'available_time_starts' => '00:00:00',
                'available_time_ends' => '23:59:00',
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => '[{"id":"1","position":1}]',
                'choice_options' => '[]',
                'discount' => 10.00,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'set_menu' => 0,
                'branch_id' => 1,
                'colors' => null,
                'popularity_count' => 0,
                'product_type' => 'non_veg',
                'is_recommended' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chicken Deluxe Burger',
                'description' => 'Crispy chicken breast with cheese, lettuce, and mayo',
                'image' => 'chicken-deluxe-burger.png',
                'price' => 100.00,
                'variations' => '[{"name":"Size","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Regular","optionPrice":"0"},{"label":"Large","optionPrice":"25"},{"label":"Extra Large","optionPrice":"45"}]}]',
                'add_ons' => '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]',
                'tax' => 5.00,
                'available_time_starts' => '00:00:00',
                'available_time_ends' => '23:59:00',
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => '[{"id":"1","position":1}]',
                'choice_options' => '[]',
                'discount' => 5.00,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'set_menu' => 0,
                'branch_id' => 1,
                'colors' => null,
                'popularity_count' => 0,
                'product_type' => 'non_veg',
                'is_recommended' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Veggie Supreme Burger',
                'description' => 'Plant-based patty with fresh vegetables and vegan cheese',
                'image' => 'veggie-supreme-burger.png',
                'price' => 90.00,
                'variations' => '[{"name":"Size","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Regular","optionPrice":"0"},{"label":"Large","optionPrice":"20"},{"label":"Extra Large","optionPrice":"40"}]}]',
                'add_ons' => '[1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]',
                'tax' => 5.00,
                'available_time_starts' => '00:00:00',
                'available_time_ends' => '23:59:00',
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => '[{"id":"1","position":1}]',
                'choice_options' => '[]',
                'discount' => 8.00,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'set_menu' => 0,
                'branch_id' => 1,
                'colors' => null,
                'popularity_count' => 0,
                'product_type' => 'veg',
                'is_recommended' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Margherita Pizza',
                'description' => 'Classic tomato sauce, mozzarella, and fresh basil',
                'image' => 'margherita-pizza.png',
                'price' => 200.00,
                'variations' => '[{"name":"Size","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Small (10\")","optionPrice":"0"},{"label":"Medium (12\")","optionPrice":"50"},{"label":"Large (14\")","optionPrice":"100"},{"label":"Extra Large (16\")","optionPrice":"150"}]},{"name":"Crust","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Thin Crust","optionPrice":"0"},{"label":"Regular Crust","optionPrice":"0"},{"label":"Thick Crust","optionPrice":"20"},{"label":"Stuffed Crust","optionPrice":"30"}]}]',
                'add_ons' => '[1,7,8,9,10,20,21,22,23,24]',
                'tax' => 5.00,
                'available_time_starts' => '00:00:00',
                'available_time_ends' => '23:59:00',
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => '[{"id":"2","position":1}]',
                'choice_options' => '[]',
                'discount' => 10.00,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'set_menu' => 0,
                'branch_id' => 1,
                'colors' => null,
                'popularity_count' => 0,
                'product_type' => 'veg',
                'is_recommended' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pepperoni Pizza',
                'description' => 'Classic pepperoni with mozzarella cheese',
                'image' => 'pepperoni-pizza.png',
                'price' => 250.00,
                'variations' => '[{"name":"Size","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Small (10\")","optionPrice":"0"},{"label":"Medium (12\")","optionPrice":"60"},{"label":"Large (14\")","optionPrice":"120"},{"label":"Extra Large (16\")","optionPrice":"180"}]},{"name":"Crust","type":"single","min":1,"max":1,"required":"on","values":[{"label":"Thin Crust","optionPrice":"0"},{"label":"Regular Crust","optionPrice":"0"},{"label":"Thick Crust","optionPrice":"25"},{"label":"Stuffed Crust","optionPrice":"35"}]}]',
                'add_ons' => '[1,2,7,8,9,10,20,21,22,23,24]',
                'tax' => 5.00,
                'available_time_starts' => '00:00:00',
                'available_time_ends' => '23:59:00',
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => '[{"id":"2","position":1}]',
                'choice_options' => '[]',
                'discount' => 8.00,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'set_menu' => 0,
                'branch_id' => 1,
                'colors' => null,
                'popularity_count' => 0,
                'product_type' => 'non_veg',
                'is_recommended' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }

        $this->command->info('✅ Test data setup completed!');
        $this->command->info('📊 Created: 5 Categories, 3 Cuisines, 20 Addons, 5 Products');
        $this->command->info('🎯 Test KDS at: http://127.0.0.1:8000/branch/kds/dashboard');
    }
}
