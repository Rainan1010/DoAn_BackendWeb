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

@section('title', 'Thêm thuộc tính')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center text-sm font-medium">
        <a href="{{ route('admin.attributes.index') }}" class="text-gray-500 hover:text-[#0A2540] transition-colors">
            Admin
        </a>
        <span class="mx-2 text-gray-400">›</span>
        <a href="{{ route('admin.attributes.index') }}" class="text-gray-500 hover:text-[#0A2540] transition-colors">
            Thuộc tính
        </a>
        <span class="mx-2 text-gray-400">›</span>
        <span class="text-[#0A2540] font-bold">Thêm mới</span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#0A2540]">
                Thêm Thuộc Tính Mới
            </h1>
            <p class="text-gray-500 text-sm mt-2">
                Định nghĩa các thông số kỹ thuật mới cho hệ thống quản lý sản phẩm B-Tris.
            </p>
        </div>

        <a href="{{ route('admin.attributes.index') }}"
            class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    {{-- Error --}}
    @if($errors->any())
    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.attributes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            {{-- LEFT: FORM --}}
            <div class="xl:col-span-8 space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-sm font-black uppercase tracking-[0.14em] text-[#0A2540]">
                            Thông tin thuộc tính
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Nhập tên thuộc tính, đơn vị và các giá trị biến thể ban đầu.
                        </p>
                    </div>

                    <div class="p-6 space-y-7">

                        {{-- Tên thuộc tính --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:items-center">
                            <label class="text-[12px] font-bold text-[#0A2540] uppercase tracking-wider">
                                Tên thuộc tính
                            </label>

                            <div class="md:col-span-3">
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Ví dụ: RAM, Bộ nhớ trong, Màu sắc..."
                                    class="w-full bg-[#F4F5F7] border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium"
                                    required>
                            </div>
                        </div>

                        {{-- Đơn vị --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:items-center">
                            <label class="text-[12px] font-bold text-[#0A2540] uppercase tracking-wider">
                                Đơn vị
                            </label>

                            <div class="md:col-span-3">
                                <input
                                    type="text"
                                    name="unit"
                                    value="{{ old('unit') }}"
                                    placeholder="Ví dụ: GB, inch, mAh hoặc bỏ trống nếu là màu sắc"
                                    class="w-full bg-[#F4F5F7] border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium">
                            </div>
                        </div>

                        {{-- Loại dữ liệu --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:items-start">
                            <label class="text-[12px] font-bold text-[#0A2540] uppercase tracking-wider pt-3">
                                Loại dữ liệu
                            </label>

                            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-3">

                                <label class="relative cursor-pointer group">
                                    <input checked class="peer sr-only" name="data_type" value="select" type="radio">
                                    <div class="p-4 rounded-xl bg-[#F4F5F7] border-2 border-transparent peer-checked:border-[#0A2540] peer-checked:bg-white transition-all group-hover:shadow-sm">
                                        <i data-lucide="list" class="w-5 h-5 text-[#0A2540] mb-2"></i>
                                        <span class="text-sm font-bold text-[#0A2540] block">Lựa chọn</span>
                                        <span class="text-[11px] text-gray-500 mt-1 block">RAM, ROM...</span>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input class="peer sr-only" name="data_type" value="text" type="radio">
                                    <div class="p-4 rounded-xl bg-[#F4F5F7] border-2 border-transparent peer-checked:border-[#0A2540] peer-checked:bg-white transition-all group-hover:shadow-sm">
                                        <i data-lucide="type" class="w-5 h-5 text-[#0A2540] mb-2"></i>
                                        <span class="text-sm font-bold text-[#0A2540] block">Văn bản</span>
                                        <span class="text-[11px] text-gray-500 mt-1 block">CPU, GPU...</span>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input class="peer sr-only" name="data_type" value="color" type="radio">
                                    <div class="p-4 rounded-xl bg-[#F4F5F7] border-2 border-transparent peer-checked:border-[#0A2540] peer-checked:bg-white transition-all group-hover:shadow-sm">
                                        <i data-lucide="palette" class="w-5 h-5 text-[#0A2540] mb-2"></i>
                                        <span class="text-sm font-bold text-[#0A2540] block">Màu sắc</span>
                                        <span class="text-[11px] text-gray-500 mt-1 block">Đen, Trắng...</span>
                                    </div>
                                </label>

                            </div>
                        </div>

                        {{-- Giá trị --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:items-start">
                            <label class="text-[12px] font-bold text-[#0A2540] uppercase tracking-wider pt-3">
                                Biến thể / Giá trị
                            </label>

                            <div class="md:col-span-3 space-y-3">
                                <input
                                    type="text"
                                    name="values"
                                    value="{{ old('values') }}"
                                    placeholder="Ví dụ: 8, 16, 32 hoặc Đen, Trắng, Titan tự nhiên"
                                    class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] transition-colors text-[#0A2540] font-medium">

                                <p class="text-xs text-gray-500">
                                    Nhập nhiều giá trị, cách nhau bằng dấu phẩy.
                                </p>

                                <div class="flex flex-wrap gap-2 pt-1">
                                    <span class="px-3 py-1.5 rounded-full bg-[#E8F0FF] text-[#0A2540] text-xs font-bold border border-blue-100">
                                        8GB
                                    </span>
                                    <span class="px-3 py-1.5 rounded-full bg-[#E8F0FF] text-[#0A2540] text-xs font-bold border border-blue-100">
                                        16GB
                                    </span>
                                    <span class="px-3 py-1.5 rounded-full bg-[#E8F0FF] text-[#0A2540] text-xs font-bold border border-blue-100">
                                        32GB
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-5 bg-[#F8F9FA] border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.attributes.index') }}"
                            class="px-6 py-2.5 text-gray-600 hover:text-[#0A2540] font-bold transition-colors text-sm text-center">
                            Hủy bỏ
                        </a>

                        <button type="submit"
                            class="flex items-center justify-center gap-2 px-7 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm shadow-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Lưu thuộc tính
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT: GUIDE + PREVIEW --}}
            <div class="xl:col-span-4 space-y-6">

                {{-- Guide --}}
                <div class="bg-[#0A2540] rounded-xl shadow-sm border border-[#0A2540] p-6 text-white overflow-hidden relative">
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/5"></div>

                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-5">
                            <i data-lucide="info" class="w-6 h-6 text-[#A7C8FF]"></i>
                        </div>

                        <h3 class="text-xl font-black mb-3">
                            Hướng dẫn thiết lập
                        </h3>

                        <p class="text-sm leading-7 text-blue-100 mb-5">
                            Các thuộc tính được tạo sẽ được áp dụng cho quy trình cấu hình sản phẩm tại mục quản lý kho.
                        </p>

                        <ul class="space-y-4">
                            <li class="flex gap-3">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-[#A7C8FF] flex-shrink-0"></i>
                                <span class="text-sm text-blue-100">
                                    Tên thuộc tính nên ngắn gọn và mang tính mô tả kỹ thuật cao.
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-[#A7C8FF] flex-shrink-0"></i>
                                <span class="text-sm text-blue-100">
                                    RAM, ROM nên dùng đơn vị GB để dữ liệu đồng bộ.
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-[#A7C8FF] flex-shrink-0"></i>
                                <span class="text-sm text-blue-100">
                                    Có thể nhập nhiều giá trị bằng dấu phẩy.
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.14em] text-[#0A2540] mb-5">
                        Trực quan hóa thuộc tính
                    </h3>

                    <div class="rounded-xl bg-[#F8F9FA] border border-gray-200 p-5">
                        <div class="flex items-start justify-between mb-5">
                            <div>
                                <p class="text-[11px] text-gray-400 font-bold uppercase mb-1">
                                    Mô phỏng hiển thị
                                </p>
                                <h4 class="text-lg font-black text-[#0A2540]">
                                    RAM Laptop Gen 12
                                </h4>
                            </div>

                            <span class="px-2 py-1 rounded bg-[#E8F0FF] text-[#0A2540] text-[10px] font-black uppercase">
                                Preview
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                    <i data-lucide="memory-stick" class="w-5 h-5 text-gray-400"></i>
                                </div>

                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-[11px] font-bold text-gray-400 uppercase mb-3">
                                    Chọn dung lượng
                                </p>

                                <div class="flex gap-2">
                                    <div class="w-12 h-8 rounded-lg border-2 border-[#0A2540] bg-white flex items-center justify-center text-[11px] font-bold text-[#0A2540]">
                                        8GB
                                    </div>
                                    <div class="w-12 h-8 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-[11px] font-bold text-gray-400">
                                        16GB
                                    </div>
                                    <div class="w-12 h-8 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-[11px] font-bold text-gray-400">
                                        32GB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="w-full mt-6 py-2.5 bg-[#0A2540] rounded-lg text-white text-[11px] font-bold opacity-40 cursor-not-allowed">
                            XÁC NHẬN MÔ PHỎNG
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection