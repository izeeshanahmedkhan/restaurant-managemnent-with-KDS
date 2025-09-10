<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PointTransitions extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'order_id'
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];
}
