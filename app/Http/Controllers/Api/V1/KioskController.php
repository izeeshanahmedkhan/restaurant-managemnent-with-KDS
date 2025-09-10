<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KioskUser;
use App\Models\Kiosk;
use App\Models\KioskCart;
use App\Model\Product;
use App\Model\Category;
use App\Model\AddOn;
use App\Model\Attribute;
use App\Model\ProductByBranch;
use App\Model\Order;
use App\Model\OrderDetail;
use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class KioskController extends Controller
{
    /**
     * Kiosk User Login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $kioskUser = KioskUser::where('email', $request->email)
            ->where('is_active', 1)
            ->with('kiosk.branch')
            ->first();

        if (!$kioskUser || !Hash::check($request->password, $kioskUser->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate API token
        $token = Str::random(80);
        $kioskUser->api_token = $token;
        $kioskUser->last_login_at = Carbon::now();
        $kioskUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $kioskUser->id,
                    'name' => $kioskUser->full_name,
                    'email' => $kioskUser->email,
                    'kiosk' => [
                        'id' => $kioskUser->kiosk->id,
                        'name' => $kioskUser->kiosk->name,
                        'branch' => [
                            'id' => $kioskUser->kiosk->branch->id,
                            'name' => $kioskUser->kiosk->branch->name
                        ]
                    ]
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Kiosk User Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $kioskUser = $request->get('kiosk_user');
        
        if ($kioskUser) {
            $kioskUser->api_token = null;
            $kioskUser->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get Current Kiosk User Info
     */
    public function me(Request $request): JsonResponse
    {
        $kioskUser = $request->get('kiosk_user');
        $kiosk = $request->get('kiosk');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $kioskUser->id,
                    'name' => $kioskUser->full_name,
                    'email' => $kioskUser->email,
                    'kiosk' => [
                        'id' => $kiosk->id,
                        'name' => $kiosk->name,
                        'branch' => [
                            'id' => $kiosk->branch->id,
                            'name' => $kiosk->branch->name
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get Branch Information
     */
    public function branch(Request $request): JsonResponse
    {
        $kiosk = $request->get('kiosk');
        $branch = $kiosk->branch;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'email' => $branch->email,
                'phone' => $branch->phone,
                'address' => $branch->address,
                'latitude' => $branch->latitude,
                'longitude' => $branch->longitude,
                'cover_image' => $branch->cover_image
            ]
        ]);
    }

    /**
     * Get Business Settings
     */
    public function settings(Request $request): JsonResponse
    {
        $settings = DB::table('business_settings')
            ->whereIn('key', [
                'restaurant_name',
                'logo',
                'phone',
                'email',
                'address',
                'currency_symbol',
                'currency_code'
            ])
            ->pluck('value', 'key')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get Categories
     */
    public function getCategories(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        
        // Get all active parent categories (same as POS system)
        $categories = Category::where('parent_id', 0)
            ->where('status', 1)
            ->orderBy('priority', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image_full_path,
                    'priority' => $category->priority
                ];
            })
        ]);
    }

    /**
     * Get Products
     */
    public function getProducts(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');

        $products = Product::join('product_by_branches', 'products.id', '=', 'product_by_branches.product_id')
            ->select('products.*', 'product_by_branches.price', 'product_by_branches.discount', 'product_by_branches.discount_type', 
                     'products.variations', 'product_by_branches.stock_type', 'product_by_branches.stock', 'product_by_branches.halal_status')
            ->where('product_by_branches.branch_id', $branchId)
            ->where('product_by_branches.is_available', 1)
            ->when($categoryId, function($query) use ($categoryId) {
                $query->whereJsonContains('category_ids', [['id' => (string)$categoryId]]);
            })
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->where('status', 1)
            ->orderBy('popularity_count', 'desc')
            ->paginate(20);

        $products->getCollection()->transform(function($product) {
            // Get add-ons details
            $addOnIds = json_decode($product->add_ons, true) ?? [];
            $addOns = [];
            if (!empty($addOnIds)) {
                $addOns = AddOn::whereIn('id', $addOnIds)->get()->map(function($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'tax' => $addon->tax ?? 0
                    ];
                })->toArray();
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $product->image_full_path,
                'price' => $product->price,
                'discount' => $product->discount,
                'discount_type' => $product->discount_type,
                'variations' => json_decode($product->variations, true) ?? [],
                'add_ons' => $addOns,
                'choice_options' => json_decode($product->choice_options, true) ?? [],
                'category_ids' => json_decode($product->category_ids, true) ?? [],
                'tax' => $product->tax,
                'product_type' => $product->product_type,
                'is_recommended' => $product->is_recommended,
                'stock_type' => $product->stock_type,
                'stock' => $product->stock,
                'halal_status' => $product->halal_status
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get Single Product
     */
    public function getProduct(Request $request, $id): JsonResponse
    {
        $branchId = $request->get('branch_id');
        
        $product = Product::join('product_by_branches', 'products.id', '=', 'product_by_branches.product_id')
            ->select('products.*', 'product_by_branches.price', 'product_by_branches.discount', 'product_by_branches.discount_type', 
                     'products.variations', 'product_by_branches.stock_type', 'product_by_branches.stock', 'product_by_branches.halal_status')
            ->where('product_by_branches.branch_id', $branchId)
            ->where('product_by_branches.is_available', 1)
            ->where('products.id', $id)
            ->where('products.status', 1)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or not available in this branch'
            ], 404);
        }

        // Get addons for this product
        $addonIds = $product->add_ons ? json_decode($product->add_ons, true) : [];
        $addons = AddOn::whereIn('id', $addonIds)->get();

        // Get attributes for this product
        $attributeIds = $product->attributes ? json_decode($product->attributes, true) : [];
        $attributes = Attribute::whereIn('id', $attributeIds)->get();

        // Transform variations data to match frontend expectations
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

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $product->image_full_path,
                'price' => $product->price,
                'discount' => $product->discount,
                'discount_type' => $product->discount_type,
                'variations' => $transformedVariations,
                'add_ons' => $addons->map(function($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'tax' => $addon->tax ?? 0
                    ];
                }),
                'choice_options' => json_decode($product->choice_options, true) ?? [],
                'category_ids' => json_decode($product->category_ids, true) ?? [],
                'tax' => $product->tax,
                'product_type' => $product->product_type,
                'is_recommended' => $product->is_recommended,
                'stock_type' => $product->stock_type,
                'stock' => $product->stock,
                'halal_status' => $product->halal_status
            ]
        ]);
    }

    /**
     * Search Products
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        $search = $request->query('q');

        if (!$search) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $products = Product::join('product_by_branches', 'products.id', '=', 'product_by_branches.product_id')
            ->select('products.*', 'product_by_branches.price', 'product_by_branches.discount', 'product_by_branches.discount_type', 
                     'products.variations', 'product_by_branches.stock_type', 'product_by_branches.stock', 'product_by_branches.halal_status')
            ->where('product_by_branches.branch_id', $branchId)
            ->where('product_by_branches.is_available', 1)
            ->where('products.name', 'like', "%{$search}%")
            ->where('products.status', 1)
            ->limit(20)
            ->get();

        $products = $products->map(function($product) {
            // Get add-ons details
            $addOnIds = json_decode($product->add_ons, true) ?? [];
            $addOns = [];
            if (!empty($addOnIds)) {
                $addOns = AddOn::whereIn('id', $addOnIds)->get()->map(function($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'tax' => $addon->tax ?? 0
                    ];
                })->toArray();
            }

            // Transform variations data to match frontend expectations
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

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $product->image_full_path,
                'price' => $product->price,
                'discount' => $product->discount,
                'discount_type' => $product->discount_type,
                'variations' => $transformedVariations,
                'add_ons' => $addOns,
                'choice_options' => json_decode($product->choice_options, true) ?? [],
                'category_ids' => json_decode($product->category_ids, true) ?? []
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get Addons
     */
    public function getAddons(Request $request): JsonResponse
    {
        $addons = AddOn::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $addons->map(function($addon) {
                return [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                    'tax' => $addon->tax ?? 0
                ];
            })
        ]);
    }

    /**
     * Get Attributes
     */
    public function getAttributes(Request $request): JsonResponse
    {
        $attributes = Attribute::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $attributes->map(function($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name
                ];
            })
        ]);
    }

    /**
     * Get Cart
     */
    public function getCart(Request $request): JsonResponse
    {
        $kiosk = $request->get('kiosk');
        $sessionId = $request->header('X-Session-ID', session()->getId());
        
        $cart = KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->active()
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'total' => 0,
                    'item_count' => 0,
                    'expires_at' => null
                ]
            ]);
        }

        // Check if cart is expired
        if ($cart->isExpired()) {
            $cart->delete();
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'total' => 0,
                    'item_count' => 0,
                    'expires_at' => null
                ]
            ]);
        }

        $cartData = $cart->cart_data ?? [];
        $total = 0;
        $itemCount = 0;

        // Convert object to array for frontend compatibility
        $itemsArray = [];
        foreach ($cartData as $key => $item) {
            $item['item_key'] = $key; // Add the key for frontend reference
            $itemsArray[] = $item;
            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;
            $itemCount += $item['quantity'];
            
            \Log::info('Cart Item Calculation', [
                'item_key' => $key,
                'product_name' => $item['product_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'item_total' => $itemTotal,
                'variations' => $item['variations'] ?? [],
                'add_ons' => $item['add_ons'] ?? []
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $itemsArray,
                'total' => $total,
                'item_count' => $itemCount,
                'expires_at' => $cart->expires_at->toISOString()
            ]
        ]);
    }

    /**
     * Add to Cart
     */
    public function addToCart(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'variations' => 'array',
                'add_ons' => 'array',
                'add_on_qtys' => 'array'
            ]);

            $kiosk = $request->get('kiosk');
            $branchId = $request->get('branch_id');
            $sessionId = $request->header('X-Session-ID', session()->getId());
            
            \Log::info('Cart Add Request', [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'session_id' => $sessionId,
                'kiosk_id' => $kiosk ? $kiosk->id : null,
                'branch_id' => $branchId,
                'variations' => $request->variations,
                'add_ons' => $request->add_ons
            ]);

        // Get product with branch pricing using direct query
        $product = Product::join('product_by_branches', 'products.id', '=', 'product_by_branches.product_id')
            ->where('products.id', $request->product_id)
            ->where('products.status', 1)
            ->where('product_by_branches.branch_id', $branchId)
            ->where('product_by_branches.is_available', 1)
            ->select('products.*', 'product_by_branches.price as branch_price', 'product_by_branches.discount as branch_discount', 'product_by_branches.discount_type as branch_discount_type', 'product_by_branches.variations as branch_variations')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not available in this branch'
            ], 404);
        }

        // Calculate price with variations and addons
        $price = $product->branch_price;
        $addonPrice = 0;
        $variationPrice = 0;

        // Handle variations
        $variationDetails = [];
        if ($request->variations && $product->variations) {
            $productVariations = is_string($product->variations) ? json_decode($product->variations, true) : $product->variations;
            
            \Log::info('Variation Processing', [
                'product_variations' => $productVariations,
                'request_variations' => $request->variations,
                'product_variations_json' => json_encode($productVariations, JSON_PRETTY_PRINT)
            ]);
            
            // Try using the helper function first
            $variationData = Helpers::get_varient($productVariations, $request->variations);
            $variationPrice = $variationData['price'] ?? 0;
            
            \Log::info('Helper Function Result', [
                'variationData' => $variationData,
                'variationPrice' => $variationPrice
            ]);
            
            // Store variation details for display
            $totalVariationPrice = 0;
            foreach ($request->variations as $variation) {
                $individualVariationPrice = 0;
                $selectedOptions = [];
                
                // Calculate price for each selected option
                if (isset($variation['values']['label'])) {
                    $selectedLabels = is_array($variation['values']['label']) ? $variation['values']['label'] : [$variation['values']['label']];
                    
                    foreach ($selectedLabels as $label) {
                        // Find the option in product variations and get its price
                        foreach ($productVariations as $productVariation) {
                            if ($productVariation['name'] === $variation['name'] && isset($productVariation['values'])) {
                                foreach ($productVariation['values'] as $option) {
                                    if ($option['label'] === $label) {
                                        // Try different possible field names for price
                                        $optionPrice = 0;
                                        if (isset($option['optionPrice'])) {
                                            $optionPrice = floatval($option['optionPrice']);
                                        } elseif (isset($option['price'])) {
                                            $optionPrice = floatval($option['price']);
                                        } elseif (isset($option['delta'])) {
                                            $optionPrice = floatval($option['delta']);
                                        }
                                        
                                        \Log::info('Option Price Found', [
                                            'label' => $label,
                                            'option' => $option,
                                            'optionPrice' => $optionPrice
                                        ]);
                                        
                                        $individualVariationPrice += $optionPrice;
                                        $totalVariationPrice += $optionPrice;
                                        $selectedOptions[] = [
                                            'label' => $label,
                                            'price' => $optionPrice
                                        ];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                
                $variationDetails[] = [
                    'name' => $variation['name'],
                    'values' => $variation['values'],
                    'price' => $individualVariationPrice,
                    'selected_options' => $selectedOptions
                ];
            }
            
            // Update the total variation price
            $variationPrice = $totalVariationPrice;
            
            \Log::info('Variation Price Calculation', [
                'product_id' => $request->product_id,
                'totalVariationPrice' => $totalVariationPrice,
                'variationPrice' => $variationPrice,
                'variationDetails' => $variationDetails
            ]);
        } else {
            // No variations selected
            $variationPrice = 0;
            $variationDetails = [];
        }

        // Handle addons (from products table)
        $addonDetails = [];
        if ($request->add_ons && $request->add_on_qtys) {
            $addons = AddOn::whereIn('id', $request->add_ons)->get();
            $addonData = Helpers::calculate_addon_price($addons, $request->add_on_qtys);
            $addonPrice = $addonData['total_add_on_price'] ?? 0;
            
            // Store addon details for display
            foreach ($addons as $index => $addon) {
                $addonDetails[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                    'quantity' => $request->add_on_qtys[$index] ?? 1
                ];
            }
            
            \Log::info('Addon Price Calculation', [
                'product_id' => $request->product_id,
                'addons' => $request->add_ons,
                'add_on_qtys' => $request->add_on_qtys,
                'addonPrice' => $addonPrice,
                'addonDetails' => $addonDetails
            ]);
        }

        $finalPrice = $price + $variationPrice + $addonPrice;

        \Log::info('Price Calculation', [
            'product_id' => $request->product_id,
            'product_name' => $product->name,
            'base_price' => $price,
            'variation_price' => $variationPrice,
            'addon_price' => $addonPrice,
            'final_price' => $finalPrice,
            'variations' => $request->variations,
            'add_ons' => $request->add_ons
        ]);

        // Get or create cart - use updateOrCreate to handle duplicates
        $cart = KioskCart::updateOrCreate(
            [
                'session_id' => $sessionId,
                'kiosk_id' => $kiosk->id
            ],
            [
                'expires_at' => Carbon::now()->addMinutes(5)
            ]
        );

        $cartData = $cart->cart_data ?? [];
        $itemKey = $request->product_id . '_' . json_encode($request->variations ?? []) . '_' . json_encode($request->add_ons ?? []);

        if (isset($cartData[$itemKey])) {
            $cartData[$itemKey]['quantity'] += $request->quantity;
        } else {
            $cartData[$itemKey] = [
                'product_id' => $request->product_id,
                'product_name' => $product->name,
                'product_image' => $product->image ? asset('storage/product/' . $product->image) : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=100&h=100&fit=crop',
                'price' => $finalPrice,
                'quantity' => $request->quantity,
                'variations' => $request->variations ?? [],
                'add_ons' => $request->add_ons ?? [],
                'add_on_qtys' => $request->add_on_qtys ?? [],
                'addon_details' => $addonDetails,
                'variation_details' => $variationDetails,
                'base_price' => $price,
                'variation_price' => $variationPrice,
                'addon_price' => $addonPrice,
                'item_breakdown' => [
                    'base_price' => $price,
                    'variation_cost' => $variationPrice,
                    'addon_cost' => $addonPrice,
                    'total' => $finalPrice
                ]
            ];
        }

        $cart->cart_data = $cartData;
        $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'data' => [
                    'item' => $cartData[$itemKey],
                    'cart_total' => array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, $cartData))
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart Add Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding item to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Cart Item
     */
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0'
        ]);

        $kiosk = $request->get('kiosk');
        $sessionId = $request->header('X-Session-ID', session()->getId());

        $cart = KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->active()
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        $cartData = $cart->cart_data ?? [];

        if ($request->quantity == 0) {
            unset($cartData[$request->item_key]);
        } else {
            if (isset($cartData[$request->item_key])) {
                $cartData[$request->item_key]['quantity'] = $request->quantity;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart'
                ], 404);
            }
        }

        $cart->cart_data = $cartData;
        $cart->extendExpiration(5);
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => [
                'items' => $cartData,
                'cart_total' => array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $cartData))
            ]
        ]);
    }

    /**
     * Remove from Cart
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'item_key' => 'required|string'
        ]);

        $kiosk = $request->get('kiosk');
        $sessionId = $request->header('X-Session-ID', session()->getId());

        $cart = KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->active()
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        $cartData = $cart->cart_data ?? [];
        unset($cartData[$request->item_key]);

        $cart->cart_data = $cartData;
        $cart->extendExpiration(5);
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'items' => $cartData,
                'cart_total' => array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $cartData))
            ]
        ]);
    }

    /**
     * Clear Cart
     */
    public function clearCart(Request $request): JsonResponse
    {
        $kiosk = $request->get('kiosk');
        $sessionId = $request->header('X-Session-ID', session()->getId());

        $cart = KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->active()
            ->first();

        if ($cart) {
            $cart->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    /**
     * Start Over (Clear and create new cart)
     */
    public function startOver(Request $request): JsonResponse
    {
        $kiosk = $request->get('kiosk');
        $sessionId = $request->header('X-Session-ID', session()->getId());

        // Delete existing cart
        KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->delete();

        // Create new cart
        $cart = KioskCart::create([
            'session_id' => $sessionId,
            'kiosk_id' => $kiosk->id,
            'cart_data' => [],
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Started fresh',
            'data' => [
                'items' => [],
                'total' => 0,
                'item_count' => 0,
                'expires_at' => $cart->expires_at->toISOString()
            ]
        ]);
    }

    /**
     * Create Order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card',
            'order_note' => 'nullable|string|max:500'
        ]);

        $kiosk = $request->get('kiosk');
        $kioskUser = $request->get('kiosk_user');
        $branchId = $request->get('branch_id');
        $sessionId = $request->header('X-Session-ID', session()->getId());

        // Get cart
        $cart = KioskCart::where('session_id', $sessionId)
            ->where('kiosk_id', $kiosk->id)
            ->active()
            ->first();

        if (!$cart || empty($cart->cart_data)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Check if cart is expired
        if ($cart->isExpired()) {
            $cart->delete();
            return response()->json([
                'success' => false,
                'message' => 'Cart has expired. Please start over.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Generate order ID
            $orderId = 100000 + Order::count() + 1;
            while (Order::find($orderId)) {
                $orderId = Order::orderBy('id', 'DESC')->first()->id + 1;
            }

            $cartData = $cart->cart_data;
            $totalTaxAmount = 0;
            $totalAddonPrice = 0;
            $totalAddonTax = 0;
            $productPrice = 0;
            $orderDetails = [];

            // Process each cart item
            foreach ($cartData as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                $branchProduct = ProductByBranch::where('product_id', $item['product_id'])
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$branchProduct) continue;

                // Calculate pricing - use base price from cart, not total price
                $basePrice = $item['base_price'] ?? $item['price']; // Fallback to total if base_price not available
                $variationPrice = $item['variation_price'] ?? 0;
                $addonPrice = $item['addon_price'] ?? 0;
                $totalPrice = $item['price']; // This is the final price (base + variations + addons)
                $discountOnProduct = 0;
                $discount = 0;

                // No discount calculation - set to 0
                $discount = 0;
                $discountData = [
                    'discount_type' => 'amount',
                    'discount' => 0
                ];

                $productSubtotal = $totalPrice * $item['quantity'];
                $discountOnProduct = 0;

                // Calculate addon prices
                $addonData = Helpers::calculate_addon_price(
                    AddOn::whereIn('id', $item['add_ons'])->get(), 
                    $item['add_on_qtys']
                );

                // Calculate tax
                $taxAmount = Helpers::new_tax_calculate($product, $basePrice, $discountData);
                $addonTax = $addonData['total_add_on_tax'] ?? 0;

                $orderDetail = [
                    'product_id' => $item['product_id'],
                    'product_details' => Helpers::product_data_formatting($product),
                    'quantity' => $item['quantity'],
                    'price' => $basePrice, // Save base price, not total price
                    'tax_amount' => $taxAmount,
                    'discount_on_product' => $discount,
                    'discount_type' => 'discount_on_product',
                    'variation' => json_encode($item['variations']),
                    'add_on_ids' => json_encode($item['add_ons']),
                    'add_on_qtys' => json_encode($item['add_on_qtys']),
                    'add_on_prices' => json_encode($addonData['addon_prices'] ?? []),
                    'add_on_taxes' => json_encode($addonData['addon_taxes'] ?? []),
                    'add_on_tax_amount' => $addonTax,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                \Log::info('Order Detail Creation', [
                    'base_price' => $basePrice,
                    'variation_price' => $variationPrice,
                    'addon_price' => $addonPrice,
                    'total_price' => $totalPrice,
                    'variations' => $item['variations'],
                    'add_ons' => $item['add_ons'],
                    'addon_prices' => $addonData['addon_prices'] ?? []
                ]);

                $totalTaxAmount += $taxAmount * $item['quantity'];
                $totalAddonTax += $addonTax;
                $productPrice += $totalPrice * $item['quantity'] - $discountOnProduct;
                $orderDetails[] = $orderDetail;

                // Update stock if needed
                if ($branchProduct->stock_type == 'daily' || $branchProduct->stock_type == 'fixed') {
                    $availableStock = $branchProduct->stock - $branchProduct->sold_quantity;
                    if ($availableStock < $item['quantity']) {
                        throw new \Exception('Insufficient stock for ' . $product->name);
                    }
                    $branchProduct->sold_quantity += $item['quantity'];
                    $branchProduct->save();
                }
            }

            $finalTotal = $productPrice + $totalTaxAmount + $totalAddonTax;

            // Create order
            $order = new Order();
            $order->id = $orderId;
            $order->user_id = null; // Guest order
            $order->kiosk_id = $kiosk->id;
            $order->kiosk_user_id = $kioskUser->id;
            $order->branch_id = $branchId;
            $order->order_source = 'kiosk';
            $order->payment_status = 'unpaid'; // Will be paid at counter
            $order->order_status = 'confirmed';
            $order->order_type = 'pos';
            $order->payment_method = $request->payment_method;
            $order->order_amount = $finalTotal;
            $order->total_tax_amount = $totalTaxAmount;
            $order->delivery_charge = 0;
            $order->order_note = $request->order_note;
            $order->checked = 1;
            $order->created_at = now();
            $order->updated_at = now();
            $order->save();

            // Add order ID to order details
            foreach ($orderDetails as $key => $detail) {
                $orderDetails[$key]['order_id'] = $order->id;
            }
            OrderDetail::insert($orderDetails);

            // Clear cart
            $cart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => 'K-' . $order->id,
                    'total_amount' => $finalTotal,
                    'payment_method' => $request->payment_method,
                    'status' => 'confirmed',
                    'created_at' => $order->created_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Order failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Orders
     */
    public function getOrders(Request $request): JsonResponse
    {
        $kiosk = $request->get('kiosk');
        $perPage = $request->query('per_page', 20);
        $status = $request->query('status');

        $query = Order::where('kiosk_id', $kiosk->id)
            ->where('order_source', 'kiosk')
            ->with('details');

        if ($status) {
            $query->where('order_status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $orders->getCollection()->transform(function($order) {
            return [
                'id' => $order->id,
                'order_number' => 'K-' . $order->id,
                'total_amount' => $order->order_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'item_count' => $order->details->sum('quantity'),
                'created_at' => $order->created_at->toISOString()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get Single Order
     */
    public function getOrder(Request $request, $id): JsonResponse
    {
        $kiosk = $request->get('kiosk');

        $order = Order::where('id', $id)
            ->where('kiosk_id', $kiosk->id)
            ->where('order_source', 'kiosk')
            ->with('details')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $orderDetails = $order->details->map(function($detail) {
            return [
                'id' => $detail->id,
                'product_name' => $detail->product_details['name'] ?? 'Unknown Product',
                'product_image' => $detail->product_details['image'] ?? '',
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'variation' => $detail->variation,
                'add_ons' => $detail->add_on_ids,
                'tax_amount' => $detail->tax_amount,
                'discount' => $detail->discount_on_product
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => 'K-' . $order->id,
                'total_amount' => $order->order_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'order_note' => $order->order_note,
                'created_at' => $order->created_at->toISOString(),
                'items' => $orderDetails
            ]
        ]);
    }

    /**
     * Get Order Receipt
     */
    public function getReceipt(Request $request, $id): JsonResponse
    {
        $kiosk = $request->get('kiosk');

        $order = Order::where('id', $id)
            ->where('kiosk_id', $kiosk->id)
            ->where('order_source', 'kiosk')
            ->with('details')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Get business settings for receipt
        $settings = DB::table('business_settings')
            ->whereIn('key', [
                'restaurant_name',
                'logo',
                'phone',
                'email',
                'address',
                'currency_symbol'
            ])
            ->pluck('value', 'key')
            ->toArray();

        $branch = $kiosk->branch;

        // Create a detailed receipt data structure for the frontend
        $receiptData = [
            'order_id' => $order->id,
            'order_number' => 'K-' . $order->id,
            'total' => $order->order_amount,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'items' => $order->details->map(function($detail) {
                $productDetails = json_decode($detail->product_details, true);
                $variations = json_decode($detail->variation, true) ?? [];
                $addonIds = json_decode($detail->add_on_ids, true) ?? [];
                $addonQtys = json_decode($detail->add_on_qtys, true) ?? [];
                $addonPrices = json_decode($detail->add_on_prices, true) ?? [];
                
                // Get the original product base price from product details
                $originalBasePrice = $productDetails['price'] ?? 0;
                $discountAmount = 0; // No discounts
                
                // Calculate variation price from stored data
                $variationPrice = 0;
                if (!empty($variations)) {
                    \Log::info('Receipt Debug - Variations', [
                        'variations_raw' => $variations,
                        'detail_id' => $detail->id
                    ]);
                    
                    foreach ($variations as $variation) {
                        if (isset($variation['values']) && is_array($variation['values'])) {
                            // Handle the structure: {name: "Size", values: {label: ["Large"], price: [2.00]}}
                            if (isset($variation['values']['price']) && is_array($variation['values']['price'])) {
                                foreach ($variation['values']['price'] as $price) {
                                    $variationPrice += floatval($price);
                                }
                            } else {
                                // Handle other structures
                                foreach ($variation['values'] as $value) {
                                    if (isset($value['optionPrice'])) {
                                        $variationPrice += floatval($value['optionPrice']);
                                    } elseif (isset($value['price'])) {
                                        $variationPrice += floatval($value['price']);
                                    } elseif (isset($value['delta'])) {
                                        $variationPrice += floatval($value['delta']);
                                    }
                                }
                            }
                        }
                    }
                }
                
                \Log::info('Receipt Debug - Variation Price', [
                    'variation_price' => $variationPrice,
                    'detail_id' => $detail->id
                ]);
                
                // Calculate addon price from stored data
                $addonPrice = 0;
                if (!empty($addonPrices)) {
                    \Log::info('Receipt Debug - Addon Prices', [
                        'addon_prices_raw' => $addonPrices,
                        'detail_id' => $detail->id
                    ]);
                    
                    foreach ($addonPrices as $price) {
                        $addonPrice += floatval($price);
                    }
                }
                
                \Log::info('Receipt Debug - Addon Price', [
                    'addon_price' => $addonPrice,
                    'detail_id' => $detail->id
                ]);
                
                // Use the original base price for display, but calculate total correctly
                $basePrice = $originalBasePrice;
                $itemTotalPrice = $basePrice + $variationPrice + $addonPrice;
                
                // Get addon details
                $addons = [];
                if (!empty($addonIds)) {
                    $addonModels = \App\Model\AddOn::whereIn('id', $addonIds)->get();
                    foreach ($addonIds as $index => $addonId) {
                        $addon = $addonModels->firstWhere('id', $addonId);
                        if ($addon) {
                            $qty = $addonQtys[$index] ?? 1;
                            $price = $addonPrices[$index] ?? $addon->price;
                            $addons[] = [
                                'name' => $addon->name,
                                'quantity' => $qty,
                                'price' => $price
                            ];
                        }
                    }
                }
                
                // Process variations for display
                $variationDetails = [];
                if (!empty($variations)) {
                    foreach ($variations as $variation) {
                        if (isset($variation['values']) && is_array($variation['values'])) {
                            // Handle the structure: {name: "Size", values: {label: ["Large"], price: [2.00]}}
                            if (isset($variation['values']['label']) && is_array($variation['values']['label'])) {
                                $labels = $variation['values']['label'];
                                $prices = $variation['values']['price'] ?? [];
                                
                                foreach ($labels as $index => $label) {
                                    $variationDetails[] = [
                                        'name' => $variation['name'] ?? 'Variation',
                                        'label' => $label,
                                        'price' => isset($prices[$index]) ? floatval($prices[$index]) : 0
                                    ];
                                }
                            } else {
                                // Handle other structures
                                foreach ($variation['values'] as $value) {
                                    $variationDetails[] = [
                                        'name' => $variation['name'] ?? 'Variation',
                                        'label' => $value['label'] ?? $value['name'] ?? 'Option',
                                        'price' => $value['optionPrice'] ?? $value['price'] ?? $value['delta'] ?? 0
                                    ];
                                }
                            }
                        } else {
                            // Handle case where variation is stored differently
                            $variationDetails[] = [
                                'name' => $variation['name'] ?? 'Variation',
                                'label' => $variation['label'] ?? $variation['name'] ?? 'Option',
                                'price' => $variation['price'] ?? $variation['delta'] ?? 0
                            ];
                        }
                    }
                }
                
                return [
                    'product_name' => $productDetails['name'] ?? 'Unknown Product',
                    'quantity' => $detail->quantity,
                    'base_price' => $basePrice,
                    'discount_amount' => $discountAmount,
                    'variation_price' => $variationPrice,
                    'addon_price' => $addonPrice,
                    'variations' => $variationDetails,
                    'addons' => $addons,
                    'total_price' => $itemTotalPrice * $detail->quantity
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $receiptData
        ]);
    }
}
