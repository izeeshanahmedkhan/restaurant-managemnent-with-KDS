<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Model\Category;
use App\Model\Product;
use App\Model\Order;
use App\Model\OrderDetail;
use App\User;

class KioskController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Invalid credentials'
        ], 401);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['success' => true]);
    }

    public function getMenu(): JsonResponse
    {
        try {
            $categories = Category::active()
                ->where('parent_id', 0)
                ->orderBy('priority', 'asc')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'icon' => '🍽️' // Default icon, you can add icon field to categories table
                    ];
                });

            $products = Product::active()
                ->with(['branch_product' => function ($query) {
                    $query->where('branch_id', 1); // Main branch
                }])
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'categoryId' => $product->category ? $product->category['id'] : 1,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'image' => $product->image_full_path,
                        'description' => $product->description ?? 'Delicious food item',
                        'modifierGroups' => [] // You can add modifier groups later
                    ];
                });

            return response()->json([
                'success' => true,
                'categories' => $categories,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load menu',
                'categories' => [],
                'products' => []
            ], 500);
        }
    }

    public function createOrder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'total' => 'required|numeric|min:0',
                'user_id' => 'nullable|integer|exists:users,id'
            ]);

            // Create order
            $order = Order::create([
                'user_id' => $validated['user_id'] ?? Auth::id(),
                'order_amount' => $validated['total'],
                'order_status' => 'pending',
                'order_type' => 'dine_in',
                'branch_id' => 1, // Main branch
                'delivery_charge' => 0,
                'total_tax_amount' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create order details
            foreach ($validated['items'] as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'tax_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => 'ORD-' . $order->id,
                    'total' => $order->order_amount,
                    'status' => $order->order_status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }
}
