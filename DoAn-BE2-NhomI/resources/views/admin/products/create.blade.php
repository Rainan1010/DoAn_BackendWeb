@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center">
    <h1 class="text-lg font-black text-[#0A2540] tracking-tight uppercase">THÊM SẢN PHẨM MỚI</h1>
</div>
@endsection

@section('content')
<div x-data="{
    name: '',
    price: '',
    slug: '',
    imageUrl: '',
    categoryName: '',
    isPublic: true,
    isNew: false,
    isHot: false,
    isTrending: false,
    formatPrice(val) {
        const n = Number(val);
        if (!val || isNaN(n)) return '0 ₫';
        return n.toLocaleString('vi-VN') + ' ₫';
    }
}" class="pb-20">

    {{-- Validation Errors --}}
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

    <form action="{{ route('admin.products.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- 1. Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">THÔNG TIN SẢN PHẨM CƠ BẢN</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">TÊN SẢN PHẨM <span class="text-red-400">*</span></label>
                            <input type="text" name="name" x-model="name" value="{{ old('name') }}"
                                placeholder="Ví dụ: Laptop Dell Precision 5550"
                                class="w-full px-4 py-3 bg-gray-50/50 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">GIÁ CƠ BẢN (VNĐ) <span class="text-red-400">*</span></label>
                            <input type="number" name="base_price" x-model="price" value="{{ old('base_price') }}"
                                placeholder="25000000" min="0"
                                class="w-full px-4 py-3 bg-gray-50/50 border {{ $errors->has('base_price') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">ĐƯỜNG DẪN (SLUG) <span class="text-red-400">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-200 bg-gray-100 text-gray-500 text-sm font-medium">
                                /p/
                            </span>
                            <input type="text" name="slug" x-model="slug" value="{{ old('slug') }}"
                                placeholder="ten-san-pham-slug"
                                class="flex-1 w-full px-4 py-3 bg-gray-50/50 border {{ $errors->has('slug') ? 'border-red-400' : 'border-gray-200' }} rounded-r-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">DANH MỤC <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="category_id" @change="categoryName = $event.target.options[$event.target.selectedIndex].text" class="w-full appearance-none px-4 py-3 bg-gray-50/50 border {{ $errors->has('category_id') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">THƯƠNG HIỆU <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="brand_id" class="w-full appearance-none px-4 py-3 bg-gray-50/50 border {{ $errors->has('brand_id') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all">
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_id }}" {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">MÔ TẢ SẢN PHẨM</label>
                        <textarea name="description" rows="4"
                            placeholder="Nhập nội dung mô tả chi tiết sản phẩm..."
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all resize-y">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">THÔNG SỐ KỸ THUẬT (JSON)</label>
                        <div class="relative rounded-lg overflow-hidden border border-[#0A2540]">
                            <div class="absolute top-0 right-0 bg-[#0A2540] text-gray-400 text-[10px] font-black uppercase px-3 py-1.5 tracking-widest">JSON</div>
                            <textarea name="specs" rows="5"
                                class="w-full px-4 pt-10 pb-4 bg-[#0A2540] text-blue-300 font-mono text-[13px] leading-relaxed focus:outline-none resize-y"
                                spellcheck="false"
                                placeholder='{"cpu": "...", "ram": "..."}'
                            >{{ old('specs') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hình ảnh (URL) -->
            <div x-data="{ images: {{ json_encode($old_images) }} }" class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-400"></div>
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                            <i data-lucide="image" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">QUẢN LÝ HÌNH ẢNH (URL)</h2>
                    </div>
                    <button type="button" @click="images.push({url:'', is_primary: false})"
                        class="text-xs font-bold text-gray-500 hover:text-[#0A2540] transition-colors flex items-center gap-1">
                        + Thêm URL ảnh
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    <template x-for="(img, index) in images" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'images['+index+'][url]'" x-model="img.url"
                                @input="index === 0 && (imageUrl = img.url)"
                                placeholder="https://example.com/image.jpg"
                                class="flex-1 px-4 py-2.5 bg-gray-50/80 border border-gray-200 rounded-lg text-sm font-medium text-[#0A2540] focus:outline-none focus:border-[#0A2540] transition-all">
                            <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" :name="'images['+index+'][is_primary]'" value="1"
                                    x-model="img.is_primary" class="w-4 h-4 rounded border-gray-300 text-[#0A2540]">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Ảnh chính</span>
                            </label>
                            <button type="button" @click="images.splice(index,1); imageUrl = images[0]?.url || ''" x-show="images.length > 1"
                                class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Biến thể -->
            <div x-data="{ variants: {{ json_encode($old_variants) }} }" class="bg-white rounded-xl shadow-sm border border-blue-100 overflow-hidden relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">BIẾN THỂ SẢN PHẨM</h2>
                    </div>
                    <button type="button" @click="variants.push({sku:'', price:'', sale_price:'', stock: 0, is_active: true})"
                        class="px-3 py-1.5 bg-[#0A2540] text-white text-[10px] font-bold rounded shadow-sm hover:bg-[#0A2540]/90 transition-colors uppercase tracking-widest flex items-center gap-1.5">
                        <i data-lucide="plus-square" class="w-3 h-3"></i> THÊM BIẾN THỂ
                    </button>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="pb-3 w-1/5">SKU</th>
                                <th class="pb-3 w-1/5">GIÁ BÁN</th>
                                <th class="pb-3 w-1/5">GIÁ KHUYẾN MÃI</th>
                                <th class="pb-3 w-24">TỒN KHO</th>
                                <th class="pb-3 w-24 text-center">BẬT</th>
                                <th class="pb-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(v, index) in variants" :key="index">
                                <tr class="group">
                                    <td class="py-3 pr-3">
                                        <input type="text" :name="'variants['+index+'][sku]'" x-model="v.sku"
                                            placeholder="SKU-001"
                                            class="w-full px-3 py-2 bg-gray-50/50 border border-gray-200 rounded text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" :name="'variants['+index+'][price]'" x-model="v.price"
                                            placeholder="0" min="0"
                                            class="w-full px-3 py-2 bg-gray-50/50 border border-gray-200 rounded text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" :name="'variants['+index+'][sale_price]'" x-model="v.sale_price"
                                            placeholder="Không bắt buộc" min="0"
                                            class="w-full px-3 py-2 bg-[#E2F6EA]/60 border border-[#0FAF62]/30 rounded text-xs font-bold text-[#0FAF62] focus:outline-none focus:border-[#0FAF62]">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" :name="'variants['+index+'][stock]'" x-model="v.stock"
                                            placeholder="0" min="0"
                                            class="w-20 px-3 py-2 bg-gray-50/50 border border-gray-200 rounded text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540]">
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :name="'variants['+index+'][is_active]'" value="1" x-model="v.is_active" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[' '] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0A2540]"></div>
                                        </label>
                                    </td>
                                    <td class="py-3 text-right">
                                        <button type="button" @click="variants.splice(index,1)" x-show="variants.length > 1"
                                            class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition-colors mt-0.5">
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

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Trạng thái -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-500">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-[#0A2540] uppercase tracking-widest">TRẠNG THÁI & PHÂN LOẠI</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Toggle Hiển thị -->
                    <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-lg border border-gray-100">
                        <div>
                            <p class="text-[13px] font-black text-[#0A2540] uppercase tracking-wider">HIỂN THỊ CÔNG KHAI</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-0.5">Người dùng có thể tìm thấy</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="isPublic" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[' '] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A2540]"></div>
                        </label>
                    </div>

                    <!-- Badges -->
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_new" value="1" x-model="isNew" class="w-5 h-5 rounded-sm border-gray-300 text-[#0A2540]">
                            <span class="text-[13px] font-bold text-gray-600 group-hover:text-[#0A2540] transition-colors flex-1">Sản phẩm mới (New)</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#0A2540] text-white uppercase tracking-widest shadow-sm">NEW</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_hot" value="1" x-model="isHot" class="w-5 h-5 rounded-sm border-gray-300 text-[#0A2540]">
                            <span class="text-[13px] font-bold text-gray-600 group-hover:text-[#0A2540] transition-colors flex-1">Sản phẩm Hot</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#FF6B00] text-white uppercase tracking-widest shadow-sm">HOT</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_trending" value="1" x-model="isTrending" class="w-5 h-5 rounded-sm border-gray-300 text-[#0A2540]">
                            <span class="text-[13px] font-bold text-gray-600 group-hover:text-[#0A2540] transition-colors flex-1">Thịnh hành (Trending)</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-blue-500 text-white uppercase tracking-widest shadow-sm">TRENDING</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Preview + Actions (Sticky) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
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

                    <!-- Hint text -->
                    <p class="text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-3">
                        Preview cập nhật theo thời gian thực
                    </p>
                </div>

                <div class="p-5 border-t border-gray-100 bg-white grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.products.index') }}" class="py-3 px-4 border border-gray-200 text-gray-500 text-xs font-black rounded-lg hover:bg-gray-50 hover:text-[#0A2540] transition-colors text-center uppercase tracking-widest flex items-center justify-center gap-1.5">
                        <i data-lucide="x" class="w-4 h-4"></i> HỦY BỎ
                    </a>
                    <button type="submit" class="py-3 px-4 bg-[#0A2540] text-white text-xs font-black rounded-lg hover:bg-[#0A2540]/90 transition-all shadow-lg shadow-[#0A2540]/20 text-center uppercase tracking-widest flex items-center justify-center gap-1.5">
                        <i data-lucide="save" class="w-4 h-4"></i> LƯU
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
