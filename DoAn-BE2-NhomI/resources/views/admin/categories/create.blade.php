@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header section -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm font-medium">
            <span class="text-gray-500">Admin</span> <span class="mx-2 text-gray-400">›</span> 
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-[#0A2540]">Danh mục</a> <span class="mx-2 text-gray-400">›</span> 
            <span class="text-[#0A2540] font-bold">Thêm Danh mục Mới</span>
        </div>
        
        <div class="mt-2">
            <h1 class="text-3xl font-bold text-[#0A2540]">Thêm Danh mục Mới</h1>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="flex flex-col lg:flex-row gap-8 mt-6">
            <!-- Left Column: Form -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8 space-y-6">
                        
                        <!-- Tên danh mục -->
                        <div class="space-y-2">
                            <label class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">
                                Tên danh mục <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name"
                                value="{{ old('name') }}"
                                id="nameInput"
                                oninput="generateSlug(this.value)"
                                placeholder="VD: Laptop & Linh kiện" 
                                class="w-full bg-[#F4F5F7] border @error('name') border-red-500 @else border-transparent @enderror rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400"
                            />
                            @error('name')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grid 2 cols for Slug and Danh mục cha -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">
                                    Đường dẫn (Slug) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <i data-lucide="link" class="absolute left-4 text-gray-400 w-4 h-4"></i>
                                    <input 
                                        type="text" 
                                        name="slug"
                                        value="{{ old('slug') }}"
                                        id="slugInput"
                                        placeholder="laptop-linh-kien" 
                                        class="w-full bg-[#F4F5F7] border @error('slug') border-red-500 @else border-transparent @enderror rounded-lg py-3 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-mono"
                                    />
                                </div>
                                @error('slug')
                                <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">
                                    Danh mục cha
                                </label>
                                <select name="parent_id" class="w-full bg-[#F4F5F7] border @error('parent_id') border-red-500 @else border-transparent @enderror rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium appearance-none">
                                    <option value="">Không có (Danh mục gốc)</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->category_id }}" {{ old('parent_id') == $parent->category_id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Grid 2 cols for Thứ tự and Icon -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">
                                    Thứ tự hiển thị
                                </label>
                                <input 
                                    type="number" 
                                    name="sort_order"
                                    value="{{ old('sort_order', 0) }}"
                                    min="0"
                                    class="w-full bg-[#F4F5F7] border @error('sort_order') border-red-500 @else border-transparent @enderror rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium"
                                />
                                @error('sort_order')
                                <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">
                                    Tên Icon (Lucide)
                                </label>
                                <div class="relative flex items-center">
                                    <input 
                                        type="text" 
                                        name="icon_url"
                                        id="iconInput"
                                        value="{{ old('icon_url') }}"
                                        oninput="updatePreviewIcon(this.value)"
                                        placeholder="VD: Laptop, Smartphone" 
                                        class="w-full bg-[#F4F5F7] border border-transparent rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium"
                                    />
                                    <div class="absolute right-3 w-6 h-6 bg-white border border-gray-200 rounded flex items-center justify-center text-gray-500">
                                        <i id="iconPreviewSmall" data-lucide="image" class="w-3.5 h-3.5"></i>
                                    </div>
                                </div>
                                @error('icon_url')
                                <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Toggles -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-[13px] font-bold text-gray-700 uppercase tracking-wide">Trạng thái Kích hoạt</h4>
                                <p class="text-sm text-gray-500 mt-1">Cho phép hiển thị trên website ngay lập tức.</p>
                            </div>
                            <label for="isActiveToggle" class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" id="isActiveToggle" name="is_active" value="1" class="sr-only" {{ old('is_active', true) ? 'checked' : '' }} onchange="toggleActiveState(this.checked)">
                                    <div id="toggleBg" class="block w-12 h-6 rounded-full transition-colors {{ old('is_active', true) ? 'bg-[#0FAF62]' : 'bg-gray-300' }}"></div>
                                    <div id="toggleDot" class="dot absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-all shadow-sm {{ old('is_active', true) ? 'transform translate-x-[24px]' : '' }}"></div>
                                </div>
                            </label>
                        </div>

                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 p-6 flex justify-end gap-3 border-t border-gray-200">
                        <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 font-bold transition-colors shadow-sm text-sm">
                            HỦY BỎ
                        </a>
                        <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-[#0A2540] hover:bg-[#113255] text-white rounded-lg font-bold transition-colors shadow-sm text-sm disabled:opacity-50">
                            LƯU DANH MỤC
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Widgets -->
            <div class="w-full lg:w-1/3 space-y-6">
                <!-- Live Preview Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-[#0A2540] text-sm uppercase tracking-wide flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                            Live Preview
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-[#EBF1FF] flex items-center justify-center text-[#5D87FF]">
                                <i id="iconPreviewBig" data-lucide="image" class="w-7 h-7"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between w-full">
                                    <h4 id="namePreview" class="font-black text-[#0A2540] text-lg">Tên danh mục</h4>
                                </div>
                                <span id="statusPreview" class="inline-block mt-1 px-2 py-0.5 bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-bold rounded">
                                    HIỂN THỊ
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-[#F4F6F8] rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-500 font-mono flex items-center gap-2">
                                <i data-lucide="link" class="w-3 h-3"></i>
                                b-tris.com/category/<span id="slugPreview">slug</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mẹo quản trị Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-[#0A2540] text-sm uppercase tracking-wide flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-4 h-4 text-gray-400"></i>
                            Mẹo quản trị
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                            <p class="text-sm text-gray-600 leading-relaxed">Giới hạn tên danh mục dưới <strong>40 ký tự</strong> để hiển thị tối ưu trên giao diện Mobile và chuẩn hóa SEO.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                            <p class="text-sm text-gray-600 leading-relaxed">Sử dụng icon đồng nhất từ bộ thư viện <strong>Lucide Icons</strong> để đảm bảo tính mỹ thuật chung của hệ thống.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function generateSlug(text) {
        document.getElementById('namePreview').innerText = text || "Tên danh mục";
        
        let slug = text.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/[^a-z0-9]/g, "-")
            .replace(/-+/g, "-")
            .replace(/^-|-$/g, "");
            
        document.getElementById('slugInput').value = slug;
        document.getElementById('slugPreview').innerText = slug || "slug";
        
        // Disable/enable submit
        document.getElementById('submitBtn').disabled = text.trim() === '';
    }

    function toggleActiveState(isChecked) {
        const bg = document.getElementById('toggleBg');
        const dot = document.getElementById('toggleDot');
        const statusPreview = document.getElementById('statusPreview');
        
        if (isChecked) {
            bg.classList.remove('bg-gray-300');
            bg.classList.add('bg-[#0FAF62]');
            dot.classList.add('transform', 'translate-x-[24px]');
            statusPreview.className = "inline-block mt-1 px-2 py-0.5 bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-bold rounded";
            statusPreview.innerText = "HIỂN THỊ";
        } else {
            bg.classList.remove('bg-[#0FAF62]');
            bg.classList.add('bg-gray-300');
            dot.classList.remove('transform', 'translate-x-[24px]');
            statusPreview.className = "inline-block mt-1 px-2 py-0.5 bg-[#F0F2F5] text-[#6B7280] text-[10px] font-bold rounded";
            statusPreview.innerText = "ĐANG ẨN";
        }
    }

    function updatePreviewIcon(iconName) {
        const val = iconName.toLowerCase().trim() || 'image';
        
        const smallContainer = document.getElementById('iconPreviewSmall').parentElement;
        const bigContainer = document.getElementById('iconPreviewBig').parentElement;
        
        smallContainer.innerHTML = `<i data-lucide="${val}" class="w-3.5 h-3.5"></i>`;
        bigContainer.innerHTML = `<i data-lucide="${val}" class="w-7 h-7"></i>`;
        
        lucide.createIcons();
    }
    
    // Init state
    document.addEventListener('DOMContentLoaded', () => {
        const initialName = document.getElementById('nameInput').value;
        if(initialName) generateSlug(initialName);
        
        const iconVal = document.getElementById('iconInput').value;
        if(iconVal) updatePreviewIcon(iconVal);
        
        const isChecked = document.getElementById('isActiveToggle').checked;
        toggleActiveState(isChecked);
    });
</script>
@endpush
