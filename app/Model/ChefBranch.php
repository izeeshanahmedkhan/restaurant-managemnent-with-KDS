<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChefBranch extends Model
{
    protected $table = 'chef_branch';
    protected $fillable = ['user_id', 'branch_id'];
    public $timestamps = true;
    
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
