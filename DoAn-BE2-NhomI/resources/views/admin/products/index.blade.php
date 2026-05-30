@extends('admin.layouts.app')

@section('header_search')
<form action="{{ route('admin.products.index') }}" method="GET" class="relative w-full max-w-md">
    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="TÌM KIẾM SẢN PHẨM..."
        class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] focus:bg-white transition-all uppercase placeholder-gray-400" />
</form>
@endsection

@section('content')
{{-- Flash success message --}}
@if(session('success'))
<div class="mb-4 px-5 py-3 bg-[#E2F6EA] text-[#0FAF62] text-sm font-bold rounded-lg border border-[#0FAF62]/20 flex items-center gap-2">
    <i data-lucide="check-circle-2" class="w-4 h-4"></i> {{ session('success') }}
</div>
@endif

<!-- Header Section -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <nav class="flex text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">
            <a href="#" class="hover:text-[#0A2540] transition-colors">DANH MỤC</a>
            <span class="mx-2">/</span>
            <span class="text-gray-300">QUẢN LÝ SẢN PHẨM</span>
        </nav>
        <h1 class="text-[32px] leading-none font-black text-[#0A2540] tracking-tight">Quản lý Sản phẩm</h1>
        <p class="text-gray-500 text-sm mt-2 max-w-2xl font-medium">Quản lý thông số kỹ thuật, giá niêm yết và trạng thái kho hàng cho hệ thống B-Tris.</p>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <button class="px-5 py-2.5 bg-white border-2 border-gray-200 text-[#0A2540] text-sm font-bold rounded-lg hover:border-[#0A2540] hover:bg-gray-50 transition-all flex items-center gap-2 uppercase tracking-wider">
            <i data-lucide="download" class="w-4 h-4"></i> XUẤT EXCEL
        </button>
        <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 bg-[#0A2540] text-white text-sm font-bold rounded-lg hover:bg-[#0A2540]/90 transition-all shadow-lg shadow-[#0A2540]/20 flex items-center gap-2 uppercase tracking-wider border border-[#0A2540]">
            <i data-lucide="plus" class="w-4 h-4"></i> THÊM SẢN PHẨM MỚI
        </a>
    </div>
</div>

<!-- Stats Cards (dữ liệu thật) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between group hover:border-[#0A2540]/30 transition-colors">
        <div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">TỔNG SẢN PHẨM</p>
            <h3 class="text-4xl font-black text-[#0A2540] tracking-tight">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="mt-5 flex items-center text-[11px] font-black text-[#0FAF62] tracking-wider">
            <i data-lucide="package" class="w-3.5 h-3.5 mr-1.5"></i> TỔNG SỐ MẶT HÀNG
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between group hover:border-[#0A2540]/30 transition-colors">
        <div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">LƯỢT XEM TỔNG</p>
            <h3 class="text-4xl font-black text-[#0A2540] tracking-tight">{{ number_format($stats['total_views']) }}</h3>
        </div>
        <div class="mt-5 flex items-center text-[11px] font-black text-blue-600 tracking-wider">
            <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5"></i> LƯU LƯỢNG ỔN ĐỊNH
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between group hover:border-[#0A2540]/30 transition-colors">
        <div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">SẢN PHẨM HOT</p>
            <h3 class="text-4xl font-black text-[#0A2540] tracking-tight">{{ $stats['hot_count'] }}</h3>
        </div>
        <div class="mt-5 flex items-center text-[11px] font-black text-[#FF6B00] tracking-wider">
            <i data-lucide="zap" class="w-3.5 h-3.5 mr-1.5"></i> ĐANG ĐƯỢC QUAN TÂM CAO
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between group hover:border-[#0A2540]/30 transition-colors">
        <div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">TỶ LỆ ĐANG BÁN</p>
            <h3 class="text-4xl font-black text-[#0A2540] tracking-tight">{{ $stats['active_pct'] }}<span class="text-2xl">%</span></h3>
        </div>
        <div class="mt-5 flex items-center text-[11px] font-black text-gray-500 tracking-wider">
            <i data-lucide="check-circle" class="w-3.5 h-3.5 mr-1.5"></i> SẢN PHẨM ĐANG KÍCH HOẠT
        </div>
    </div>
</div>

<!-- Main Table Area -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
    <!-- Filter Bar -->
    <form action="{{ route('admin.products.index') }}" method="GET">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gray-50/30 rounded-t-xl">
            <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-gray-400 tracking-wider">
                <!-- Category filter -->
                <div class="flex items-center gap-2">
                    <span class="uppercase text-gray-400">DANH MỤC:</span>
                    <select name="category_id" onchange="this.form.submit()"
                        class="appearance-none bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540] cursor-pointer">
                        <option value="">Tất cả</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-px h-4 bg-gray-300"></div>
                <!-- Brand filter -->
                <div class="flex items-center gap-2">
                    <span class="uppercase text-gray-400">THƯƠNG HIỆU:</span>
                    <select name="brand_id" onchange="this.form.submit()"
                        class="appearance-none bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540] cursor-pointer">
                        <option value="">Tất cả</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-px h-4 bg-gray-300"></div>
                <!-- Status filter -->
                <div class="flex items-center gap-2">
                    <span class="uppercase text-gray-400">TRẠNG THÁI:</span>
                    <select name="status" onchange="this.form.submit()"
                        class="appearance-none bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs font-bold text-[#0A2540] focus:outline-none focus:border-[#0A2540] cursor-pointer">
                        <option value="">Tất cả</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Đang bán</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã ẩn</option>
                    </select>
                </div>
                @if(request()->hasAny(['category_id','brand_id','status','search']))
                    <a href="{{ route('admin.products.index') }}" class="text-red-400 hover:text-red-600 uppercase tracking-widest text-[10px] font-black ml-2">
                        ✕ Xóa bộ lọc
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <th class="p-5 pl-8 w-24">ID</th>
                    <th class="p-5 min-w-[250px]">TÊN SẢN PHẨM / SLUG</th>
                    <th class="p-5 min-w-[150px]">PHÂN LOẠI</th>
                    <th class="p-5 min-w-[140px]">GIÁ CƠ BẢN</th>
                    <th class="p-5">LƯỢT XEM</th>
                    <th class="p-5">NGÀY TẠO</th>
                    <th class="p-5 min-w-[160px]">TRẠNG THÁI</th>
                    <th class="p-5 pr-8 text-right">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50/80 transition-colors group relative cursor-pointer">
                    <td class="p-5 pl-8 align-top">
                        <span class="text-[11px] font-bold text-gray-300 tracking-wider">#{{ $product->product_id }}</span>
                    </td>
                    <td class="p-5 align-top">
                        <div class="flex gap-4">
                            <div class="w-14 h-14 rounded-lg bg-gray-100 shadow-sm border border-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 items-center justify-center hidden">
                                        <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center">
                                        <i data-lucide="image" class="w-5 h-5 text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="pt-1">
                                <h4 class="text-sm font-black text-[#0A2540] leading-tight mb-1">{{ $product->name }}</h4>
                                <p class="text-[11px] font-medium text-gray-400 max-w-[180px] truncate">{{ $product->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 align-top">
                        <div class="flex flex-col gap-3 pt-1">
                            <div>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none block mb-0.5">DANH MỤC</span>
                                <p class="text-[11px] font-bold text-[#0A2540]">{{ $product->category->name ?? '—' }}</p>
                            </div>
                            <div>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none block mb-0.5">THƯƠNG HIỆU</span>
                                <p class="text-[11px] font-bold text-[#0A2540]">{{ $product->brand->name ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 align-top">
                        <span class="text-sm font-black text-[#0A2540] pt-1 block">
                            <span data-realtime-price data-product-id="{{ $product->product_id }}">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
                        </span>
                    </td>
                    <td class="p-5 align-top">
                        <span class="text-[13px] font-bold text-gray-600 pt-1 block">{{ number_format($product->view_count) }}</span>
                    </td>
                    <td class="p-5 align-top">
                        <span class="text-[13px] font-bold text-gray-500 pt-1 block">
                            {{ $product->created_at ? $product->created_at->format('d/m/Y') : '—' }}
                        </span>
                    </td>
                    <td class="p-5 align-top">
                        <div class="flex flex-wrap gap-2 pt-1">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#E2F6EA] text-[#0FAF62] uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#0FAF62] mr-1.5"></span> ACTIVE
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-gray-100 text-gray-500 uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> INACTIVE
                                </span>
                            @endif
                            @if($product->is_new)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-blue-50 text-blue-600 uppercase tracking-widest shadow-sm border border-blue-100">NEW</span>
                            @endif
                            @if($product->is_hot)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#FFF0E6] text-[#FF6B00] uppercase tracking-widest shadow-sm border border-[#FFE5D1]">HOT</span>
                            @endif
                            @if($product->is_trending)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#F5F3FF] text-[#8B5CF6] uppercase tracking-widest shadow-sm border border-[#EDE9FE]">TRENDING</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-5 pr-8 align-top">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity pt-0.5">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors inline-block" title="Chỉnh sửa">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.products.show', $product->product_id) }}" class="p-2 text-gray-400 hover:text-[#0A2540] hover:bg-gray-100 rounded-lg transition-colors inline-block" title="Xem chi tiết">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-20 text-center">
                        <div class="flex flex-col items-center gap-4 text-gray-400">
                            <i data-lucide="package-open" class="w-12 h-12"></i>
                            <p class="text-sm font-bold uppercase tracking-widest">Không tìm thấy sản phẩm nào</p>
                            @if(request()->hasAny(['category_id','brand_id','status','search']))
                                <a href="{{ route('admin.products.index') }}" class="text-xs font-black text-[#0A2540] hover:underline uppercase tracking-widest">Xóa bộ lọc</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-b-xl bg-white">
        <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">
            HIỂN THỊ {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
            TRÊN TỔNG SỐ {{ number_format($products->total()) }} SẢN PHẨM
        </span>

        {{-- Laravel built-in pagination (styled) --}}
        @if($products->hasPages())
        <div class="flex items-center gap-1.5">
            {{-- Prev --}}
            @if($products->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-[#0A2540] transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach($products->getUrlRange(max(1,$products->currentPage()-2), min($products->lastPage(),$products->currentPage()+2)) as $page => $url)
                @if($page == $products->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0A2540] text-white font-bold text-xs shadow-md shadow-[#0A2540]/20">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 font-bold text-xs hover:bg-gray-100 hover:text-[#0A2540] transition-colors">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-[#0A2540] transition-colors">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
