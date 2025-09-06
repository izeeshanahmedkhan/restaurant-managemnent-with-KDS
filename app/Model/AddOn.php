<?php

namespace App\Model;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AddOn extends Model
{
    protected $casts = [
        'price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getPriceAttribute($price): float
    {
        return (float)Helpers::set_price($price);
    }

    // Translation functionality removed

    public function getNameAttribute($name)
    {
        if (strpos(url()->current(), '/admin')) {
            return $name;
        }
        return $name; // Translation functionality removed
    }

    // Translation functionality removed
}
