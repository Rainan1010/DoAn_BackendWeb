@extends('admin.layouts.app')

@section('content')

<!-- Include AlpineJS for Live Preview -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-6" x-data="{
    name: '',
    slug: '',

<div class="space-y-6 pb-10" x-data="{
    name: '',
    slug: '',
    logoUrl: '',

    logo: '',
    description: '',
    isActive: true,
    generateSlug() {

        this.slug = this.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');

        if(!this.slug) {
            this.slug = this.name.toLowerCase().replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a')
                .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e')
                .replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i')
                .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o')
                .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u')
                .replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y')
                .replace(/đ/gi, 'd')
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

    }
}">
    
    <!-- Breadcrumb & Header section -->

    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div class="flex items-center text-lg">
            <span class="text-[#0A2540] font-bold">Thêm thương hiệu mới</span> 
            <span class="mx-3 text-gray-300">|</span> 
            <span class="text-blue-600 font-bold uppercase text-sm tracking-widest">Tomi</span>
            <span class="ml-4 text-gray-500 text-sm">Danh sách</span>
        </div>
        <div class="flex items-center gap-4 text-gray-500">
            <i data-lucide="calendar" class="w-5 h-5"></i>
            <div class="relative">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
            <i data-lucide="help-circle" class="w-5 h-5"></i>
            <div class="flex items-center gap-2 ml-4">
                <div class="text-right">
                    <p class="text-xs font-bold text-[#0A2540]">TechAdmin</p>
                    <p class="text-[10px] text-gray-400 font-semibold tracking-wider">MASTER ADMIN</p>
                </div>
                <img src="https://i.pravatar.cc/150?img=11" alt="Admin" class="w-8 h-8 rounded-full border border-gray-200">
            </div>
        </div>
    </div>
    
    <!-- Main Form Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-[#0A2540] font-black text-lg mb-6 flex items-center gap-2">
                    <i data-lucide="edit" class="w-5 h-5 text-blue-600"></i> Thông tin cơ bản
                </h3>
                
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Tên thương hiệu -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Tên thương hiệu</label>
                            <input type="text" x-model="name" @input="generateSlug" placeholder="Ví dụ: NVIDIA, Intel..." class="w-full bg-[#F4F5F7] border border-transparent rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400">
                        </div>
                        
                        <!-- Đường dẫn thân thiện -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Đường dẫn thân thiện (SLUG)</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 rounded-l-lg bg-gray-50 border border-r-0 border-gray-200 text-gray-400 text-sm font-medium">brand/</span>
                                <input type="text" x-model="slug" placeholder="nvidia-corporation" class="flex-1 w-full bg-[#F4F5F7] border border-transparent rounded-r-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                    
                    <!-- URL Logo -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">URL Logo thương hiệu</label>
                        <div class="relative">
                            <i data-lucide="link" class="absolute left-4 top-3.5 text-gray-400 w-4 h-4"></i>
                            <input type="url" x-model="logo" placeholder="https://domain.com/logo.png" class="w-full bg-[#F4F5F7] border border-transparent rounded-lg py-3 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400">
                        </div>
                    </div>
                    
                    <!-- Mô tả -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Mô tả thương hiệu</label>
                        <textarea x-model="description" rows="5" placeholder="Nhập mô tả chi tiết về lịch sử và định hướng của thương hiệu..." class="w-full bg-[#F4F5F7] border border-transparent rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400 resize-none"></textarea>
                    </div>
                    
                    <!-- Trạng thái -->
                    <div class="mb-8 bg-[#F4F5F7] p-4 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                                <i data-lucide="radio" class="w-5 h-5 text-gray-700"></i>
                            </div>
                            <div>
                                <h4 class="text-[#0A2540] font-bold">Trạng thái hoạt động</h4>
                                <p class="text-xs text-blue-600 font-medium">Thương hiệu sẽ được hiển thị ngay sau khi kích hoạt</p>
                            </div>
                        </div>
                        
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isActive" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A2540]"></div>
                        </label>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-[#0A2540] hover:bg-blue-900 text-white px-6 py-3.5 rounded-lg font-bold text-sm tracking-wide flex items-center gap-2 transition-colors">
                            <i data-lucide="save" class="w-4 h-4"></i> LƯU THƯƠNG HIỆU
                        </button>
                        <a href="#" class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-6 py-3.5 rounded-lg font-bold text-sm tracking-wide transition-colors">
                            HỦY BỎ
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Metadata logs -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-[#F4F5F7] p-4 rounded-xl flex items-center gap-4 border border-gray-100">
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <i data-lucide="history" class="w-5 h-5 text-gray-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Khởi tạo</p>
                        <p class="text-sm font-bold text-[#0A2540]">Hệ thống (Tự động)</p>
                    </div>
                </div>
                <div class="bg-[#F4F5F7] p-4 rounded-xl flex items-center gap-4 border border-gray-100">
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <i data-lucide="clock" class="w-5 h-5 text-gray-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Ngày tạo</p>
                        <p class="text-sm font-bold text-[#0A2540]">15/10/2023 - 09:41</p>

    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm font-medium">
            <span class="text-gray-500">Admin</span> 
            <span class="mx-2 text-gray-400">›</span> 
            <a href="{{ route('admin.brands.index') }}" class="text-blue-600 hover:underline">Thương hiệu</a>
            <span class="mx-2 text-gray-400">›</span> 
            <span class="text-[#0A2540] font-bold">Thêm mới</span>
        </div>
        
        <div class="flex items-center justify-between mt-2">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.brands.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-[#0A2540]">Thêm thương hiệu mới</h1>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.brands.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors text-sm shadow-sm">
                    Danh sách
                </a>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 mt-6">
        <!-- Form Section (Left Column) -->
        <div class="lg:w-2/3 space-y-6">
            <form action="{{ route('admin.brands.store') }}" method="POST" id="brandForm" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                @csrf
                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="edit" class="w-5 h-5 text-[#0A2540]"></i>
                    <h2 class="text-lg font-bold text-[#0A2540]">Thông tin cơ bản</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tên thương hiệu -->
                    <div>
                        <label for="name" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tên thương hiệu</label>
                        <input type="text" id="name" name="name" x-model="name" @input="generateSlug" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-0 focus:bg-white transition-colors text-[#0A2540] placeholder-gray-400" placeholder="Ví dụ: NVIDIA, Intel..." required>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Đường dẫn thân thiện (Slug)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-transparent bg-gray-100 text-gray-500 text-sm">
                                brand/
                            </span>
                            <input type="text" id="slug" name="slug" x-model="slug" class="flex-1 bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-r-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-0 focus:bg-white transition-colors text-[#0A2540] placeholder-gray-400" placeholder="nvidia-corporation" required>
                        </div>
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- URL Logo -->
                <div class="mb-6">
                    <label for="logo_url" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">URL Logo thương hiệu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                            <i data-lucide="link" class="w-4 h-4"></i>
                        </div>
                        <input type="url" id="logo_url" name="logo_url" x-model="logoUrl" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg pl-11 pr-4 py-2.5 text-sm focus:outline-none focus:ring-0 focus:bg-white transition-colors text-[#0A2540] placeholder-gray-400" placeholder="https://domain.com/logo.png">
                    </div>
                    @error('logo_url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Mô tả -->
                <div class="mb-8">
                    <label for="description" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Mô tả thương hiệu</label>
                    <textarea id="description" name="description" x-model="description" rows="4" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-0 focus:bg-white transition-colors text-[#0A2540] placeholder-gray-400 resize-none" placeholder="Nhập mô tả chi tiết về lịch sử và định hướng của thương hiệu..."></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Trạng thái -->
                <div class="bg-[#F8F9FA] rounded-xl p-5 mb-8 flex items-center justify-between border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-[#0A2540] shadow-sm">
                            <i data-lucide="radio" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#0A2540] text-sm">Trạng thái hoạt động</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Thương hiệu sẽ được hiển thị ngay sau khi kích hoạt</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A2540]"></div>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="flex-1 bg-[#0A2540] hover:bg-[#113255] text-white py-3 rounded-lg font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i> LƯU THƯƠNG HIỆU
                    </button>
                    <a href="{{ route('admin.brands.index') }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-colors shadow-sm text-center">
                        HỦY BỎ
                    </a>
                </div>
            </form>

            <div class="grid grid-cols-2 gap-6 mt-6">
                <div class="bg-[#F4F5F7] rounded-xl p-5 flex items-center gap-4 border border-gray-200">
                    <div class="text-gray-400">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-500 uppercase">KHỞI TẠO</p>
                        <p class="text-sm font-bold text-[#0A2540]">Hệ thống (Tự động)</p>
                    </div>
                </div>
                <div class="bg-[#F4F5F7] rounded-xl p-5 flex items-center gap-4 border border-gray-200">
                    <div class="text-gray-400">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-500 uppercase">NGÀY TẠO</p>
                        <p class="text-sm font-bold text-[#0A2540]">{{ now()->format('d/m/Y - H:i') }}</p>

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Preview & Help -->
        <div class="space-y-6">
            <!-- Live Preview Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Cover / Logo Area -->
                <div class="bg-[#0A2540] h-48 relative flex items-center justify-center p-6">
                    <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CjxjaXJjbGUgY3g9IjIiIGN5PSIyIiByPSIyIiBmaWxsPSIjZmZmIi8+Cjwvc3ZnPg==')] bg-repeat"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-24 h-24 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm mb-4 overflow-hidden">
                            <template x-if="logo">
                                <img :src="logo" class="w-full h-full object-contain p-2" alt="Logo preview">
                            </template>
                            <template x-if="!logo">
                                <i data-lucide="image" class="w-8 h-8 text-white/50"></i>
                            </template>
                        </div>
                        <span class="bg-white text-[#0A2540] text-xs font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-lg">LIVE PREVIEW</span>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h2 class="text-2xl font-black text-[#0A2540] leading-tight" x-text="name || 'Tên thương hiệu'">Tên thương hiệu</h2>
                            <p class="text-blue-500 font-medium text-sm mt-1">brand/<span x-text="slug || 'slug-preview'">slug-preview</span></p>
                        </div>
                        <span x-show="isActive" class="bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md">ACTIVE</span>
                        <span x-show="!isActive" class="bg-gray-100 text-gray-500 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md">INACTIVE</span>
                    </div>
                    
                    <div class="mt-4 mb-6 relative">
                        <i data-lucide="quote" class="absolute -top-2 -left-2 w-8 h-8 text-gray-100 -z-10"></i>
                        <p class="text-gray-500 text-sm leading-relaxed" x-text="description || '&quot;Mô tả của thương hiệu sẽ xuất hiện ở đây để khách hàng có thể hiểu thêm về giá trị và chất lượng sản phẩm...&quot;'">
                            "Mô tả của thương hiệu sẽ xuất hiện ở đây để khách hàng có thể hiểu thêm về giá trị và chất lượng sản phẩm..."
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                            <p class="text-2xl font-black text-[#0A2540]">0</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Sản phẩm</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                            <p class="text-2xl font-black text-[#0A2540]">0.0</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Đánh giá</p>

        <!-- Preview Section (Right Column) -->
        <div class="lg:w-1/3 space-y-6 sticky top-6 self-start">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Preview Header/Cover -->
                <div class="h-48 bg-[#0A2540] relative flex items-center justify-center overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative z-10 w-24 h-24 bg-[#113255] rounded-2xl flex items-center justify-center border border-[#1e4d82] shadow-xl overflow-hidden p-2">
                        <template x-if="logoUrl">
                            <img :src="logoUrl" class="w-full h-full object-contain" @@error="$event.target.style.display='none'; $refs.fallbackIcon.style.display='flex'">
                        </template>
                        <div x-ref="fallbackIcon" :class="{'hidden': logoUrl}" class="w-full h-full flex flex-col items-center justify-center text-[#2A5C96]">
                            <i data-lucide="image" class="w-8 h-8 mb-1"></i>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                        <span class="bg-white text-[#0A2540] text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-md">
                            LIVE PREVIEW
                        </span>
                    </div>
                </div>

                <!-- Preview Content -->
                <div class="p-6 text-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <h3 class="text-2xl font-black text-[#0A2540] truncate" x-text="name || 'Tên thương hiệu'"></h3>
                        <span x-show="isActive" class="bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-bold px-2 py-0.5 rounded uppercase">ACTIVE</span>
                        <span x-show="!isActive" class="bg-[#F0F2F5] text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded uppercase">HIDDEN</span>
                    </div>
                    <p class="text-blue-600 text-sm mb-4 font-mono truncate" x-text="'brand/' + (slug || 'slug-preview')"></p>
                    
                    <p class="text-sm text-gray-500 italic mb-6 line-clamp-3 min-h-[60px]" x-text="description || 'Mô tả của thương hiệu sẽ xuất hiện ở đây để khách hàng có thể hiểu thêm về giá trị và chất lượng sản phẩm...'"></p>

                    <div class="grid grid-cols-2 border-t border-gray-100 pt-5">
                        <div class="border-r border-gray-100">
                            <p class="text-2xl font-black text-[#0A2540]">0</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">SẢN PHẨM</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-[#0A2540]">0.0</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">ĐÁNH GIÁ</p>

                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Instructions Card -->
            <div class="bg-[#0A2540] rounded-xl p-6 text-white shadow-sm relative overflow-hidden">
                <i data-lucide="compass" class="absolute -bottom-6 -right-6 w-32 h-32 text-white/5"></i>
                
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400"></i> HƯỚNG DẪN THIẾT LẬP
                </h3>
                
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                        <p class="text-blue-100 text-sm leading-relaxed">Sử dụng logo có nền trong suốt (PNG/SVG) để đạt hiệu quả thẩm mỹ cao nhất.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                        <p class="text-blue-100 text-sm leading-relaxed">Slug nên ngắn gọn, không dấu và sử dụng gạch nối ngang.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                        <p class="text-blue-100 text-sm leading-relaxed">Mô tả tối ưu cho SEO nên có độ dài từ 150-300 ký tự.</p>
            <!-- Hướng dẫn -->
            <div class="bg-[#0A2540] rounded-xl p-6 text-white shadow-md">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                    <h3 class="font-bold text-sm uppercase tracking-wider">Hướng dẫn thiết lập</h3>
                </div>
                
                <ul class="space-y-4 text-sm text-gray-300">
                    <li class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1e4d82] flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5">1</span>
                        <p>Sử dụng logo có nền trong suốt (PNG/SVG) để đạt hiệu quả thẩm mỹ cao nhất.</p>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1e4d82] flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5">2</span>
                        <p>Slug nên ngắn gọn, không dấu và sử dụng gạch nối ngang.</p>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1e4d82] flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5">3</span>
                        <p>Mô tả tối ưu cho SEO nên có độ dài từ 150-300 ký tự.</p>

                    </li>
                </ul>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lucide icons will be initialized by app.blade.php
</script>
@endpush

    </div>
</div>
@endsection

