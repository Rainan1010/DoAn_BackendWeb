<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{

    // =====================================================
    // TÊN BẢNG
    // =====================================================
    protected $table = 'shipping_addresses';

    // =====================================================
    // PRIMARY KEY
    // =====================================================
    protected $primaryKey = 'address_id';

    // =====================================================
    // FIELD CHO PHÉP INSERT
    // =====================================================
    protected $fillable = [

        'user_id',
        'full_name',
        'phone',
        'province',
        'district',
        'ward',
        'street_address',
        'is_default',
        'editing_by',
        'editing_at',
        'editing_token',
    ];

}