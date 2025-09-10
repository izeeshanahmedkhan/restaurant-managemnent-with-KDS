<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kiosk extends Model
{
    protected $fillable = [
        'name',
        'branch_id',
        'device_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Model\Branch::class);
    }

    public function kioskUsers(): HasMany
    {
        return $this->hasMany(KioskUser::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(KioskCart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
