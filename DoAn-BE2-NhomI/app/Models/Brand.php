<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'description',
        'country',
        'is_active',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }
}
