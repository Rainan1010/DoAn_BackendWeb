<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'voucher_id';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_at',
        'end_at',
        'is_active',
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'start_at'   => 'datetime',
        'end_at'     => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'is_active'  => 'boolean',
    ];
}

