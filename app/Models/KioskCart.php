<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class KioskCart extends Model
{
    protected $fillable = [
        'session_id',
        'kiosk_id',
        'cart_data',
        'expires_at'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(Kiosk::class);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    public function isExpired()
    {
        return $this->expires_at <= Carbon::now();
    }

    public function extendExpiration($minutes = 5)
    {
        $this->expires_at = Carbon::now()->addMinutes($minutes);
        $this->save();
    }
}
