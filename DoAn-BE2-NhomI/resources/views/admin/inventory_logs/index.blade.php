@extends('admin.layouts.app')

@section('title', 'Nhật ký Kho hàng')

@section('header_search')
<form action="{{ route('admin.stock-logs.index') }}" method="GET" class="relative">
    <i data-lucide="search" class="absolute left-4 top-2.5 text-gray-400 w-5 h-5"></i>

    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Tìm kiếm log, sản phẩm, đơn hàng..."
        class="w-full bg-[#F4F5F7] border border-transparent rounded-full py-2.5 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540] focus:bg-white transition-colors text-[#0A2540] font-medium placeholder-gray-400" />

    @if(request('action_type'))
    <input type="hidden" name="action_type" value="{{ request('action_type') }}">
    @endif
</form>
@endsection

@section('content')
@php
$actionMap = [
'import' => [
'text' => 'IMPORT',
'class' => 'bg-green-100 text-green-700',
],
'export' => [
'text' => 'EXPORT',
'class' => 'bg-red-100 text-red-700',
],
'adjust' => [
'text' => 'ADJUST',
'class' => 'bg-amber-100 text-amber-700',
],
'return' => [
'text' => 'RETURN',
'class' => 'bg-blue-100 text-blue-700',
],
];
@endphp

<div class="space-y-8">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <nav class="flex text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 gap-2">
                <span>Dashboard</span>
                <span>›</span>
                <span class="text-[#003366] font-black">Quản lý Kho hàng</span>
            </nav>

            <h1 class="text-4xl font-black tracking-tight text-[#001e40]">
                Nhật ký Kho hàng
            </h1>

            <p class="text-gray-500 text-sm font-medium mt-1">
                Lịch sử biến động tồn kho chi tiết theo từng giao dịch.
            </p>
        </div>

        <div class="flex gap-3">
            <button type="button"
                class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-[#001e40] rounded-md font-bold text-sm shadow-sm hover:bg-slate-50 transition-all">
                <i data-lucide="download" class="w-5 h-5"></i>
                XUẤT FILE EXCEL
            </button>

            <a href="{{ route('admin.inventory-logs.create') }}"
                class="flex items-center gap-2 px-6 py-3 bg-[#003366] text-white rounded-md font-bold text-sm shadow-lg shadow-[#003366]/20 hover:opacity-90 transition-all active:scale-95">
                <i data-lucide="plus-square" class="w-5 h-5"></i>
                NHẬP KHO MỚI
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border-l-4 border-[#003366] shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-[#001e40]/10 rounded-lg text-[#003366]">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>

                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">
                    LIVE
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium uppercase tracking-tight">
                Tổng tồn kho
            </p>

            <h3 class="text-2xl font-black text-[#003366] mt-1">
                {{ number_format($totalStock ?? 0) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-amber-500 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <i data-lucide="triangle-alert" class="w-5 h-5"></i>
                </div>

                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">
                    Cần nhập
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium uppercase tracking-tight">
                Sản phẩm sắp hết
            </p>

            <h3 class="text-2xl font-black text-[#003366] mt-1">
                {{ number_format($lowStockProducts ?? 0) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-red-500 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <i data-lucide="ban" class="w-5 h-5"></i>
                </div>

                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">
                    Khẩn cấp
                </span>
            </div>

            <p class="text-gray-500 text-sm font-medium uppercase tracking-tight">
                Sản phẩm hết hàng
            </p>

            <h3 class="text-2xl font-black text-[#003366] mt-1">
                {{ number_format($outOfStockProducts ?? 0) }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl border-l-4 border-blue-400 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
            </div>

            <p class="text-gray-500 text-sm font-medium uppercase tracking-tight">
                Giao dịch hôm nay
            </p>

            <h3 class="text-2xl font-black text-[#003366] mt-1">
                {{ number_format($todayLogs ?? 0) }}
            </h3>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Filters --}}
        <div class="p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4 bg-[#f2f4f6]/70">
            <form action="{{ route('admin.stock-logs.index') }}" method="GET" class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-md border border-gray-200 text-sm">
                    <span class="text-gray-500 font-medium">Hành động:</span>

                    <select name="action_type"
                        onchange="this.form.submit()"
                        class="border-none bg-transparent p-0 focus:ring-0 text-[#003366] font-black text-sm">
                        <option value="">Tất cả</option>
                        <option value="import" {{ request('action_type') === 'import' ? 'selected' : '' }}>
                            Nhập kho
                        </option>
                        <option value="export" {{ request('action_type') === 'export' ? 'selected' : '' }}>
                            Xuất kho
                        </option>
                        <option value="adjust" {{ request('action_type') === 'adjust' ? 'selected' : '' }}>
                            Điều chỉnh
                        </option>
                        <option value="return" {{ request('action_type') === 'return' ? 'selected' : '' }}>
                            Hoàn hàng
                        </option>
                    </select>

                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </div>

                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-md border border-gray-200 text-sm">
                    <span class="text-gray-500 font-medium">Thời gian:</span>
                    <span class="text-[#003366] font-black">Hôm nay</span>
                    <i data-lucide="calendar-days" class="w-4 h-4 text-gray-500"></i>
                </div>
            </form>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">
                    Hiển thị:
                </span>

                <button class="w-8 h-8 flex items-center justify-center rounded bg-[#003366] text-white text-xs font-bold">
                    10
                </button>

                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-xs font-bold hover:bg-slate-100 transition-colors">
                    25
                </button>

                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-xs font-bold hover:bg-slate-100 transition-colors">
                    50
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f2f4f6] text-[11px] font-black text-[#003366] uppercase tracking-widest">
                        <th class="px-6 py-4">Mã Log</th>
                        <th class="px-6 py-4">Mã Biến thể</th>
                        <th class="px-6 py-4">Mã Đơn hàng</th>
                        <th class="px-6 py-4">Hành động</th>
                        <th class="px-6 py-4 text-center">Thay đổi</th>
                        <th class="px-6 py-4 text-center">Tồn sau thay đổi</th>
                        <th class="px-6 py-4">Ghi chú</th>
                        <th class="px-6 py-4">Người thực hiện</th>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4">HÀNH ĐỘNG</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    @php
                    $action = $actionMap[$log->action_type] ?? [
                    'text' => strtoupper($log->action_type ?? 'LOG'),
                    'class' => 'bg-slate-100 text-slate-700',
                    ];

                    $quantityChange = $log->quantity_change ?? 0;
                    $quantityClass = $quantityChange > 0
                    ? 'text-green-600'
                    : ($quantityChange < 0 ? 'text-red-600' : 'text-gray-600' );

                        $quantityText=$quantityChange> 0
                        ? '+' . $quantityChange
                        : $quantityChange;
                        @endphp

                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-sm font-mono text-gray-500">
                                #LOG-{{ str_pad($log->log_id, 6, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#001e40]">
                                        {{ $log->sku ?? 'N/A' }}
                                    </span>

                                    <span class="text-[10px] text-gray-500">
                                        {{ $log->product_name ?? 'Sản phẩm' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-slate-500">
                                {{ $log->order_code ?? '---' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full {{ $action['class'] }} text-[10px] font-bold">
                                    {{ $action['text'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black {{ $quantityClass }}">
                                    {{ $quantityText }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black text-[#001e40]">
                                    {{ number_format($log->stock_after ?? 0) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-500 max-w-[150px] truncate">
                                {{ $log->note ?? 'Không có ghi chú' }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-[#001e40]/10 flex items-center justify-center text-[8px] font-bold text-[#001e40]">
                                        {{ mb_substr($log->user_name ?? 'AD', 0, 2) }}
                                    </div>

                                    <span class="text-xs font-bold text-[#001e40]">
                                        {{ $log->user_name ?? 'System' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-xs font-medium text-gray-500">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 text-[#003366]">
                                    <button type="button" class="hover:scale-110 transition-transform" title="Xem lịch sử">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </button>

                                    <button type="button" class="hover:scale-110 transition-transform" title="Điều chỉnh">
                                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                Chưa có nhật ký kho hàng nào.
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
                <span class="font-bold text-[#003366]">
                    {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }}
                </span>
                trong
                <span class="font-bold text-[#003366]">
                    {{ number_format($logs->total()) }}
                </span>
                bản ghi log
            </p>

            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    {{-- Bottom Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 relative overflow-hidden rounded-xl bg-[#003366] h-48 group shadow-xl">
            <div class="absolute inset-0 bg-gradient-to-r from-[#003366] via-[#003366]/90 to-transparent z-10 p-8 flex flex-col justify-center">
                <span class="text-blue-200 font-bold text-xs uppercase tracking-widest mb-2">
                    Đồng bộ tự động
                </span>

                <h4 class="text-2xl font-black text-white mb-4 leading-tight">
                    Hệ thống đang đồng bộ tồn kho<br>
                    với dữ liệu bán hàng theo thời gian thực.
                </h4>

                <a class="inline-flex items-center gap-2 text-sm font-bold text-white group-hover:gap-4 transition-all" href="#">
                    Xem nhật ký đồng bộ
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(167,200,255,0.35),transparent_35%)] opacity-80"></div>
        </div>

        <div class="bg-gradient-to-br from-slate-50 to-white rounded-xl p-8 border border-gray-200 flex flex-col justify-between items-start shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-full bg-[#003366]/10 flex items-center justify-center mb-4">
                    <i data-lucide="headphones" class="w-6 h-6 text-[#003366]"></i>
                </div>

                <h4 class="text-[#001e40] font-black text-xl mb-2">
                    Hỗ trợ kỹ thuật
                </h4>

                <p class="text-sm font-medium text-gray-500 leading-relaxed">
                    Gặp vấn đề về quét mã vạch hoặc nhập log? Liên hệ ngay.
                </p>
            </div>

            <button type="button" class="mt-4 px-6 py-2 bg-[#003366] text-white rounded-md text-xs font-bold shadow-md hover:opacity-90 transition-all">
                LIÊN HỆ KỸ THUẬT
            </button>
        </div>
    </div>

    {{-- Footer Summary --}}
    <div class="p-4 bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl flex justify-end gap-8">
        <div class="text-right">
            <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest leading-none mb-1">
                Tổng biến động ngày
            </p>

            <p class="text-lg font-black {{ ($todayChange ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ ($todayChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($todayChange ?? 0) }} sản phẩm
            </p>
        </div>

        <div class="text-right">
            <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest leading-none mb-1">
                Giao dịch thành công
            </p>

            <p class="text-lg font-black text-green-600">
                {{ ($todayLogs ?? 0) > 0 ? '100%' : '0%' }}
            </p>
        </div>
    </div>

</div>
@endsection