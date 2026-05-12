<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;

class Product extends Model
{
    // 🔥 bảng products
    protected $table = 'products';

    // 🔥 khóa chính
    protected $primaryKey = 'product_id';

    public $incrementing = true;

    // 🔥 vì không có updated_at
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'specs',
        'base_price',
        'is_active',
        'is_new',
        'is_hot',
        'is_trending',
        'view_count'
    ];

    // ================== QUAN HỆ ==================

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    // 🔥 1 sản phẩm có nhiều ảnh
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')->where('is_primary', 1);
    }

    // 🔥 1 sản phẩm có nhiều biến thể (RAM, ROM…)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    // 🔥 1 sản phẩm có nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }
}