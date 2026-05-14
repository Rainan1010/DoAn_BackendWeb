<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = true;

    // Bảng chỉ có created_at, không có updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    public $timestamps = true;

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

    protected $casts = [
        'specs'      => 'array',
        'is_active'  => 'boolean',
        'is_new'     => 'boolean',
        'is_hot'     => 'boolean',
        'is_trending' => 'boolean',
        'created_at' => 'datetime',
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

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')->where('is_primary', 1);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    // 🔥 1 sản phẩm có nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'product_id');
    }
}
