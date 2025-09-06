<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'status' => 'integer',
        'priority' => 'integer'
    ];

    // Translation functionality removed

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function childes(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getNameAttribute($name)
    {
        if (auth('admin')->check() || auth('branch')->check()) {
            return $name;
        }
        return $name; // Translation functionality removed
    }

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('public/assets/admin/img/160x160/img2.jpg');

        if (!is_null($image) && Storage::disk('public')->exists('category/' . $image)) {
            $path = asset('storage/category/' . $image);
        }
        return $path;
    }

    public function getBannerImageFullPathAttribute(): string
    {
        $image = $this->banner_image ?? null;
        $path = asset('public/assets/admin/img/160x160/img2.jpg');

        if (!is_null($image) && Storage::disk('public')->exists('category/banner/' . $image)) {
            $path = asset('storage/category/banner/' . $image);
        }
        return $path;
    }

    // Translation functionality removed
}
