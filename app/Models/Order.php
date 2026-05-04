<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'order_number',
        'outlet_id',
        'sub_total',
        'total_price',
        'total_items',
        'tax',
        'discount',
        'payment_method',
        'status',
        'cashier_id'
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
