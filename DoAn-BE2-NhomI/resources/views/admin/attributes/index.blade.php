@extends('admin.layouts.app')

@section('header_search')
<form action="{{ route('admin.attributes.index') }}" method="GET" class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Tìm kiếm thuộc tính..."
        class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />
</form>
@endsection

@section('title', 'Quản lý thuộc tính')

@section('content')
@php
    $totalAttributes = method_exists($attributes, 'total') ? $attributes->total() : $attributes->count();
    $totalValues = $attributes->sum(fn($attribute) => $attribute->values->count());
    $emptyAttributes = $attributes->filter(fn($attribute) => $attribute->values->count() === 0)->count();
@endphp

<div class="space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm font-medium">
            <span class="text-gray-500">Admin</span>
            <span class="mx-2 text-gray-400">›</span>
            <span class="text-[#0A2540] font-bold">Thuộc tính</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
            <div>
                <h1 class="text-3xl font-bold text-[#0A2540]">
                    Quản lý Thuộc tính
                </h1>
                <p class="text-gray-500 text-sm mt-2">
                    Quản lý RAM, ROM, màu sắc và các giá trị biến thể dùng cho sản phẩm.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Xuất Excel
                </button>

                <a href="{{ route('admin.attributes.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Thêm thuộc tính mới
                </a>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KPI Statistic Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Tổng thuộc tính
            </h3>
            <p class="text-3xl font-black text-[#0A2540]">
                {{ $totalAttributes }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Đang hoạt động
            </h3>
            <p class="text-3xl font-black text-[#0FAF62]">
                {{ $totalAttributes }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Giá trị biến thể
            </h3>
            <p class="text-3xl font-black text-[#0A2540]">
                {{ $totalValues }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Chưa có giá trị
            </h3>
            <p class="text-3xl font-black text-gray-500">
                {{ $emptyAttributes }}
            </p>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-black uppercase tracking-[0.14em] text-[#0A2540]">
                    Danh sách thuộc tính
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Các thông số dùng để tạo biến thể sản phẩm.
                </p>
            </div>

            <span class="px-3 py-1 rounded bg-[#E8F0FF] text-[#0A2540] text-[11px] font-bold uppercase">
                {{ $totalAttributes }} Active
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F8F9FA] uppercase text-[11px] font-bold text-[#556987] tracking-wider">
                        <th class="py-4 px-6 w-28">ID</th>
                        <th class="py-4 px-4 min-w-[220px]">Tên thuộc tính</th>
                        <th class="py-4 px-4 w-28">Đơn vị</th>
                        <th class="py-4 px-4 min-w-[260px]">Giá trị</th>
                        <th class="py-4 px-4 w-32">Trạng thái</th>
                        <th class="py-4 px-6 text-right w-32">Hành động</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($attributes as $attribute)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-5 px-6 font-medium text-gray-500 text-sm">
                                AT-{{ str_pad($attribute->attribute_id, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="py-5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#F4F5F7] border border-gray-200 flex items-center justify-center text-[#0A2540]">
                                        @php
                                            $nameLower = mb_strtolower($attribute->name);
                                        @endphp

                                        @if(str_contains($nameLower, 'ram'))
                                            <i data-lucide="memory-stick" class="w-5 h-5"></i>
                                        @elseif(str_contains($nameLower, 'rom') || str_contains($nameLower, 'bộ nhớ'))
                                            <i data-lucide="hard-drive" class="w-5 h-5"></i>
                                        @elseif(str_contains($nameLower, 'màu'))
                                            <i data-lucide="palette" class="w-5 h-5"></i>
                                        @else
                                            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-bold text-[#0A2540] text-[15px]">
                                            {{ $attribute->name }}
                                        </p>
                                        <p class="text-[12px] text-gray-500 mt-0.5">
                                            Attribute ID: {{ $attribute->attribute_id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-5 px-4">
                                @if($attribute->unit)
                                    <span class="text-sm text-gray-600 font-mono">
                                        {{ $attribute->unit }}
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-gray-400 uppercase">
                                        Không có
                                    </span>
                                @endif
                            </td>

                            <td class="py-5 px-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($attribute->values as $value)
                                        <span class="px-2.5 py-1 rounded-md bg-[#F4F5F7] border border-gray-200 text-[12px] font-bold text-gray-600">
                                            {{ $value->value }}{{ $attribute->unit ? $attribute->unit : '' }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-gray-400 italic">
                                            Chưa có giá trị
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="py-5 px-4">
                                @if($attribute->values->count() > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[11px] font-bold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-[11px] font-bold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Empty
                                    </span>
                                @endif
                            </td>

                            <td class="py-5 px-6">
                                <div class="flex items-center justify-end gap-3 text-[#0A2540]">
                                    <a href="{{ route('admin.attributes.edit', $attribute->attribute_id) }}"
                                       class="hover:text-blue-600 transition-colors"
                                       title="Chỉnh sửa">
                                        <i data-lucide="edit-2" class="w-[18px] h-[18px]"></i>
                                    </a>

                                    <form action="{{ route('admin.attributes.destroy', $attribute->attribute_id) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa thuộc tính này?');">
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
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                Không tìm thấy thuộc tính nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
            <p class="text-gray-500 text-[13px] font-medium">
                Hiển thị {{ $attributes->count() }} thuộc tính
            </p>

            @if(method_exists($attributes, 'links'))
                <div class="flex items-center gap-1.5">
                    {{ $attributes->links('pagination::tailwind') }}
                </div>
            @else
                <div class="flex items-center gap-1.5">
                    <span class="w-8 h-8 rounded bg-[#0A2540] text-white flex items-center justify-center text-xs font-bold">
                        1
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection