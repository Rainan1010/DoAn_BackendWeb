@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center gap-4">
    <div class="relative hidden md:block w-80 ml-8">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" placeholder="Tìm kiếm nhanh sản phẩm, đơn hàng..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-none rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 transition-all placeholder-gray-400" />
    </div>
</div>
@endsection

@section('content')
<div class="pb-20 max-w-7xl mx-auto">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 px-5 py-3 bg-[#E2F6EA] text-[#0FAF62] text-sm font-bold rounded-lg border border-[#0FAF62]/20 flex items-center gap-2">
        <i data-lucide="check-circle-2" class="w-4 h-4"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <nav class="flex text-[10px] font-black text-gray-400 mb-3 uppercase tracking-widest gap-2 items-center">
            <a href="{{ route('admin.products.index') }}" class="hover:text-[#0A2540] transition-colors">KHO SẢN PHẨM</a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-gray-300"></i>
            <span class="text-[#0A2540]">CHI TIẾT MẶT HÀNG</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1 flex-wrap">
                    <h1 class="text-3xl font-black text-[#0A2540] tracking-tight">{{ $product->name }}</h1>
                    @if($product->is_active)
                        <span class="px-2.5 py-1 bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-bold rounded uppercase tracking-widest shadow-sm">SẴN HÀNG</span>
                    @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-widest shadow-sm">ĐÃ ẨN</span>
                    @endif
                    @if($product->is_new)
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase tracking-widest border border-blue-100">NEW</span>
                    @endif
                    @if($product->is_hot)
                        <span class="px-2.5 py-1 bg-[#FFF0E6] text-[#FF6B00] text-[10px] font-bold rounded uppercase tracking-widest border border-[#FFE5D1]">HOT</span>
                    @endif
                    @if($product->is_trending)
                        <span class="px-2.5 py-1 bg-[#F5F3FF] text-[#8B5CF6] text-[10px] font-bold rounded uppercase tracking-widest border border-[#EDE9FE]">TRENDING</span>
                    @endif
                </div>
                <p class="text-sm font-bold text-gray-500">ID: <span class="text-blue-600">PRD-{{ str_pad($product->product_id, 4, '0', STR_PAD_LEFT) }}</span></p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-[#0A2540] text-xs font-black rounded-lg hover:bg-gray-50 transition-all flex items-center gap-2 uppercase tracking-widest shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> TRỞ VỀ
                </a>
                <a href="{{ route('admin.products.edit', $product->product_id) }}" class="px-5 py-2.5 bg-[#0A2540] text-white text-xs font-black rounded-lg hover:bg-[#0A2540]/90 transition-all shadow-md shadow-[#0A2540]/20 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="edit-3" class="w-4 h-4"></i> SỬA THÔNG TIN
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Left Column -->
        <div class="space-y-8">
            
            <!-- Gallery -->
            @php 
                $images = $product->images->sortBy('sort_order')->values(); 
                $primaryImage = $images->firstWhere('is_primary', 1) ?? $images->first();
            @endphp
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="w-full h-[360px] bg-gray-900 rounded-2xl overflow-hidden shadow-lg border border-gray-100 flex items-center justify-center relative">
                    @if($images->isNotEmpty())
                        <img id="adminMainProductImage" src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}"
                             class="w-full h-full object-contain p-4"
                             onerror="this.src=''; this.closest('div').classList.add('bg-gray-100'); this.remove();">
                    @else
                        <div class="flex flex-col items-center gap-3 text-gray-600">
                            <i data-lucide="image" class="w-16 h-16 opacity-20"></i>
                            <span class="text-xs font-bold uppercase tracking-widest opacity-40">Chưa có hình ảnh</span>
                        </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($images->count() > 1)
                <div class="grid grid-cols-4 gap-4">
                    @foreach($images->take(4) as $i => $img)
                    <div onclick="changeAdminMainImage(this, '{{ $img->image_url }}')"
                         class="admin-thumbnail-item aspect-square border-2 {{ $img->image_id === $primaryImage->image_id ? 'border-blue-500 opacity-100' : 'border-transparent opacity-70' }} rounded-xl overflow-hidden cursor-pointer hover:opacity-100 transition-all bg-gray-50 flex items-center justify-center relative">
                        @if($i === 3 && $images->count() > 4)
                            <img src="{{ $img->image_url }}" class="w-full h-full object-cover" alt="">
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white font-black text-xl">+{{ $images->count() - 4 }}</span>
                            </div>
                        @else
                            <img src="{{ $img->image_url }}" class="w-full h-full object-contain p-2" alt="">
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    MÔ TẢ TÓM TẮT
                </h3>
                <p class="text-gray-500 text-sm leading-relaxed font-medium">
                    {{ $product->description ?? '(Chưa có mô tả)' }}
                </p>
            </div>

            <!-- Variants Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-500">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        BIẾN THỂ TỒN KHO
                    </h3>
                    <span class="px-2.5 py-1 bg-[#0A2540] text-white text-[10px] font-bold rounded-full uppercase tracking-wider">{{ $product->variants->count() }} SKU</span>
                </div>

                @if($product->variants->isEmpty())
                    <p class="text-sm text-gray-400 font-medium text-center py-6">(Chưa có biến thể nào)</p>
                @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="pb-3 w-1/4">MÃ SKU</th>
                            <th class="pb-3">THUỘC TÍNH</th>
                            <th class="pb-3 text-right">GIÁ BÁN</th>
                            <th class="pb-3 text-right">TỒN KHO</th>
                            <th class="pb-3 text-center">TRẠNG THÁI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-xs font-bold text-[#0A2540]">
                        @foreach($product->variants as $variant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 font-mono text-[10px] text-gray-500 uppercase">{{ $variant->sku }}</td>
                            <td class="py-4">
                                @if($variant->attribute_values)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach((array)$variant->attribute_values as $key => $val)
                                            <span class="px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-600 rounded">{{ $key }}: {{ $val }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-[10px]">—</span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                <div>
                                    @if($variant->sale_price)
                                        <p class="text-[#0FAF62]">{{ number_format($variant->sale_price, 0, ',', '.') }}₫</p>
                                        <p class="text-gray-400 line-through text-[10px]">{{ number_format($variant->price, 0, ',', '.') }}₫</p>
                                    @else
                                        {{ number_format($variant->price, 0, ',', '.') }}₫
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span>{{ $variant->stock_quantity }}</span>
                                    <div class="w-2 h-2 rounded-full {{ $variant->stock_quantity > 10 ? 'bg-[#0FAF62]' : ($variant->stock_quantity > 0 ? 'bg-[#FFB020]' : 'bg-[#FF4C4C]') }}"></div>
                                </div>
                            </td>
                            <td class="py-4 text-center">
                                @if($variant->is_active)
                                    <span class="text-[9px] font-black text-[#0FAF62] uppercase">ACTIVE</span>
                                @else
                                    <span class="text-[9px] font-black text-gray-400 uppercase">OFF</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-[#0A2540] rounded-2xl p-6 shadow-lg shadow-[#0A2540]/20 text-white relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full"></div>
                    <p class="text-[10px] font-black text-blue-300 uppercase tracking-widest mb-2 opacity-80">LƯỢT TIẾP CẬN</p>
                    <h3 class="text-4xl font-black tracking-tight mb-4">{{ number_format($product->view_count) }}</h3>
                    <div class="flex items-center text-[10px] font-black text-blue-300 tracking-wider">
                        <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5"></i> TỔNG LƯỢT XEM
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">THÔNG TIN LỊCH SỬ</p>
                        @if($product->is_active)
                            <span class="px-2 py-0.5 bg-[#E2F6EA] text-[#0FAF62] text-[9px] font-black rounded uppercase tracking-widest">ACTIVE</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-black rounded uppercase tracking-widest">INACTIVE</span>
                        @endif
                    </div>
                    <div class="space-y-4">
                        <div class="flex gap-3 items-start">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">NGÀY KHỞI TẠO</p>
                                <p class="text-xs font-bold text-[#0A2540]">
                                    {{ $product->created_at ? $product->created_at->format('d/m/Y') : '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start">
                            <i data-lucide="layers" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">SỐ BIẾN THỂ</p>
                                <p class="text-xs font-bold text-[#0A2540]">{{ $product->variants->count() }} SKU</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cấu hình & Nhận diện -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    CẤU HÌNH & NHẬN DIỆN
                </h3>

                <div class="grid grid-cols-2 gap-y-6 gap-x-8 mb-8">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> TÊN SẢN PHẨM
                        </p>
                        <p class="text-sm font-bold text-[#0A2540] leading-snug">{{ $product->name }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> ĐƯỜNG DẪN SLUG
                        </p>
                        <p class="text-xs font-medium text-gray-500 truncate">{{ $product->slug }}</p>
                    </div>
                    
                    <div class="col-span-2 w-full h-px bg-gray-100"></div>

                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> PHÂN LOẠI DANH MỤC
                        </p>
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-600 text-[10px] font-black rounded uppercase tracking-widest">
                            {{ $product->category->name ?? '—' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> THƯƠNG HIỆU
                        </p>
                        <p class="text-xs font-bold text-[#0A2540] flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-gray-400"></i>
                            {{ $product->brand->name ?? '—' }}
                        </p>
                    </div>
                </div>

                <!-- Price -->
                <div class="bg-gray-50/80 rounded-xl p-6 border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">GIÁ BÁN NIÊM YẾT</p>
                        <h2 class="text-3xl font-black text-[#0A2540]">
                            {{ number_format($product->base_price, 0, ',', '.') }} <span class="text-lg">₫</span>
                        </h2>
                    </div>
                    @if($product->variants->isNotEmpty())
                    <div class="w-px h-12 bg-gray-200 hidden md:block"></div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">KHO TỒN (TỔNG)</p>
                        <p class="text-2xl font-black text-[#0A2540]">{{ number_format($product->variants->sum('stock_quantity')) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thông số kỹ thuật -->
            @if($product->specs)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-500">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                    </div>
                    THÔNG SỐ KỸ THUẬT
                </h3>
                <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                    @foreach((array)$product->specs as $key => $val)
                    <div class="grid grid-cols-[100px_1fr] items-center gap-2 border-b border-gray-50 pb-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">{{ $key }}</span>
                        <span class="text-[11px] font-bold text-[#0A2540]">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function changeAdminMainImage(el, url) {
        const mainImg = document.getElementById('adminMainProductImage');
        if (mainImg) {
            mainImg.src = url;
        }

        // Bỏ active border của toàn bộ thumbnails
        document.querySelectorAll('.admin-thumbnail-item').forEach(item => {
            item.classList.remove('border-blue-500', 'opacity-100');
            item.classList.add('border-transparent', 'opacity-70');
        });

        // Thêm active border cho thumbnail được click
        el.classList.remove('border-transparent', 'opacity-70');
        el.classList.add('border-blue-500', 'opacity-100');
    }
</script>
@endpush
