<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // 🔥 1 sản phẩm có nhiều ảnh
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    // 🔥 1 sản phẩm có nhiều biến thể (RAM, ROM…)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // 🔥 1 sản phẩm có nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }
}