@extends('admin.layouts.app')

@section('header_search')
<form action="{{ route('admin.categories.index') }}" method="GET" class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm danh mục..." class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />
</form>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Breadcrumb & Header section -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm font-medium">
            <span class="text-gray-500">Admin</span> <span class="mx-2 text-gray-400">›</span> <span class="text-[#0A2540] font-bold">Danh mục</span>
        </div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
            <div>
                <h1 class="text-3xl font-bold text-[#0A2540]">Quản lý Danh mục</h1>
                <p class="text-gray-500 text-sm mt-2">Quản lý cấu trúc phân cấp và hiển thị của các nhóm sản phẩm.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Xuất Excel
                </button>
                <a href="{{ route('admin.categories.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-medium transition-colors text-sm shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Thêm danh mục mới
                </a>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 uppercase text-[11px] font-bold text-gray-500 tracking-wider">
                        <th class="py-4 px-6 w-24">ID</th>
                        <th class="py-4 px-2 w-16">ICON</th>
                        <th class="py-4 px-4 w-64">TÊN DANH MỤC</th>
                        <th class="py-4 px-4">DANH MỤC CHA</th>
                        <th class="py-4 px-4">SLUG</th>
                        <th class="py-4 px-4 text-center">THỨ TỰ</th>
                        <th class="py-4 px-4 text-center">TRẠNG THÁI</th>
                        <th class="py-4 px-6 text-right">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-5 px-6 font-bold text-[#0A2540] text-sm">
                            #CAT-{{ str_pad($category->category_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-5 px-2">
                            <div class="w-10 h-10 rounded bg-[#EBF1FF] flex items-center justify-center text-[#5D87FF]">
                                @if($category->icon_url)
                                <i data-lucide="{{ strtolower($category->icon_url) }}" class="w-5 h-5"></i>
                                @else
                                <i data-lucide="folder" class="w-5 h-5"></i>
                                @endif
                            </div>
                        </td>
                        <td class="py-5 px-4 block">
                            <div class="flex flex-col">
                                @if($category->parent_id)
                                <div class="flex gap-2">
                                    <i data-lucide="corner-down-right" class="w-4 h-4 text-gray-300 mt-1 flex-shrink-0"></i>
                                    <div>
                                        <p class="font-bold text-[#0A2540] text-[15px]">{{ $category->name }}</p>
                                        <p class="text-[11px] text-gray-500 font-medium uppercase mt-0.5">{{ $category->children_count ?? 0 }} SẢN PHẨM</p>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <p class="font-bold text-[#0A2540] text-[15px]">{{ $category->name }}</p>
                                    <p class="text-[11px] text-gray-500 font-medium uppercase mt-0.5">{{ $category->children_count ?? 0 }} SẢN PHẨM</p>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-5 px-4 text-sm text-gray-600 font-medium">
                            @if($category->parent)
                                <div class="flex items-center gap-2 font-bold text-[#0A2540] text-[13px]">
                                    @if($category->parent->icon_url)
                                    <i data-lucide="{{ strtolower($category->parent->icon_url) }}" class="w-4 h-4 text-gray-400"></i>
                                    @endif
                                    {{ $category->parent->name }}
                                </div>
                            @else
                                <span class="text-gray-400">— Gốc —</span>
                            @endif
                        </td>
                        <td class="py-5 px-4">
                            <span class="inline-block px-2.5 py-1 bg-[#F4F6F8] text-[#556987] font-mono text-[13px] rounded-md font-medium border border-gray-100">
                                {{ $category->slug }}
                            </span>
                        </td>
                        <td class="py-5 px-4 text-center font-bold text-[#0A2540]">
                            {{ $category->sort_order }}
                        </td>
                        <td class="py-5 px-4 text-center">
                            @if($category->is_active)
                            <span class="inline-flex items-center justify-center px-4 py-1.5 bg-[#E2F6EA] text-[#0FAF62] text-[11px] font-bold rounded-full w-20">
                                HIỂN THỊ
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center px-4 py-1.5 bg-[#F0F2F5] text-[#6B7280] text-[11px] font-bold rounded-full w-20">
                                ĐANG ẨN
                            </span>
                            @endif
                        </td>
                        <td class="py-5 px-6">
                            <div class="flex items-center justify-end gap-3 text-gray-400">
                                <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="hover:text-[#0A2540] transition-colors"><i data-lucide="edit" class="w-[18px] h-[18px]"></i></a>
                                
                                <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không? Hành động này không thể hoàn tác.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-red-500 transition-colors ml-1"><i data-lucide="trash-2" class="w-[18px] h-[18px] text-red-500"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">Không tìm thấy danh mục phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 flex items-center justify-between bg-white text-sm">
            <p class="text-gray-500 font-bold text-[13px] tracking-wide">HIỂN THỊ {{ $categories->count() }} TRÊN {{ $categories->total() }} DANH MỤC</p>
            <div class="flex items-center gap-1.5">
                {{ $categories->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- KPI Statistic Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-[#0A2540] flex flex-col justify-between h-36">
            <div class="flex justify-between items-start">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div class="bg-[#E2F6EA] text-[#0FAF62] px-3 py-1 rounded text-xs font-bold">
                    TẤT CẢ
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-black text-[#0A2540]">{{ $totalCategories }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Tổng số danh mục</p>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-[#3B82F6] flex flex-col justify-between h-36">
            <div class="flex justify-between items-start">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                </div>
                <div class="bg-[#F0F2F5] text-[#6B7280] px-3 py-1 rounded text-xs font-bold tracking-wide">
                    CẤU TRÚC ỔN ĐỊNH
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-black text-[#0A2540]">{{ $rootCategories }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Danh mục cấp 1 (Gốc)</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-[#6B7280] flex flex-col justify-between h-36">
            <div class="flex justify-between items-start">
                <div class="w-10 h-10 bg-[#F0F2F5] rounded-lg flex items-center justify-center text-gray-400">
                    <i data-lucide="eye-off" class="w-5 h-5"></i>
                </div>
                @if($hiddenCategories > 0)
                <div class="bg-red-50 text-red-500 px-3 py-1 rounded text-xs font-bold border border-red-100">
                    {{ $hiddenCategories }} ẨN
                </div>
                @endif
            </div>
            <div>
                <h3 class="text-3xl font-black text-[#0A2540]">{{ $activeCategories }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Danh mục đang hoạt động</p>
            </div>
        </div>
    </div>
</div>
@endsection
