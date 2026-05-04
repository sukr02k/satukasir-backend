<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    //$table->id();
    // $table->string('name');
    // $table->foreignId('category_id')->constrained('categories');
    // //business_id is a foreign key that references the id column on the businesses table
    // $table->foreignId('business_id')->constrained('businesses');
    // $table->string('description');
    // $table->string('image')->nullable();
    // //color is a string that can be null
    // $table->string('color')->nullable();
    // $table->decimal('price', 8, 2);
    // $table->decimal('cost', 8, 2);
    // $table->integer('stock');
    // $table->string('barcode');
    // $table->string('sku');
    // $table->timestamps();

    protected $fillable = [
        'name',
        'category_id',
        'business_id',
        'description',
        'image',
        'color',
        'price',
        'cost',
        'stock',
        'barcode',
        'sku'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
