<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'price',
        'product_details',
        'variation',
        'add_on_ids',
        'discount_on_product',
        'discount_type',
        'quantity',
        'variant',
        'add_on_qtys',
        'add_on_taxes',
        'add_on_prices',
        'add_on_tax_amount',
        'tax_amount'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'order_id' => 'integer',
        'price' => 'float',
        'discount_on_product' => 'float',
        'quantity' => 'integer',
        'tax_amount' => 'float',
        'add_on_tax_amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Review functionality removed
}
