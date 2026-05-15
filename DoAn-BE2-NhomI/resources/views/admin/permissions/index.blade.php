@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    <input type="text" placeholder="Tìm kiếm nhân viên hoặc vai trò..." 
        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] transition-all">
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#0A2540] tracking-tight">Quản lý Phân quyền</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Thiết lập và quản lý quyền truy cập cho nhân sự hệ thống.</p>
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="bg-[#0A2540] text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest flex items-center gap-2 hover:bg-[#0A2540]/90 transition-all shadow-lg shadow-[#0A2540]/20">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Thêm nhân sự mới
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between relative">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">TỔNG NHÂN VIÊN</span>
                <i data-lucide="users" class="w-4 h-4 text-blue-500"></i>
            </div>
            <div class="relative">
                <p class="text-3xl font-black text-[#0A2540]">{{ number_format($stats['total']) }}</p>
                <p class="text-[10px] font-bold text-gray-400 mt-1">Tổng số tài khoản</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between relative">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">VAI TRÒ QUẢN TRỊ</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-purple-500"></i>
            </div>
            <div class="relative">
                <p class="text-3xl font-black text-[#0A2540]">{{ number_format($stats['admins']) }}</p>
                <p class="text-[10px] font-bold text-gray-400 mt-1">Toàn quyền hệ thống</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-orange-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between relative">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">VAI TRÒ NHÂN VIÊN</span>
                <i data-lucide="user-cog" class="w-4 h-4 text-orange-500"></i>
            </div>
            <div class="relative">
                <p class="text-3xl font-black text-[#0A2540]">{{ number_format($stats['staff']) }}</p>
                <p class="text-[10px] font-bold text-gray-400 mt-1">Quyền hạn theo module</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between relative">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">TÀI KHOẢN KHÓA</span>
                <i data-lucide="lock" class="w-4 h-4 text-red-500"></i>
            </div>
            <div class="relative">
                <p class="text-3xl font-black text-[#0A2540]">{{ number_format($stats['inactive']) }}</p>
                <p class="text-[10px] font-bold text-red-500 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Inactive
                </p>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/30">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">PHÂN QUYỀN:</span>
                    <select class="text-xs font-bold text-[#0A2540] bg-white border-gray-200 rounded-lg focus:ring-0 focus:border-[#0A2540] py-1.5 pl-3 pr-8">
                        <option>Tất cả</option>
                        <option>Toàn quyền</option>
                        <option>Sản phẩm</option>
                        <option>Đơn hàng</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">VAI TRÒ:</span>
                    <select class="text-xs font-bold text-[#0A2540] bg-white border-gray-200 rounded-lg focus:ring-0 focus:border-[#0A2540] py-1.5 pl-3 pr-8">
                        <option>Tất cả</option>
                        <option>Admin</option>
                        <option>Staff</option>
                        <option>User</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="p-2 text-gray-400 hover:text-[#0A2540] hover:bg-white rounded-lg border border-gray-200 transition-all shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                <button class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all shadow-sm uppercase tracking-widest">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Xuất báo cáo
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-4">ID USER</th>
                        <th class="px-8 py-4">THÔNG TIN CƠ BẢN</th>
                        <th class="px-8 py-4">VAI TRÒ</th>
                        <th class="px-8 py-4">PHÂN QUYỀN</th>
                        <th class="px-8 py-4">TRẠNG THÁI</th>
                        <th class="px-8 py-4 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <span class="text-xs font-black text-gray-400">#{{ $user->id_code ?? 'USR-'.$user->user_id }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->full_name).'&background=0A2540&color=fff' }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="">
                                <div>
                                    <p class="text-sm font-black text-[#0A2540]">{{ $user->full_name }}</p>
                                    <p class="text-[11px] font-medium text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-black {{ $user->role === 'admin' ? 'bg-[#0A2540] text-white' : ($user->role === 'staff' ? 'bg-blue-600 text-white' : 'bg-gray-400 text-white') }} uppercase tracking-widest">{{ $user->role }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($user->permissions)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->permissions as $module => $actions)
                                        @if(count($actions) > 0)
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[9px] font-bold rounded uppercase">{{ $module }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-400 text-[10px] font-bold rounded uppercase tracking-wide">Chưa thiết lập</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold {{ $user->is_active ? 'text-[#0FAF62]' : 'text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-[#0FAF62]' : 'bg-gray-400' }}"></span> {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.permissions.show', $user->user_id) }}" class="p-2 text-gray-400 hover:text-[#0A2540] hover:bg-gray-100 rounded-lg transition-all" title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.permissions.toggle-status', $user->user_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 {{ $user->is_active ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-red-500 hover:text-green-600 hover:bg-green-50' }} rounded-lg transition-all" title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                        <i data-lucide="{{ $user->is_active ? 'lock' : 'unlock' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.permissions.edit', $user->user_id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Chỉnh sửa">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.permissions.destroy', $user->user_id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân sự này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 border-t border-gray-50 flex items-center justify-between bg-gray-50/10">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hiển thị 1 - 4 trong số 1,248 nhân sự</p>
            <div class="flex items-center gap-1">
                <button class="p-2 text-gray-400 hover:text-[#0A2540] transition-colors"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0A2540] text-white text-[11px] font-black">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 text-[11px] font-bold transition-all">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 text-[11px] font-bold transition-all">3</button>
                <span class="px-2 text-gray-400">...</span>
                <button class="p-2 text-gray-400 hover:text-[#0A2540] transition-colors"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection
