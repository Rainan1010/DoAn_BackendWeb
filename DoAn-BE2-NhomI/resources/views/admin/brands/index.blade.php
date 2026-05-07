@extends('admin.layouts.app')

@section('header_search')
<form action="{{ route('admin.brands.index') }}" method="GET" class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm hệ thống..." class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />
</form>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Breadcrumb & Header section -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm font-medium">
            <span class="text-gray-500">Admin</span> <span class="mx-2 text-gray-400">›</span> <span class="text-[#0A2540] font-bold">Thương hiệu</span>
        </div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
            <div>
                <h1 class="text-3xl font-bold text-[#0A2540]">Quản lý Thương hiệu</h1>
                <p class="text-gray-500 text-sm mt-2">Danh sách và thông tin các thương hiệu trong hệ thống.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Xuất Excel
                </button>
                <a href="{{ route('admin.brands.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Thêm thương hiệu mới
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Statistic Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tổng thương hiệu</h3>
            <p class="text-3xl font-black text-[#0A2540]">{{ $totalBrands }}</p>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Đang hoạt động</h3>
            <p class="text-3xl font-black text-[#0FAF62]">{{ $activeBrands }}</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Đang ẩn</h3>
            <p class="text-3xl font-black text-gray-500">{{ $hiddenBrands }}</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mới trong tháng</h3>
            <p class="text-3xl font-black text-[#0A2540]">{{ $newBrands }}</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F8F9FA] uppercase text-[11px] font-bold text-[#556987] tracking-wider">
                        <th class="py-4 px-6 w-20">ID</th>
                        <th class="py-4 px-2 w-20">LOGO</th>
                        <th class="py-4 px-4 w-48">TÊN THƯƠNG HIỆU</th>
                        <th class="py-4 px-4 w-40">SLUG</th>
                        <th class="py-4 px-4 w-64">MÔ TẢ</th>
                        <th class="py-4 px-4 w-32">TRẠNG THÁI</th>
                        <th class="py-4 px-4 w-32">NGÀY TẠO</th>
                        <th class="py-4 px-6 text-right w-32">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($brands as $brand)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-5 px-6 font-medium text-gray-500 text-sm">
                            BR-{{ str_pad($brand->brand_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-5 px-2">
                            <div class="w-10 h-10 rounded bg-[#F4F5F7] border border-gray-200 flex items-center justify-center p-1.5 bg-white">
                                @if($brand->logo_url)
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="max-w-full max-h-full object-contain mix-blend-multiply" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($brand->name) }}&background=0D8ABC&color=fff'" />
                                @else
                                <span class="text-xs font-bold text-gray-400">{{ substr($brand->name, 0, 1) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-5 px-4 block">
                            <div class="flex flex-col">
                                <p class="font-bold text-[#0A2540] text-[15px]">{{ $brand->name }}</p>
                                @if($brand->country)
                                <p class="text-[12px] text-gray-500 mt-0.5">{{ $brand->country }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-5 px-4 text-sm">
                            <span class="text-gray-500 font-mono text-[13px]">
                                {{ $brand->slug }}
                            </span>
                        </td>
                        <td class="py-5 px-4">
                            <p class="text-sm text-gray-500 truncate max-w-[250px]">
                                {{ $brand->description ?? 'Không có mô tả' }}
                            </p>
                        </td>
                        <td class="py-5 px-4">
                            <form action="{{ route('admin.brands.toggleStatus', $brand->brand_id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex items-center cursor-pointer focus:outline-none" title="{{ $brand->is_active ? 'Click để ẩn' : 'Click để hiển thị' }}">
                                    <div class="w-9 h-5 rounded-full transition-colors duration-200 {{ $brand->is_active ? 'bg-[#0A2540]' : 'bg-gray-300' }} relative">
                                        <span class="absolute top-0.5 {{ $brand->is_active ? 'left-4' : 'left-0.5' }} w-4 h-4 bg-white rounded-full shadow transition-all duration-200"></span>
                                    </div>
                                </button>
                                @if($brand->is_active)
                                <span class="text-[11px] font-bold text-[#0A2540] uppercase">HOẠT ĐỘNG</span>
                                @else
                                <span class="text-[11px] font-bold text-gray-400 uppercase">ĐANG ẨN</span>
                                @endif
                            </form>
                        </td>
                        <td class="py-5 px-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($brand->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="py-5 px-6">
                            <div class="flex items-center justify-end gap-3 text-[#0A2540]">
                                <a href="{{ route('admin.brands.show', $brand->brand_id) }}" class="hover:text-blue-600 transition-colors" title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-[18px] h-[18px]"></i>
                                </a>
                                <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" class="hover:text-blue-600 transition-colors" title="Chỉnh sửa">
                                    <i data-lucide="edit-2" class="w-[18px] h-[18px]"></i>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand->brand_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-red-500 transition-colors" title="Xóa">
                                        <i data-lucide="trash-2" class="w-[18px] h-[18px] text-red-500"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">Không tìm thấy thương hiệu nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
            <p class="text-gray-500 text-[13px] font-medium">Hiển thị 1 - {{ $brands->count() }} của {{ $brands->total() }} thương hiệu</p>
            <div class="flex items-center gap-1.5">
                {{ $brands->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>
@endsection
