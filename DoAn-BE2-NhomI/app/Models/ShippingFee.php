<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    protected $table = 'shipping_fees';

    protected $primaryKey = 'fee_id';

    protected $fillable = [

        'province',

        'fee',

        'estimated_days',
    ];

    public $timestamps = false;
}