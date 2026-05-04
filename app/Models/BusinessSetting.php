<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'charge_type',
        'type',
        'value',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
