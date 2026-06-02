@extends('admin.layouts.app')

@section('header_search')
<div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
    <a href="{{ route('admin.permissions.index') }}" class="hover:text-[#0A2540] transition-colors">Quản lý phân quyền</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-[#0A2540]">Chi tiết nhân sự</span>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#0A2540] tracking-tight">Chi tiết nhân sự</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Thông tin chi tiết và quyền hạn của {{ $user->full_name }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.permissions.toggle-status', $user->user_id) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest flex items-center gap-2 transition-all border {{ $user->is_active ? 'bg-red-50 text-red-600 border-red-100 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' }}">
                    <i data-lucide="{{ $user->is_active ? 'lock' : 'unlock' }}" class="w-4 h-4"></i>
                    {{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}
                </button>
            </form>
            <a href="{{ route('admin.permissions.edit', $user->user_id) }}" class="bg-[#0A2540] text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest flex items-center gap-2 hover:bg-[#0A2540]/90 transition-all shadow-lg shadow-[#0A2540]/20">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Profile Card -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-b from-gray-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative mb-6">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->full_name).'&background=0A2540&color=fff&size=256' }}" 
                        class="w-32 h-32 rounded-3xl object-cover shadow-xl border-4 border-white transition-transform group-hover:scale-105" alt="">
                    <span class="absolute -bottom-2 -right-2 w-8 h-8 {{ $user->is_active ? 'bg-[#0FAF62]' : 'bg-gray-400' }} border-4 border-white rounded-full flex items-center justify-center shadow-sm">
                        <i data-lucide="{{ $user->is_active ? 'check' : 'x' }}" class="w-3 h-3 text-white"></i>
                    </span>
                </div>
                <h2 class="text-xl font-black text-[#0A2540] relative">{{ $user->full_name }}</h2>
                <span class="mt-2 px-3 py-1 bg-[#0A2540]/5 text-[#0A2540] text-[10px] font-black rounded-full uppercase tracking-widest relative">{{ $user->role }}</span>
                
                <div class="w-full mt-8 pt-8 border-t border-gray-50 grid grid-cols-2 gap-4 relative">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mã NV</p>
                        <p class="text-xs font-bold text-[#0A2540]">#{{ $user->id_code ?? $user->user_id }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Trạng thái</p>
                        <p class="text-xs font-bold {{ $user->is_active ? 'text-[#0FAF62]' : 'text-gray-400' }}">
                            {{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Thông tin liên hệ</h3>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Email</p>
                        <p class="text-xs font-bold text-[#0A2540] lowercase">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ngày gia nhập</p>
                        <p class="text-xs font-bold text-[#0A2540]">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Chưa rõ' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Permissions Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-xs font-black text-[#0A2540] uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Danh sách quyền hạn được cấp
                    </h3>
                </div>
                <div class="p-8">
                    @if($user->permissions && count((array)$user->permissions) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $modules = [
                                'products' => ['name' => 'Sản phẩm', 'icon' => 'box'],
                                'brands' => ['name' => 'Thương hiệu', 'icon' => 'tag'],
                                'categories' => ['name' => 'Danh mục', 'icon' => 'boxes'],
                                'vouchers' => ['name' => 'Vouchers', 'icon' => 'ticket'],
                                'orders' => ['name' => 'Đơn hàng', 'icon' => 'shopping-cart'],
                                'customers' => ['name' => 'Khách hàng', 'icon' => 'users'],
                                'revenue' => ['name' => 'Doanh thu', 'icon' => 'bar-chart-3'],
                                'inventory' => ['name' => 'Kho hàng', 'icon' => 'archive'],
                            ];
                            $actionLabels = [
                                'read' => 'Xem',
                                'create' => 'Tạo mới',
                                'update' => 'Cập nhật',
                                'delete' => 'Xóa',
                            ];
                        @endphp

                        @foreach((array)$user->permissions as $moduleKey => $actions)
                            @if(isset($modules[$moduleKey]) && count($actions) > 0)
                            <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-white shadow-sm border border-gray-100 flex items-center justify-center text-[#0A2540]">
                                    <i data-lucide="{{ $modules[$moduleKey]['icon'] }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-black text-[#0A2540] uppercase tracking-widest mb-2">{{ $modules[$moduleKey]['name'] }}</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($actions as $action)
                                            <span class="px-2 py-0.5 bg-[#0A2540]/5 text-[#0A2540] text-[9px] font-bold rounded uppercase tracking-widest">
                                                {{ $actionLabels[$action] ?? $action }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <i data-lucide="shield-alert" class="w-8 h-8"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Chưa có quyền hạn nào được thiết lập</p>
                        <a href="{{ route('admin.permissions.edit', $user->user_id) }}" class="mt-4 inline-flex items-center gap-2 text-xs font-black text-[#0A2540] hover:underline uppercase tracking-widest">
                            Thiết lập ngay <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Activity / History (Placeholder) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black text-[#0A2540] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    Lịch sử hoạt động gần đây
                </h3>
                <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-gray-100">
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 bg-white border-2 border-blue-500 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                        <p class="text-xs font-bold text-[#0A2540]">Đã cập nhật phân quyền hệ thống</p>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-medium">10 phút trước • Bởi Administrator</p>
                    </div>
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 bg-white border-2 border-gray-200 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-gray-200 rounded-full"></div>
                        </div>
                        <p class="text-xs font-bold text-gray-500">Đăng nhập vào hệ thống</p>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-medium">2 giờ trước • IP: 127.0.0.1</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
