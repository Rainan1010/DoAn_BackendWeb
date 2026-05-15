@extends('layouts.app')

@section('content')
    <style>
        @keyframes shimmer {

            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .text-shimmer {

            background:
                linear-gradient(90deg,
                    #003366,
                    #2563EB,
                    #60A5FA,
                    #003366);

            background-size: 200% auto;

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            animation: shimmer 6s linear infinite;
        }

        @keyframes floatSlow {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @keyframes glowPulse {

            0%,
            100% {
                box-shadow:
                    0 0 0 rgba(59, 130, 246, 0),
                    0 0 0 rgba(59, 130, 246, 0);
            }

            50% {
                box-shadow:
                    0 0 25px rgba(59, 130, 246, .25),
                    0 0 60px rgba(59, 130, 246, .12);
            }
        }

        @keyframes shine {

            from {
                transform: translateX(-120%);
            }

            to {
                transform: translateX(220%);
            }
        }

        .float-slow {
            animation: floatSlow 4s ease-in-out infinite;
        }

        .glow-pulse {
            animation: glowPulse 3s ease-in-out infinite;
        }

        .shine-effect::before {

            content: '';

            position: absolute;

            inset: 0;

            background:
                linear-gradient(120deg,
                    transparent,
                    rgba(255, 255, 255, .35),
                    transparent);

            transform: translateX(-120%);

            animation: shine 4s infinite;
        }

        @keyframes cardFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-4px);
            }
        }

        .card-float {
            animation: cardFloat 5s ease-in-out infinite;
        }

        @keyframes moveGlow {

            0% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(40px, -20px);
            }

            100% {
                transform: translate(0, 0);
            }
        }

        .bg-move {
            animation: moveGlow 12s ease-in-out infinite;
        }

        .success-modal {

            position: fixed;

            inset: 0;

            background: rgba(0, 0, 0, .5);

            display: flex;

            align-items: center;

            justify-content: center;

            z-index: 9999;

            animation: fadeIn .3s ease;
        }

        .success-box {

            width: 420px;

            background: #fff;

            border-radius: 24px;

            padding: 40px;

            text-align: center;

            animation: zoomIn .3s ease;
        }

        .success-icon {

            width: 90px;

            height: 90px;

            border-radius: 50%;

            background: #22c55e;

            color: #fff;

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 40px;

            margin-bottom: 20px;
        }

        .success-box h2 {

            color: #001e40;

            margin-bottom: 16px;
        }

        .success-box p {

            margin-bottom: 10px;

            color: #555;
        }

        .success-box button {

            margin-top: 20px;

            border: none;

            background: #001e40;

            color: #fff;

            padding: 12px 30px;

            border-radius: 12px;

            cursor: pointer;

            font-weight: 700;
        }

        @keyframes fadeIn {

            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes zoomIn {

            from {

                transform: scale(.7);

                opacity: 0;
            }

            to {

                transform: scale(1);

                opacity: 1;
            }
        }
    </style>
    <main class="relative pt-10 pb-20 px-4 max-w-5xl mx-auto overflow-hidden">

        {{-- BACKGROUND GLOW --}}
        <div class="fixed top-[-200px] left-[-200px]
                        w-[450px] h-[450px]
                        bg-blue-400/20 blur-3xl rounded-full bg-move pointer-events-none">
        </div>

        <div class="fixed bottom-[-200px] right-[-200px]
                        w-[450px] h-[450px]
                        bg-indigo-400/20 blur-3xl rounded-full pointer-events-none">
        </div>

        <!-- HEADER -->
        <header class="mb-14 relative">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">

                <div>

                    {{-- BREADCRUMB --}}
                    <div class="flex items-center gap-2 mb-6">

                        <a href="/" class="group flex items-center gap-2
                                        bg-white/70 backdrop-blur-xl
                                        border border-white/50
                                        px-4 py-2 rounded-2xl
                                        shadow-[0_8px_25px_rgba(15,23,42,.06)]
                                        hover:-translate-y-1
                                        hover:shadow-[0_15px_35px_rgba(59,130,246,.15)]
                                        transition-all duration-500">

                            <span class="material-symbols-outlined
                                            text-[18px] text-blue-600
                                            group-hover:rotate-12
                                            transition duration-500">

                                home

                            </span>

                            <span class="text-sm font-bold text-slate-700">
                                Trang chủ
                            </span>

                        </a>

                        <span class="material-symbols-outlined
                                        text-slate-400 text-[18px]">

                            chevron_right

                        </span>

                        <div class="flex items-center gap-2
                                        bg-gradient-to-r
                                        from-[#003366]
                                        to-[#2563EB]
                                        text-white
                                        px-4 py-2 rounded-2xl
                                        shadow-[0_10px_30px_rgba(37,99,235,.22)]">

                            <span class="material-symbols-outlined
                                            text-[18px]">

                                receipt_long

                            </span>

                            <span class="text-sm font-bold">
                                Lịch sử đơn hàng
                            </span>

                        </div>

                    </div>

                    <span class="text-on-secondary-container
                                    bg-secondary-container px-3 py-1
                                    text-[10px] font-bold tracking-[0.1em]
                                    uppercase rounded-full mb-4 inline-block">

                        TÀI KHOẢN CỦA TÔI

                    </span>

                    <h1 class="text-4xl md:text-5xl font-black
                                    text-brand-blue tracking-tighter
                                    leading-none mb-3
                                    drop-shadow-[0_10px_30px_rgba(0,51,102,.18)]">

                        <span class="text-shimmer">
                            Lịch sử đơn hàng
                        </span>

                    </h1>

                    <p class="text-on-surface-variant max-w-md leading-7">

                        Theo dõi, quản lý và xem lại các giao dịch kỹ thuật cao
                        của bạn với độ chính xác tuyệt đối.

                    </p>

                </div>

                <div class="flex gap-3">

                    {{-- FILTER --}}
                    {{-- FILTER --}}
                    <div class="relative">

                        <button onclick="toggleFilterMenu()" class="group relative overflow-hidden
                    active:scale-95
                    bg-white/80 backdrop-blur-2xl
                    border border-white/60
                    px-6 py-3 rounded-2xl
                    shadow-[0_10px_35px_rgba(15,23,42,.08)]
                    hover:shadow-[0_20px_45px_rgba(59,130,246,.18)]
                    hover:border-cyan-200
                    hover:-translate-y-1
                    hover:scale-[1.02]
                    transition-all duration-500
                    flex items-center gap-3">

                            {{-- animated glow --}}
                            <div class="absolute inset-0 opacity-0
                        group-hover:opacity-100
                        bg-gradient-to-r
                        from-cyan-400/10
                        via-blue-400/10
                        to-indigo-500/10
                        transition duration-500">
                            </div>

                            {{-- shine --}}
                            <div class="absolute inset-0
                        -translate-x-full
                        group-hover:translate-x-full
                        bg-gradient-to-r
                        from-black/40 via-black/10 to-transparent
                        transition duration-1000">
                            </div>

                            {{-- icon box --}}
                            <div class="relative z-10
                        w-10 h-10 rounded-2xl
                        bg-gradient-to-br
                        from-cyan-100
                        to-blue-100
                        flex items-center justify-center
                        shadow-inner
                        group-hover:rotate-2
                        group-hover:scale-125
                        transition duration-500">

                                <span class="material-symbols-outlined
                            text-[20px]
                            text-blue-600">

                                    tune

                                </span>

                            </div>

                            {{-- text --}}
                            <div class="relative z-10 text-left">

                                <p class="text-[10px]
                            uppercase tracking-[0.25em]
                            text-slate-400
                            mb-0.5">

                                    Smart Filter

                                </p>

                                <p class="text-sm font-black
                            text-slate-700">

                                    Bộ lọc

                                </p>

                            </div>

                            {{-- floating dot --}}
                            <div class="absolute top-2 right-2
                        w-2 h-2 rounded-full
                        bg-cyan-400
                        shadow-[0_0_12px_rgba(34,211,238,.8)]
                        animate-pulse">
                            </div>

                        </button>



                        {{-- DROPDOWN --}}
                        <div id="filterMenu" class="hidden absolute right-0 top-[120%]
                    w-72 rounded-3xl
                    bg-white/95 backdrop-blur-2xl
                    border border-white/50
                    shadow-[0_20px_60px_rgba(15,23,42,.12)]
                    p-3 z-50">

                            {{-- ALL --}}
                            <a href="{{ route('orders.history') }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-slate-100
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-slate-500">
                                    apps
                                </span>

                                <span class="font-medium">
                                    Tất cả đơn hàng
                                </span>

                            </a>



                            {{-- PENDING --}}
                            <a href="{{ route('orders.history', ['status' => 'pending']) }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-yellow-50
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-yellow-500">
                                    schedule
                                </span>

                                <span class="font-medium">
                                    Chờ xác nhận
                                </span>

                            </a>



                            {{-- PROCESSING --}}
                            <a href="{{ route('orders.history', ['status' => 'processing']) }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-violet-50
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-violet-500">
                                    inventory_2
                                </span>

                                <span class="font-medium">
                                    Đang xử lý
                                </span>

                            </a>



                            {{-- SHIPPED --}}
                            <a href="{{ route('orders.history', ['status' => 'shipped']) }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-indigo-50
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-indigo-500">
                                    local_shipping
                                </span>

                                <span class="font-medium">
                                    Đang giao
                                </span>

                            </a>



                            {{-- DELIVERED --}}
                            <a href="{{ route('orders.history', ['status' => 'delivered']) }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-emerald-50
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-emerald-500">
                                    check_circle
                                </span>

                                <span class="font-medium">
                                    Đã giao
                                </span>

                            </a>



                            {{-- CANCELLED --}}
                            <a href="{{ route('orders.history', ['status' => 'cancelled']) }}" class="flex items-center gap-3
                        px-4 py-3 rounded-2xl
                        hover:bg-red-50
                        transition-all duration-300">

                                <span class="material-symbols-outlined text-red-500">
                                    cancel
                                </span>

                                <span class="font-medium">
                                    Đã huỷ
                                </span>

                            </a>

                        </div>

                    </div>

                    {{-- PDF --}}
                    <button class="group relative overflow-hidden
                                    active:scale-95
                                    bg-gradient-to-r
                                    from-[#003366]
                                    via-[#0F5BCC]
                                    to-[#2563EB]
                                    px-7 py-3 rounded-2xl
                                    text-white
                                    shadow-[0_12px_40px_rgba(37,99,235,.28)]
                                    hover:shadow-[0_20px_55px_rgba(37,99,235,.45)]
                                    hover:-translate-y-1
                                    hover:scale-[1.02]
                                    transition-all duration-500
                                    flex items-center gap-3">

                        {{-- animated bg --}}
                        <div class="absolute inset-0
                                        opacity-0 group-hover:opacity-100
                                        bg-gradient-to-r
                                        from-cyan-400/20
                                        via-white/10
                                        to-indigo-400/20
                                        transition duration-500">
                        </div>

                        {{-- shine --}}
                        <div class="absolute inset-0
                                        -translate-x-full
                                        group-hover:translate-x-full
                                        bg-gradient-to-r
                                        from-transparent
                                        via-white/30
                                        to-transparent
                                        transition duration-1000">
                        </div>

                        {{-- icon --}}
                        <div class="relative z-10
                                        w-9 h-9 rounded-xl
                                        bg-white/15 backdrop-blur-xl
                                        flex items-center justify-center
                                        border border-white/10
                                        group-hover:rotate-[20deg]
                                        group-hover:scale-110
                                        transition duration-500">

                            <span class="material-symbols-outlined
                                            text-[20px]">

                                picture_as_pdf

                            </span>

                        </div>

                        {{-- text --}}
                        <div class="relative z-10 text-left">

                            <p class="text-[10px]
                                            uppercase tracking-[0.25em]
                                            text-blue-100">

                                Xuất hoá đơn

                            </p>

                            <p class="font-black leading-none">
                                (In PDF)
                            </p>

                        </div>

                    </button>

                </div>

            </div>

        </header>
        @forelse($orders as $order)

            @php

                $item = $order->items->first();

                if (!$item) {
                    continue;
                }

                $statusText = [

                    'pending' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'processing' => 'Đang chuẩn bị hàng',
                    'shipped' => 'Đang vận chuyển',
                    'delivered' => 'Đã giao hàng',
                    'cancelled' => 'Đã huỷ',

                ];

                $statusColor = [

                    'pending' => 'yellow',
                    'confirmed' => 'sky',
                    'processing' => 'violet',
                    'shipped' => 'indigo',
                    'delivered' => 'emerald',
                    'cancelled' => 'rose',

                ];

                $statusIcon = [

                    'pending' => 'schedule',
                    'confirmed' => 'task_alt',
                    'processing' => 'hourglass_top',
                    'shipped' => 'local_shipping',
                    'delivered' => 'check_circle',
                    'cancelled' => 'cancel',

                ];

                $color = $statusColor[$order->order_status] ?? 'gray';
                $status = $statusText[$order->order_status] ?? 'Không xác định';
                $icon = $statusIcon[$order->order_status] ?? 'info';

            @endphp

            {{-- ĐƠN ĐANG XỬ LÝ --}}
            @if($order->order_status == 'pending')

                <div class="bg-white/80 backdrop-blur-2xl
                                                            rounded-3xl border border-white/50
                                                            shadow-[0_10px_40px_rgba(15,23,42,.05)]
                                                            hover:shadow-[0_20px_60px_rgba(59,130,246,.12)]
                                                            transition-all duration-500
                                                            p-8 flex flex-col md:flex-row
                                                            items-center justify-between gap-6">

                    <div class="flex items-center gap-6">

                        <div class="w-20 h-20 bg-surface-container-high
                                                                    flex items-center justify-center
                                                                    rounded-2xl border border-outline-variant
                                                                    overflow-hidden shadow-md">

                            <img src="{{ asset($item->image_url ?? 'images/no-image.png') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover
                                                                        hover:scale-110 transition duration-700">

                        </div>

                        <div>

                            <div class="flex items-center gap-2 mb-1">

                                <span class="w-2 h-2 rounded-full
                                                                            bg-amber-500 animate-pulse
                                                                            shadow-[0_0_15px_rgba(245,158,11,.6)]">
                                </span>

                                <span class="text-[10px] font-bold
                                                                            text-amber-700 uppercase tracking-widest">

                                    {{ $status }}

                                </span>

                            </div>

                            <h4 class="font-black text-brand-blue text-lg">

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

                            <p class="text-[10px]
                                                                        uppercase tracking-widest
                                                                        text-on-surface-variant">

                                GIÁ TRỊ

                            </p>

                            <p class="font-black text-brand-blue text-xl">

                                {{ number_format($order->total_amount, 0, ',', '.') }}₫

                            </p>

                        </div>

                        <a href="#" class="bg-white border border-outline-variant
                                                                    p-3 rounded-xl hover:bg-slate-50
                                                                    hover:-translate-y-1
                                                                    transition-all duration-300 shadow-sm">

                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>

                        </a>

                    </div>

                </div>

            @else

                {{-- CARD BÌNH THƯỜNG --}}
                <div class="group card-float bg-white/70 backdrop-blur-3xl
                                                            rounded-3xl border border-white/50
                                                            hover:border-blue-300
                                                            hover:shadow-[0_25px_80px_rgba(59,130,246,.18)]
                                                            transition-all duration-500
                                                            overflow-hidden
                                                            shadow-[0_10px_40px_rgba(15,23,42,.05)]
                                                            hover:shadow-[0_20px_60px_rgba(59,130,246,.15)]
                                                            hover:-translate-y-1">

                    <div class="flex flex-col lg:flex-row">

                        <!-- IMAGE -->
                        <div class="relative w-full lg:w-72
                                                                    h-48 lg:h-auto overflow-hidden">

                            @if(!empty($item->image_url))

                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover
                                                                                            group-hover:scale-110
                                                                                            group-hover:rotate-1
                                                                                            transition duration-700 ease-out">

                            @else

                                <div class="w-full h-full flex
                                                                                            items-center justify-center
                                                                                            bg-gray-100">

                                    <span class="material-symbols-outlined
                                                                                                text-5xl text-gray-400">

                                        inventory_2

                                    </span>

                                </div>

                            @endif

                            {{-- OVERLAY --}}
                            <div class="absolute -bottom-20 -right-20
                                                                        w-40 h-40 bg-blue-400/30
                                                                        blur-3xl rounded-full">
                            </div>

                            {{-- CODE --}}
                            <div class="absolute top-4 left-4
                                                                        bg-white/85 backdrop-blur-xl
                                                                        border border-white/40
                                                                        shadow-lg
                                                                        px-3 py-1 rounded-xl
                                                                        text-[10px] font-black
                                                                        tracking-widest
                                                                        text-brand-blue uppercase">

                                MÃ ĐƠN: #{{ $order->order_code }}

                            </div>

                        </div>

                        <!-- CONTENT -->
                        <div class="flex-1 p-8
                                                                    flex flex-col md:flex-row
                                                                    justify-between gap-8">

                            <div class="space-y-4">

                                <div class="flex items-center gap-3">

                                    <span class="material-symbols-outlined
                                                                                text-{{ $color }}-600
                                                                                bg-{{ $color }}-50
                                                                                p-1.5 rounded-full
                                                                                drop-shadow-[0_0_12px_rgba(59,130,246,.25)]
                                                                                float-slow glow-pulse"
                                        style="font-variation-settings: 'FILL' 1;">

                                        {{ $icon }}

                                    </span>

                                    <span class="text-sm font-bold
                                                                                text-{{ $color }}-700
                                                                                tracking-tight">

                                        {{ $status }}

                                    </span>

                                    <span class="text-xs
                                                                                text-on-surface-variant font-medium">

                                        |
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}

                                    </span>

                                </div>

                                <h3 class="text-2xl font-black
                                                                            text-brand-blue tracking-tight
                                                                            group-hover:text-blue-700
                                                                            transition duration-300">

                                    {{ $item->product_name }}

                                </h3>

                                <div class="flex gap-8 flex-wrap">

                                    <div>

                                        <p class="text-[10px]
                                                                                    uppercase tracking-widest
                                                                                    text-on-surface-variant mb-1">

                                            TỔNG CỘNG

                                        </p>

                                        <p class="text-lg font-black text-brand-blue">

                                            {{ number_format($order->total_amount, 0, ',', '.') }}₫

                                        </p>

                                    </div>

                                    <div>

                                        <p class="text-[10px]
                                                                                    uppercase tracking-widest
                                                                                    text-on-surface-variant mb-1">

                                            PHƯƠNG THỨC

                                        </p>

                                        <p class="text-sm font-medium
                                                                                    text-on-surface">

                                            {{ strtoupper($order->payment_method) }}

                                        </p>

                                    </div>

                                </div>

                            </div>

                            <!-- BUTTON -->
                            <div class="flex flex-col justify-center gap-3 min-w-[220px]">

                                {{-- XEM CHI TIẾT --}}
                                <a href="{{ route('orders.detail', $order->order_id) }}" class="relative overflow-hidden
                                                                            shine-effect glow-pulse
                                                                            bg-gradient-to-r
                                                                            from-[#003366] to-[#0F5BCC]
                                                                            text-white px-8 py-3 rounded-xl
                                                                            text-xs font-black
                                                                            tracking-[0.18em]
                                                                            uppercase
                                                                            shadow-lg shadow-blue-500/20
                                                                            hover:shadow-blue-500/40
                                                                            hover:-translate-y-1
                                                                            hover:scale-[1.02]
                                                                            hover:shadow-[0_15px_40px_rgba(59,130,246,.2)]
                                                                            transition-all duration-300
                                                                            text-center">

                                    Xem chi tiết

                                </a>

                                {{-- CHỜ XÁC NHẬN --}}
                                @if(
                                        $order->order_status == 'confirmed' ||
                                        $order->order_status == 'processing' ||
                                        $order->order_status == 'pending'
                                    )

                                    {{-- HUỶ ĐƠN --}}
                                    <button type="button" onclick="openCancelModal(
                                                                                                    '{{ $order->order_id }}',
                                                                                                    '{{ $order->items->first()?->product_name }}'
                                                                                                )" class="border border-red-200
                                                                                                active:scale-95
                                                                                                bg-red-50 text-red-600
                                                                                                px-8 py-3 rounded-xl
                                                                                                text-xs font-bold
                                                                                                tracking-widest uppercase
                                                                                                hover:bg-red-500
                                                                                                hover:text-white
                                                                                                hover:-translate-y-1
                                                                                                hover:shadow-lg
                                                                                                transition-all duration-300">

                                        Huỷ đơn hàng

                                    </button>

                                    {{-- ĐÃ GIAO --}}
                                @elseif($order->order_status == 'delivered')

                                    <form action="{{ route('orders.reorder', $order->order_id) }}" method="POST">

                                        @csrf

                                        <button type="submit" class="border border-emerald-200
                                                                                                    active:scale-95
                                                                                                    bg-emerald-50 text-emerald-600
                                                                                                    px-8 py-3 rounded-xl
                                                                                                    text-xs font-bold
                                                                                                    tracking-widest uppercase
                                                                                                    hover:bg-emerald-500
                                                                                                    hover:text-white
                                                                                                    hover:-translate-y-1
                                                                                                    hover:shadow-lg
                                                                                                    transition-all duration-300">

                                            Mua lại

                                        </button>

                                    </form>

                                    {{-- ĐÃ HUỶ --}}
                                @elseif($order->order_status == 'cancelled')

                                    <form action="{{ route('orders.reorder', $order->order_id) }}" method="POST">

                                        @csrf

                                        <button type="submit" class="border border-emerald-200
                                                                                                    active:scale-95
                                                                                                    bg-emerald-50 text-emerald-600
                                                                                                    px-8 py-3 rounded-xl
                                                                                                    text-xs font-bold
                                                                                                    tracking-widest uppercase
                                                                                                    hover:bg-emerald-500
                                                                                                    hover:text-white
                                                                                                    hover:-translate-y-1
                                                                                                    hover:shadow-lg
                                                                                                    transition-all duration-300">

                                            Mua lại

                                        </button>

                                    </form>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        @empty

            <div class="bg-white rounded-3xl p-14
                                        text-center shadow-sm border">

                <div class="w-28 h-28 rounded-full
                                            bg-blue-50 text-blue-500
                                            flex items-center justify-center
                                            mx-auto mb-8 glow-pulse float-slow">

                    <span class="material-symbols-outlined text-[60px]">
                        inventory_2
                    </span>

                </div>

                <h3 class="text-3xl font-black text-gray-700 mb-3">
                    Chưa có đơn hàng nào
                </h3>

                <p class="text-gray-500">
                    Hãy mua sắm để trải nghiệm hệ thống đơn hàng hiện đại.
                </p>

            </div>

        @endforelse
        <!-- PAGINATION -->

        <div class="mt-14 flex flex-col md:flex-row
                    items-center justify-between gap-6">

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

                    <span class="w-10 h-10 rounded-xl
                                                border bg-gray-100 text-gray-400
                                                flex items-center justify-center
                                                cursor-not-allowed">

                        ←

                    </span>

                @else

                    <a href="{{ $orders->previousPageUrl() }}" class="w-10 h-10 rounded-xl border
                                                hover:bg-blue-50 hover:border-blue-500
                                                flex items-center justify-center
                                                transition">

                        ←

                    </a>

                @endif

                {{-- PAGE --}}
                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)

                    @if ($page == $orders->currentPage())

                        <span class="w-10 h-10 rounded-xl
                                                                    bg-gradient-to-r
                                                                    from-[#003366] to-[#0F5BCC]
                                                                    shadow-[0_10px_30px_rgba(37,99,235,.35)]
                                                                    text-white shadow-lg
                                                                    shadow-blue-500/20
                                                                    font-bold flex items-center justify-center">

                            {{ $page }}

                        </span>

                    @else

                        <a href="{{ $url }}" class="w-10 h-10 rounded-xl border
                                                                    hover:bg-blue-50 hover:border-blue-500
                                                                    flex items-center justify-center
                                                                    transition">

                            {{ $page }}

                        </a>

                    @endif

                @endforeach

                {{-- NEXT --}}
                @if ($orders->hasMorePages())

                    <a href="{{ $orders->nextPageUrl() }}" class="w-10 h-10 rounded-xl border
                                                hover:bg-blue-50 hover:border-blue-500
                                                flex items-center justify-center
                                                transition">

                        →

                    </a>

                @else

                    <span class="w-10 h-10 rounded-xl
                                                border bg-gray-100 text-gray-400
                                                flex items-center justify-center
                                                cursor-not-allowed">

                        →

                    </span>

                @endif

            </div>

        </div>

    </main>
    @if(session('success_order'))

        <div id="successModal" class="success-modal">

            <div class="success-box">

                <div class="success-icon">
                    ✔
                </div>

                <h2>
                    Đặt hàng thành công
                </h2>

                <p>
                    Mã đơn:
                    <b>
                        {{ session('success_order.code') }}
                    </b>
                </p>

                <p>
                    Tổng tiền:
                    <b>
                        {{ number_format(session('success_order.total')) }}đ
                    </b>
                </p>

                <button onclick="closeSuccessModal()">
                    OK
                </button>

            </div>

        </div>

    @endif
    {{-- CANCEL MODAL --}}
    <div id="cancelModal" class="fixed inset-0 z-[9999]
                    hidden items-center justify-center
                    bg-black/50 backdrop-blur-sm">

        <div class="w-full max-w-md
                        rounded-3xl bg-white
                        p-8 shadow-2xl">

            {{-- ICON --}}
            <div class="mx-auto mb-5
                            flex h-20 w-20
                            items-center justify-center
                            rounded-full
                            bg-red-100">

                <i class="fa-solid fa-triangle-exclamation
                                text-4xl text-red-500"></i>

            </div>



            {{-- TITLE --}}
            <h2 class="text-center
                            text-2xl font-black
                            text-slate-800">

                Huỷ đơn hàng

            </h2>



            {{-- CONTENT --}}
            <p class="mt-4 text-center
                            text-slate-500 leading-7">

                Bạn có chắc muốn huỷ mua sản phẩm

                <span id="cancelProductName" class="font-bold text-red-500">
                </span>

                không?

            </p>



            {{-- ACTION --}}
            <div class="mt-8 flex gap-4">

                {{-- KHÔNG --}}
                <button onclick="closeCancelModal()" class="flex-1 rounded-2xl
                                border border-slate-200
                                py-3 font-bold
                                text-slate-600
                                transition hover:bg-slate-100">

                    Không

                </button>



                {{-- CÓ --}}
                <form id="cancelForm" method="POST" class="flex-1">

                    @csrf

                    <button type="submit" class="w-full rounded-2xl
                                    bg-red-500 py-3
                                    font-bold text-white
                                    transition hover:bg-red-600">

                        Có, huỷ đơn

                    </button>

                </form>

            </div>

        </div>

    </div>
    <script>
        function openCancelModal(orderId, productName) {

            document
                .getElementById('cancelModal')
                .classList
                .remove('hidden');



            document
                .getElementById('cancelModal')
                .classList
                .add('flex');



            document
                .getElementById('cancelProductName')
                .innerText = productName;



            document
                .getElementById('cancelForm')
                .action =
                `/orders/cancel/${orderId}`;
        }



        function closeCancelModal() {

            document
                .getElementById('cancelModal')
                .classList
                .remove('flex');



            document
                .getElementById('cancelModal')
                .classList
                .add('hidden');
        }
        function closeSuccessModal() {

            document
                .getElementById(
                    'successModal'
                )
                .style.display = 'none';
        }
        function toggleFilterMenu() {
            document
                .getElementById('filterMenu')
                .classList
                .toggle('hidden');
        }

    </script>
@endsection