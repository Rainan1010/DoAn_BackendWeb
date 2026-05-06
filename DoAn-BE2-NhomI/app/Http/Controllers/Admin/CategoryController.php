<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Category::with('parent');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category_id', $request->search);
        }
        
        $categories = $query->orderBy('sort_order', 'asc')->paginate(10);
        
        $totalCategories = \App\Models\Category::count();
        $rootCategories = \App\Models\Category::whereNull('parent_id')->count();
        $activeCategories = \App\Models\Category::where('is_active', 1)->count();
        $hiddenCategories = \App\Models\Category::where('is_active', 0)->count();

        return view('admin.categories.index', compact('categories', 'totalCategories', 'rootCategories', 'activeCategories', 'hiddenCategories'));
    }

    public function create()
    {
        $parentCategories = \App\Models\Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,category_id',
            'sort_order' => 'nullable|integer',
            'icon_url' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 40 ký tự.',
            'slug.required' => 'Vui lòng nhập đường dẫn (slug).',
            'slug.unique' => 'Slug này đã tồn tại trong hệ thống.',
            'sort_order.integer' => 'Vui lòng nhập số nguyên.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.'
        ]);

        \App\Models\Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'icon_url' => $request->icon_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
    }


    public function edit(string $id)
    {
        $category = \App\Models\Category::find($id);
        if (!$category) {
            return redirect()->route('admin.categories.index')->with('error', 'Không tìm thấy danh mục này!');
        }
        $parentCategories = \App\Models\Category::whereNull('parent_id')->where('category_id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, string $id)
    {
        $category = \App\Models\Category::find($id);
        if (!$category) {
            return redirect()->route('admin.categories.index')->with('error', 'Không tìm thấy danh mục này!');
        }

        $request->validate([
        'name' => 'required|max:40',
        'slug' => 'required|unique:categories,slug,' . $category->category_id . ',category_id',
        'parent_id' => 'nullable|exists:categories,category_id',
        'sort_order' => 'nullable|integer',
        'icon_url' => 'nullable|string|max:500',
        'version' => 'required|integer', // Validate version gửi lên từ form
    ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 40 ký tự.',
            'slug.required' => 'Vui lòng nhập đường dẫn (slug).',
            'slug.unique' => 'Slug này đã được sử dụng bởi danh mục khác.',
            'sort_order.integer' => 'Vui lòng nhập số nguyên.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.'
        ]);

        $updated = \App\Models\Category::where('category_id', $id)
        ->where('version', $request->version) // Chỉ update nếu version trong DB khớp với version ở form
        ->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'icon_url' => $request->icon_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'version' => $request->version + 1, // Tăng version lên 1 đơn vị
        ]);
        if ($updated === 0) {
            return redirect()->back()
                ->with('error', 'Dữ liệu đã được thay đổi bởi một người dùng khác. Vui lòng tải lại trang và thử lại.');
        }
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật dữ liệu thành công.');
    }

    public function destroy(string $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục đang chứa danh mục con.');
        }

        if (\Illuminate\Support\Facades\DB::table('products')->where('category_id', $id)->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục đang chứa sản phẩm.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục.');
    }
}
