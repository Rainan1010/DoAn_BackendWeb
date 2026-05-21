@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center gap-2 text-sm font-medium">
    <span class="font-bold text-[#0A2540]">Edit Brand</span>
    <span class="text-gray-300">|</span>
    <a href="{{ route('admin.brands.index') }}" class="text-gray-500 hover:text-[#0A2540]">Thương hiệu</a>
    <span class="text-gray-400">›</span>
    <span class="text-blue-600 font-semibold">{{ $brand->name }}</span>
</div>
@endsection

@section('content')
<div x-data="{
    name: '{{ addslashes($brand->name) }}',
    slug: '{{ addslashes($brand->slug) }}',
    logoUrl: '{{ addslashes($brand->logo_url ?? '') }}',
    description: '{{ addslashes($brand->description ?? '') }}',
    isActive: {{ $brand->is_active ? 'true' : 'false' }},
}">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Quản lý cơ sở dữ liệu</p>
            <h1 class="text-3xl font-black text-[#0A2540]">Chỉnh sửa Thương hiệu</h1>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('admin.brands.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
                Huỷ thay đổi
            </a>
            <button form="editForm" type="submit" class="px-5 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors text-sm shadow-sm flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Lưu cập nhật
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- ===== LEFT COLUMN: FORM ===== -->
        <div class="lg:w-[55%] space-y-5">

            <!-- Card 1: Thông tin cơ bản -->
            <form id="editForm" action="{{ route('admin.brands.update', $brand->brand_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                        <h2 class="font-bold text-[#0A2540]">Thông tin cơ bản</h2>
                    </div>

                    <!-- Tên thương hiệu -->
                    <div class="mb-4">
                        <label for="name" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tên thương hiệu</label>
                        <input type="text" id="name" name="name" x-model="name"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] transition-colors text-[#0A2540] bg-white"
                            required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Đường dẫn (Slug)</label>
                        <div class="flex rounded-lg overflow-hidden border border-gray-200 focus-within:ring-2 focus-within:ring-[#0A2540]/20 focus-within:border-[#0A2540] transition-colors">
                            <span class="inline-flex items-center px-3 bg-[#F4F5F7] text-gray-500 text-sm font-medium border-r border-gray-200 whitespace-nowrap">
                                btris.com/brand/
                            </span>
                            <input type="text" id="slug" name="slug" x-model="slug"
                                class="flex-1 px-4 py-2.5 text-sm focus:outline-none text-[#0A2540] bg-white"
                                required>
                        </div>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-5">
                        <label for="description" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Mô tả chi tiết</label>
                        <textarea id="description" name="description" x-model="description" rows="5"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] transition-colors text-[#0A2540] resize-none bg-white"
                            placeholder="Nhập mô tả thương hiệu..."></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Trạng thái -->
                    <div class="flex items-center justify-between py-4 border-t border-gray-100">
                        <div>
                            <p class="font-semibold text-[#0A2540] text-sm">Trạng thái hiển thị</p>
                            <p class="text-xs text-gray-400 mt-0.5">Cho phép khách hàng nhìn thấy thương hiệu này</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer" {{ $brand->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A2540]"></div>
                        </label>
                    </div>
                </div>

                <!-- Card 2: Hình ảnh & Định danh -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-5">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                        <h2 class="font-bold text-[#0A2540]">Hình ảnh & Định danh</h2>
                    </div>
                    <div class="flex gap-5">
                        <!-- Logo Preview Box -->
                        <div class="w-28 h-28 flex-shrink-0 rounded-xl border-2 border-dashed border-gray-200 bg-[#F8F9FA] flex items-center justify-center overflow-hidden p-2">
                            <template x-if="logoUrl">
                                <img :src="logoUrl" class="max-w-full max-h-full object-contain" @@error="$event.target.style.display='none'">
                            </template>
                            <div x-show="!logoUrl" class="flex flex-col items-center justify-center text-gray-300">
                                <i data-lucide="image" class="w-8 h-8 mb-1"></i>
                                <span class="text-[10px]">Logo</span>
                            </div>
                        </div>

                        <!-- URL Input -->
                        <div class="flex-1">
                            <label for="logo_url" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">URL Logo trực tiếp</label>
                            <input type="url" id="logo_url" name="logo_url" x-model="logoUrl"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540]/20 focus:border-[#0A2540] transition-colors text-[#0A2540] bg-white"
                                placeholder="https://assets.btris.com/logos/brand-da">
                            <p class="text-[11px] text-gray-400 mt-2">* Kích thước đề xuất: 512×512px, định dạng SVG hoặc PNG trong suốt.</p>
                            @error('logo_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <!-- ===== RIGHT COLUMN: PREVIEW + HISTORY ===== -->
        <div class="lg:w-[45%] space-y-5">

            <!-- Preview Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-gray-100">
                    <span class="bg-[#E8F0FE] text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Xem trước hiển thị</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-[#F8F9FA] rounded-xl p-6 flex flex-col items-center text-center relative">
                        <!-- Active Badge -->
                        <div class="absolute top-4 right-4">
                            <span x-show="isActive" class="bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-black px-2.5 py-1 rounded-md uppercase">ACTIVE</span>
                            <span x-show="!isActive" class="bg-[#F0F2F5] text-gray-500 text-[10px] font-black px-2.5 py-1 rounded-md uppercase">HIDDEN</span>
                        </div>

                        <!-- Logo -->
                        <div class="w-20 h-20 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center overflow-hidden p-2 mb-4">
                            <template x-if="logoUrl">
                                <img :src="logoUrl" class="max-w-full max-h-full object-contain" @@error="$event.target.style.display='none'">
                            </template>
                            <div x-show="!logoUrl" class="flex items-center justify-center text-gray-300">
                                <i data-lucide="image" class="w-8 h-8"></i>
                            </div>
                        </div>

                        <!-- Brand Name -->
                        <h3 class="text-2xl font-black text-[#0A2540] mb-2" x-text="name || 'Tên thương hiệu'"></h3>

                        <!-- Description -->
                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 mb-5" x-text="description || 'Mô tả thương hiệu sẽ xuất hiện ở đây để khách hàng hiểu hơn về sản phẩm...'"></p>

                        <!-- Stats -->
                        <div class="w-full grid grid-cols-3 border-t border-gray-200 pt-4 gap-2">
                            <div class="text-center">
                                <p class="text-xl font-black text-[#0A2540]">0</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">Sản phẩm</p>
                            </div>
                            <div class="text-center border-x border-gray-200">
                                <p class="text-xl font-black text-[#0A2540] flex items-center justify-center gap-1">0.0 <span class="text-yellow-400 text-base">★</span></p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">Đánh giá</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-black text-[#0A2540]">0</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">Dự án</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update History Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                    <h3 class="font-bold text-[#0A2540] text-sm">Lịch sử cập nhật</h3>
                </div>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A2540] flex-shrink-0 mt-1.5"></span>
                        <p class="text-sm text-gray-600">Đang xem và chỉnh sửa thông tin thương hiệu <span class="font-semibold text-[#0A2540]">{{ $brand->name }}</span></p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0 mt-1.5"></span>
                        <p class="text-sm text-gray-600">Cập nhật thông tin lần cuối lúc <span class="font-semibold">{{ \Carbon\Carbon::parse($brand->created_at)->format('H:i d/m/Y') }}</span></p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 flex-shrink-0 mt-1.5"></span>
                        <p class="text-sm text-gray-400">Thương hiệu được tạo vào {{ \Carbon\Carbon::parse($brand->created_at)->format('d/m/Y') }}</p>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
