@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
    <a href="{{ route('admin.permissions.index') }}" class="hover:text-[#0A2540] transition-colors">Quản lý phân quyền</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-[#0A2540]">Thêm nhân sự mới</span>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-[#0A2540] tracking-tight">Thêm nhân sự mới</h1>
    </div>

    <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-8" x-data="{
        permissions: {},
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

        <!-- Basic Info Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            <h3 class="text-xs font-black text-[#0A2540] uppercase tracking-widest border-b border-gray-50 pb-4">Thông tin cơ bản</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Họ và tên</label>
                    <input type="text" name="full_name" required value="{{ old('full_name') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('full_name') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] outline-none transition-all">
                    @error('full_name') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] outline-none transition-all">
                    @error('email') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mật khẩu</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] outline-none transition-all">
                    @error('password') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Vai trò</label>
                    <select name="role" required
                        class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('role') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] outline-none transition-all">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="staff" {{ old('role') == 'staff' || !old('role') ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Permissions Grid -->
        <div class="space-y-4">
            <h3 class="text-xs font-black text-[#0A2540] uppercase tracking-widest px-1">Thiết lập quyền hạn</h3>
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
        </div>

        <!-- Footer Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.permissions.index') }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-500 text-xs font-black rounded-xl hover:bg-gray-50 transition-all uppercase tracking-widest">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3 bg-[#0A2540] text-white text-xs font-black rounded-xl hover:bg-[#0A2540]/90 transition-all uppercase tracking-widest shadow-lg shadow-[#0A2540]/20 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Thêm nhân sự
            </button>
        </div>
    </form>
</div>
@endsection
