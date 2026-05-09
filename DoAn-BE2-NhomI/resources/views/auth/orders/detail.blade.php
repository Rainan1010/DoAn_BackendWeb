@extends('layouts.app')

@section('content')

    <style>
        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes breathe {

            0%,
            100% {
                box-shadow:
                    0 0 20px rgba(59, 130, 246, .25),
                    0 0 40px rgba(59, 130, 246, .15);
            }

            50% {
                box-shadow:
                    0 0 45px rgba(59, 130, 246, .55),
                    0 0 80px rgba(59, 130, 246, .25);
            }
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes energyFlow {

            from {
                transform: translateX(-120px);
            }

            to {
                transform: translateX(1200px);
            }
        }

        @keyframes blobMorph {

            0% {
                border-radius: 40% 60% 70% 30%;
                transform: translate(0px, 0px) rotate(0deg);
            }

            50% {
                border-radius: 70% 30% 40% 60%;
                transform: translate(30px, -20px) rotate(180deg);
            }

            100% {
                border-radius: 40% 60% 70% 30%;
                transform: translate(0px, 0px) rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spinSlow 7s linear infinite;
        }

        .animate-breathe {
            animation: breathe 3s ease-in-out infinite;
        }

        .animate-floating {
            animation: floating 4s ease-in-out infinite;
        }

        .blob {
            animation: blobMorph 18s ease-in-out infinite;
        }

        .energy-line::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 140px;
            height: 100%;
            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .9),
                    transparent);

            animation: energyFlow 2.4s linear infinite;
        }

        @keyframes shimmer {

            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        @keyframes softPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.08);
                opacity: .8;
            }
        }

        .shimmer-text {

            background: linear-gradient(90deg,
                    #fff,
                    #dbeafe,
                    #fff);

            background-size: 200% auto;

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            animation: shimmer 3s linear infinite;
        }

        .soft-pulse {
            animation: softPulse 2s infinite;
        }
    </style>

    @php

        $statusText = [
            'pending' => 'Đang chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
            'cancelled' => 'Đã huỷ',
        ];

        $statusColor = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'orange',
            'shipping' => 'indigo',
            'delivered' => 'emerald',
            'cancelled' => 'red',
        ];

        $status = $statusText[$order->order_status] ?? 'Không xác định';

        $color = $statusColor[$order->order_status] ?? 'gray';

        $steps = [
            'pending' => 1,
            'confirmed' => 2,
            'processing' => 2,
            'shipping' => 3,
            'delivered' => 4,
        ];

        $currentStep = $steps[$order->order_status] ?? 1;

    @endphp

    <main
        class="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe,_#eff6ff,_white)] pt-14 pb-24 px-4 overflow-hidden">

        {{-- BLOBS --}}

        <div class="fixed top-0 left-0 w-[500px] h-[500px] bg-cyan-300/20 blur-3xl blob pointer-events-none"></div>

        <div class="fixed bottom-0 right-0 w-[500px] h-[500px] bg-indigo-400/20 blur-3xl blob pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">

            {{-- BACK --}}

            <a href="{{ route('orders.history') }}"
                class="inline-flex items-center gap-3 bg-white/70 backdrop-blur-xl border border-white/50 hover:border-blue-400 hover:bg-white px-6 py-4 rounded-2xl shadow-lg hover:shadow-blue-200/50 transition-all duration-500 font-bold text-slate-700 mb-8 group">

                <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">
                    arrow_back
                </span>

                Quay lại đơn hàng

            </a>

            {{-- HERO --}}

            <div
                class="relative rounded-[32px] overflow-hidden shadow-[0_20px_70px_rgba(37,99,235,.22)] mb-8 max-w-6xl mx-auto">

                {{-- GLOW --}}

                <div class="relative w-full rounded-[30px] overflow-hidden
                bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500
                shadow-[0_20px_60px_rgba(59,130,246,.25)]
                mb-8">

                    {{-- glow --}}
                    <div class="absolute -top-20 -left-20 w-72 h-72 bg-cyan-300/10 rounded-full blur-3xl"></div>

                    <div class="absolute bottom-0 right-0 w-72 h-72 bg-violet-300/10 rounded-full blur-3xl"></div>

                    {{-- grid effect --}}
                    <div class="absolute inset-0 opacity-[0.06]" style="
                        background-image:
                        linear-gradient(to right, white 1px, transparent 1px),
                        linear-gradient(to bottom, white 1px, transparent 1px);
                        background-size: 32px 32px;
                    ">
                    </div>

                    <div class="relative px-8 md:px-10 py-7">

                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">

                            {{-- LEFT --}}
                            <div>

                                <div class="inline-flex items-center gap-2
                                bg-white/10 border border-white/20
                                px-4 py-2 rounded-full mb-5 backdrop-blur-xl">

                                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>

                                    <span class="uppercase tracking-[3px]
                                    text-[10px] font-semibold text-white/90">
                                        Smart Order Tracking
                                    </span>

                                </div>

                                <h1 class="text-[52px] md:text-[64px]
                                font-black leading-none tracking-tight text-white">

                                    #{{ $order->order_code }}

                                </h1>

                                <div class="mt-4 flex flex-wrap items-center gap-3 text-white/80">

                                    <span class="text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                    </span>

                                    <span class="w-1.5 h-1.5 rounded-full bg-white/50"></span>

                                    {{-- payment --}}
                                    <div class="px-3 py-1 rounded-full
                                    bg-emerald-400/20 text-emerald-100
                                    border border-emerald-200/20
                                    text-xs font-semibold uppercase tracking-wide">

                                        {{ $order->payment_method }}

                                    </div>

                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="lg:text-right">

                                {{-- status --}}
                                <div class="inline-flex items-center gap-2
                                px-5 py-2 rounded-2xl
                                bg-white/12 border border-white/15
                                backdrop-blur-xl
                                shadow-lg mb-5">

                                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>

                                    <span class="text-white text-sm font-semibold">
                                        {{ $status }}
                                    </span>

                                </div>

                                <p class="uppercase tracking-[4px]
                                text-[11px] text-white/60 mb-2">

                                    Tổng thanh toán

                                </p>

                                <h2 class="text-4xl md:text-5xl
                                font-black text-white leading-none">

                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫

                                </h2>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- TIMELINE --}}

                <div
                    class="bg-white/70 backdrop-blur-2xl rounded-[40px] p-8 md:p-10 shadow-[0_20px_80px_rgba(0,0,0,.06)] border border-white/50 mb-10 relative overflow-hidden">

                    <div class="absolute inset-0 opacity-50">

                        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-100 rounded-full blur-3xl"></div>

                    </div>

                    <div class="relative">

                        {{-- BASE LINE --}}

                        <div class="absolute top-8 left-0 w-full h-2 bg-slate-200 rounded-full"></div>

                        {{-- ACTIVE LINE --}}

                        <div class="absolute top-8 left-0 h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 rounded-full transition-all duration-1000 ease-out shadow-[0_0_25px_rgba(59,130,246,.7)] energy-line overflow-hidden"
                            style="
                                    width:
                                    {{ $currentStep == 1 ? '12%' : '' }}
                                    {{ $currentStep == 2 ? '40%' : '' }}
                                    {{ $currentStep == 3 ? '68%' : '' }}
                                    {{ $currentStep == 4 ? '100%' : '' }}
                                "></div>

                        <div class="relative grid grid-cols-2 md:grid-cols-4 gap-8">

                            @php

                                $timeline = [
                                    [
                                        'title' => 'Đặt hàng',
                                        'icon' => 'shopping_bag',
                                        'color' => 'blue',
                                        'step' => 1,
                                    ],
                                    [
                                        'title' => 'Xác nhận',
                                        'icon' => 'inventory_2',
                                        'color' => 'emerald',
                                        'step' => 2,
                                    ],
                                    [
                                        'title' => 'Vận chuyển',
                                        'icon' => 'local_shipping',
                                        'color' => 'indigo',
                                        'step' => 3,
                                    ],
                                    [
                                        'title' => 'Hoàn thành',
                                        'icon' => 'task_alt',
                                        'color' => 'amber',
                                        'step' => 4,
                                    ],
                                ];

                            @endphp

                            @foreach($timeline as $time)

                                                <div class="flex flex-col items-center relative z-10">

                                                    <div class="relative">

                                                        @if($currentStep == $time['step'])

                                                            <div
                                                                class="absolute inset-0 rounded-full border-2 border-cyan-400/40 animate-spin-slow">
                                                            </div>

                                                            <div class="absolute inset-[-8px] rounded-full border border-blue-400/20"></div>

                                                        @endif

                                                        <div class="
                                                                                                            w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center
                                                                                                            transition-all duration-500 shadow-2xl relative z-10

                                                                                                            {{ $currentStep >= $time['step']
                                ? 'bg-' . $time['color'] . '-500 text-white scale-110 animate-breathe'
                                : 'bg-slate-200 text-slate-400'
                                                                                                            }}

                                                                                                            {{ $currentStep == $time['step']
                                ? 'animate-floating'
                                : ''
                                                                                                            }}
                                                                                                        ">

                                                            <span class="material-symbols-outlined text-3xl">
                                                                {{ $time['icon'] }}
                                                            </span>

                                                        </div>

                                                    </div>

                                                    <h3 class="mt-5 font-black text-slate-800 text-center">

                                                        {{ $time['title'] }}

                                                    </h3>

                                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>
                {{-- SMART STATUS --}}

                <div class="mb-8 grid md:grid-cols-3 gap-5">

                    <div class="rounded-[28px] bg-white/75 backdrop-blur-2xl
                border border-white/50 shadow-lg p-6">

                        <div class="flex items-center gap-4">

                            <div class="w-14 h-14 rounded-2xl
                        bg-blue-100 text-blue-600
                        flex items-center justify-center">

                                <span class="material-symbols-outlined">
                                    package_2
                                </span>

                            </div>

                            <div>

                                <p class="text-slate-400 text-sm mb-1">
                                    Trạng thái hiện tại
                                </p>

                                <h3 class="font-black text-slate-800 text-lg">
                                    {{ $status }}
                                </h3>

                            </div>

                        </div>

                    </div>

                    <div class="rounded-[28px] bg-white/75 backdrop-blur-2xl
                border border-white/50 shadow-lg p-6">

                        <div class="flex items-center gap-4">

                            <div class="w-14 h-14 rounded-2xl
                        bg-emerald-100 text-emerald-600
                        flex items-center justify-center">

                                <span class="material-symbols-outlined">
                                    schedule
                                </span>

                            </div>

                            <div>

                                <p class="text-slate-400 text-sm mb-1">
                                    Dự kiến giao
                                </p>

                                <h3 class="font-black text-slate-800 text-lg">
                                    2 - 3 ngày
                                </h3>

                            </div>

                        </div>

                    </div>

                    <div class="rounded-[28px] bg-white/75 backdrop-blur-2xl
                border border-white/50 shadow-lg p-6">

                        <div class="flex items-center gap-4">

                            <div class="w-14 h-14 rounded-2xl
                        bg-violet-100 text-violet-600
                        flex items-center justify-center">

                                <span class="material-symbols-outlined">
                                    verified_user
                                </span>

                            </div>

                            <div>

                                <p class="text-slate-400 text-sm mb-1">
                                    Bảo mật
                                </p>

                                <h3 class="font-black text-slate-800 text-lg">
                                    SSL Protected
                                </h3>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- PRODUCTS --}}

                <div class="space-y-8 mb-10">

                    @foreach($order->items as $item)

                        <div
                            class="group relative bg-white/75 backdrop-blur-2xl rounded-[40px] border border-white/50 overflow-hidden shadow-[0_20px_80px_rgba(0,0,0,.06)] hover:-translate-y-1 hover:shadow-[0_30px_100px_rgba(0,0,0,.1)] transition-all duration-500">

                            {{-- LIGHT EFFECT --}}

                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/[0.03] to-indigo-500/[0.03] opacity-0 group-hover:opacity-100 transition duration-500">
                            </div>

                            <div class="relative p-8 md:p-10 flex flex-col xl:flex-row gap-8 justify-between">

                                {{-- LEFT --}}

                                <div class="flex flex-col md:flex-row gap-8 flex-1">

                                    {{-- IMAGE --}}

                                    <div
                                        class="w-full md:w-56 h-56 rounded-[30px] overflow-hidden bg-slate-100 shadow-2xl shrink-0 animate-floating">

                                        @if($item->image_url)

                                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->product_name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">

                                        @else

                                            <div class="w-full h-full flex items-center justify-center">

                                                <span class="material-symbols-outlined text-8xl text-slate-300">
                                                    inventory_2
                                                </span>

                                            </div>

                                        @endif

                                    </div>

                                    {{-- INFO --}}

                                    <div class="flex-1 flex flex-col justify-between">

                                        <div>

                                            <h2 class="text-3xl md:text-4xl font-black text-slate-800 leading-tight mb-5">

                                                {{ $item->product_name }}

                                            </h2>

                                            @if($item->variant_info)

                                                <div class="inline-flex flex-wrap gap-3 mb-6">

                                                    <div
                                                        class="px-4 py-3 rounded-2xl bg-slate-100/80 backdrop-blur text-slate-700 font-semibold shadow-sm">

                                                        {{ $item->variant_info }}

                                                    </div>

                                                    @if($order->discount_amount > 0)

                                                        <div
                                                            class="px-4 py-3 rounded-2xl bg-red-50 text-red-500 font-bold flex items-center gap-2 shadow-sm">

                                                            <span class="material-symbols-outlined text-lg">
                                                                local_offer
                                                            </span>

                                                            Giảm giá

                                                        </div>

                                                    @endif

                                                </div>

                                            @endif

                                        </div>

                                        <div class="flex flex-wrap gap-4">

                                            <div class="bg-blue-50 text-blue-700 px-5 py-4 rounded-2xl font-black shadow-sm">

                                                SL: {{ $item->quantity }}

                                            </div>

                                            <div class="bg-slate-100 text-slate-700 px-5 py-4 rounded-2xl font-black shadow-sm">

                                                {{ number_format($item->unit_price, 0, ',', '.') }}₫

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                {{-- PRICE --}}

                                <div class="xl:w-80 rounded-[34px] overflow-hidden shadow-[0_25px_80px_rgba(15,23,42,.4)]">

                                    <div
                                        class="h-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8 flex flex-col justify-center relative overflow-hidden">

                                        <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/5 rounded-full blur-3xl">
                                        </div>

                                        <div class="relative z-10">

                                            <p class="uppercase tracking-[5px] text-slate-400 text-sm mb-4">
                                                Thành tiền
                                            </p>

                                            <h3 class="text-4xl md:text-5xl font-black text-white break-all leading-tight">

                                                {{ number_format($item->subtotal, 0, ',', '.') }}₫

                                            </h3>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>
                {{-- RECEIVER INFO --}}

                <div class="rounded-[34px] bg-white/75 backdrop-blur-2xl
    border border-white/50 shadow-lg p-7 mb-8">

                    <div class="flex items-center gap-4 mb-6">

                        <div class="w-14 h-14 rounded-2xl
            bg-cyan-100 text-cyan-600
            flex items-center justify-center">

                            <span class="material-symbols-outlined">
                                location_on
                            </span>

                        </div>

                        <div>

                            <p class="text-slate-400 text-sm">
                                Receiver Information
                            </p>

                            <h2 class="text-2xl font-black text-slate-800">
                                Thông tin nhận hàng
                            </h2>

                        </div>

                    </div>

                    <div class="grid md:grid-cols-3 gap-5">

                        {{-- NAME --}}

                        <div class="bg-slate-50 rounded-2xl p-5">

                            <p class="text-slate-400 text-sm mb-2">
                                Người nhận
                            </p>

                            <h3 class="font-black text-slate-800 text-lg">

                                {{ $shippingAddress->full_name ?? 'Chưa cập nhật' }}

                            </h3>

                        </div>

                        {{-- PHONE --}}

                        <div class="bg-slate-50 rounded-2xl p-5">

                            <p class="text-slate-400 text-sm mb-2">
                                Số điện thoại
                            </p>

                            <h3 class="font-black text-slate-800 text-lg">

                                {{ $shippingAddress->phone ?? 'Chưa cập nhật' }}

                            </h3>

                        </div>

                        {{-- ADDRESS --}}

                        <div class="bg-slate-50 rounded-2xl p-5">

                            <p class="text-slate-400 text-sm mb-2">
                                Địa chỉ
                            </p>

                            <h3 class="font-black text-slate-800 leading-7">

                                {{ $shippingAddress->street_address ?? '' }}

                                @if(!empty($shippingAddress->ward))
                                    , {{ $shippingAddress->ward }}
                                @endif

                                @if(!empty($shippingAddress->district))
                                    , {{ $shippingAddress->district }}
                                @endif

                                @if(!empty($shippingAddress->province))
                                    , {{ $shippingAddress->province }}
                                @endif

                            </h3>

                        </div>

                    </div>

                </div>
                {{-- INFO + PAYMENT --}}

                <div class="grid lg:grid-cols-2 gap-6 mb-10">

                    {{-- ORDER INFO --}}

                    <div
                        class="relative overflow-hidden rounded-[34px] bg-white/75 backdrop-blur-2xl border border-white/60 shadow-[0_15px_60px_rgba(15,23,42,.06)] p-7 hover:-translate-y-1 transition duration-500 group">

                        {{-- SHINE --}}

                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-700">

                            <div
                                class="absolute -left-40 top-0 w-40 h-full bg-gradient-to-r from-transparent via-white/40 to-transparent rotate-12 animate-[energyFlow_2s_linear_infinite]">
                            </div>

                        </div>

                        {{-- GLOW --}}

                        <div class="absolute -top-20 -right-20 w-56 h-56 bg-blue-200/30 rounded-full blur-3xl"></div>

                        <div class="relative z-10">

                            {{-- HEADER --}}

                            <div class="flex items-center gap-4 mb-7">

                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 text-white flex items-center justify-center shadow-lg">

                                    <span class="material-symbols-outlined text-2xl">
                                        receipt_long
                                    </span>

                                </div>

                                <div>

                                    <p class="uppercase tracking-[3px] text-[11px] text-slate-400 mb-1">
                                        ORDER INFO
                                    </p>

                                    <h2 class="text-2xl font-black text-slate-800">
                                        Thông tin đơn hàng
                                    </h2>

                                </div>

                            </div>

                            {{-- CONTENT --}}

                            <div class="space-y-4">

                                <div class="flex justify-between items-center bg-slate-50/80 rounded-2xl px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Mã đơn
                                    </span>

                                    <span class="font-black text-slate-800">
                                        #{{ $order->order_code }}
                                    </span>

                                </div>

                                <div class="flex justify-between items-center bg-slate-50/80 rounded-2xl px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Ngày đặt
                                    </span>

                                    <span class="font-bold text-slate-800 text-sm">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                    </span>

                                </div>

                                <div class="flex justify-between items-center bg-slate-50/80 rounded-2xl px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Thanh toán
                                    </span>

                                    <span class="uppercase font-black text-blue-600">
                                        {{ $order->payment_method }}
                                    </span>

                                </div>

                                <div class="flex justify-between items-center bg-slate-50/80 rounded-2xl px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Trạng thái
                                    </span>

                                    <div
                                        class="px-4 py-2 rounded-xl bg-{{ $color }}-100 text-{{ $color }}-700 font-bold text-sm">

                                        {{ $status }}

                                    </div>

                                </div>

                            </div>

                            {{-- MINI STATS --}}

                            <div class="grid grid-cols-3 gap-3 mt-6">

                                <div class="rounded-2xl bg-blue-50 p-4 text-center">

                                    <p class="text-xs text-slate-500 mb-1">
                                        Sản phẩm
                                    </p>

                                    <h3 class="text-xl font-black text-blue-600">

                                        {{ $order->items->count() }}

                                    </h3>

                                </div>

                                <div class="rounded-2xl bg-emerald-50 p-4 text-center">

                                    <p class="text-xs text-slate-500 mb-1">
                                        Voucher
                                    </p>

                                    <h3 class="text-xl font-black text-emerald-600">

                                        {{ $order->discount_amount > 0 ? 'Có' : 'Không' }}

                                    </h3>

                                </div>

                                <div class="rounded-2xl bg-orange-50 p-4 text-center">

                                    <p class="text-xs text-slate-500 mb-1">
                                        Phí ship
                                    </p>

                                    <h3 class="text-lg font-black text-orange-500">

                                        {{ number_format($order->shipping_fee, 0, ',', '.') }}₫

                                    </h3>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- PAYMENT --}}

                    <div
                        class="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-[#eff6ff] via-white to-[#eef2ff] border border-white/60 shadow-[0_15px_60px_rgba(15,23,42,.06)] p-7 hover:-translate-y-1 transition duration-500 group">

                        {{-- LIGHT --}}

                        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-200/40 rounded-full blur-3xl"></div>

                        <div class="absolute top-0 right-0 w-56 h-56 bg-cyan-100/50 rounded-full blur-3xl"></div>

                        {{-- FLOATING ORB --}}

                        <div
                            class="absolute top-6 right-6 w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 opacity-20 blur-xl animate-pulse">
                        </div>

                        <div class="relative z-10">

                            {{-- HEADER --}}

                            <div class="flex items-center gap-4 mb-7">

                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center shadow-lg">

                                    <span class="material-symbols-outlined text-2xl">
                                        account_balance_wallet
                                    </span>

                                </div>

                                <div>

                                    <p class="uppercase tracking-[3px] text-[11px] text-slate-400 mb-1">
                                        PAYMENT
                                    </p>

                                    <h2 class="text-2xl font-black text-slate-800">
                                        Thanh toán
                                    </h2>

                                </div>

                            </div>

                            {{-- PRICE LIST --}}

                            <div class="space-y-4">

                                <div class="flex justify-between items-center rounded-2xl bg-white/70 px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Tạm tính
                                    </span>

                                    <span class="font-black text-slate-800">

                                        {{ number_format($order->subtotal, 0, ',', '.') }}₫

                                    </span>

                                </div>

                                <div class="flex justify-between items-center rounded-2xl bg-white/70 px-5 py-4">

                                    <span class="text-slate-500 text-sm">
                                        Vận chuyển
                                    </span>

                                    <span class="font-black text-slate-800">

                                        {{ number_format($order->shipping_fee, 0, ',', '.') }}₫

                                    </span>

                                </div>

                                <div class="flex justify-between items-center rounded-2xl bg-red-50 px-5 py-4">

                                    <span class="text-red-400 text-sm">
                                        Giảm giá
                                    </span>

                                    <span class="font-black text-red-500">

                                        -{{ number_format($order->discount_amount, 0, ',', '.') }}₫

                                    </span>

                                </div>

                            </div>

                            {{-- TOTAL CARD --}}

                            <div
                                class="mt-6 relative overflow-hidden rounded-[28px] bg-gradient-to-r from-blue-500 via-indigo-500 to-violet-500 p-6 shadow-[0_15px_50px_rgba(59,130,246,.35)]">

                                {{-- LIGHT EFFECT --}}

                                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>

                                <div class="absolute bottom-0 left-0 w-full h-[2px] bg-white/30"></div>

                                <div class="relative z-10">

                                    <p class="uppercase tracking-[4px] text-white/70 text-xs mb-3">
                                        TOTAL PAYMENT
                                    </p>

                                    <h2 class="text-4xl md:text-5xl font-black text-white leading-tight">

                                        {{ number_format($order->total_amount, 0, ',', '.') }}₫

                                    </h2>

                                </div>

                            </div>

                            {{-- SECURITY --}}

                            <div class="mt-5 flex items-center gap-3 text-slate-500 text-sm">

                                <span class="material-symbols-outlined text-green-500">
                                    verified_user
                                </span>

                                Thanh toán được bảo mật & mã hoá an toàn

                            </div>

                        </div>

                    </div>
                    {{-- QR TRACKING --}}

                    <div class="mt-8 rounded-[34px]
        bg-white/75 backdrop-blur-2xl
        border border-white/50 shadow-lg p-8">

                        <div class="flex flex-col md:flex-row items-center justify-between gap-8">

                            <div>

                                <p class="uppercase tracking-[4px]
                    text-[11px] text-slate-400 mb-3">

                                    SMART TRACKING

                                </p>

                                <h2 class="text-3xl font-black text-slate-800 mb-4">
                                    Theo dõi đơn hàng realtime
                                </h2>

                                <p class="text-slate-500 leading-8 max-w-xl">

                                    Quét mã QR để xem trạng thái đơn hàng,
                                    vị trí giao hàng và thời gian dự kiến.

                                </p>

                            </div>

                            <div class="w-44 h-44 rounded-[28px]
                    bg-gradient-to-br from-blue-500 to-indigo-600
                    p-4 shadow-2xl">

                                <div class="w-full h-full rounded-[22px]
                        bg-white flex items-center justify-center">

                                    <span class="material-symbols-outlined text-[90px] text-slate-800">
                                        qr_code_2
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

    </main>
    {{-- FLOAT ACTIONS --}}

    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-4">

        <button class="w-14 h-14 rounded-full
                bg-gradient-to-r from-blue-500 to-indigo-500
                text-white shadow-2xl
                hover:scale-110 transition duration-300
                flex items-center justify-center">

            <span class="material-symbols-outlined">
                support_agent
            </span>

        </button>

        <button class="w-14 h-14 rounded-full
                bg-gradient-to-r from-emerald-500 to-green-500
                text-white shadow-2xl
                hover:scale-110 transition duration-300
                flex items-center justify-center">

            <span class="material-symbols-outlined">
                local_shipping
            </span>

        </button>

    </div>
@endsection