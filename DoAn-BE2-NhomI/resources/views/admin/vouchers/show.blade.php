@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
    <input type="text" placeholder="Tìm kiếm voucher..." class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#0A2540]/10 text-sm">
</div>
@endsection

@section('content')
<div class="pb-10 space-y-8">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <nav class="flex text-xs font-medium text-gray-400 mb-2 gap-2">
                <span>Vouchers</span>
                <span>&rsaquo;</span>
                <span class="text-gray-600">Xem Chi Tiết Voucher</span>
            </nav>
            <h1 class="text-4xl font-black text-[#0A2540] tracking-tight uppercase">Voucher: {{ $voucher->code }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.vouchers.toggleStatus', $voucher->voucher_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-8 py-3 {{ $voucher->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-50 text-green-600 hover:bg-green-100' }} border border-gray-200 rounded-xl text-sm font-bold transition-colors">
                    {{ $voucher->is_active ? 'Tạm dừng' : 'Kích hoạt' }}
                </button>
            </form>
            <a href="{{ route('admin.vouchers.edit', $voucher->voucher_id) }}" class="px-8 py-3 bg-[#0A2540] text-white rounded-xl text-sm font-bold hover:bg-[#113255] transition-colors shadow-lg shadow-[#0A2540]/20 flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4"></i> Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Configuration Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#0A2540]"></div>
                    <h2 class="text-xs font-black text-[#0A2540] uppercase tracking-widest">Thông tin cấu hình</h2>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mã Voucher</p>
                            <div class="bg-gray-50 rounded-xl p-3 text-sm font-bold text-[#0A2540] uppercase tracking-wider">
                                {{ $voucher->code }}
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Loại Voucher</p>
                            <div class="bg-gray-50 rounded-xl p-3 text-sm font-bold text-[#0A2540]">
                                {{ $voucher->type == 'percent' ? 'Giảm phần trăm' : 'Giảm trực tiếp' }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Giá trị ưu đãi</p>
                        <div class="bg-gray-50 rounded-2xl p-5">
                            <h3 class="text-3xl font-black text-[#0A2540] mb-1">
                                {{ $voucher->type == 'percent' ? $voucher->value . '%' : number_format($voucher->value) . ' đ' }}
                            </h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Áp dụng cho đơn hàng từ {{ number_format($voucher->min_order_value) }} đ</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Giới hạn sử dụng</p>
                            <div class="bg-gray-50 rounded-xl p-3 text-sm font-bold text-[#0A2540]">
                                {{ number_format($voucher->usage_limit ?? 0) }} Lượt
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mỗi khách hàng</p>
                            <div class="bg-gray-50 rounded-xl p-3 text-sm font-bold text-[#0A2540]">
                                01 Lượt
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Thời gian áp dụng</p>
                        <div class="bg-gray-50 rounded-xl p-3 flex items-center gap-3 text-sm font-bold text-[#0A2540]">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($voucher->start_at)->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($voucher->end_at)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Sản phẩm áp dụng</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-lg uppercase">Toàn sàn</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Dashboard Stats -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Usage Progress Card -->
            <div class="bg-[#0A2540] rounded-3xl p-10 text-white relative overflow-hidden shadow-2xl shadow-[#0A2540]/20">
                <div class="absolute right-10 top-10 w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="w-6 h-6 text-white"></i>
                </div>
                
                <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-2">Tiến độ sử dụng</p>
                <div class="flex items-baseline gap-2 mb-8">
                    <h3 class="text-5xl font-black">{{ number_format($voucher->used_count) }}</h3>
                    <span class="text-xl text-blue-300/50">/ {{ number_format($voucher->usage_limit ?? 0) }} lượt</span>
                </div>

                <div class="space-y-4">
                    @php
                        $percentUsed = ($voucher->usage_limit > 0) ? ($voucher->used_count / $voucher->usage_limit) * 100 : 0;
                    @endphp
                    <div class="h-4 w-full bg-white/10 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ $percentUsed }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-blue-100/40">
                        <span>Đã sử dụng {{ round($percentUsed, 1) }}%</span>
                        <span>Còn lại {{ number_format(($voucher->usage_limit ?? 0) - $voucher->used_count) }} lượt</span>
                    </div>
                </div>
            </div>

            <!-- Revenue and AOV Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Revenue -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                            <i data-lucide="banknote" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Doanh thu tạo ra</p>
                            <h4 class="text-2xl font-black text-[#0A2540]">{{ number_format($revenue) }} đ</h4>
                        </div>
                    </div>
                    <!-- Mini Chart Placeholder -->
                    <div class="flex items-end gap-1.5 h-16 pt-2">
                        <div class="flex-1 bg-green-50 rounded-t-lg h-4"></div>
                        <div class="flex-1 bg-green-50 rounded-t-lg h-8"></div>
                        <div class="flex-1 bg-green-50 rounded-t-lg h-6"></div>
                        <div class="flex-1 bg-green-50 rounded-t-lg h-10"></div>
                        <div class="flex-1 bg-green-50 rounded-t-lg h-12"></div>
                        <div class="flex-1 bg-green-600 rounded-t-lg h-16"></div>
                    </div>
                </div>

                <!-- AOV -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Giá trị đơn trung bình</p>
                            <h4 class="text-2xl font-black text-[#0A2540]">{{ number_format($avg_order) }} đ</h4>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dựa trên dữ liệu đơn hàng</span>
                        <div class="flex items-center gap-1 text-[10px] font-black text-green-500 bg-green-50 px-2 py-1 rounded-full">
                            <i data-lucide="trending-up" class="w-3 h-3"></i>
                            +0%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion and ROI -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative w-20 h-20">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path class="text-gray-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-[#0A2540]" stroke-width="3" stroke-dasharray="80, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-xs font-black text-[#0A2540]">80%</div>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-[#0A2540]">Chỉ số chuyển đổi</h4>
                        <p class="text-[10px] text-gray-400 font-medium leading-relaxed max-w-[200px]">Khách hàng áp dụng mã trên tổng số lượt xem voucher.</p>
                    </div>
                </div>

                <div class="flex gap-12 items-center">
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tỷ lệ hủy</p>
                        <p class="text-lg font-black text-red-500">0%</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">ROI ước tính</p>
                        <p class="text-lg font-black text-[#0A2540]">--</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom: Recent Transactions Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-xl font-black text-[#0A2540] uppercase tracking-tight">Giao dịch gần đây</h2>
            <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Xem tất cả</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Mã đơn hàng</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Khách hàng</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Thời gian</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Giá trị đơn</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Giảm giá</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($recent_orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-5 px-8 font-black text-[#0A2540]">#{{ $order->order_code }}</td>
                        <td class="py-5 px-8 flex items-center gap-3">
                            @if($order->avatar_url)
                                <img src="{{ $order->avatar_url }}" class="w-8 h-8 rounded-full border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-400">
                                    {{ substr($order->full_name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-[#0A2540]">{{ $order->full_name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $order->email }}</p>
                            </div>
                        </td>
                        <td class="py-5 px-8 text-gray-500">
                            {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}
                        </td>
                        <td class="py-5 px-8 font-bold text-[#0A2540]">{{ number_format($order->total_amount) }} đ</td>
                        <td class="py-5 px-8 font-black text-green-500">-{{ number_format($order->discount_amount) }} đ</td>
                        <td class="py-5 px-8 text-right">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-50 text-yellow-600',
                                    'confirmed' => 'bg-blue-50 text-blue-600',
                                    'delivered' => 'bg-green-50 text-green-600',
                                    'cancelled' => 'bg-red-50 text-red-600',
                                ];
                            @endphp
                            <span class="{{ $statusColors[$order->order_status] ?? 'bg-gray-50 text-gray-600' }} text-[9px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                                {{ $order->order_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400 font-medium">Chưa có giao dịch nào sử dụng voucher này.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
