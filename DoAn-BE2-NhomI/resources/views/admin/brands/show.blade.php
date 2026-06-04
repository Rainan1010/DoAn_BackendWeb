@extends('admin.layouts.app')

@section('header_search')
<form class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>
    <input type="text" placeholder="Tìm kiếm..." class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />
</form>
@endsection

@section('content')
<div class="space-y-6 pb-10">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.brands.index') }}" class="text-gray-400 hover:text-[#0A2540] transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h1 class="text-xl font-bold text-blue-600">Chi tiết thương hiệu</h1>
    </div>

    <!-- Top Row -->
    <div class="flex flex-col lg:flex-row gap-5">

        <!-- Brand Info Card -->
        <div class="lg:flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-start gap-5">
                <!-- Logo -->
                <div class="w-20 h-20 flex-shrink-0 rounded-xl border border-gray-200 bg-[#F8F9FA] flex items-center justify-center overflow-hidden p-2">
                    @if($brand->logo_url)
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="max-w-full max-h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                        <div style="display:none" class="w-full h-full items-center justify-center text-gray-300">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    @else
                        <div class="flex items-center justify-center text-gray-300">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap mb-1">
                        <h2 class="text-2xl font-black text-[#0A2540]">{{ $brand->name }}</h2>
                        @if($brand->is_active)
                            <span class="bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-black px-2.5 py-1 rounded uppercase tracking-wide">ACTIVE</span>
                        @else
                            <span class="bg-[#F0F2F5] text-gray-500 text-[10px] font-black px-2.5 py-1 rounded uppercase tracking-wide">HIDDEN</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-sm font-mono mb-3">{{ $brand->slug }}</p>
                    <div class="flex items-center gap-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div>
                            <span class="block text-[10px] text-gray-400 mb-0.5">Brand ID</span>
                            <span class="text-[#0A2540]">#{{ str_pad($brand->brand_id, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 mb-0.5">Ngày tạo</span>
                            <span class="text-[#0A2540]">{{ \Carbon\Carbon::parse($brand->created_at)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-all text-sm">
                        CHỈNH SỬA
                    </a>
                    <button onclick="copyBrandId('{{ $brand->brand_id }}', this)" class="px-4 py-2 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-all text-sm flex items-center gap-1.5 min-w-[130px] justify-center">
                        <i data-lucide="copy" class="w-4 h-4"></i> SAO CHÉP ID
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="lg:w-56 bg-[#0A2540] rounded-xl shadow-sm p-6 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute right-0 top-0 bottom-0 flex items-center opacity-10 pr-2">
                <i data-lucide="package" class="w-24 h-24 text-white"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-blue-300 uppercase tracking-widest mb-2">Tổng sản phẩm</p>
                <p class="text-5xl font-black text-white">{{ number_format($totalProducts) }}</p>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                @if($growth >= 0)
                    <span class="text-[#0FAF62] text-xs font-bold">↑ {{ $growth }}%</span>
                @else
                    <span class="text-red-500 text-xs font-bold">↓ {{ abs($growth) }}%</span>
                @endif
                <span class="text-gray-400 text-xs">so với tháng trước</span>
            </div>
        </div>
    </div>

    <!-- Description + Performance Row -->
    <div class="flex flex-col lg:flex-row gap-5">
        <!-- Description -->
        <div class="lg:flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Mô tả thương hiệu</p>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $brand->description ?? 'Chưa có mô tả cho thương hiệu này.' }}
            </p>
        </div>

        <!-- Performance -->
        <div class="lg:w-72 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Hiệu suất bán hàng</p>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Tháng này</span>
                <span class="text-base font-black text-[#0A2540]">{{ number_format($thisMonthRevenue) }} đ</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="bg-[#0A2540] h-2 rounded-full" style="width: {{ $performancePercent }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium">
                <span>Mục tiêu: {{ number_format($monthlyGoal) }} đ</span>
                <span>{{ $performancePercent }}%</span>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div x-data="{ activeFilter: 'all', openFilter: false }" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-[#0A2540]">Sản phẩm thuộc thương hiệu</h2>
                <p class="text-xs text-gray-400 mt-0.5">Danh sách các sản phẩm đang liên kết với thương hiệu này.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Filter Dropdown -->
                <div class="relative">
                    <button @click="openFilter = !openFilter" @click.away="openFilter = false" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 font-medium transition-colors">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                        <span>Bộ lọc: <strong class="text-[#0A2540]" x-text="activeFilter === 'all' ? 'Tất cả' : (activeFilter === 'active' ? 'Đang bán' : 'Tạm ẩn')"></strong></span>
                    </button>
                    <div x-show="openFilter" x-transition class="absolute right-0 mt-1 w-40 rounded-xl bg-white border border-gray-100 shadow-xl z-20 overflow-hidden" style="display: none;">
                        <div class="py-1">
                            <button @click="activeFilter = 'all'; openFilter = false" class="w-full text-left px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Tất cả
                            </button>
                            <button @click="activeFilter = 'active'; openFilter = false" class="w-full text-left px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#0FAF62]"></span> Đang bán
                            </button>
                            <button @click="activeFilter = 'inactive'; openFilter = false" class="w-full text-left px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Tạm ẩn
                            </button>
                        </div>
                    </div>
                </div>

                <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 font-medium transition-colors opacity-50 cursor-not-allowed" disabled title="Xuất dữ liệu đã bị vô hiệu hóa">
                    <i data-lucide="download" class="w-4 h-4"></i> Xuất dữ liệu
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F8F9FA] text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-6 w-28">ID</th>
                        <th class="py-3 px-4">Tên sản phẩm</th>
                        <th class="py-3 px-4">Danh mục</th>
                        <th class="py-3 px-4">Giá bán</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr x-show="activeFilter === 'all' || (activeFilter === 'active' && {{ $product->is_active ? 'true' : 'false' }}) || (activeFilter === 'inactive' && {{ !$product->is_active ? 'true' : 'false' }})" class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <span class="text-sm font-bold text-blue-600">PRD-{{ str_pad($product->product_id, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $imageUrl = $product->primaryImage ? $product->primaryImage->image_url : ($product->images->first() ? $product->images->first()->image_url : null);
                                    @endphp
                                    <div class="w-10 h-10 rounded-lg bg-[#F4F5F7] border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-[#0A2540] text-sm">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">
                                {{ $product->category ? $product->category->name : 'Không có danh mục' }}
                            </td>
                            <td class="py-4 px-4 text-sm font-bold text-[#0A2540]">
                                {{ number_format($product->base_price) }} đ
                            </td>
                            <td class="py-4 px-4">
                                @if($product->is_active)
                                    <span class="text-[11px] font-black text-[#0FAF62] bg-[#E2F6EA] px-2.5 py-1 rounded uppercase tracking-wide">Đang bán</span>
                                @else
                                    <span class="text-[11px] font-black text-gray-500 bg-[#F0F2F5] px-2.5 py-1 rounded uppercase tracking-wide">Tạm ẩn</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <!-- Action menu using Alpine.js -->
                                <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                    <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="text-gray-400 hover:text-[#0A2540] p-1 rounded hover:bg-gray-100 transition-colors">
                                        <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                    </button>
                                    <div x-show="openMenu" x-transition class="absolute right-0 mt-1 w-36 rounded-xl bg-white border border-gray-100 shadow-xl z-20 overflow-hidden" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('admin.products.show', $product->product_id) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                                <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i> Xem chi tiết
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                                <i data-lucide="edit-3" class="w-4 h-4 text-gray-400"></i> Chỉnh sửa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i data-lucide="package" class="w-10 h-10 mb-2"></i>
                                    <p class="text-sm font-medium">Chưa có sản phẩm nào thuộc thương hiệu này</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination Footer -->
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
                <p class="text-gray-500 font-medium">Hiển thị {{ $products->firstItem() }}–{{ $products->lastItem() }} trên {{ $products->total() }} sản phẩm</p>
                <div class="flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($products->onFirstPage())
                        <button class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-400 opacity-40 cursor-not-allowed" disabled>
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <span class="w-8 h-8 rounded bg-[#0A2540] text-white text-sm font-bold flex items-center justify-center">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-sm font-bold transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <button class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-400 opacity-40 cursor-not-allowed" disabled>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 text-sm text-gray-500 font-medium">
                Hiển thị {{ $products->count() }} sản phẩm
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyBrandId(id, button) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(id).then(function() {
            showCopyFeedback(button);
        }, function() {
            fallbackCopy(id, button);
        });
    } else {
        fallbackCopy(id, button);
    }
}

function fallbackCopy(id, button) {
    var textArea = document.createElement("textarea");
    textArea.value = id;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showCopyFeedback(button);
    } catch (err) {
        console.error('Không thể sao chép ID', err);
    }
    document.body.removeChild(textArea);
}

function showCopyFeedback(button) {
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> ĐÃ SAO CHÉP!';
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    button.classList.remove('bg-[#0A2540]', 'hover:bg-[#113255]');
    button.classList.add('bg-green-600', 'text-white');
    setTimeout(() => {
        button.innerHTML = originalHTML;
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        button.classList.remove('bg-green-600');
        button.classList.add('bg-[#0A2540]', 'hover:bg-[#113255]');
    }, 2000);
}
</script>
@endpush
