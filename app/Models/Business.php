<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'owner_id', 'status', 'activated_at', 'expired_at'];

    protected $casts = [
        'activated_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function settings()
    {
        return $this->hasMany(BusinessSetting::class);
    }
}
