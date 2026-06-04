@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
    <input type="text" placeholder="Tìm kiếm mã voucher, chiến dịch..." class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#0A2540]/10 text-sm">
</div>
@endsection

@section('content')
<div class="space-y-8 pb-10">

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Thông báo lỗi / xung đột --}}
    @if(session('error'))
        <div class="p-4 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Tổng Voucher --}}
        <div class="bg-[#0A2540] rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl shadow-[#0A2540]/20">
            <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                <i data-lucide="ticket" class="w-40 h-40"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-300 mb-2">Tổng Voucher</p>
                <h3 class="text-5xl font-black mb-4">{{ number_format($stats['total'] ?? 0) }}</h3>
                <div class="flex items-center gap-2 text-xs font-bold text-green-400">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    <span>Toàn bộ mã khuyến mãi</span>
                </div>
            </div>
        </div>

        {{-- Đang hoạt động --}}
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Đang Hoạt Động</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ number_format($stats['active'] ?? 0) }}</h3>

            <div class="space-y-2">
                @php
                    $percentActive = ($stats['total'] ?? 0) > 0 ? ($stats['active'] / $stats['total']) * 100 : 0;
                @endphp
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0A2540] rounded-full" style="width: {{ $percentActive }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ round($percentActive) }}% trên tổng số đang kích hoạt</p>
            </div>
        </div>

        {{-- Tỷ lệ sử dụng --}}
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative">
            <div class="absolute right-8 top-8 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Tỷ Lệ Sử Dụng</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ $stats['used_rate'] ?? 0 }}%</h3>

            <div class="flex items-center gap-3">
                <div class="flex -space-x-2">
                    <img class="w-7 h-7 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1" alt="">
                    <img class="w-7 h-7 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2" alt="">
                    <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[8px] font-bold text-gray-500">+12k</div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Đã được khách hàng sử dụng</p>
            </div>
        </div>
    </div>

    {{-- Bảng dữ liệu --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#0A2540] mb-1">Danh Sách Voucher</h2>
                <p class="text-sm text-gray-500">Quản lý các mã khuyến mãi và chương trình giảm giá.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4"></i> Lọc
                </button>
                <a href="{{ route('admin.vouchers.create') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold hover:bg-[#113255] transition-colors shadow-lg shadow-[#0A2540]/20">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tạo Voucher
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Mã ID</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Mã Code</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Loại</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Giá trị</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Đơn tối thiểu</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Giới hạn / Đã dùng</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Thời hạn</th>
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
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">Bắt đầu:</span> {{ $voucher->start_at ? $voucher->start_at->format('d/m/Y') : 'N/A' }}</p>
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">Kết thúc:</span> {{ $voucher->end_at ? $voucher->end_at->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            @php
                                $isActive = $voucher->is_active && ($voucher->end_at ? $voucher->end_at->isFuture() : true);
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $isActive ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $isActive ? 'Hoạt động' : 'Hết hạn' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.vouchers.show', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-blue-50 text-gray-400 hover:text-blue-600 transition-colors" title="Chỉnh sửa">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->voucher_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa voucher \"{{ $voucher->code }}\" không? Hành động này không thể hoàn tác.')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="Xóa voucher">
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
                                    <p class="text-lg font-bold text-[#0A2540]">Chưa có voucher nào</p>
                                    <p class="text-sm text-gray-400">Bắt đầu bằng cách tạo mã khuyến mãi đầu tiên.</p>
                                </div>
                                <a href="{{ route('admin.vouchers.create') }}" class="mt-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold">Tạo Voucher</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-8 border-t border-gray-50 flex justify-between items-center">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                Hiển thị {{ $vouchers->count() }} / {{ $stats['total'] }} voucher
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
