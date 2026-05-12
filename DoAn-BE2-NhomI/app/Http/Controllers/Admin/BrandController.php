<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Carbon;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Brand::query();
            
            if ($request->has('search') && $request->search != '') {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('brand_id', $request->search);
            }
            
            $brands = $query->orderBy('created_at', 'desc')->paginate(10);
            
            $totalBrands = Brand::count();
            $activeBrands = Brand::where('is_active', 1)->count();
            $hiddenBrands = Brand::where('is_active', 0)->count();
            $newBrands = Brand::whereMonth('created_at', Carbon::now()->month)->count();
        } catch (\Exception $e) {
            // Log error for debugging but don't crash
            \Illuminate\Support\Facades\Log::error("Brand index error: " . $e->getMessage());
            $brands = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $totalBrands = $activeBrands = $hiddenBrands = $newBrands = 0;
        }

        return view('admin.brands.index', compact('brands', 'totalBrands', 'activeBrands', 'hiddenBrands', 'newBrands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|unique:brands,slug|max:100',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn này đã tồn tại trong hệ thống.',
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

    public function show(string $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|max:100|unique:brands,slug,' . $brand->brand_id . ',brand_id',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn này đã tồn tại trong hệ thống.',
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
