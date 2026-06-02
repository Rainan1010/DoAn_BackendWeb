<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', '24h');
        $startDate = now();

        switch ($range) {
            case '7d':
                $startDate = now()->subDays(7);
                break;
            case '30d':
                $startDate = now()->subDays(30);
                break;
            default: // 24h
                $startDate = now()->subHours(24);
                break;
        }

        // Fetch Real Data based on range
        $totalUsers = User::count();
        $totalProducts = \App\Models\Product::count();
        
        $latestReport = DB::table('revenue_reports')
            ->where('report_date', '>=', $startDate->toDateString())
            ->orderBy('report_date', 'desc')
            ->first();
            
        // If no data in range, get the absolute latest as fallback for "Total" display
        if (!$latestReport) {
            $latestReport = DB::table('revenue_reports')->orderBy('report_date', 'desc')->first();
        }

        $previousReport = DB::table('revenue_reports')
            ->where('report_date', '<', $startDate->toDateString())
            ->orderBy('report_date', 'desc')
            ->first();

        $revenueTotal = $latestReport->total_revenue ?? 0;
        $ordersTotal = $latestReport->total_orders ?? 0;
        
        // Calculate Growth (simple comparison with previous report)
        $revenueGrowth = 0;
        if ($previousReport && $previousReport->total_revenue > 0) {
            $revenueGrowth = round((($revenueTotal - $previousReport->total_revenue) / $previousReport->total_revenue) * 100, 1);
        }

        $ordersGrowth = 0;
        if ($previousReport && $previousReport->total_orders > 0) {
            $ordersGrowth = round((($ordersTotal - $previousReport->total_orders) / $previousReport->total_orders) * 100, 1);
        }

        $stats = [
            'revenue' => [
                'total' => $revenueTotal,
                'growth' => $revenueGrowth,
                'trend' => $revenueGrowth >= 0 ? 'up' : 'down'
            ],
            'orders' => [
                'total' => $ordersTotal,
                'growth' => $ordersGrowth,
                'trend' => $ordersGrowth >= 0 ? 'up' : 'down',
                'fulfillment_rate' => 98
            ],
            'customers' => [
                'total' => $totalUsers,
                'growth' => 5.1,
                'trend' => 'up',
                'target' => 85
            ],
            'products' => [
                'total' => $totalProducts,
                'out_of_stock' => \App\Models\ProductVariant::where('stock_quantity', '<=', 0)->count(),
                'status' => 'Active'
            ]
        ];

        // Fetch monthly revenue for chart
        $revenueTrendsRaw = DB::table('revenue_reports')
            ->select(DB::raw('DATE_FORMAT(report_date, "%b") as month'), DB::raw('SUM(total_revenue) as revenue'), 'report_date')
            ->groupBy('month', 'report_date')
            ->orderBy('report_date', 'asc')
            ->limit(7)
            ->get();

        $revenueTrends = [];
        foreach ($revenueTrendsRaw as $row) {
            $revenueTrends[$row->month] = $row->revenue;
        }

        // Default if empty
        if (empty($revenueTrends)) {
            $revenueTrends = ['Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0];
        }

        $orderStatus = [
            'delivered' => $latestReport->total_orders ?? 0,
            'processing' => 0,
            'returned' => 0,
            'success_rate' => 100
        ];

        $search = $request->get('search');
        $topSellingProductsQuery = \App\Models\Product::with('category', 'primaryImage');
        
        if ($search) {
            $topSellingProductsQuery->where('name', 'like', '%' . $search . '%');
        }

        $topSellingProducts = $topSellingProductsQuery
            ->orderBy('view_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function($p) {
                return [
                    'name' => $p->name,
                    'category' => $p->category->name ?? 'Uncategorized',
                    'revenue' => $p->base_price * ($p->view_count / 10),
                    'sold' => round($p->view_count / 5),
                    'image' => $p->primaryImage->image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($p->name)
                ];
            });

        $recentActivities = [
            [
                'title' => $search ? 'Kết quả tìm kiếm cho "' . $search . '"' : 'Cập nhật hệ thống',
                'desc' => $search ? 'Đã tìm thấy ' . $topSellingProducts->count() . ' sản phẩm phù hợp.' : 'Dữ liệu thống kê đã được làm mới.',
                'time' => 'Vừa xong',
                'icon' => $search ? 'search' : 'refresh-cw',
                'color' => 'blue'
            ],
            [
                'title' => 'Sản phẩm mới',
                'desc' => 'Đã cập nhật thêm ' . $totalProducts . ' sản phẩm vào kho.',
                'time' => '1 giờ trước',
                'icon' => 'package',
                'color' => 'green'
            ]
        ];

        $maxRevenue = !empty($revenueTrends) ? max(array_values($revenueTrends)) : 100;
        if ($maxRevenue == 0) $maxRevenue = 100;

        return view('admin.dashboard', compact(
            'stats', 
            'revenueTrends', 
            'maxRevenue',
            'orderStatus', 
            'topSellingProducts', 
            'recentActivities'
        ));
    }
}
