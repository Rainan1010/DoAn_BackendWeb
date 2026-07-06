@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 pb-10" x-data="{
    name: '',
    slug: '',
    logoUrl: '',
    description: '',
    isActive: true,
    generateSlug() {
        this.slug = this.name.toLowerCase()
            .replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a')
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
}">

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
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 mt-6">
        <div class="lg:w-2/3">
            <form action="{{ route('admin.brands.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                @csrf

                @if(session('error') || $errors->any())
                    <div class="mb-6 flex flex-col gap-2 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-sm">
                        @if(session('error'))
                            <div class="flex items-center gap-2 font-semibold text-sm">
                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif
                        @if($errors->any())
                            @foreach($errors->all() as $error)
                                <div class="flex items-center gap-2 font-semibold text-sm">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif

                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="edit" class="w-5 h-5 text-[#0A2540]"></i>
                    <h2 class="text-lg font-bold text-[#0A2540]">Thông tin cơ bản</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tên thương hiệu</label>
                        <input type="text" name="name" x-model="name" @input="generateSlug" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:bg-white transition-all" placeholder="Ví dụ: NVIDIA..." required>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Slug</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border-r-0 bg-gray-100 text-gray-500 text-sm">brand/</span>
                            <input type="text" name="slug" x-model="slug" class="flex-1 bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-r-lg px-4 py-2.5 text-sm focus:outline-none focus:bg-white transition-all" required>
                        </div>
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">URL Logo</label>
                    <input type="url" name="logo_url" x-model="logoUrl" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:bg-white transition-all" placeholder="https://domain.com/logo.png">
                </div>

                <div class="mb-8">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Mô tả</label>
                    <textarea name="description" x-model="description" rows="4" class="w-full bg-[#F4F5F7] border border-transparent focus:border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="bg-[#F8F9FA] rounded-xl p-5 mb-8 flex items-center justify-between border border-gray-100">
                    <div>
                        <h3 class="font-bold text-[#0A2540] text-sm">Trạng thái hoạt động</h3>
                        <p class="text-xs text-gray-500">Thương hiệu sẽ hiển thị ngay sau khi kích hoạt</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[#0A2540] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="flex-1 bg-[#0A2540] hover:bg-[#113255] text-white py-3 rounded-lg font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i> LƯU THƯƠNG HIỆU
                    </button>
                    <a href="{{ route('admin.brands.index') }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-all shadow-sm">HỦY BỎ</a>
                </div>
            </form>
        </div>

        <div class="lg:w-1/3 space-y-6 sticky top-6 self-start">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="h-40 bg-[#0A2540] relative flex items-center justify-center">
                    <div class="relative z-10 w-20 h-20 bg-[#113255] rounded-2xl flex items-center justify-center border border-[#1e4d82] shadow-xl overflow-hidden p-2">
                        <template x-if="logoUrl">
                            <img :src="logoUrl" class="w-full h-full object-contain">
                        </template>
                        <div x-show="!logoUrl" class="text-[#2A5C96]">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <div class="p-6 text-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <h3 class="text-xl font-black text-[#0A2540]" x-text="name || 'Tên thương hiệu'"></h3>
                        <span x-show="isActive" class="bg-[#E2F6EA] text-[#0FAF62] text-[10px] font-bold px-2 py-0.5 rounded">ACTIVE</span>
                    </div>
                    <p class="text-blue-600 text-xs font-mono mb-4" x-text="'brand/' + (slug || 'preview')"></p>
                    <p class="text-sm text-gray-500 italic line-clamp-3" x-text="description || 'Mô tả sẽ xuất hiện tại đây...'"></p>
                </div>
            </div>

            <div class="bg-[#0A2540] rounded-xl p-6 text-white shadow-md">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                    <h3 class="font-bold text-sm uppercase tracking-wider">Hướng dẫn</h3>
                </div>
                <ul class="space-y-3 text-xs text-gray-300">
                    <li class="flex gap-2"><span>•</span> Logo nên là PNG/SVG nền trong suốt.</li>
                    <li class="flex gap-2"><span>•</span> Slug không nên chứa dấu tiếng Việt.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lucide icons sẽ được layout app.blade.php tự động init
</script>
@endpush