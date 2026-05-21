<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueReport extends Model
{
    protected $table =
        'revenue_reports';

    protected $primaryKey =
        'report_id';

    public $timestamps =
        false;

    protected $fillable = [

        'report_date',

        'total_revenue',

        'total_orders',

        'total_items_sold',

        'avg_order_value'
    ];
}