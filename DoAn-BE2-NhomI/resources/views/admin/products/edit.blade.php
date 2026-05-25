@extends('admin.layouts.app')

@section('content')
<div class="pb-20 max-w-7xl mx-auto" x-data="{
    name: {{ json_encode(old('name', $product->name)) }},
    price: '{{ old('base_price', $product->base_price) }}',
    slug: {{ json_encode(old('slug', $product->slug)) }},
    imageUrl: {{ json_encode($product->images->where('is_primary', 1)->first()?->image_url ?? $product->images->first()?->image_url ?? '') }},
    categoryName: {{ json_encode($product->category?->name ?? 'Chọn danh mục') }},
    isPublic: {{ $product->is_active ? 'true' : 'false' }},
    isNew: {{ $product->is_new ? 'true' : 'false' }},
    isHot: {{ $product->is_hot ? 'true' : 'false' }},
    isTrending: {{ $product->is_trending ? 'true' : 'false' }},
    formatPrice(val) {
        const n = Number(val);
        if (!val || isNaN(n)) return '0 ₫';
        return n.toLocaleString('vi-VN') + ' ₫';
    }
}">

    @if($errors->any())
    <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm font-black text-red-600 uppercase tracking-widest mb-2">Vui lòng kiểm tra lại:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-xs font-medium text-red-500">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4 border-b border-gray-200 pb-6">
            <div>
                <nav class="flex text-[10px] font-black text-gray-400 mb-3 uppercase tracking-widest gap-2 items-center">
                    <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600 transition-colors">Sản phẩm</a>
                    <i data-lucide="chevron-right" class="w-3 h-3 text-gray-300"></i>
                    <a href="{{ route('admin.products.show', $product->product_id) }}" class="hover:text-blue-600 transition-colors truncate max-w-[200px]">{{ $product->name }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3 text-gray-300"></i>
                    <span class="text-blue-600">Sửa</span>
                </nav>
                <h1 class="text-3xl font-black text-[#0A2540] tracking-tight">Sửa Sản phẩm</h1>
                <p class="text-sm font-medium text-gray-500 mt-1">ID: PRD-{{ str_pad($product->product_id, 4, '0', STR_PAD_LEFT) }} | Chỉnh sửa thông tin chi tiết và biến thể.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0 mt-4 md:mt-0">
                <a href="{{ route('admin.products.show', $product->product_id) }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-xs font-black rounded-lg hover:bg-gray-50 transition-all flex items-center gap-2 uppercase tracking-widest shadow-sm">
                    HỦY BỎ
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0A2540] text-white text-xs font-black rounded-lg hover:bg-[#0A2540]/90 transition-all shadow-md shadow-[#0A2540]/20 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="save" class="w-4 h-4"></i> LƯU THAY ĐỔI
                </button>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- Left: Main Form -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">Thông tin Cơ bản</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">TÊN SẢN PHẨM <span class="text-red-400">*</span></label>
                            <input type="text" name="name" x-model="name" value="{{ old('name', $product->name) }}"
                                class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-bold text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">SLUG <span class="text-red-400">*</span></label>
                                <input type="text" name="slug" x-model="slug" value="{{ old('slug', $product->slug) }}"
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">GIÁ NIÊM YẾT (₫) <span class="text-red-400">*</span></label>
                                <input type="number" name="base_price" x-model="price" value="{{ old('base_price', $product->base_price) }}" min="0"
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-bold text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">DANH MỤC <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <select name="category_id" x-on:change="categoryName = $event.target.options[$event.target.selectedIndex].text" class="w-full appearance-none px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540] transition-colors">
                                        <option value="">Chọn danh mục</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}" {{ old('category_id', $product->category_id) == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">THƯƠNG HIỆU <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <select name="brand_id" class="w-full appearance-none px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540] transition-colors">
                                        <option value="">Chọn thương hiệu</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->brand_id }}" {{ old('brand_id', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">MÔ TẢ SẢN PHẨM</label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all resize-y">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">THÔNG SỐ KỸ THUẬT (JSON)</label>
                            <textarea name="specs" rows="4"
                                class="w-full px-4 py-3 bg-[#0A2540] text-blue-300 font-mono text-[13px] border border-[#0A2540] rounded-lg focus:outline-none resize-y"
                                spellcheck="false">{{ old('specs', is_array($product->specs) ? json_encode($product->specs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $product->specs) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Ảnh hiện có -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-600">
                            <i data-lucide="image" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">Hình ảnh hiện có ({{ $product->images->count() }} ảnh)</h2>
                    </div>
                    <div class="p-6">
                        @if($product->images->isNotEmpty())
                        <div class="flex flex-wrap gap-4 mb-6">
                            @foreach($product->images->sortBy('sort_order') as $img)
                            <div class="w-28 h-28 rounded-xl overflow-hidden border-2 {{ $img->is_primary ? 'border-blue-500' : 'border-gray-200' }} relative group bg-gray-50 flex items-center justify-center">
                                <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-contain p-1"
                                     onerror="this.style.display='none'">
                                @if($img->is_primary)
                                    <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-blue-600 text-white text-[8px] font-black rounded">CHÍNH</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 mb-4">(Chưa có ảnh nào)</p>
                        @endif

                        <!-- Thêm ảnh mới -->
                        <div x-data="{ newImages: [{url:''}] }">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">THÊM ẢNH MỚI (URL)</p>
                            <template x-for="(img, index) in newImages" :key="index">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" :name="'images['+index+'][url]'" x-model="img.url"
                                        x-on:input="if(index === 0 && img.is_primary) imageUrl = img.url"
                                        placeholder="https://example.com/image.jpg"
                                        class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:border-[#0A2540] transition-all">
                                    <label class="flex items-center gap-1.5 cursor-pointer shrink-0">
                                        <input type="checkbox" :name="'images['+index+'][is_primary]'" value="1" x-model="img.is_primary" x-on:change="if(img.is_primary) imageUrl = img.url" class="w-4 h-4">
                                        <span class="text-[10px] font-black text-gray-400 uppercase">Chính</span>
                                    </label>
                                    <button type="button" x-on:click="newImages.splice(index,1)" x-show="newImages.length > 1"
                                        class="p-1.5 text-gray-300 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" x-on:click="newImages.push({url:''})"
                                class="mt-2 text-xs font-bold text-[#0A2540] hover:text-blue-600 transition-colors flex items-center gap-1">
                                + Thêm URL ảnh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Biến thể -->
                <div x-data="{
                    variants: {{ json_encode($product->variants->map(function($v) {
                        return [
                            'sku'        => $v->sku,
                            'price'      => $v->price,
                            'sale_price' => $v->sale_price,
                            'stock'      => $v->stock_quantity,
                            'is_active'  => (bool)$v->is_active,
                        ];
                    })->values()) }}
                }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                <i data-lucide="layers" class="w-4 h-4"></i>
                            </div>
                            <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">Biến thể sản phẩm</h2>
                        </div>
                        <button type="button" x-on:click="variants.push({sku:'', price:0, sale_price:'', stock:0, is_active:true})"
                            class="px-3 py-1.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-lg hover:bg-blue-200 transition-colors">
                            + Thêm biến thể
                        </button>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[500px]">
                            <thead>
                                <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 bg-gray-50/50">
                                    <th class="py-3 px-4 w-1/4">SKU</th>
                                    <th class="py-3 px-4">GIÁ BÁN</th>
                                    <th class="py-3 px-4">KHUYẾN MÃI</th>
                                    <th class="py-3 px-4 text-center">TỒN KHO</th>
                                    <th class="py-3 px-4 text-center">BẬT</th>
                                    <th class="py-3 px-4 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(v, index) in variants" :key="index">
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <input type="text" :name="'variants['+index+'][sku]'" x-model="v.sku"
                                                class="w-full px-3 py-2 bg-gray-50/80 border border-gray-200 rounded text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" :name="'variants['+index+'][price]'" x-model="v.price" min="0"
                                                class="w-full px-3 py-2 bg-gray-50/80 border border-gray-200 rounded text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" :name="'variants['+index+'][sale_price]'" x-model="v.sale_price" min="0"
                                                class="w-full px-3 py-2 bg-[#E2F6EA]/60 border border-[#0FAF62]/30 rounded text-xs font-bold text-[#0FAF62] focus:outline-none focus:border-[#0FAF62]">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="number" :name="'variants['+index+'][stock]'" x-model="v.stock" min="0"
                                                class="w-20 px-3 py-2 bg-gray-50/80 border border-gray-200 rounded text-xs font-bold text-center text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex justify-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" :name="'variants['+index+'][is_active]'" value="1" x-model="v.is_active" class="sr-only peer">
                                                    <div class="w-8 h-4 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-[#0A2540]"></div>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button type="button" x-on:click="variants.splice(index,1)"
                                                class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right: Sidebar (cuộn theo trang, không sticky để không che phần tử phía dưới) -->
            <div class="space-y-6 xl:self-start">

                <!-- Preview + Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#0A2540] flex items-center justify-center text-white">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </div>
                            <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">XEM TRƯỚC NHANH</h2>
                        </div>
                        <span class="text-[9px] font-black text-[#0FAF62] uppercase tracking-widest flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0FAF62] animate-pulse"></span> LIVE
                        </span>
                    </div>

                    <div class="p-5 bg-[#F4F5F7]">
                        <!-- Product Card Preview -->
                        <div class="bg-white rounded-2xl shadow-lg w-full overflow-hidden border border-gray-100">
                            
                            <!-- Image Area -->
                            <div class="h-44 relative overflow-hidden flex items-center justify-center"
                                 :class="imageUrl ? 'bg-white' : 'bg-[#0A2540]'">
                                
                                <!-- Real image from URL -->
                                <template x-if="imageUrl">
                                    <img :src="imageUrl" class="w-full h-full object-contain p-2" alt=""
                                         x-on:error="imageUrl = ''">
                                </template>

                                <!-- Placeholder khi chưa có ảnh -->
                                <template x-if="!imageUrl">
                                    <div class="flex flex-col items-center gap-2 text-white/30">
                                        <i data-lucide="image" class="w-10 h-10"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Chưa có hình ảnh</span>
                                    </div>
                                </template>

                                <!-- Badges overlay -->
                                <div class="absolute top-2 left-2 flex flex-col gap-1.5 items-start">
                                    <template x-if="isNew">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-[#0A2540] text-white uppercase shadow tracking-widest">NEW</span>
                                    </template>
                                    <template x-if="isHot">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-[#FF6B00] text-white uppercase shadow tracking-widest">HOT</span>
                                    </template>
                                    <template x-if="isTrending">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-500 text-white uppercase shadow tracking-widest">TRENDING</span>
                                    </template>
                                </div>

                                <!-- Active indicator -->
                                <div class="absolute top-2 right-2">
                                    <span x-show="isPublic" class="px-2 py-0.5 rounded text-[9px] font-black bg-[#E2F6EA] text-[#0FAF62] uppercase shadow tracking-widest flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-[#0FAF62]"></span> ACTIVE
                                    </span>
                                    <span x-show="!isPublic" class="px-2 py-0.5 rounded text-[9px] font-black bg-gray-100 text-gray-400 uppercase shadow tracking-widest">HIDDEN</span>
                                </div>
                            </div>

                            <!-- Info Area -->
                            <div class="p-4 border-t border-gray-50">
                                <h3 class="text-sm font-black text-[#0A2540] leading-snug line-clamp-2 min-h-[40px] mb-1"
                                    x-text="name || 'Tên sản phẩm...'"></h3>
                                <p class="text-[10px] font-medium text-gray-400 truncate mb-3"
                                   x-text="slug ? '/p/' + slug : 'đường-dẫn-slug'"></p>

                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-black text-[#0A2540]" x-text="formatPrice(price)"></p>
                                    <template x-if="categoryName && categoryName !== 'Chọn danh mục'">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-2 py-0.5 rounded"
                                              x-text="categoryName"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions under preview -->
                    <div class="p-5 border-t border-gray-100 bg-white grid grid-cols-2 gap-3">
                        <a href="{{ route('admin.products.show', $product->product_id) }}"
                            class="py-3 px-4 border border-gray-200 text-gray-500 text-xs font-black rounded-lg hover:bg-gray-50 hover:text-[#0A2540] transition-colors text-center uppercase tracking-widest flex items-center justify-center gap-1.5">
                            <i data-lucide="x" class="w-4 h-4"></i> HỦY
                        </a>
                        <button type="submit"
                            class="py-3 px-4 bg-[#0A2540] text-white text-xs font-black rounded-lg hover:bg-[#0A2540]/90 transition-all shadow-lg shadow-[#0A2540]/20 text-center uppercase tracking-widest flex items-center justify-center gap-1.5">
                            <i data-lucide="save" class="w-4 h-4"></i> LƯU
                        </button>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                            <div>
                                <p class="text-[11px] font-black text-[#0A2540] uppercase tracking-wider">Hiển thị công khai</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">TRÊN CỬA HÀNG</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" x-model="isPublic">
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0A2540]"></div>
                            </label>
                        </div>

                        <div class="pb-6 border-b border-gray-100 space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">NHÃN SẢN PHẨM</label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_new" value="1" x-model="isNew" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-xs font-bold text-gray-600 flex-1">Sản phẩm Mới (New)</span>
                                <span class="px-2 py-0.5 bg-[#0A2540] text-white text-[9px] font-black rounded uppercase">NEW</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_hot" value="1" x-model="isHot" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-xs font-bold text-gray-600 flex-1">Bán chạy (Hot)</span>
                                <span class="px-2 py-0.5 bg-[#FF6B00] text-white text-[9px] font-black rounded uppercase">HOT</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_trending" value="1" x-model="isTrending" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-xs font-bold text-gray-600 flex-1">Thịnh hành (Trending)</span>
                                <span class="px-2 py-0.5 bg-blue-500 text-white text-[9px] font-black rounded uppercase">TRENDING</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tổng hợp tồn kho -->
                <div class="bg-[#0A2540] rounded-xl shadow-lg shadow-[#0A2540]/20 p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full pointer-events-none"></div>
                    <h3 class="text-[13px] font-black uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="box" class="w-4 h-4 text-blue-300"></i> Tổng hợp tồn kho
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-[11px] font-medium text-gray-300">Tổng số lượng</span>
                            <span class="text-lg font-black">{{ number_format($product->variants->sum('stock_quantity')) }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-[11px] font-medium text-gray-300">Số biến thể</span>
                            <span class="text-lg font-black">{{ $product->variants->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-gray-300">Lượt xem</span>
                            <span class="text-lg font-black">{{ number_format($product->view_count) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
