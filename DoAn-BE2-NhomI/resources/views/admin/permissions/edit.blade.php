@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
    <a href="{{ route('admin.permissions.index') }}" class="hover:text-[#0A2540] transition-colors">Quản lý phân quyền</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-[#0A2540]">Chỉnh sửa Phân quyền</span>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-[#0A2540] tracking-tight">Chỉnh sửa Phân quyền</h1>
        <div class="flex items-center gap-4 text-gray-500">
            <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-6 relative">
            <div class="relative">
                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->full_name).'&background=0A2540&color=fff&size=128' }}" 
                    class="w-20 h-20 rounded-2xl object-cover shadow-md border-2 border-white" alt="">
                <span class="absolute -bottom-1 -right-1 w-5 h-5 {{ $user->is_active ? 'bg-[#0FAF62]' : 'bg-gray-400' }} border-4 border-white rounded-full"></span>
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-black text-[#0A2540]">{{ $user->full_name }}</h2>
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase tracking-widest">{{ $user->role }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1.5">
                        ID: <span class="text-gray-600">#{{ $user->id_code ?? $user->user_id }}</span>
                    </p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1.5">
                        Email: <span class="text-gray-600 lowercase">{{ $user->email }}</span>
                    </p>
                </div>
                <!-- Role Select for Edit -->
                <div class="mt-4 flex items-center gap-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Vai trò:</label>
                    <select name="role" class="bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-black px-2 py-1 outline-none">
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>USER</option>
                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>STAFF</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>ADMIN</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <form action="{{ route('admin.permissions.toggle-status', $user->user_id) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border {{ $user->is_active ? 'bg-red-50 text-red-600 border-red-100 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' }}">
                    {{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}
                </button>
            </form>
            <a href="{{ route('admin.permissions.show', $user->user_id) }}" class="px-5 py-2.5 bg-white border border-gray-200 text-[#0A2540] text-[10px] font-black rounded-xl hover:bg-gray-50 transition-all uppercase tracking-widest shadow-sm">
                Xem chi tiết
            </a>
        </div>
    </div>

    <!-- Permissions Grid -->
    <form action="{{ route('admin.permissions.update', $user->user_id) }}" method="POST" class="space-y-8" x-data="{
        permissions: {{ json_encode($user->permissions ?? (object)[]) }},
        toggleAll(module, checked) {
            if (checked) {
                this.permissions[module] = ['read', 'create', 'update', 'delete'];
            } else {
                this.permissions[module] = [];
            }
        },
        initModule(module) {
            if (!this.permissions[module]) {
                this.permissions[module] = [];
            }
        }
    }">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 p-4 rounded-xl">
                <ul class="list-disc list-inside text-red-600 text-xs font-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @php
                $modules = [
                    ['id' => 'products', 'name' => 'Sản phẩm', 'icon' => 'box'],
                    ['id' => 'brands', 'name' => 'Thương hiệu', 'icon' => 'tag'],
                    ['id' => 'categories', 'name' => 'Danh mục', 'icon' => 'boxes'],
                    ['id' => 'vouchers', 'name' => 'Vouchers', 'icon' => 'ticket'],
                    ['id' => 'orders', 'name' => 'Đơn hàng', 'icon' => 'shopping-cart'],
                    ['id' => 'customers', 'name' => 'Khách hàng', 'icon' => 'users'],
                    ['id' => 'revenue', 'name' => 'Doanh thu', 'icon' => 'bar-chart-3'],
                    ['id' => 'inventory', 'name' => 'Kho hàng', 'icon' => 'archive'],
                ];
                $actions = [
                    ['id' => 'read', 'name' => 'Xem (Read)'],
                    ['id' => 'create', 'name' => 'Tạo mới (Create)'],
                    ['id' => 'update', 'name' => 'Cập nhật (Update)'],
                    ['id' => 'delete', 'name' => 'Xóa (Delete)'],
                ];
            @endphp

            @foreach($modules as $module)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-md transition-all"
                x-init="initModule('{{ $module['id'] }}')">
                <!-- Card Header -->
                <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm border border-gray-100 flex items-center justify-center text-[#0A2540]">
                            <i data-lucide="{{ $module['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <span class="text-xs font-black text-[#0A2540] uppercase tracking-widest">{{ $module['name'] }}</span>
                    </div>
                    <input type="checkbox" 
                        @change="toggleAll('{{ $module['id'] }}', $event.target.checked)"
                        :checked="permissions['{{ $module['id'] }}'] && permissions['{{ $module['id'] }}'].length === 4"
                        class="w-4 h-4 rounded border-gray-300 text-[#0A2540] focus:ring-0">
                </div>
                <!-- Card Body -->
                <div class="p-4 space-y-3">
                    @foreach($actions as $action)
                    <label class="flex items-center justify-between cursor-pointer group/item">
                        <span class="text-xs font-medium text-gray-500 group-hover/item:text-[#0A2540] transition-colors">{{ $action['name'] }}</span>
                        <input type="checkbox" name="permissions[{{ $module['id'] }}][]" value="{{ $action['id'] }}" 
                            x-model="permissions['{{ $module['id'] }}']"
                            class="w-4 h-4 rounded border-gray-300 text-[#0A2540] focus:ring-0">
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-3 text-gray-400">
                <i data-lucide="clock" class="w-4 h-4"></i>
                <p class="text-[10px] font-bold uppercase tracking-widest">Lần cuối cập nhật lúc {{ $user->updated_at ? $user->updated_at->format('H:i, d/m/Y') : 'Chưa rõ' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.permissions.index') }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-500 text-xs font-black rounded-xl hover:bg-gray-50 transition-all uppercase tracking-widest">
                    Hủy
                </a>
                <button type="submit" class="px-8 py-3 bg-[#0A2540] text-white text-xs font-black rounded-xl hover:bg-[#0A2540]/90 transition-all uppercase tracking-widest shadow-lg shadow-[#0A2540]/20 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
