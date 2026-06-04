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
            'shipped' => 'Đang giao hàng',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
            'completed' => 'Đã giao thành công',
            'cancelled' => 'Đã huỷ',
        ];

        $statusColor = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'orange',
            'shipped' => 'indigo',
            'shipping' => 'indigo',
            'delivered' => 'emerald',
            'completed' => 'emerald',
            'cancelled' => 'red',
        ];

        $status = $statusText[$order->order_status] ?? 'Không xác định';

        $color = $statusColor[$order->order_status] ?? 'gray';

        $steps = [
            'pending' => 1,
            'confirmed' => 2,
            'processing' => 2,
            'shipped' => 3,
            'shipping' => 3,
            'delivered' => 4,
            'completed' => 4,
        ];

        $currentStep = $steps[$order->order_status] ?? 1;

    @endphp

    <main
        class="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe,_#eff6ff,_white)] pt-14 pb-24 px-4 overflow-hidden">

        {{-- BLOBS --}}

        <div class="fixed top-0 left-0 w-[500px] h-[500px] bg-cyan-300/20 blur-3xl blob pointer-events-none"></div>

        <div class="fixed bottom-0 right-0 w-[500px] h-[500px] bg-indigo-400/20 blur-3xl blob pointer-events-none"></div>

        <div class="max-w-5xl mx-auto relative z-10">

            {{-- BACK --}}

            <a href="{{ route('orders.history') }}"
                class="inline-flex items-center gap-3 bg-white/70 backdrop-blur-xl border border-white/50 hover:border-blue-400 hover:bg-white px-6 py-3.5 rounded-2xl shadow-lg hover:shadow-blue-200/50 transition-all duration-500 font-bold text-slate-700 mb-8 group">

                <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">
                    arrow_back
                </span>

                Quay lại đơn hàng

            </a>

            {{-- HERO --}}

            <div class="relative w-full rounded-3xl overflow-hidden
            bg-gradient-to-br from-[#003366] via-[#0b427a] to-[#0F5BCC]
            shadow-[0_20px_50px_rgba(15,23,42,.12)]
            border border-white/10
            mb-6">

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

                            <h1 class="text-[44px] md:text-[54px]
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

            {{-- TIMELINE HOẶC CHỮ ĐÃ HỦY MÀU ĐỎ --}}
            @if($order->order_status === 'cancelled' || $order->order_status === 'canceled')
                <div class="bg-red-50/70 border border-red-200/60 rounded-3xl p-6 md:p-8 shadow-[0_20px_80px_rgba(239,68,68,.05)] mb-6 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-gradient-to-tr from-red-500 to-rose-400"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center shadow-lg shadow-red-200/40 animate-pulse">
                            <span class="material-symbols-outlined text-[36px]">cancel</span>
                        </div>
                        <h2 class="text-2xl font-black text-red-600 tracking-tight uppercase">Đơn hàng đã hủy</h2>
                        @if(!empty($order->cancel_reason))
                            <p class="text-sm font-bold text-red-500 max-w-xl leading-relaxed bg-red-100/30 border border-red-200/50 px-6 py-3.5 rounded-2xl">
                                Lý do hủy: <span class="font-medium text-slate-700">{{ $order->cancel_reason }}</span>
                            </p>
                        @else
                            <p class="text-sm font-medium text-slate-500 max-w-md">Đơn hàng này đã bị hủy trên hệ thống.</p>
                        @endif
                        <p class="text-sm font-bold {{ $order->payment_status === 'refunded' ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-slate-600 bg-white/70 border-slate-200' }} max-w-xl leading-relaxed border px-6 py-3.5 rounded-2xl">
                            {{ $order->payment_status === 'refunded'
                                ? 'Trạng thái hoàn tiền: Đã ghi nhận hoàn tiền. Tiền sẽ về phương thức thanh toán ban đầu trong 3-7 ngày làm việc.'
                                : 'Trạng thái hoàn tiền: Không phát sinh hoàn tiền vì đơn chưa thanh toán hoặc thanh toán COD.' }}
                        </p>
                    </div>
                </div>
            @else
                <div
                    class="bg-white/80 backdrop-blur-2xl rounded-3xl p-6 md:p-8 shadow-[0_20px_50px_rgba(59,130,246,.04)] border border-white/50 mb-6 relative overflow-hidden">

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
                                @php
                                    $isActive = $currentStep >= $time['step'];
                                    $isCurrent = $currentStep == $time['step'];
                                    
                                    $stepBgColor = '';
                                    if ($isActive) {
                                        switch($time['color']) {
                                            case 'blue': $stepBgColor = 'bg-blue-600 text-white shadow-blue-500/20'; break;
                                            case 'emerald': $stepBgColor = 'bg-emerald-600 text-white shadow-emerald-500/20'; break;
                                            case 'indigo': $stepBgColor = 'bg-indigo-600 text-white shadow-indigo-500/20'; break;
                                            case 'amber': $stepBgColor = 'bg-amber-500 text-white shadow-amber-500/20'; break;
                                            default: $stepBgColor = 'bg-blue-600 text-white shadow-blue-500/20';
                                        }
                                    } else {
                                        $stepBgColor = 'bg-slate-100 text-slate-400 border border-slate-200/50';
                                    }
                                @endphp

                                <div class="flex flex-col items-center relative z-10">

                                    <div class="relative">

                                        @if($isCurrent)

                                            <div
                                                class="absolute inset-0 rounded-full border-2 border-cyan-400/40 animate-spin-slow">
                                            </div>

                                            <div class="absolute inset-[-6px] rounded-full border border-blue-400/10"></div>

                                        @endif

                                        <div class="
                                            w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center
                                            transition-all duration-500 shadow-md relative z-10 {{ $stepBgColor }}
                                            {{ $isCurrent ? 'scale-110 animate-breathe shadow-lg' : '' }}
                                        ">

                                            <span class="material-symbols-outlined text-2xl md:text-3xl">
                                                {{ $time['icon'] }}
                                            </span>

                                        </div>

                                    </div>

                                    <h3 class="mt-4 font-black text-slate-800 text-sm md:text-base text-center">

                                        {{ $time['title'] }}

                                    </h3>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>
            @endif

          

            @if(in_array($order->order_status, ['pending', 'confirmed', 'processing']))
                <div class="bg-white/80 backdrop-blur-2xl rounded-3xl border border-red-100 shadow-sm p-6 md:p-8 mb-6">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl">cancel</span>
                        </div>
                        <div>
                            <p class="text-red-500 text-xs uppercase tracking-wider font-black mb-1">Huỷ đơn hàng</p>
                            <h2 class="text-xl font-black text-slate-800">Chính sách hoàn tiền</h2>
                            <p class="text-sm text-slate-500 mt-2 leading-6">
                                Bạn có thể huỷ khi đơn còn ở trạng thái chờ xác nhận, đã xác nhận hoặc đang chuẩn bị.
                                Đơn COD/chưa thanh toán không phát sinh hoàn tiền; đơn đã thanh toán online sẽ được hoàn về phương thức ban đầu trong 3-7 ngày làm việc.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('orders.cancel', $order->order_id) }}" method="POST" class="space-y-4">
                        @csrf
                        <textarea name="cancel_reason" rows="3" required minlength="5" maxlength="500"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100"
                            placeholder="Nhập lý do huỷ đơn...">{{ old('cancel_reason') }}</textarea>
                        <button type="submit"
                            onclick="return confirm('Bạn chắc chắn muốn huỷ đơn hàng này?')"
                            class="rounded-2xl bg-red-500 px-6 py-3 text-sm font-black uppercase tracking-wider text-white transition hover:bg-red-600">
                            Xác nhận huỷ đơn
                        </button>
                    </form>
                </div>
            @endif

            {{-- PRODUCTS --}}
            <div class="bg-white/80 backdrop-blur-2xl rounded-3xl border border-white/50 shadow-sm p-6 md:p-8 mb-6 overflow-hidden">
                <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100/70">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100/50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-2xl">package_2</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wider font-bold">Purchased Items</p>
                        <h2 class="text-xl font-black text-slate-800">Sản phẩm đã mua</h2>
                    </div>
                </div>

                <div class="divide-y divide-slate-100/70">
                    @foreach($order->items as $item)
                        <div class="group flex flex-col md:flex-row items-start md:items-center justify-between py-5 first:pt-0 last:pb-0 gap-5 relative z-10">
                            
                            {{-- TRÁI: ẢNH & THÔNG TIN --}}
                            <div class="flex items-center gap-5 flex-1 min-w-0">
                                {{-- Ảnh --}}
                                <div class="w-20 h-20 bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm">
                                    @if($item->image_url)
                                        <img src="{{ asset($item->image_url) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                            <span class="material-symbols-outlined text-3xl text-gray-300">inventory_2</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-black text-[#001e40] text-base truncate leading-tight group-hover:text-blue-700 transition duration-300">
                                        {{ $item->product_name }}
                                    </h3>
                                    
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        @if($item->variant_info)
                                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                                {{ $item->variant_info }}
                                            </span>
                                        @endif
                                        
                                        <span class="text-[10px] font-bold text-slate-400 font-mono">
                                            Đơn giá: {{ number_format($item->unit_price, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- GIỮA: SỐ LƯỢNG --}}
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/50 flex-shrink-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Số lượng:</span>
                                <span class="text-xs font-black text-[#001e40]">{{ $item->quantity }}</span>
                            </div>

                            {{-- PHẢI: THÀNH TIỀN --}}
                            <div class="flex flex-col items-end flex-shrink-0 min-w-[120px]">
                                <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">Thành tiền</span>
                                <h4 class="text-base font-black text-[#003366] leading-tight">
                                    {{ number_format($item->subtotal, 0, ',', '.') }}₫
                                </h4>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            
            {{-- INFO + PAYMENT --}}

            <div class="grid lg:grid-cols-2 gap-6 mb-6">

                {{-- RECEIVER INFO --}}

            <div class="rounded-3xl bg-white/80 backdrop-blur-2xl
border border-white/50 shadow-sm p-6 md:p-8 mb-6">

                <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100/70">

                    <div class="w-12 h-12 rounded-xl
        bg-cyan-50 text-cyan-600 border border-cyan-100/50
        flex items-center justify-center flex-shrink-0">

                        <span class="material-symbols-outlined">
                            location_on
                        </span>

                    </div>

                    <div>

                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-0.5">
                            Receiver Information
                        </p>

                        <h2 class="text-xl font-black text-slate-800">
                            Địa chỉ giao hàng
                        </h2>

                    </div>

                </div>

                <div class="grid md:grid-cols-3 gap-5">

                    {{-- NAME --}}

                    <div class="bg-slate-50/60 border border-slate-100 rounded-2xl p-4 hover:bg-slate-50 transition duration-300">

                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                            Người nhận
                        </p>

                        <h3 class="font-black text-[#001e40] text-base leading-tight">

                            {{ $shippingAddress->full_name ?? 'Chưa cập nhật' }}

                        </h3>

                    </div>

                    {{-- PHONE --}}

                    <div class="bg-slate-50/60 border border-slate-100 rounded-2xl p-4 hover:bg-slate-50 transition duration-300">

                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                            Số điện thoại
                        </p>

                        <h3 class="font-black text-[#001e40] text-base leading-tight">

                            {{ $shippingAddress->phone ?? 'Chưa cập nhật' }}

                        </h3>

                    </div>

                    {{-- ADDRESS --}}

                    <div class="bg-slate-50/60 border border-slate-100 rounded-2xl p-4 hover:bg-slate-50 transition duration-300">

                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                            Địa chỉ
                        </p>

                        <h3 class="font-black text-[#001e40] text-sm leading-relaxed">

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

                {{-- PAYMENT --}}

                <div
                    class="relative overflow-hidden rounded-3xl bg-white/80 backdrop-blur-2xl border border-white/50 shadow-sm p-6 md:p-8 hover:-translate-y-0.5 hover:border-blue-300 transition-all duration-300 group">

                    {{-- LIGHT --}}

                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-200/20 rounded-full blur-3xl"></div>

                    <div class="absolute top-0 right-0 w-56 h-56 bg-cyan-100/20 rounded-full blur-3xl"></div>

                    <div class="relative z-10">

                        {{-- HEADER --}}

                        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100/70">

                            <div
                                class="w-12 h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-md">

                                <span class="material-symbols-outlined text-2xl">
                                    account_balance_wallet
                                </span>

                            </div>

                            <div>

                                <p class="uppercase tracking-[3px] text-[11px] text-slate-400 mb-1">
                                    PAYMENT
                                </p>

                                <h2 class="text-xl font-black text-slate-800 font-bold">
                                    Tổng thanh toán
                                </h2>

                            </div>

                        </div>

                        {{-- PRICE LIST --}}

                        <div class="space-y-3">

                            <div class="flex justify-between items-center bg-slate-50/60 border border-slate-100/70 rounded-2xl px-5 py-3.5">

                                <span class="text-slate-500 text-sm">
                                    Tạm tính
                                </span>

                                <span class="font-black text-slate-800">

                                    {{ number_format($order->subtotal, 0, ',', '.') }}₫

                                </span>

                            </div>

                            <div class="flex justify-between items-center bg-slate-50/60 border border-slate-100/70 rounded-2xl px-5 py-3.5">

                                <span class="text-slate-500 text-sm">
                                    Vận chuyển
                                </span>

                                <span class="font-black text-slate-800">

                                    {{ number_format($order->shipping_fee, 0, ',', '.') }}₫

                                </span>

                            </div>

                            <div class="flex justify-between items-center bg-red-50 border border-red-100/50 rounded-2xl px-5 py-3.5">

                                <span class="text-red-500 text-sm font-bold">
                                    Giảm giá
                                </span>

                                <span class="font-black text-red-600">

                                    -{{ number_format($order->discount_amount, 0, ',', '.') }}₫

                                </span>

                            </div>

                        </div>

                        {{-- TOTAL CARD --}}

                        <div
                            class="mt-6 relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#003366] to-[#0F5BCC] p-5 shadow-lg shadow-blue-500/10 border border-white/10">

                            {{-- LIGHT EFFECT --}}

                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>

                            <div class="absolute bottom-0 left-0 w-full h-[2px] bg-white/30"></div>

                            <div class="relative z-10">

                                <p class="uppercase tracking-[4px] text-white/70 text-[10px] font-bold mb-2">
                                    TOTAL PAYMENT
                                </p>

                                <h2 class="text-3xl md:text-4xl font-black text-white leading-tight">

                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫

                                </h2>

                            </div>

                        </div>

                        {{-- SECURITY --}}

                        <div class="mt-4 flex items-center gap-2.5 text-slate-500 text-xs font-medium">

                            <span class="material-symbols-outlined text-green-500 text-[18px]">
                                verified_user
                            </span>

                            Thanh toán được bảo mật & mã hoá an toàn

                        </div>

                    </div>

                </div>

            </div>

           

        </div>

    </main>
    
@endsection
