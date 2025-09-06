<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BusinessSetting extends Model
{
    // Translation functionality removed

    protected $fillable = [
        'key',
        'value'
    ];
}
