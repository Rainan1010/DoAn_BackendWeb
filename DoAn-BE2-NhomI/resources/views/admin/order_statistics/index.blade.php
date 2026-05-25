@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('header_search')
<form action="{{ route('admin.order-statistics.index') }}" method="GET" class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>

    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Tìm kiếm đơn hàng, khách hàng..."
        class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />

    @if(request('status'))
    <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
</form>
@endsection

@section('content')
@php
$totalOrders = $totalOrders ?? 0;
$pendingOrders = $pendingOrders ?? 0;
$completedOrders = $completedOrders ?? 0;
$cancelledOrders = $cancelledOrders ?? 0;
$totalRevenue = $totalRevenue ?? 0;
$todayRevenue = $todayRevenue ?? 0;
$todayNewOrders = $todayNewOrders ?? 0;
$recentOrders = $recentOrders ?? collect();

$statusOptions = [
'' => 'Tất cả',
'pending' => 'Chờ xác nhận',
'confirmed' => 'Đã xác nhận',
'processing' => 'Đang xử lý',
'shipped' => 'Đang giao',
'delivered' => 'Đã giao',
'cancelled' => 'Đã hủy',
];

$statusMap = [
'pending' => [
'text' => 'Chờ xác nhận',
'class' => 'bg-amber-100 text-amber-700',
'dot' => 'bg-amber-500',
],
'confirmed' => [
'text' => 'Đã xác nhận',
'class' => 'bg-slate-100 text-slate-700',
'dot' => 'bg-slate-500',
],
'processing' => [
'text' => 'Đang xử lý',
'class' => 'bg-blue-100 text-blue-700',
'dot' => 'bg-blue-500',
],
'shipped' => [
'text' => 'Đang giao',
'class' => 'bg-indigo-100 text-indigo-700',
'dot' => 'bg-indigo-500',
],
'shipping' => [
'text' => 'Đang giao',
'class' => 'bg-indigo-100 text-indigo-700',
'dot' => 'bg-indigo-500',
],
'completed' => [
'text' => 'Hoàn thành',
'class' => 'bg-green-100 text-green-700',
'dot' => 'bg-green-500',
],
'delivered' => [
'text' => 'Đã giao',
'class' => 'bg-green-100 text-green-700',
'dot' => 'bg-green-500',
],
'cancelled' => [
'text' => 'Đã hủy',
'class' => 'bg-red-100 text-red-700',
'dot' => 'bg-red-500',
],
'canceled' => [
'text' => 'Đã hủy',
'class' => 'bg-red-100 text-red-700',
'dot' => 'bg-red-500',
],
];

$paymentMethodMap = [
'cod' => [
'text' => 'COD',
'icon' => 'truck',
'class' => 'bg-slate-100 text-slate-700',
],
'vnpay' => [
'text' => 'VNPAY',
'icon' => 'wallet',
'class' => 'bg-blue-100 text-blue-700',
],
'momo' => [
'text' => 'MOMO',
'icon' => 'wallet',
'class' => 'bg-pink-100 text-pink-700',
],
'bank_transfer' => [
'text' => 'BANK TRANSFER',
'icon' => 'credit-card',
'class' => 'bg-[#d6e3fe] text-[#58657c]',
],
];
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <nav class="flex items-center text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 gap-2">
                <span>Dashboard</span>
                <span>›</span>
                <span class="text-[#001e40]">Quản lý Đơn hàng</span>
            </nav>

            <h1 class="text-4xl font-extrabold tracking-tight text-[#001e40]">
                Quản lý Đơn hàng
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.create') }}"
                class="flex items-center gap-2 px-6 py-3 bg-[#001e40] hover:bg-[#002c5c] text-white rounded-lg text-sm font-bold shadow-lg shadow-[#001e40]/20 hover:shadow-xl transition-all active:scale-95">
                <i data-lucide="plus" class="w-5 h-5"></i>
                THÊM ĐƠN HÀNG MỚI
            </a>

            <button type="button"
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-tr from-[#001e40] to-[#003366] text-white rounded-lg text-sm font-bold shadow-lg shadow-[#001e40]/20 hover:shadow-xl transition-all active:scale-95">
                <i data-lucide="download" class="w-5 h-5"></i>
                XUẤT FILE EXCEL
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border-l-4 border-[#001e40] shadow-sm hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-[#001e40]/10 rounded-lg text-[#001e40]">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">
                    LIVE
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium">Tổng Đơn hàng</p>
            <h3 class="text-2xl font-black text-[#001e40] mt-1">
                {{ number_format($totalOrders) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-amber-500 shadow-sm hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-amber-500/10 rounded-lg text-amber-600">
                    <i data-lucide="clipboard-clock" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">
                    Cần xử lý
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium">Đang Chờ Duyệt</p>
            <h3 class="text-2xl font-black text-[#001e40] mt-1">
                {{ number_format($pendingOrders) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-green-500 shadow-sm hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-green-500/10 rounded-lg text-green-600">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">
                    Done
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium">Đã Hoàn Thành</p>
            <h3 class="text-2xl font-black text-[#001e40] mt-1">
                {{ number_format($completedOrders) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-blue-600 shadow-sm hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-blue-600/10 rounded-lg text-blue-600">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                    Revenue
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium">Tổng Doanh Thu</p>
            <h3 class="text-2xl font-black text-[#001e40] mt-1">
                {{ number_format($totalRevenue, 0, ',', '.') }} ₫
            </h3>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">

        {{-- Filters --}}
        <div class="p-6 border-b border-gray-100 bg-[#f2f4f6]/70">
            <form action="{{ route('admin.order-statistics.index') }}" method="GET" id="filterForm"
                class="flex flex-wrap items-center justify-between gap-4 w-full">

                <div class="flex flex-wrap items-center gap-4">
                    {{-- Trạng thái --}}
                    <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm">
                        <span class="text-gray-500 font-medium">Trạng thái:</span>
                        <select name="status"
                            onchange="this.form.submit()"
                            class="border-none bg-transparent p-0 focus:ring-0 text-[#001e40] font-bold text-sm">
                            @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status', '') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="h-4 w-px bg-gray-200 hidden md:block"></div>

                    {{-- Ngày --}}
                    <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm" x-data="{ dateFilter: '{{ request('date_filter', 'all') }}' }">
                        <span class="text-gray-500 font-medium">Ngày:</span>
                        <select name="date_filter"
                            x-model="dateFilter"
                            @change="if(dateFilter !== 'custom') $el.form.submit()"
                            class="border-none bg-transparent p-0 focus:ring-0 text-[#001e40] font-bold text-sm">
                            <option value="all" {{ request('date_filter', 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                            <option value="7days" {{ request('date_filter') === '7days' ? 'selected' : '' }}>7 ngày qua</option>
                            <option value="30days" {{ request('date_filter') === '30days' ? 'selected' : '' }}>30 ngày qua</option>
                            <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Chọn ngày cụ thể...</option>
                        </select>

                        <input type="date" name="custom_date"
                            x-show="dateFilter === 'custom'"
                            value="{{ request('custom_date') }}"
                            onchange="this.form.submit()"
                            class="border border-gray-200 rounded px-2 py-0.5 text-xs text-[#001e40] focus:outline-none focus:ring-1 focus:ring-[#001e40]">

                        <i data-lucide="calendar" class="w-4 h-4 text-gray-500" x-show="dateFilter !== 'custom'"></i>
                    </div>
                </div>

                {{-- Hiển thị per_page --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-tight">Hiển thị:</span>
                    <input type="hidden" id="per_page_input" name="per_page" value="{{ request('per_page', 10) }}">
                    @foreach([10, 25, 50] as $size)
                        <button type="button"
                            onclick="document.getElementById('per_page_input').value = {{ $size }}; document.getElementById('filterForm').submit();"
                            class="w-8 h-8 flex items-center justify-center rounded text-xs font-bold transition-colors {{ request('per_page', 10) == $size ? 'bg-[#001e40] text-white' : 'border border-gray-200 text-[#001e40] hover:bg-gray-50 bg-white' }}">
                            {{ $size }}
                        </button>
                    @endforeach
                </div>

                @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f2f4f6] text-xs font-bold text-[#001e40] uppercase tracking-wider">
                        <th class="px-6 py-4">order_id</th>
                        <th class="px-6 py-4">order_code</th>
                        <th class="px-6 py-4">full_name</th>
                        <th class="px-6 py-4">total_amount</th>
                        <th class="px-6 py-4">payment_method</th>
                        <th class="px-6 py-4">order_status</th>
                        <th class="px-6 py-4 text-center">HÀNH ĐỘNG</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    @php
                    $customerName = $order->customer_name
                    ?? $order->full_name
                    ?? 'Khách hàng';

                    $email = $order->customer_email
                    ?? $order->email
                    ?? 'Không có email';

                    $initials = collect(explode(' ', trim($customerName)))
                    ->filter()
                    ->map(fn($part) => mb_substr($part, 0, 1))
                    ->take(2)
                    ->implode('');

                    $paymentMethod = strtolower($order->payment_method ?? 'cod');

                    $payment = $paymentMethodMap[$paymentMethod] ?? [
                    'text' => strtoupper($paymentMethod),
                    'icon' => 'credit-card',
                    'class' => 'bg-slate-100 text-slate-700',
                    ];

                    $orderStatus = strtolower($order->order_status ?? 'pending');

                    $status = $statusMap[$orderStatus] ?? [
                    'text' => ucfirst($orderStatus),
                    'class' => 'bg-slate-100 text-slate-700',
                    'dot' => 'bg-slate-500',
                    ];
                    @endphp

                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">
                            #{{ $order->order_id }}
                        </td>

                        <td class="px-6 py-4 text-sm font-bold text-[#001e40]">
                            {{ $order->order_code ?? 'ORD-' . $order->order_id }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#d5e3ff] flex items-center justify-center text-[10px] font-bold text-[#001b3c]">
                                    {{ $initials ?: 'KH' }}
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-[#001e40]">
                                        {{ $customerName }}
                                    </p>

                                    <p class="text-[10px] text-gray-500">
                                        {{ $email }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm font-black text-[#001e40]">
                            {{ number_format($order->total_amount ?? 0, 0, ',', '.') }} ₫
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $payment['class'] }}">
                                <i data-lucide="{{ $payment['icon'] }}" class="w-3.5 h-3.5"></i>
                                {{ $payment['text'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold {{ $status['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                {{ $status['text'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('orders.invoice', $order->order_id) }}" target="_blank" class="p-1.5 text-[#003366] hover:bg-[#003366]/10 rounded transition-colors block" title="Xem đơn">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </a>

                                <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="p-1.5 text-[#003366] hover:bg-[#003366]/10 rounded transition-colors block" title="Sửa đơn">
                                    <i data-lucide="edit" class="w-5 h-5"></i>
                                </a>

                                @if($order->order_status === 'pending')
                                    <form action="{{ route('admin.orders.confirm', $order->order_id) }}" method="POST" class="inline m-0">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-[#003366] hover:bg-green-50 hover:text-green-600 rounded transition-colors block" title="Xác nhận đơn hàng" onclick="return confirm('Bạn có chắc chắn muốn duyệt nhanh đơn hàng này?')">
                                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="p-1.5 text-gray-300 cursor-not-allowed rounded" title="Đã duyệt / Không thể xác nhận" disabled>
                                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Chưa có đơn hàng nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 bg-[#f2f4f6]/70 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs font-medium text-gray-500">
                Hiển thị
                <span class="font-bold text-[#001e40]">1 - {{ $recentOrders->count() }}</span>
                trong
                <span class="font-bold text-[#001e40]">{{ number_format($totalOrders) }}</span>
                đơn hàng
            </p>

            <div class="flex items-center gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white text-gray-500 disabled:opacity-30" disabled>
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>

                <button class="w-8 h-8 flex items-center justify-center rounded bg-[#001e40] text-white text-xs font-bold">
                    1
                </button>

                <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white text-gray-500 text-xs font-bold">
                    2
                </button>

                <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white text-gray-500 text-xs font-bold">
                    3
                </button>

                <span class="px-2 text-gray-500">...</span>

                <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white text-gray-500">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Bottom Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 relative overflow-hidden rounded-xl bg-[#001e40] h-48 group">
            <div class="absolute inset-0 bg-gradient-to-r from-[#001e40] via-[#001e40]/90 to-transparent z-10 p-8 flex flex-col justify-center">
                <span class="text-[#d5e3ff] font-bold text-xs uppercase tracking-widest mb-2">
                    Thông báo hệ thống
                </span>

                <h4 class="text-2xl font-black text-white mb-4 leading-tight">
                    Cần cập nhật cấu hình vận chuyển<br />
                    cho khu vực miền Tây.
                </h4>

                <a class="inline-flex items-center gap-2 text-sm font-bold text-white group-hover:gap-4 transition-all" href="#">
                    Xem chi tiết
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(167,200,255,0.35),transparent_35%)] opacity-80"></div>
        </div>

        <div class="bg-gradient-to-br from-[#d6e3fe] to-[#d5e3ff] rounded-xl p-8 flex flex-col justify-between items-start">
            <div>
                <h4 class="text-[#001e40] font-black text-xl mb-2">
                    Trung tâm Trợ giúp
                </h4>

                <p class="text-sm font-medium text-[#58657c] leading-relaxed">
                    Bạn cần hỗ trợ trong việc xử lý các đơn hàng hoàn/hủy?
                </p>
            </div>

            <button class="mt-4 px-6 py-2 bg-white text-[#001e40] rounded-lg text-xs font-bold shadow-sm hover:shadow-md transition-shadow">
                LIÊN HỆ KỸ THUẬT
            </button>
        </div>
    </div>

    {{-- Footer Summary --}}
    <div class="p-4 bg-white/70 backdrop-blur-md border border-gray-200 rounded-xl flex justify-end gap-8">
        <div class="text-right">
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-tight leading-none">
                Doanh thu hôm nay
            </p>

            <p class="text-lg font-black text-[#001e40]">
                {{ number_format($todayRevenue, 0, ',', '.') }} ₫
            </p>
        </div>

        <div class="text-right">
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-tight leading-none">
                Đơn mới
            </p>

            <p class="text-lg font-black text-amber-600">
                {{ number_format($todayNewOrders) }}
            </p>
        </div>
    </div>

</div>
@endsection