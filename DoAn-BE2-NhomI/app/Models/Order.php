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

    protected $casts = [
        'created_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
     /*
    |--------------------------------------------------------------------------
    | Quan hệ với users
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Quan hệ với shipping_addresses
    |--------------------------------------------------------------------------
    */
    public function shippingAddress()
    {
        return $this->belongsTo(
            ShippingAddress::class,
            'shipping_address_id',
            'address_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Quan hệ với vouchers
    |--------------------------------------------------------------------------
    */
    public function voucher()
    {
        return $this->belongsTo(
            Voucher::class,
            'voucher_id',
            'voucher_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Quan hệ với payments
    |--------------------------------------------------------------------------
    */
    public function payment()
    {
        return $this->hasOne(
            Payment::class,
            'order_id',
            'order_id'
        );
    }
}