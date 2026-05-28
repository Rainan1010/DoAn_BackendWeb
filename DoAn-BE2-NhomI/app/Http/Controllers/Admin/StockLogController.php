<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockLogController extends Controller
{
    public function index(Request $request)
    {
        // Tổng tồn kho hiện tại
        $totalStock = DB::table('product_variants')
            ->sum('stock_quantity');

        // Sản phẩm sắp hết hàng
        $lowStockProducts = DB::table('product_variants')
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->count();

        // Sản phẩm hết hàng
        $outOfStockProducts = DB::table('product_variants')
            ->where('stock_quantity', '<=', 0)
            ->count();

        // Query log kho
        $logsQuery = DB::table('stock_logs')
            ->leftJoin('product_variants', 'stock_logs.variant_id', '=', 'product_variants.variant_id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.product_id')
            ->leftJoin('orders', 'stock_logs.order_id', '=', 'orders.order_id')
            ->select(
                'stock_logs.log_id',
                'stock_logs.variant_id',
                'stock_logs.order_id',
                'stock_logs.action',
                'stock_logs.quantity_change',
                'stock_logs.quantity_after',
                'stock_logs.note',
                'stock_logs.created_at',
                'product_variants.sku',
                'products.name as product_name',
                'orders.order_code'
            );

        // Lọc theo hành động
        if ($request->filled('action')) {
            $logsQuery->where('stock_logs.action', $request->action);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $keyword = $request->search;

            $logsQuery->where(function ($query) use ($keyword) {
                $query->where('product_variants.sku', 'like', '%' . $keyword . '%')
                    ->orWhere('products.name', 'like', '%' . $keyword . '%')
                    ->orWhere('orders.order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('stock_logs.note', 'like', '%' . $keyword . '%');
            });
        }

        $logs = $logsQuery
            ->orderByDesc('stock_logs.created_at')
            ->paginate(10)
            ->withQueryString();

        // Tổng biến động tồn kho trong ngày
        $todayChange = DB::table('stock_logs')
            ->whereDate('created_at', today())
            ->sum('quantity_change');

        // Số giao dịch kho hôm nay
        $todayLogs = DB::table('stock_logs')
            ->whereDate('created_at', today())
            ->count();

        return view('admin.stock_logs.index', compact(
            'totalStock',
            'lowStockProducts',
            'outOfStockProducts',
            'logs',
            'todayChange',
            'todayLogs'
        ));
    }
}