@extends('layouts.app')

@section('content')

    <main class="pt-32 pb-16 px-6 max-w-5xl mx-auto">

        <!-- HEADER -->

        <header class="mb-12">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span
                        class="text-on-secondary-container bg-secondary-container px-3 py-1 text-[10px] font-bold tracking-[0.1em] uppercase rounded-full mb-4 inline-block">TÀI
                        KHOẢN CỦA TÔI</span>
                    <h1 class="text-4xl md:text-5xl font-black text-brand-blue tracking-tighter leading-none mb-2">Lịch sử
                        đơn hàng</h1>
                    <p class="text-on-surface-variant max-w-md">Theo dõi, quản lý và xem lại các giao dịch kỹ thuật cao của
                        bạn với độ chính xác tuyệt đối.</p>
                </div>
                <div class="flex gap-2">
                    <button
                        class="bg-surface-container-high text-on-surface px-6 py-3 rounded-md text-sm font-bold flex items-center gap-2 hover:bg-surface-container-highest transition-colors">
                        <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span> Bộ lọc
                    </button>
                    <button
                        class="bg-brand-blue text-white text-on-primary px-6 py-3 rounded-md text-sm font-bold shadow-lg hover:shadow-brand-blue/20 transition-all">
                        Tải hóa đơn (PDF)
                    </button>
                </div>
            </div>

        </header>

        <!-- ORDER LIST -->

        <div class="grid grid-cols-1 gap-6">

            @forelse($orders as $order)

                @php

                    $item = $order->items->first();

                    $statusText = [
                        'pending' => 'Đang xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang vận chuyển',
                        'delivered' => 'Đã giao hàng',
                        'cancelled' => 'Đã huỷ',
                    ];

                    $statusColor = [
                        'pending' => 'amber',
                        'confirmed' => 'blue',
                        'processing' => 'amber',
                        'shipping' => 'blue',
                        'delivered' => 'green',
                        'cancelled' => 'red',
                    ];

                    $statusIcon = [
                        'pending' => 'schedule',
                        'confirmed' => 'task_alt',
                        'processing' => 'hourglass_top',
                        'shipping' => 'local_shipping',
                        'delivered' => 'check_circle',
                        'cancelled' => 'cancel',
                    ];

                    $color = $statusColor[$order->order_status] ?? 'gray';
                    $status = $statusText[$order->order_status] ?? 'Không xác định';
                    $icon = $statusIcon[$order->order_status] ?? 'info';

                @endphp

                {{-- ĐƠN ĐANG XỬ LÝ --}}

                @if($order->order_status == 'processing' || $order->order_status == 'pending')

                    <div
                        class="bg-surface-container rounded-md p-8 flex flex-col md:flex-row items-center justify-between gap-6 opacity-90 shadow-sm border border-slate-100">

                        <div class="flex items-center gap-6">

                            <div
                                class="w-20 h-20 bg-surface-container-high flex items-center justify-center rounded-md border border-outline-variant overflow-hidden">

                                <img src="{{ asset($item->image_url ?? 'images/no-image.png') }}" alt="{{ $item->product_name }}"
                                    class="w-full h-full object-cover">

                            </div>

                            <div>

                                <div class="flex items-center gap-2 mb-1">

                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>

                                    <span class="text-[10px] font-bold text-amber-700 uppercase tracking-widest">
                                        {{ $status }}
                                    </span>

                                </div>

                                <h4 class="font-bold text-brand-blue">
                                    {{ $item->product_name }}
                                </h4>

                                <p class="text-xs text-on-surface-variant">

                                    Đơn hàng #{{ $order->order_code }}
                                    |
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}

                                </p>

                            </div>

                        </div>

                        <div class="flex items-center gap-12">

                            <div class="text-right">

                                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant">
                                    GIÁ TRỊ
                                </p>

                                <p class="font-black text-brand-blue">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                </p>

                            </div>

                            <button class="bg-white border border-outline-variant p-2 rounded hover:bg-slate-50">

                                <span class="material-symbols-outlined">
                                    chevron_right
                                </span>

                            </button>

                        </div>

                    </div>

                @else

                    {{-- CARD BÌNH THƯỜNG --}}

                    <div
                        class="group bg-surface-container-lowest rounded-md border-b-2 border-transparent hover:border-brand-blue transition-all duration-300 overflow-hidden shadow-sm">

                        <div class="flex flex-col lg:flex-row">

                            <!-- IMAGE -->

                            <div class="relative w-full lg:w-72 h-48 lg:h-auto overflow-hidden">

                                @if(!empty($item->image_url))

                                    <img src="{{ asset($item->image_url) }}" alt="{{ $item->product_name }}"
                                        class="w-full h-full object-cover">

                                @else

                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">

                                        <span class="material-symbols-outlined text-5xl text-gray-400">
                                            inventory_2
                                        </span>

                                    </div>

                                @endif

                                <div
                                    class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded text-[10px] font-black tracking-widest text-brand-blue uppercase">

                                    MÃ ĐƠN: #{{ $order->order_code }}

                                </div>

                            </div>

                            <!-- CONTENT -->

                            <div class="flex-1 p-8 flex flex-col md:flex-row justify-between gap-8">

                                <div class="space-y-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="material-symbols-outlined text-{{ $color }}-600 bg-{{ $color }}-50 p-1.5 rounded-full"
                                            style="font-variation-settings: 'FILL' 1;">
                                            {{ $icon }}
                                        </span>

                                        <span class="text-sm font-bold text-{{ $color }}-700 tracking-tight">
                                            {{ $status }}
                                        </span>

                                        <span class="text-xs text-on-surface-variant font-medium">

                                            |
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}

                                        </span>

                                    </div>

                                    <h3 class="text-2xl font-bold text-brand-blue tracking-tight">

                                        {{ $item->product_name }}

                                    </h3>

                                    <div class="flex gap-8 flex-wrap">

                                        <div>

                                            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">
                                                TỔNG CỘNG
                                            </p>

                                            <p class="text-lg font-black text-brand-blue">

                                                {{ number_format($order->total_amount, 0, ',', '.') }}₫

                                            </p>

                                        </div>

                                        <div>

                                            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">
                                                PHƯƠNG THỨC
                                            </p>

                                            <p class="text-sm font-medium text-on-surface">

                                                {{ strtoupper($order->payment_method) }}

                                            </p>

                                        </div>

                                    </div>

                                </div>

                                <!-- BUTTON -->

                                <div class="flex flex-col justify-center gap-3">
                                    <a href="{{ route('orders.detail', $order->order_id) }}"
                                        class="bg-brand-blue text-white px-8 py-3 rounded-md text-xs font-bold tracking-widest uppercase hover:bg-primary-container transition-colors text-center">
                                        Xem chi tiết
                                    </a>
                                    @if($order->order_status == 'delivered')

                                        <button
                                            class="border border-outline-variant text-on-surface-variant px-8 py-3 rounded-md text-xs font-bold tracking-widest uppercase hover:bg-surface-container-low transition-colors">

                                            Mua lại

                                        </button>

                                    @elseif($order->order_status == 'shipping')

                                        <button
                                            class="border border-blue-500 text-blue-500 px-8 py-3 rounded-md text-xs font-bold tracking-widest uppercase hover:bg-blue-500 hover:text-white transition-colors">

                                            Theo dõi đơn hàng

                                        </button>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            @empty

                <div class="bg-white rounded-xl p-10 text-center shadow-sm border">

                    <h3 class="text-2xl font-bold text-gray-700">
                        Chưa có đơn hàng nào
                    </h3>

                </div>

            @endforelse

        </div>

        <!-- PAGINATION -->
        <div class="mt-14 flex flex-col md:flex-row items-center justify-between gap-6">

            <div class="text-sm text-gray-500">
                Hiển thị
                <span class="font-bold text-blue-600">
                    {{ $orders->firstItem() }}
                </span>
                -
                <span class="font-bold text-blue-600">
                    {{ $orders->lastItem() }}
                </span>
                trên tổng
                <span class="font-bold text-blue-600">
                    {{ $orders->total() }}
                </span>
                đơn hàng
            </div>

            <div class="flex items-center gap-2">

                {{-- PREVIOUS --}}
                @if ($orders->onFirstPage())

                    <span
                        class="w-10 h-10 rounded-lg border bg-gray-100 text-gray-400 flex items-center justify-center cursor-not-allowed">
                        ←
                    </span>

                @else

                    <a href="{{ $orders->previousPageUrl() }}"
                        class="w-10 h-10 rounded-lg border hover:bg-blue-50 hover:border-blue-500 flex items-center justify-center transition">

                        ←

                    </a>

                @endif

                {{-- PAGE NUMBER --}}
                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)

                    @if ($page == $orders->currentPage())

                        <span class="w-10 h-10 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center shadow">

                            {{ $page }}

                        </span>

                    @else

                        <a href="{{ $url }}"
                            class="w-10 h-10 rounded-lg border hover:bg-blue-50 hover:border-blue-500 flex items-center justify-center transition">

                            {{ $page }}

                        </a>

                    @endif

                @endforeach

                {{-- NEXT --}}
                @if ($orders->hasMorePages())

                    <a href="{{ $orders->nextPageUrl() }}"
                        class="w-10 h-10 rounded-lg border hover:bg-blue-50 hover:border-blue-500 flex items-center justify-center transition">

                        →

                    </a>

                @else

                    <span
                        class="w-10 h-10 rounded-lg border bg-gray-100 text-gray-400 flex items-center justify-center cursor-not-allowed">
                        →
                    </span>

                @endif

            </div>

        </div>

    </main>

@endsection