<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'voucher_id',
        'order_code',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'cancel_reason',
        'paid_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}