<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    public $timestamps = false;

    protected $fillable = [

        'order_id',
        'gateway',
        'transaction_id',
        'amount',
        'status',
        'gateway_response',
        'paid_at',
    ];
}