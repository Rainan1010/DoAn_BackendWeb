<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $primaryKey = 'variant_id';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'attribute_values',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'attribute_values' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id', 'variant_id');
    }
}