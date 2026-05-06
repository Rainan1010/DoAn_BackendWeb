<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';       // tên bảng trong DB
    protected $primaryKey = 'voucher_id'; // nếu khóa chính không phải 'id'
    public $timestamps = false;           // nếu bảng không có created_at, updated_at

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
        'is_active'
    ];
}
