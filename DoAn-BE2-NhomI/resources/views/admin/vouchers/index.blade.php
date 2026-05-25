@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
    <input type="text" placeholder="Tìm mã giảm giá, mã code hoặc chiến dịch..." class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#0A2540]/10 text-sm">
</div>
@endsection

@section('content')
<div class="space-y-8 pb-10">
    <div>
        <h1 class="text-3xl font-black text-[#0A2540] tracking-tight">Quản lý mã giảm giá</h1>
        <p class="text-sm font-medium text-gray-500 mt-1">Tạo, chỉnh sửa và theo dõi hiệu quả các chương trình khuyến mãi.</p>
    </div>

    <!-- Thẻ thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Vouchers -->
        <div class="bg-[#0A2540] rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl shadow-[#0A2540]/20">
            <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                <i data-lucide="ticket" class="w-40 h-40"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-300 mb-2">Tổng mã giảm giá</p>
                <h3 class="text-5xl font-black mb-4">{{ number_format($stats['total'] ?? 0) }}</h3>
                @php
                    $growth = $stats['month_growth'] ?? 0;
                    $growthPositive = $growth >= 0;
                @endphp
                <div class="flex items-center gap-2 text-xs font-bold {{ $growthPositive ? 'text-green-400' : 'text-red-400' }}">
                    <i data-lucide="{{ $growthPositive ? 'trending-up' : 'trending-down' }}" class="w-4 h-4"></i>
                    <span>{{ $growthPositive ? '+' : '' }}{{ number_format($growth, 1) }}% đơn dùng mã (tháng này)</span>
                </div>
            </div>
        </div>

        <!-- Active Now -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Đang hoạt động</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ number_format($stats['active'] ?? 0) }}</h3>
            
            <div class="space-y-2">
                @php
                    $percentActive = $stats['total'] > 0 ? ($stats['active'] / $stats['total']) * 100 : 0;
                @endphp
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0A2540] rounded-full" style="width: {{ $percentActive }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ round($percentActive) }}% mã đang hoạt động</p>
            </div>
        </div>

        <!-- Used Rate -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative">
            <div class="absolute right-8 top-8 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Tỷ lệ đã dùng</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ $stats['used_rate'] ?? 0 }}%</h3>
            
            <div class="flex items-center gap-3">
                <div class="flex -space-x-2">
                    @forelse($recentRedeemers as $user)
                        @if($user->avatar_url)
                            <img class="w-7 h-7 rounded-full border-2 border-white object-cover" src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}">
                        @else
                            <div class="w-7 h-7 rounded-full border-2 border-white bg-[#0A2540] text-white flex items-center justify-center text-[9px] font-black">
                                {{ strtoupper(substr($user->full_name ?? 'K', 0, 1)) }}
                            </div>
                        @endif
                    @empty
                        <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-gray-400">
                            <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        </div>
                    @endforelse
                    @if(($stats['orders_with_voucher'] ?? 0) > ($recentRedeemers->count()))
                        <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[8px] font-bold text-gray-500">
                            +{{ number_format($stats['orders_with_voucher'] - $recentRedeemers->count()) }}
                        </div>
                    @endif
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    {{ number_format($stats['total_redeemed'] ?? 0) }} lượt · {{ number_format($stats['orders_with_voucher'] ?? 0) }} đơn
                </p>
            </div>
        </div>
    </div>

    <!-- Main Table Section -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#0A2540] mb-1">Danh sách mã giảm giá</h2>
                <p class="text-sm text-gray-500">Quản lý mã khuyến mãi và mức giảm giá trên hệ thống.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4"></i> Lọc
                </button>
                <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i> Xuất CSV
                </button>
                <a href="{{ route('admin.vouchers.create') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold hover:bg-[#113255] transition-colors shadow-lg shadow-[#0A2540]/20">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tạo mã giảm giá
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Mã HT</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Mã</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Loại</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Giá trị</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Đơn tối thiểu</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Giới hạn / Đã dùng</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Hiệu lực</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Trạng thái</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-5 px-8">
                            <span class="text-[11px] font-bold text-gray-400">VCH-{{ str_pad($voucher->voucher_id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-black text-[#0A2540] uppercase tracking-wide">{{ $voucher->code }}</span>
                        </td>
                        <td class="py-5 px-8">
                            @if($voucher->type == 'percent')
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Phần trăm</span>
                            @else
                            <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Cố định</span>
                            @endif
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-black text-[#0A2540]">{{ $voucher->type == 'percent' ? $voucher->value . '%' : number_format($voucher->value) . ' đ' }}</span>
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-medium text-gray-600">{{ number_format($voucher->min_order_value) }} đ</span>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex flex-col items-center gap-1">
                                @php
                                    $usagePercent = ($voucher->usage_limit > 0) ? ($voucher->used_count / $voucher->usage_limit) * 100 : 0;
                                @endphp
                                <div class="h-1.5 w-16 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#0A2540] rounded-full" style="width: {{ $usagePercent }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">{{ $voucher->used_count }} / {{ $voucher->usage_limit ?? '∞' }}</span>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <div class="text-[10px] leading-relaxed">
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">BĐ:</span> {{ $voucher->start_at ? \Carbon\Carbon::parse($voucher->start_at)->format('d/m/Y') : 'Chưa có' }}</p>
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">KT:</span> {{ $voucher->end_at ? \Carbon\Carbon::parse($voucher->end_at)->format('d/m/Y') : 'Chưa có' }}</p>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            @php
                                $isActive = $voucher->is_active && ($voucher->end_at ? \Carbon\Carbon::parse($voucher->end_at)->isFuture() : true);
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $isActive ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $isActive ? 'Hoạt động' : 'Ngừng / Hết hạn' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.vouchers.show', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Chỉnh sửa">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->voucher_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="Xóa">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                    <i data-lucide="ticket" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-[#0A2540]">Chưa có mã giảm giá nào</p>
                                    <p class="text-sm text-gray-400">Bắt đầu bằng cách tạo mã khuyến mãi đầu tiên.</p>
                                </div>
                                <a href="{{ route('admin.vouchers.create') }}" class="mt-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold">Tạo mã giảm giá</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-8 border-t border-gray-50 flex justify-between items-center">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                Hiển thị 1 đến {{ $vouchers->count() }} trong tổng {{ $stats['total'] }} kết quả
            </p>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50" disabled>
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0A2540] text-white text-xs font-bold shadow-lg shadow-[#0A2540]/20">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
