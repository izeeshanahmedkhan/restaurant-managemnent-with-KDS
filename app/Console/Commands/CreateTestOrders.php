<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Model\Product;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Category;

class CreateTestOrders extends Command
{
    protected $signature = 'test:orders';
    protected $description = 'Create test orders for KDS demonstration';

    public function handle()
    {
        $this->info('Creating test orders for KDS demonstration...');

        // Get or create customer
        $customer = User::first();
        if (!$customer) {
            $this->info('Creating test customer...');
            $customer = User::create([
                'f_name' => 'Test',
                'l_name' => 'Customer',
                'phone' => '1234567890',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'is_phone_verified' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get or create category
        $category = Category::first();
        if (!$category) {
            $this->info('Creating test category...');
            $category = Category::create([
                'name' => 'Test Category',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get or create product
        $product = Product::first();
        if (!$product) {
            $this->info('Creating test product...');
            $product = Product::create([
                'name' => 'Test Pizza',
                'description' => 'A delicious test pizza',
                'price' => 15.99,
                'branch_id' => 1,
                'category_id' => $category->id,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Create test orders with different statuses
        $orders = [
            [
                'user_id' => $customer->id,
                'branch_id' => 1,
                'order_status' => 'confirmed',
                'order_amount' => 15.99,
                'delivery_charge' => 2.00,
                'total_tax_amount' => 1.50,
                'order_note' => 'Test confirmed order - Ready to cook',
                'token' => 'C001',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $customer->id,
                'branch_id' => 1,
                'order_status' => 'cooking',
                'order_amount' => 25.99,
                'delivery_charge' => 2.00,
                'total_tax_amount' => 2.50,
                'order_note' => 'Test cooking order - In progress',
                'token' => 'C002',
                'created_at' => now()->subMinutes(10),
                'updated_at' => now()->subMinutes(5)
            ],
            [
                'user_id' => $customer->id,
                'branch_id' => 1,
                'order_status' => 'done',
                'order_amount' => 35.99,
                'delivery_charge' => 2.00,
                'total_tax_amount' => 3.50,
                'order_note' => 'Test done order - Ready for pickup',
                'token' => 'C003',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(5)
            ]
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);
            
            // Create order details
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 3),
                'price' => $product->price,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("Created order #{$order->id} with status: {$order->order_status} (Token: {$order->token})");
        }

        $this->info('Test orders created successfully!');
        $this->info('You can now refresh the KDS dashboard to see the orders.');
        
        return 0;
    }
}