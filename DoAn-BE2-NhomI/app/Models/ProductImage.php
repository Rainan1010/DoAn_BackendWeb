<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    public $timestamps = false;

    protected $fillable = ['product_id', 'image_url', 'sort_order', 'is_primary'];

    public function getImageUrlAttribute($value)
    {
        if ($value && strpos($value, '/storage/products/') === 0) {
            return str_replace('/storage/products/', '/products/', $value);
        }
        return $value;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
