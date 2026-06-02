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

    $selectedAttribute = $selectedAttribute ?? $attributes->first();
@endphp

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div
    class="space-y-6"
    x-data="{
        showDeleteModal: false,
        deleteUrl: '',
        deleteName: ''
    }"
>

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
                <!-- <button type="button"
                    class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Xuất Excel
                </button> -->

                <a href="{{ route('admin.attributes.create') }}"
                    class="flex items-center gap-2 px-5 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Thêm thuộc tính mới
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
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

                                    <button
                                        type="button"
                                        class="hover:text-red-500 transition-colors"
                                        title="Xóa"
                                        @click="
                                            showDeleteModal = true;
                                            deleteUrl = @js(route('admin.attributes.destroy', $attribute->attribute_id));
                                            deleteName = @js($attribute->name);
                                        "
                                    >
                                        <i data-lucide="trash-2" class="w-[18px] h-[18px] text-red-500"></i>
                                    </button>
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

    {{-- Attribute Values Preview --}}
    @if($selectedAttribute)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-[0.14em] text-[#0A2540]">
                        Giá trị thuộc tính
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Đang xem:
                        <span class="font-bold text-[#0A2540]">
                            {{ $selectedAttribute->name }}
                        </span>
                    </p>
                </div>

                <a href="{{ route('admin.attributes.edit', $selectedAttribute->attribute_id) }}"
                    class="px-4 py-2 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-xs uppercase">
                    Quản lý giá trị
                </a>
            </div>

            <div class="p-6 bg-gradient-to-r from-[#F4F5F7] via-white to-[#EEF3F8]">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @forelse($selectedAttribute->values as $value)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <p class="text-[11px] text-gray-400 font-mono mb-3">
                                VAL-{{ str_pad($value->value_id, 3, '0', STR_PAD_LEFT) }}
                            </p>

                            <p class="text-2xl font-black text-[#0A2540]">
                                {{ $value->value }}{{ $selectedAttribute->unit ? $selectedAttribute->unit : '' }}
                            </p>

                            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mt-2">
                                Attribute Value
                            </p>
                        </div>
                    @empty
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <p class="text-2xl font-black text-[#0A2540]">--</p>
                            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mt-2">
                                Chưa có giá trị
                            </p>
                        </div>
                    @endforelse

                    <a href="{{ route('admin.attributes.edit', $selectedAttribute->attribute_id) }}"
                        class="min-h-[130px] border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center text-gray-400 hover:bg-white/70 hover:text-[#0A2540] transition-colors">
                        <i data-lucide="plus" class="w-6 h-6 mb-2"></i>
                        <span class="text-[11px] font-bold uppercase tracking-wider">
                            Add Value
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirm Modal --}}
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
        style="display: none;"
    >
        <div
            @click.away="showDeleteModal = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
        >
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trash-2" class="w-8 h-8"></i>
                </div>

                <h3 class="text-xl font-black text-[#0A2540] mb-2">
                    Xác nhận xoá thuộc tính
                </h3>

                <p class="text-sm text-gray-500 leading-6">
                    Bạn có chắc chắn muốn xoá thuộc tính
                    <span class="font-bold text-red-600" x-text="deleteName"></span>
                    không?
                </p>

                <p class="text-xs text-gray-400 mt-3">
                    Các giá trị thuộc tính liên quan cũng sẽ bị xoá khỏi hệ thống.
                </p>
            </div>

            <div class="flex gap-3 p-4 bg-[#F8F9FA] border-t border-gray-100">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="flex-1 px-4 py-3 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 font-bold text-sm transition-colors"
                >
                    Huỷ bỏ
                </button>

                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="w-full px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-sm transition-colors shadow-sm"
                    >
                        Xác nhận xoá
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection