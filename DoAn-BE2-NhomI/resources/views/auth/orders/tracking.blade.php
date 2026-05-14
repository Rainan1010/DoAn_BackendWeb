@extends('layouts.app')

@section('content')

<style>

    body {
        font-family: 'Inter', sans-serif;
    }

    .material-symbols-outlined {

        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24;

        display: inline-block;
        vertical-align: middle;
    }

    @keyframes truckMove {

        0%,100% {
            transform: translateX(0px);
        }

        50% {
            transform: translateX(10px);
        }
    }

    @keyframes glowPulse {

        0%,100% {
            box-shadow:
                0 0 0 rgba(59,130,246,0);
        }

        50% {
            box-shadow:
                0 0 30px rgba(59,130,246,.35);
        }
    }

    @keyframes floatCard {

        0%,100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .truck-animation {
        animation: truckMove 2s ease-in-out infinite;
    }

    .glow-pulse {
        animation: glowPulse 3s ease infinite;
    }

    .float-card {
        animation: floatCard 5s ease-in-out infinite;
    }

</style>

@php

    $item = $order->items->first();

@endphp

<div class="fixed top-[-200px] left-[-200px]
w-[400px] h-[400px]
bg-blue-400/20 blur-3xl rounded-full
pointer-events-none">
</div>

<div class="fixed bottom-[-200px] right-[-200px]
w-[400px] h-[400px]
bg-indigo-400/20 blur-3xl rounded-full
pointer-events-none">
</div>

<div class="min-h-screen
bg-gradient-to-br
from-slate-100
via-blue-50
to-indigo-100
py-14">

    <div class="max-w-6xl mx-auto px-6">

        {{-- BACK --}}
        <a href="{{ route('orders.history') }}"
            class="group inline-flex items-center gap-2

            bg-white/80 backdrop-blur-xl
            border border-white/50

            px-5 py-3 rounded-2xl

            shadow-[0_10px_30px_rgba(15,23,42,.08)]

            hover:-translate-y-1
            hover:shadow-[0_15px_40px_rgba(59,130,246,.18)]

            transition-all duration-500 mb-10">

            <span class="material-symbols-outlined
            group-hover:-translate-x-1
            transition duration-300">

                arrow_back

            </span>

            <span class="font-bold">
                Quay lại đơn hàng
            </span>

        </a>

        {{-- HERO --}}
        <div class="relative overflow-hidden

        rounded-[36px]

        bg-gradient-to-r
        from-[#003366]
        via-[#0F5BCC]
        to-[#6D5BFF]

        p-10

        shadow-[0_25px_80px_rgba(37,99,235,.28)]">

            <div class="absolute top-0 right-0
            w-96 h-96
            bg-white/10 blur-3xl rounded-full">
            </div>

            <div class="absolute -bottom-32 -left-32
            w-96 h-96
            bg-cyan-400/10 blur-3xl rounded-full">
            </div>

            <div class="flex flex-col lg:flex-row
            justify-between gap-10 relative z-10">

                <div>

                    <div class="flex items-center gap-2 mb-5">

                        <span class="w-2 h-2 rounded-full
                        bg-emerald-400 animate-pulse">
                        </span>

                        <span class="text-xs
                        uppercase tracking-[0.3em]
                        text-blue-100 font-bold">

                            LIVE TRACKING

                        </span>

                    </div>

                    <h1 class="text-5xl lg:text-6xl
                    font-black text-white mb-5">

                        #{{ $order->order_code }}

                    </h1>

                    <div class="flex flex-wrap items-center gap-4
                    text-blue-100 font-medium">

                        <span>
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                        </span>

                        <span>•</span>

                        <span>
                            {{ strtoupper($order->payment_method) }}
                        </span>

                    </div>

                </div>

                <div class="text-right">

                    <div
                        class="inline-flex items-center gap-2

                        bg-white/10 backdrop-blur-xl

                        px-5 py-3 rounded-2xl

                        border border-white/10 mb-5">

                        <span class="material-symbols-outlined
                        truck-animation text-white">

                            local_shipping

                        </span>

                        <span class="font-black text-white">

                            Đang vận chuyển

                        </span>

                    </div>

                    <p class="text-blue-100
                    uppercase tracking-[0.25em]
                    text-sm mb-2">

                        Tổng thanh toán

                    </p>

                    <h2 class="text-4xl lg:text-5xl
                    font-black text-white">

                        {{ number_format($order->total_amount,0,',','.') }}₫

                    </h2>

                </div>

            </div>

        </div>

        {{-- TIMELINE --}}
        <div class="mt-10

        bg-white/80 backdrop-blur-2xl

        rounded-[32px]

        border border-white/50

        p-10

        shadow-[0_15px_50px_rgba(15,23,42,.08)]">

            <div class="flex items-center justify-between
            relative">

                {{-- LINE --}}
                <div class="absolute top-8 left-0
                w-full h-2

                bg-slate-200 rounded-full">

                    <div
                        class="h-full w-[75%]

                        bg-gradient-to-r
                        from-blue-500
                        to-indigo-600

                        rounded-full

                        shadow-[0_0_25px_rgba(59,130,246,.45)]

                        glow-pulse">
                    </div>

                </div>

                {{-- STEP --}}
                <div class="relative z-10 text-center">

                    <div class="w-16 h-16 rounded-2xl
                    bg-blue-500 text-white

                    flex items-center justify-center

                    shadow-lg">

                        <span class="material-symbols-outlined">
                            shopping_bag
                        </span>

                    </div>

                    <p class="mt-3 text-sm font-bold">
                        Đặt hàng
                    </p>

                </div>

                <div class="relative z-10 text-center">

                    <div class="w-16 h-16 rounded-2xl
                    bg-emerald-500 text-white

                    flex items-center justify-center

                    shadow-lg">

                        <span class="material-symbols-outlined">
                            inventory_2
                        </span>

                    </div>

                    <p class="mt-3 text-sm font-bold">
                        Xác nhận
                    </p>

                </div>

                <div class="relative z-10 text-center">

                    <div class="w-16 h-16 rounded-2xl

                    bg-gradient-to-br
                    from-indigo-500
                    to-indigo-700

                    text-white

                    flex items-center justify-center

                    shadow-[0_0_30px_rgba(79,70,229,.5)]

                    truck-animation">

                        <span class="material-symbols-outlined">
                            local_shipping
                        </span>

                    </div>

                    <p class="mt-3 text-sm font-bold">
                        Vận chuyển
                    </p>

                </div>

                <div class="relative z-10 text-center">

                    <div class="w-16 h-16 rounded-2xl
                    bg-slate-200 text-slate-500

                    flex items-center justify-center">

                        <span class="material-symbols-outlined">
                            check_circle
                        </span>

                    </div>

                    <p class="mt-3 text-sm font-bold">
                        Hoàn thành
                    </p>

                </div>

            </div>

        </div>

        {{-- INFO GRID --}}
        <div class="grid lg:grid-cols-3 gap-8 mt-10">

            {{-- PRODUCT --}}
            <div class="lg:col-span-2">

                <div class="float-card

                bg-white/80 backdrop-blur-2xl

                rounded-[32px]

                border border-white/50

                p-8

                shadow-[0_15px_50px_rgba(15,23,42,.08)]

                hover:-translate-y-1
                hover:shadow-[0_20px_60px_rgba(59,130,246,.12)]

                transition-all duration-500">

                    <div class="flex items-center gap-6">

                        <div class="group
                        w-32 h-32 rounded-3xl
                        overflow-hidden border">

                            <img
                                src="{{ asset($item->image_url ?? 'images/no-image.png') }}"
                                class="w-full h-full object-cover

                                group-hover:scale-110

                                transition duration-700">
                        </div>

                        <div class="flex-1">

                            <h3 class="text-3xl font-black
                            text-[#003366] mb-3">

                                {{ $item->product_name }}

                            </h3>

                            <div class="flex flex-wrap gap-8">

                                <div>

                                    <p class="text-xs uppercase
                                    tracking-widest text-slate-400 mb-1">

                                        Mã đơn

                                    </p>

                                    <p class="font-bold">

                                        #{{ $order->order_code }}

                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs uppercase
                                    tracking-widest text-slate-400 mb-1">

                                        Trạng thái

                                    </p>

                                    <p class="font-bold text-indigo-600">

                                        Đang vận chuyển

                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs uppercase
                                    tracking-widest text-slate-400 mb-1">

                                        Dự kiến giao

                                    </p>

                                    <p class="font-bold">

                                        {{ now()->addDays(2)->format('d/m/Y') }}

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- SHIPPING ACTIVITY --}}
                <div class="mt-8

                float-card

                bg-white/80 backdrop-blur-2xl

                rounded-[32px]

                border border-white/50

                p-8

                shadow-[0_15px_50px_rgba(15,23,42,.08)]">

                    <h3 class="text-2xl font-black
                    text-[#003366] mb-8">

                        Hoạt động vận chuyển

                    </h3>

                    <div class="space-y-6">

                        <div class="flex gap-4 items-start">

                            <div class="w-2 h-2 rounded-full
                            bg-blue-500 mt-2
                            shadow-[0_0_12px_rgba(59,130,246,.6)]">
                            </div>

                            <div>

                                <p class="font-bold text-slate-800">

                                    Đơn hàng đã rời kho

                                </p>

                                <p class="text-sm text-slate-500">

                                    {{ now()->subHours(5)->format('d/m/Y H:i') }}

                                </p>

                            </div>

                        </div>

                        <div class="flex gap-4 items-start">

                            <div class="w-2 h-2 rounded-full
                            bg-emerald-500 mt-2
                            shadow-[0_0_12px_rgba(16,185,129,.6)]">
                            </div>

                            <div>

                                <p class="font-bold text-slate-800">

                                    Shipper đã nhận hàng

                                </p>

                                <p class="text-sm text-slate-500">

                                    {{ now()->subHours(2)->format('d/m/Y H:i') }}

                                </p>

                            </div>

                        </div>

                        <div class="flex gap-4 items-start">

                            <div class="w-2 h-2 rounded-full
                            bg-indigo-500 mt-2
                            shadow-[0_0_12px_rgba(99,102,241,.6)]">
                            </div>

                            <div>

                                <p class="font-bold text-slate-800">

                                    Đang giao đến bạn

                                </p>

                                <p class="text-sm text-slate-500">

                                    Dự kiến trong hôm nay

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- PAYMENT --}}
            <div>

                <div class="relative overflow-hidden

                float-card

                bg-gradient-to-br
                from-[#003366]
                via-[#0F5BCC]
                to-[#2563EB]

                text-white

                p-8 rounded-[32px]

                shadow-[0_25px_80px_rgba(37,99,235,.28)]">

                    <div class="absolute -top-20 -right-20
                    w-56 h-56 rounded-full
                    bg-white/10 blur-3xl">
                    </div>

                    <div class="relative z-10">

                        <h3 class="text-2xl font-black mb-8">

                            Thanh toán

                        </h3>

                        <div class="space-y-5">

                            <div class="flex justify-between">

                                <span class="text-blue-100">
                                    Tạm tính
                                </span>

                                <span class="font-bold">
                                    {{ number_format($order->total_amount - 30000,0,',','.') }}₫
                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span class="text-blue-100">
                                    Phí vận chuyển
                                </span>

                                <span class="font-bold">
                                    30.000₫
                                </span>

                            </div>

                            <div class="border-t border-white/20 pt-5
                            flex justify-between text-xl font-black">

                                <span>Tổng cộng</span>

                                <span>
                                    {{ number_format($order->total_amount,0,',','.') }}₫
                                </span>

                            </div>

                        </div>

                        <button
                            class="w-full mt-8

                            bg-white text-[#003366]

                            py-4 rounded-2xl

                            font-black

                            hover:-translate-y-1
                            hover:shadow-xl

                            active:scale-95

                            transition-all duration-300">

                            Liên hệ hỗ trợ

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection