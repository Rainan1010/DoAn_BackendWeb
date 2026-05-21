@extends('layouts.app')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background:
                linear-gradient(135deg,
                    #f5f9ff 0%,
                    #eef4ff 100%);
        }

        .glass-card {
            background:
                rgba(255, 255, 255, .88);

            backdrop-filter:
                blur(14px);

            border:
                1px solid rgba(255, 255, 255, .6);

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .06);
        }

        .fade-up {
            animation:
                fadeUp .6s ease;
        }

        @keyframes fadeUp {

            from {
                opacity: 0;
                transform:
                    translateY(20px);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0);
            }
        }
    </style>

    <div class="max-w-7xl mx-auto py-10 px-5 fade-up">

        <div class="grid lg:grid-cols-12 gap-8 items-start">

            {{-- LEFT --}}
            <div class="lg:col-span-3">

                <div class="glass-card rounded-[32px]
                        p-7 sticky top-24">

                    <h2 class="text-3xl font-black
                            text-[#001e40]">
                        Checkout
                    </h2>

                    <p class="text-gray-400 mt-2 text-sm">
                        Hoàn tất đơn hàng của bạn
                    </p>

                    <div class="mt-10 space-y-5">

                        {{-- STEP 1 --}}
                        <a href="{{ route('checkout') }}" class="bg-white rounded-3xl
                                p-5 flex items-center gap-4
                                border border-gray-100
                                text-gray-400 block">

                            <div class="text-3xl">
                                👤
                            </div>

                            <div>

                                <p class="uppercase
                                        tracking-[3px]
                                        text-xs font-black">
                                    BƯỚC 1
                                </p>

                                <p class="font-bold mt-1">
                                    Thông tin giao hàng
                                </p>

                            </div>

                        </a>

                        {{-- STEP 2 --}}
                        <div class="bg-blue-50
                                border-l-4 border-[#001e40]
                                rounded-3xl p-5
                                flex items-center gap-4">

                            <div class="text-3xl">
                                💳
                            </div>

                            <div>

                                <p class="uppercase
                                        tracking-[3px]
                                        text-xs font-black
                                        text-[#001e40]">
                                    BƯỚC 2
                                </p>

                                <p class="font-bold
                                        text-[#001e40]
                                        mt-1">
                                    Thanh toán
                                </p>

                            </div>

                        </div>
                        {{-- GUIDE --}}
                        <div class="mt-8
        bg-[#f8fbff]
        border border-[#dbe2ea]
        rounded-[30px]
        p-5
        shadow-sm">
                            <h2 class="text-[#001e40]
                                font-black
                                uppercase
                                tracking-[3px]
                                mb-5">
                                Hướng dẫn thanh toán
                            </h2>

                            <div class="space-y-4">

                                {{-- ITEM --}}
                                <div class="bg-white rounded-3xl
                                    p-5 flex items-center
                                    gap-5 border border-gray-100">

                                    <div class="w-12 h-12
                                        rounded-2xl
                                        bg-[#001e40]
                                        text-white
                                        font-black
                                        flex items-center
                                        justify-center">
                                        1
                                    </div>

                                    <p class="text-gray-700">
                                        Mở ứng dụng ngân hàng
                                        hoặc ví VNPAY
                                    </p>

                                </div>

                                <div class="bg-white rounded-3xl
                                    p-5 flex items-center
                                    gap-5 border border-gray-100">

                                    <div class="w-12 h-12
                                        rounded-2xl
                                        bg-[#001e40]
                                        text-white
                                        font-black
                                        flex items-center
                                        justify-center">
                                        2
                                    </div>

                                    <p class="text-gray-700">
                                        Chọn chức năng
                                        "Quét mã QR"
                                    </p>

                                </div>

                                <div class="bg-white rounded-3xl
                                    p-5 flex items-center
                                    gap-5 border border-gray-100">

                                    <div class="w-12 h-12
                                        rounded-2xl
                                        bg-[#001e40]
                                        text-white
                                        font-black
                                        flex items-center
                                        justify-center">
                                        3
                                    </div>

                                    <p class="text-gray-700">
                                        Hướng camera đến mã QR
                                        hiển thị phía trên
                                    </p>

                                </div>

                                <div class="bg-white rounded-3xl
                                    p-5 flex items-center
                                    gap-5 border border-gray-100">

                                    <div class="w-12 h-12
                                        rounded-2xl
                                        bg-[#001e40]
                                        text-white
                                        font-black
                                        flex items-center
                                        justify-center">
                                        4
                                    </div>

                                    <p class="text-gray-700">
                                        Xác nhận thanh toán
                                        để hoàn tất đơn hàng
                                    </p>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- CENTER --}}
            <div class="lg:col-span-5">

                <div class="glass-card rounded-[32px]
                        p-8">

                    {{-- HEADER --}}
                    <div class="flex items-center
                            justify-between mb-10">

                        <div>

                            <h1 class="text-5xl
                                    font-black
                                    text-[#001e40]
                                    leading-tight">
                                THANH TOÁN
                                QUA VNPAY
                            </h1>

                            <p class="text-gray-400
                                    mt-3 text-lg">
                                Quét mã QR để hoàn tất giao dịch
                            </p>

                        </div>

                        <div class="hidden md:flex
                                w-20 h-20 rounded-3xl
                                bg-blue-50
                                items-center justify-center
                                text-4xl">
                            🏦
                        </div>

                    </div>

                    {{-- QR BOX --}}
                    <div class="bg-white rounded-[32px]
                            border border-gray-100
                            p-10 shadow-sm">

                        <div class="flex justify-center">

                            <div class="bg-[#f7fbff]
                                    border-2 border-dashed
                                    border-[#d6e4ff]
                                    rounded-[30px]
                                    p-8">

                                <img src="{{ $qr }}" class="w-[320px]
                                        h-[320px]
                                        object-contain">

                            </div>

                        </div>

                        {{-- TIMER --}}
                        <div class="flex justify-center mt-8">

                            <div class="bg-blue-50
                                    rounded-2xl
                                    px-6 py-4
                                    flex items-center
                                    gap-3">

                                <div class="text-xl">
                                    ⏱️
                                </div>

                                <p class="font-black
                                        text-[#001e40]">

                                    Mã QR sẽ hết hạn sau:
                                    <span id="countdown">
                                        10:00
                                    </span>

                                </p>

                            </div>

                        </div>

                        <p class="text-center
                                text-gray-400 text-sm
                                italic mt-6">
                            Vui lòng không tắt trang này
                            cho đến khi giao dịch hoàn tất
                        </p>

                    </div>



                </div>

            </div>

            {{-- RIGHT --}}
            <div class="lg:col-span-4">

                <div class="glass-card rounded-[32px]
                        p-7 sticky top-24">

                    {{-- HEADER --}}
                    <div class="flex items-center
                            justify-between mb-6">

                        <div>

                            <h2 class="text-[28px]
                                    font-black
                                    text-[#001e40]">
                                ĐƠN HÀNG
                            </h2>

                            <p class="text-sm
                                    text-gray-400 mt-2">
                                Tổng quan sản phẩm thanh toán
                            </p>

                        </div>

                        <div class="w-14 h-14
                                rounded-2xl
                                bg-blue-50
                                flex items-center
                                justify-center
                                text-2xl">
                            🛒
                        </div>

                    </div>

                    {{-- PRODUCT --}}
                    @if(!empty($checkoutItems))

                        @php

                            $item =
                                collect($checkoutItems)
                                    ->first();

                        @endphp

                        <div class="border border-gray-100
                                        rounded-[28px]
                                        p-4 bg-white">

                            <div class="flex gap-4
                                            items-center">

                                <div class="w-[110px]
                                                h-[110px]
                                                rounded-3xl
                                                overflow-hidden
                                                border bg-gray-50">

                                    <img src="{{ asset($item['image'] ?? 'images/default-product.png') }}" class="w-full
                                                    h-full
                                                    object-cover">

                                </div>

                                <div class="flex-1">

                                    <h3 class="font-black
                                                    text-[#001e40]
                                                    text-[18px]
                                                    leading-7">

                                        {{ $item['name'] ?? 'Sản phẩm' }}

                                    </h3>

                                    <div class="flex items-center
                                                    justify-between
                                                    mt-5">

                                        <span class="bg-gray-100
                                                        text-gray-500
                                                        text-sm px-3 py-1
                                                        rounded-full">

                                            x{{ $item['quantity'] ?? 1 }}

                                        </span>

                                        <span class="font-black
                                                        text-[#001e40]
                                                        text-[18px]">

                                            {{ number_format($item['price'] ?? 0) }}đ

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    {{-- PRICE --}}
                    <div class="mt-7 border-t
                            border-gray-200
                            pt-6 space-y-5">

                        <div class="flex justify-between">

                            <span class="text-gray-500
                                    text-lg">
                                Tạm tính
                            </span>

                            <span class="font-black
                                    text-[#001e40]
                                    text-[20px]">

                                {{ number_format($subtotal) }}đ

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500
                                    text-lg">
                                Phí vận chuyển
                            </span>

                            <span class="font-black
                                    text-[#001e40]
                                    text-[20px]">

                                {{ number_format($shippingFee) }}đ

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500
                                    text-lg">
                                Giảm giá
                            </span>

                            <span class="font-black
                                    text-red-500
                                    text-[20px]">

                                -{{ number_format($discount) }}đ

                            </span>

                        </div>

                    </div>

                    {{-- TOTAL --}}
                    <div class="mt-7 bg-[#001e40]
                            rounded-[30px]
                            p-6 text-white">

                        <div>

                            <p class="uppercase
                                    tracking-[4px]
                                    text-[11px]
                                    text-blue-200">
                                Tổng thanh toán
                            </p>

                            <p class="text-blue-100
                                    text-sm mt-2">
                                Đã bao gồm VAT
                                & phí vận chuyển
                            </p>

                        </div>

                        <div class="text-[40px]
                                font-black mt-5">

                            {{ number_format($total) }}đ

                        </div>

                        {{-- BUTTON --}}
                        <div class="mt-7 space-y-4">

                            {{-- SUCCESS --}}
                            <form action="{{ $request->vnp_ReturnUrl }}" method="GET">

                                @foreach($request->all() as $k => $v)

                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">

                                @endforeach

                                <button name="vnp_ResponseCode" value="00" class="w-full
                                        bg-white
                                        text-[#001e40]
                                        font-black
                                        py-5 rounded-2xl
                                        hover:scale-[1.02]
                                        transition-all">

                                    XÁC NHẬN ĐÃ THANH TOÁN

                                </button>

                            </form>

                            {{-- CANCEL --}}
                            <form action="{{ $request->vnp_ReturnUrl }}" method="GET">

                                @foreach($request->all() as $k => $v)

                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">

                                @endforeach

                                <button name="vnp_ResponseCode" value="24" class="w-full
                                        bg-red-500
                                        text-white
                                        font-bold
                                        py-4 rounded-2xl
                                        hover:bg-red-600
                                        transition-all">

                                    Hủy giao dịch

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>

        let time = 600;

        let countdown = setInterval(() => {

            let minutes =
                Math.floor(time / 60);

            let seconds =
                time % 60;

            seconds =
                seconds < 10
                    ? '0' + seconds
                    : seconds;

            document
                .getElementById(
                    'countdown'
                )
                .innerHTML =
                minutes + ':' + seconds;

            time--;

            if (time < 0) {

                clearInterval(
                    countdown
                );

                document
                    .getElementById(
                        'countdown'
                    )
                    .innerHTML =
                    'HẾT HẠN';
            }

        }, 1000);

    </script>

@endsection