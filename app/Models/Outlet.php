<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'business_id', 'address', 'phone', 'receipt_header', 'receipt_footer'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function printers()
    {
        return $this->hasMany(Printer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
