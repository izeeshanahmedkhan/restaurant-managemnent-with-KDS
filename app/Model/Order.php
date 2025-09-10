<?php

namespace App\Model;

// Offline payment functionality removed
use App\Models\GuestUser;
use App\Models\OrderChangeAmount;
use App\Models\OrderPartialPayment;
use App\User;
use App\Models\OrderArea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $casts = [
        'order_amount' => 'float',
        'total_tax_amount' => 'float',
        'total_add_on_tax' => 'float',
        'delivery_address_id' => 'integer',
        'delivery_charge' => 'float',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'delivery_address' => 'array',
        'bring_change_amount' => 'float',
        'referral_discount' => 'float',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withCount('orders');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id')->withCount('orders');
    }

    public function delivery_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function customer_delivery_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }


    public function scopePos($query)
    {
        return $query->where('order_type', '=', 'pos');
    }

    public function scopeDineIn($query)
    {
        return $query->where('order_type', '=', 'dine_in');
    }


    public function scopeNotDineIn($query)
    {
        return $query->where('order_type', '!=', 'dine_in');
    }

    public function scopeNotPos($query)
    {
        return $query->where('order_type', '!=', 'pos');
    }

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kiosk::class, 'kiosk_id');
    }

    public function kioskUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\KioskUser::class, 'kiosk_user_id');
    }

    public function scopeKiosk($query)
    {
        return $query->where('order_source', '=', 'kiosk');
    }

    public function scopeSchedule($query)
    {
        return $query->whereDate('delivery_date', '>', \Carbon\Carbon::now()->format('Y-m-d'));
    }

    public function scopeNotSchedule($query)
    {
        return $query->whereDate('delivery_date', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
    }

    public function scopeEarningReport($query)
    {
        return $query->whereIn('order_status', ['delivered', 'completed']);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(OrderTransaction::class);
    }

    public function order_partial_payments(): HasMany
    {
        return $this->hasMany(OrderPartialPayment::class)->orderBy('id', 'DESC');
    }

    // Offline payment functionality removed

    public function scopePartial($query)
    {
        return $query->whereHas('partial_payment');
    }

    public function guest()
    {
        return $this->belongsTo(GuestUser::class, 'user_id');
    }


    public function order_area()
    {
        return $this->hasOne(OrderArea::class, 'order_id');
    }

    public function order_change_amount()
    {
        return $this->hasOne(OrderChangeAmount::class, 'order_id');
    }
}
