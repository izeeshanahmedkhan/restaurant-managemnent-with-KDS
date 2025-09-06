<?php

namespace App\CentralLogics;


use App\Model\Product;
// Review functionality removed
use App\Model\Wishlist;
use Carbon\Carbon;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->branchProductAvailability()
            // Review functionality removed
            ->with(['branch_product'])
            ->where('id', $id)
            ->first();
    }

    public static function get_latest_products($limit, $offset, $product_type, $name, $category_ids, $sort_by, $is_halal)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $key = explode(' ', $name);
        $paginator = Product::active()
            // Review functionality removed
            ->with(['branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }})
            ->when(isset($product_type) && ($product_type == 'veg' || $product_type == 'non_veg'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->when(isset($category_ids), function ($query) use ($category_ids) {
                return $query->whereJsonContains('category_ids', ['id'=>$category_ids]);
            })
            ->when(isset($is_halal) && $is_halal == 1, function ($query) {
                return $query->whereHas('branch_product', function ($q) {
                    $q->where('halal_status', 1);
                });
            })
            ->when($sort_by == 'popular', function ($query){
                return $query->orderBy('popularity_count', 'desc');
            })
            ->when($sort_by == 'price_high_to_low', function ($query){
                return $query->orderBy('price', 'desc');
            })
            ->when($sort_by == 'price_low_to_high', function ($query){
                return $query->orderBy('price', 'asc');
            })
            ->latest() // default sorting
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items(),
        ];
    }

    public static function get_wishlished_products($limit, $offset, $request)
    {
        $product_ids = Wishlist::where('user_id', auth('api')->user()->id)->get()->pluck('product_id')->toArray();
        $products = Product::active()
            // Review functionality removed
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->whereIn('id', $product_ids)
            ->orderBy("created_at", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $products->items()
        ];
    }

    public static function get_popular_products($limit, $offset, $product_type, $name, $is_halal)
    {
        $limit = is_null($limit) ? null : $limit;
        $offset = is_null($offset) ? 1 : $offset;
        $key = explode(' ', $name);

        $paginator = Product::active()
            // Review functionality removed
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when(isset($product_type) && ($product_type == 'veg' || $product_type == 'non_veg'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->when(isset($is_halal) && $is_halal == 1, function ($query) {
                return $query->whereHas('branch_product', function ($q) {
                    $q->where('halal_status', 1);
                });
            })
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('tags',function($query) use ($key){
                        $query->where(function($q) use ($key){
                            foreach ($key as $value) {
                                $q->where('tag', 'like', "%{$value}%");
                            };
                        });
                    });
                });
            })
            ->orderBy('popularity_count', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()
            // Review functionality removed
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $rating, $category_id, $product_type, $sort_by, $limit, $offset, $min_price, $max_price, $is_halal)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        if($product_type != 'veg' && $product_type != 'non_veg') {
            $product_type = 'all';
        }

        $rating_product_ids = [];
        if (isset($rating)){
            $rating_product_ids = Product::active()
                // Review functionality removed
                ->pluck('id')
                ->toArray();
        }

        $product_ids_for_category = [];
        if (isset($category_id)){
            foreach (gettype($category_id) != 'array' ? json_decode($category_id) : $category_id as $categoryId) {
                $product_ids = Product::active()
                    ->where(function ($query) use ($categoryId) {
                        $query->whereJsonContains('category_ids', ['id' => (string)$categoryId]);
                    })
                    ->pluck('id')
                    ->toArray();
                $product_ids_for_category = array_unique(array_merge($product_ids_for_category, $product_ids));
            }
        }

        $key = explode(' ', $name);

        $paginator = Product::active()
            // Review functionality removed
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when(isset($product_type) && ($product_type != 'all'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->when(isset($is_halal) && $is_halal == 1, function ($query) {
                return $query->whereHas('branch_product', function ($q) {
                    $q->where('halal_status', 1);
                });
            })
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('tags',function($query) use ($key){
                        $query->where(function($q) use ($key){
                            foreach ($key as $value) {
                                $q->where('tag', 'like', "%{$value}%");
                            }
                        });
                    });
                });
            })
            ->when(($max_price != null), function ($query) use ($max_price) {
                return $query->where('price', '<=', $max_price);
            })
            ->when(($min_price != null && $max_price != null), function ($query) use ($min_price, $max_price) {
                return $query->where('price', '>=', $min_price)
                    ->where('price', '<', $max_price);
            })
            ->when(isset($category_id), function ($query) use ($product_ids_for_category) {
                $query->whereIn('id', $product_ids_for_category);
            })
            ->when(isset($rating), function ($query) use ($rating_product_ids) {
                $query->whereIn('id', $rating_product_ids);
            })
            ->when(isset($sort_by) && $sort_by == 'new_arrival', function ($query) use ($sort_by) {
                return $query->whereBetween('created_at', [Carbon::now()->subMonth(3), Carbon::now()]);
            })
            ->when(isset($sort_by) && $sort_by == 'popular', function ($query) use ($sort_by) {
                return $query->orderBy('popularity_count', 'DESC');
            })
            ->when(isset($sort_by) && $sort_by == 'price_high_to_low', function ($query) use ($sort_by) {
                return $query->orderBy('price', 'DESC');
            })
            ->when(isset($sort_by) && $sort_by == 'price_low_to_high', function ($query) use ($sort_by) {
                return $query->orderBy('price', 'ASC');
            })
            ->when(isset($sort_by) && $sort_by == 'a_to_z', function ($query) use ($sort_by) {
                return $query->orderBy('name', 'ASC');
            })
            ->when(isset($sort_by) && $sort_by == 'z_to_a', function ($query) use ($sort_by) {
                return $query->orderBy('name', 'DESC');
            })
            ->when(is_null($sort_by), function ($query) use ($name){
                $query->orderByRaw("
                    CASE
                        WHEN name = '$name' THEN 0
                        WHEN name LIKE '$name%' THEN 1
                        WHEN name LIKE '%$name%' THEN 2
                        WHEN name LIKE '%$name' THEN 3
                        ELSE 4
                    END
                ");
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        $productMaxPrice = Product::active()->max('price') ?? 0;

        return [
            'total_size' => $paginator->total(),
            'product_max_price' => $productMaxPrice,
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    // Review functionality removed

    // Review rating functionality removed

    public static function get_recommended_products($limit, $offset, $name)
    {
        $limit = is_null($limit) ? null : $limit;
        $offset = is_null($offset) ? 1 : $offset;
        $key = explode(' ', $name);

        $paginator = Product::active()
            // Review functionality removed
            ->with(['branch_product'])
            ->where('is_recommended', 1)
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('tags',function($query) use ($key){
                        $query->where(function($q) use ($key){
                            foreach ($key as $value) {
                                $q->where('tag', 'like', "%{$value}%");
                            };
                        });
                    });
                });
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items(),
        ];
    }

    public static function get_frequently_bought_products($limit, $offset)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $paginator = Product::active()
            // Review functionality removed
            ->with(['branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->inRandomOrder() // Random order
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items(),
        ];
    }

}
