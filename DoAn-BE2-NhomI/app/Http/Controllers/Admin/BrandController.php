<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Carbon;

class BrandController extends Controller
{
    // =====================================================
    // 1. DANH SÁCH THƯƠNG HIỆU
    // =====================================================
    public function index(Request $request)
    {
        $query = Brand::query();
        
        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand_id', $request->search);
        }

        $brands = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Thống kê KPI cho giao diện Admin
        $stats = [
            'total' => Brand::count(),
            'active' => Brand::where('is_active', 1)->count(),
            'inactive' => Brand::where('is_active', 0)->count(),
            'new' => Brand::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        return view('admin.brands.index', compact('brands', 'stats'));
    }

    // =====================================================
    // 2. THÊM MỚI THƯƠNG HIỆU
    // =====================================================
    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|unique:brands,slug|max:100',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'name.max' => 'Tên thương hiệu không được vượt quá 100 ký tự.',
            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn này đã tồn tại trong hệ thống.',
            'slug.max' => 'Đường dẫn không được vượt quá 100 ký tự.',
            'logo_url.url' => 'URL logo không hợp lệ.'
        ]);

        Brand::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'logo_url' => $request->logo_url,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu mới thành công!');
    }

    // =====================================================
    // 3. CHI TIẾT & CHỈNH SỬA
    // =====================================================
    public function show(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Fetch real products of this brand with pagination
        $products = \App\Models\Product::where('brand_id', $brand->brand_id)
            ->with(['category', 'primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Count real products
        $totalProducts = \App\Models\Product::where('brand_id', $brand->brand_id)->count();

        // Calculate real revenue for this brand (sum of order item subtotals where order is paid)
        $revenue = \DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('products.brand_id', $brand->brand_id)
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.subtotal');

        // Growth calculation compared to last month
        $lastMonthRevenue = \DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('products.brand_id', $brand->brand_id)
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('order_items.subtotal');

        $growth = 0;
        if ($lastMonthRevenue > 0) {
            $growth = round((($revenue - $lastMonthRevenue) / $lastMonthRevenue) * 100);
        }

        // Monthly target (e.g. 50,000,000 VND)
        $monthlyGoal = 50000000;
        $thisMonthRevenue = \DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('products.brand_id', $brand->brand_id)
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('order_items.subtotal');

        $performancePercent = $monthlyGoal > 0 ? min(100, round(($thisMonthRevenue / $monthlyGoal) * 100)) : 0;

        return view('admin.brands.show', compact(
            'brand',
            'products',
            'totalProducts',
            'revenue',
            'growth',
            'thisMonthRevenue',
            'monthlyGoal',
            'performancePercent'
        ));
    }

    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->route('admin.brands.index')
                ->withErrors(['concurrency_error' => 'Thương hiệu này đã bị xóa bởi một người dùng khác.']);
        }

        // Kiểm tra xung đột sửa đổi đồng thời (Optimistic Concurrency Control)
        if ($request->has('last_updated_at')) {
            $clientUpdatedAt = $request->input('last_updated_at');
            $dbUpdatedAt = $brand->updated_at ? $brand->updated_at->toIso8601String() : ($brand->created_at ? $brand->created_at->toIso8601String() : '');
            
            if ($clientUpdatedAt !== $dbUpdatedAt) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['concurrency_error' => 'Thương hiệu này đã được một người dùng khác cập nhật trong khi bạn đang chỉnh sửa. Vui lòng lưu lại các thay đổi của bạn ở nơi khác, tải lại trang và thực hiện lại.']);
            }
        }

        $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|max:100|unique:brands,slug,' . $id . ',brand_id',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'name.max' => 'Tên thương hiệu không được vượt quá 100 ký tự.',
            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn này đã tồn tại.',
            'slug.max' => 'Đường dẫn không được vượt quá 100 ký tự.',
            'logo_url.url' => 'URL logo không hợp lệ.'
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'logo_url' => $request->logo_url,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        
        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    // =====================================================
    // 4. XÓA & ĐỔI TRẠNG THÁI
    // =====================================================
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        
        return redirect()->route('admin.brands.index')->with('success', 'Đã xóa thương hiệu thành công!');
    }

    public function toggleStatus(string $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->is_active = !$brand->is_active;
        $brand->save();

        $status = $brand->is_active ? 'Hoạt động' : 'Đang ẩn';
        return redirect()->back()->with('success', "Đã chuyển trạng thái thương hiệu \"{$brand->name}\" sang {$status}.");
    }
}