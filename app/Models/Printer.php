<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'connection_type',
        'mac_address',
        'ip_address',
        'paper_width',
        'default',
        'outlet_id',
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
