<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX – Danh sách sản phẩm (đã có dữ liệu thật)
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'primaryImage'])
            ->orderByDesc('product_id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        $total = Product::count();
        $stats = [
            'total'      => $total,
            'total_views'=> Product::sum('view_count'),
            'hot_count'  => Product::where('is_hot', 1)->count(),
            'active_pct' => $total > 0
                ? round(Product::where('is_active', 1)->count() / $total * 100, 1)
                : 100,
        ];

        return view('admin.products.index', compact('products', 'categories', 'brands', 'stats'));
    }

    // ─────────────────────────────────────────────
    // CREATE – Form thêm mới
    // ─────────────────────────────────────────────
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'))->with([
            'old_images'   => old('images') ?? [ ['url' => '', 'is_primary' => true] ],
            'old_variants' => old('variants') ?? [ ['sku' => '', 'price' => '', 'sale_price' => '', 'stock' => 0, 'is_active' => true] ],
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE – Lưu sản phẩm mới vào DB
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        // Loại bỏ các ảnh trống trước khi validate
        if ($request->has('images')) {
            $images = array_filter($request->images, function($img) {
                return !empty($img['url']);
            });
            $request->merge(['images' => array_values($images)]);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:191|unique:products,slug',
            'category_id' => 'required|integer|exists:categories,category_id',
            'brand_id'    => 'required|integer|exists:brands,brand_id',
            'base_price'  => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'specs'       => 'nullable|string',
            'images.*.url'    => 'nullable|string',
            'variants.*.sku'        => 'nullable|string|max:100',
            'variants.*.price'      => 'nullable|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock'      => 'nullable|integer|min:0',
        ], [
            'name.required'        => 'Vui lòng nhập tên sản phẩm.',
            'name.max'             => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'slug.required'        => 'Vui lòng nhập đường dẫn (slug) cho sản phẩm.',
            'slug.max'             => 'Slug không được vượt quá 191 ký tự.',
            'slug.unique'          => 'Slug này đã được sử dụng, vui lòng chọn slug khác.',
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'category_id.exists'   => 'Danh mục được chọn không hợp lệ.',
            'brand_id.required'    => 'Vui lòng chọn thương hiệu sản phẩm.',
            'brand_id.exists'      => 'Thương hiệu được chọn không hợp lệ.',
            'base_price.required'  => 'Vui lòng nhập giá niêm yết.',
            'base_price.numeric'   => 'Giá niêm yết phải là số hợp lệ.',
            'base_price.min'       => 'Giá niêm yết không được nhỏ hơn 0.',
            'images.*.url.url'     => 'Một hoặc nhiều URL hình ảnh không hợp lệ.',
            'variants.*.sku.max'   => 'Mã SKU không được vượt quá 100 ký tự.',
            'variants.*.price.numeric'      => 'Giá biến thể phải là số hợp lệ.',
            'variants.*.price.min'          => 'Giá biến thể không được nhỏ hơn 0.',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi phải là số hợp lệ.',
            'variants.*.sale_price.min'     => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'variants.*.stock.integer'      => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock.min'          => 'Số lượng tồn kho không được nhỏ hơn 0.',
        ]);

        // Xử lý specs JSON
        $specs = null;
        if ($request->filled('specs')) {
            $decoded = json_decode($request->specs, true);
            $specs   = json_last_error() === JSON_ERROR_NONE ? $request->specs : null;
        }

        $product = Product::create([
            'name'        => $request->name,
            'slug'        => $request->slug,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'base_price'  => (float) str_replace(['.', ','], ['', '.'], $request->base_price),
            'description' => $request->description,
            'specs'       => $specs,
            'is_active'   => $request->boolean('is_active', true),
            'is_new'      => $request->boolean('is_new'),
            'is_hot'      => $request->boolean('is_hot'),
            'is_trending' => $request->boolean('is_trending'),
            'view_count'  => 0,
        ]);

        // Lưu ảnh
        if ($request->has('images')) {
            $order = 0;
            foreach ($request->images as $img) {
                if (!empty($img['url'])) {
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_url'  => $img['url'],
                        'sort_order' => $order++,
                        'is_primary' => !empty($img['is_primary']) ? 1 : 0,
                    ]);
                }
            }
        }

        // Lưu biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                if (!empty($v['sku'])) {
                    ProductVariant::create([
                        'product_id'       => $product->product_id,
                        'sku'              => $v['sku'],
                        'price'            => (float) ($v['price'] ?? 0),
                        'sale_price'       => !empty($v['sale_price']) ? (float) $v['sale_price'] : null,
                        'stock_quantity'   => (int) ($v['stock'] ?? 0),
                        'attribute_values' => null,
                        'is_active'        => !empty($v['is_active']) ? 1 : 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Đã thêm sản phẩm \"{$product->name}\" thành công!");
    }

    // ─────────────────────────────────────────────
    // SHOW – Xem chi tiết sản phẩm
    // ─────────────────────────────────────────────
    public function show(string $id)
    {
        $product = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('product_id', $id)
            ->firstOrFail();

        // Tăng view_count
        Product::where('product_id', $id)->increment('view_count');

        return view('admin.products.show', compact('product'));
    }

    // ─────────────────────────────────────────────
    // EDIT – Form chỉnh sửa
    // ─────────────────────────────────────────────
    public function edit(string $id)
    {
        $product    = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('product_id', $id)
            ->firstOrFail();
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    // ─────────────────────────────────────────────
    // UPDATE – Cập nhật dữ liệu vào DB
    // ─────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Loại bỏ các ảnh trống trước khi validate
        if ($request->has('images')) {
            $images = array_filter($request->images, function($img) {
                return !empty($img['url']);
            });
            $request->merge(['images' => array_values($images)]);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:191|unique:products,slug,' . $id . ',product_id',
            'category_id' => 'required|integer|exists:categories,category_id',
            'brand_id'    => 'required|integer|exists:brands,brand_id',
            'base_price'  => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'specs'       => 'nullable|string',
            'images.*.url'          => 'nullable|string',
            'variants.*.sku'        => 'nullable|string|max:100',
            'variants.*.price'      => 'nullable|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock'      => 'nullable|integer|min:0',
        ], [
            'name.required'        => 'Vui lòng nhập tên sản phẩm.',
            'name.max'             => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'slug.required'        => 'Vui lòng nhập đường dẫn (slug) cho sản phẩm.',
            'slug.max'             => 'Slug không được vượt quá 191 ký tự.',
            'slug.unique'          => 'Slug này đã được sử dụng bởi sản phẩm khác.',
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'category_id.exists'   => 'Danh mục được chọn không hợp lệ.',
            'brand_id.required'    => 'Vui lòng chọn thương hiệu sản phẩm.',
            'brand_id.exists'      => 'Thương hiệu được chọn không hợp lệ.',
            'base_price.required'  => 'Vui lòng nhập giá niêm yết.',
            'base_price.numeric'   => 'Giá niêm yết phải là số hợp lệ.',
            'base_price.min'       => 'Giá niêm yết không được nhỏ hơn 0.',
            'images.*.url.url'     => 'Một hoặc nhiều URL hình ảnh không hợp lệ.',
            'variants.*.sku.max'   => 'Mã SKU không được vượt quá 100 ký tự.',
            'variants.*.price.numeric'      => 'Giá biến thể phải là số hợp lệ.',
            'variants.*.price.min'          => 'Giá biến thể không được nhỏ hơn 0.',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi phải là số hợp lệ.',
            'variants.*.sale_price.min'     => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'variants.*.stock.integer'      => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock.min'          => 'Số lượng tồn kho không được nhỏ hơn 0.',
        ]);

        $specs = null;
        if ($request->filled('specs')) {
            $decoded = json_decode($request->specs, true);
            $specs   = json_last_error() === JSON_ERROR_NONE ? $request->specs : null;
        }

        $product->update([
            'name'        => $request->name,
            'slug'        => $request->slug,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'base_price'  => (float) str_replace(['.', ','], ['', '.'], $request->base_price),
            'description' => $request->description,
            'specs'       => $specs,
            'is_active'   => $request->boolean('is_active', true),
            'is_new'      => $request->boolean('is_new'),
            'is_hot'      => $request->boolean('is_hot'),
            'is_trending' => $request->boolean('is_trending'),
        ]);

        // Cập nhật biến thể (xóa cũ, thêm mới)
        if ($request->has('variants')) {
            // Chỉ xóa các variant do form gửi lên (giữ các variant không trong form)
            $product->variants()->delete();
            foreach ($request->variants as $v) {
                if (!empty($v['sku'])) {
                    ProductVariant::create([
                        'product_id'       => $product->product_id,
                        'sku'              => $v['sku'],
                        'price'            => (float) ($v['price'] ?? 0),
                        'sale_price'       => !empty($v['sale_price']) ? (float) $v['sale_price'] : null,
                        'stock_quantity'   => (int) ($v['stock'] ?? 0),
                        'attribute_values' => null,
                        'is_active'        => isset($v['is_active']) ? 1 : 0,
                    ]);
                }
            }
        }

        // Thêm ảnh mới nếu có
        if ($request->has('images')) {
            $order = $product->images()->max('sort_order') ?? 0;
            foreach ($request->images as $img) {
                if (!empty($img['url'])) {
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_url'  => $img['url'],
                        'sort_order' => ++$order,
                        'is_primary' => !empty($img['is_primary']) ? 1 : 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.show', $product->product_id)
            ->with('success', "Đã cập nhật sản phẩm \"{$product->name}\" thành công!");
    }

    // ─────────────────────────────────────────────
    // DESTROY – Xóa sản phẩm
    // ─────────────────────────────────────────────
    public function destroy(string $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();
        $name    = $product->name;

        // Xóa ảnh và biến thể liên quan
        $product->images()->delete();
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Đã xóa sản phẩm \"{$name}\" thành công!");
    }
}
