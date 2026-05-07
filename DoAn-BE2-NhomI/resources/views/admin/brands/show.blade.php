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
                    <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm">
                        CHỈNH SỬA
                    </a>
                    <button onclick="navigator.clipboard.writeText('{{ $brand->brand_id }}')" class="px-4 py-2 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm">
                        SAO CHÉP ID
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
                <p class="text-5xl font-black text-white">0</p>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                <span class="text-[#0FAF62] text-xs font-bold">↑ 0%</span>
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
                <span class="text-base font-black text-[#0A2540]">$0</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="bg-[#0A2540] h-2 rounded-full" style="width: 0%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium">
                <span>Mục tiêu: $50,000</span>
                <span>0%</span>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#0A2540]">Sản phẩm thuộc thương hiệu</h2>
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 font-medium transition-colors">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Bộ lọc
                </button>
                <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 font-medium transition-colors">
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
                    @php
                        $mockProducts = [
                            ['id' => 'PRD-001', 'name' => $brand->name . ' - Sản phẩm 1', 'category' => 'Electronics', 'price' => '$299.00', 'status' => 'con_hang', 'img' => null],
                            ['id' => 'PRD-002', 'name' => $brand->name . ' - Sản phẩm 2', 'category' => 'Accessories', 'price' => '$149.00', 'status' => 'sap_het', 'img' => null],
                        ];
                    @endphp
                    @if(count($mockProducts) > 0)
                        @foreach($mockProducts as $product)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <span class="text-sm font-bold text-blue-600">{{ $product['id'] }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#F4F5F7] border border-gray-200 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                    </div>
                                    <span class="font-semibold text-[#0A2540] text-sm">{{ $product['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ $product['category'] }}</td>
                            <td class="py-4 px-4 text-sm font-bold text-[#0A2540]">{{ $product['price'] }}</td>
                            <td class="py-4 px-4">
                                @if($product['status'] === 'con_hang')
                                    <span class="text-[11px] font-black text-[#0FAF62] uppercase tracking-wide">Còn hàng</span>
                                @elseif($product['status'] === 'sap_het')
                                    <span class="text-[11px] font-black text-orange-500 uppercase tracking-wide">Sắp hết</span>
                                @else
                                    <span class="text-[11px] font-black text-red-500 uppercase tracking-wide">Ngừng KD</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button class="text-gray-400 hover:text-[#0A2540] transition-colors">
                                    <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i data-lucide="package" class="w-10 h-10 mb-2"></i>
                                    <p class="text-sm font-medium">Chưa có sản phẩm nào thuộc thương hiệu này</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <p class="text-gray-500 font-medium">Hiển thị 1–{{ count($mockProducts) }} trên {{ count($mockProducts) }} sản phẩm</p>
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-50 disabled:opacity-40" disabled>
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <button class="w-8 h-8 rounded bg-[#0A2540] text-white text-sm font-bold">1</button>
                <button class="w-8 h-8 rounded border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-50" disabled>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
