<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Attribute extends Model
{
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
