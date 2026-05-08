<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $primaryKey = 'order_item_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'variant_info',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}