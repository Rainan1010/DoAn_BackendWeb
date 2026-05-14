@extends('layouts.app')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

<style>

*{
    font-family:'Inter',sans-serif;
}

body{
    background:
        linear-gradient(135deg,
        #f5f9ff 0%,
        #eef4ff 100%);
}

.glass-card{
    background:rgba(255,255,255,.88);
    backdrop-filter:blur(14px);

    border:1px solid rgba(255,255,255,.6);

    box-shadow:
        0 10px 30px rgba(0,0,0,.06);
}

.payment-card{
    transition:.35s;
    border:1px solid #dbe2ea;
    position:relative;
    overflow:hidden;
    cursor:pointer;
}

.payment-card:hover{

    transform:
        translateY(-4px);

    border-color:#001e40;

    box-shadow:
        0 12px 28px rgba(0,30,64,.08);
}

.payment-card.active{

    border-color:#001e40;
    background:#eff6ff;
}

.floating-button{
    transition:.35s;
}

.floating-button:hover{

    transform:
        translateY(-3px) scale(1.01);

    box-shadow:
        0 18px 30px rgba(0,30,64,.2);
}

.cart-item{
    transition:.3s;
}

.cart-item:hover{
    transform:translateX(4px);
}

.fade-up{
    animation:fadeUp .6s ease;
}

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

<div class="max-w-7xl mx-auto py-10 px-5 fade-up">

    ```blade
<div class="grid lg:grid-cols-12 gap-8 items-start">

    {{-- SIDEBAR --}}
    <div class="lg:col-span-3">

        <div class="glass-card rounded-[32px] p-7 sticky top-24">

            <h2 class="text-3xl font-black text-[#001e40]">
                Checkout
            </h2>

            <p class="text-gray-400 mt-2 text-sm">
                Hoàn tất đơn hàng của bạn
            </p>

            <div class="mt-10 space-y-5">

                {{-- STEP 1 --}}
                <a href="{{ route('checkout') }}"
                   class="sidebar-step bg-white rounded-3xl p-5 flex items-center gap-4 border border-gray-100 text-gray-400 block">

                    <div class="text-3xl">
                        👤
                    </div>

                    <div>

                        <p class="uppercase tracking-[3px] text-xs font-black">
                            Bước 1
                        </p>

                        <p class="font-bold mt-1">
                            Thông tin giao hàng
                        </p>

                    </div>

                </a>

                {{-- STEP 2 --}}
                <div class="sidebar-step bg-blue-50 border-l-4 border-[#001e40] rounded-3xl p-5 flex items-center gap-4">

                    <div class="text-3xl">
                        💳
                    </div>

                    <div>

                        <p class="uppercase tracking-[3px] text-xs font-black text-[#001e40]">
                            Bước 2
                        </p>

                        <p class="font-bold text-[#001e40] mt-1">
                            Thanh toán
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- MAIN --}}
    <div class="lg:col-span-5">

        <div class="glass-card rounded-[32px] p-8 fade-slide">

            <div class="flex items-center justify-between mb-10">

                <div>

                    <h1 class="text-4xl font-black text-[#001e40]">
                        PHƯƠNG THỨC THANH TOÁN
                    </h1>

                    <p class="text-gray-400 mt-2">
                        Chọn phương thức thanh toán phù hợp
                    </p>

                </div>

                <div class="hidden md:flex w-16 h-16 rounded-2xl bg-blue-50 items-center justify-center text-3xl">
                    💳
                </div>

            </div>

            {{-- INFO --}}
            <div class="bg-[#f8fbff] border border-[#dbe2ea] rounded-[28px] p-6 mb-8">

                <h3 class="font-black text-[#001e40] text-xl mb-5">
                    Thông tin nhận hàng
                </h3>

                <div class="space-y-3 text-gray-600 leading-7">

                    <p>
                        <span class="font-bold">
                            Họ tên:
                        </span>

                        {{ $info['full_name'] ?? '' }}
                    </p>

                    <p>
                        <span class="font-bold">
                            Số điện thoại:
                        </span>

                        {{ $info['phone'] ?? '' }}
                    </p>

                    <p>

                        <span class="font-bold">
                            Địa chỉ:
                        </span>

                        @if(($info['address_type'] ?? '') == 'saved')

                            @php

                                $selectedAddress =
                                    $addresses
                                        ->where(
                                            'address_id',
                                            $info['shipping_address_id']
                                        )
                                        ->first();

                            @endphp

                            @if($selectedAddress)

                                {{ $selectedAddress->street_address }},
                                {{ $selectedAddress->ward }},
                                {{ $selectedAddress->district }},
                                {{ $selectedAddress->province }}

                            @endif

                        @else

                            {{ $info['street_address'] ?? '' }},
                            {{ $info['ward'] ?? '' }},
                            {{ $info['district'] ?? '' }},
                            {{ $info['province'] ?? '' }}

                        @endif

                    </p>

                </div>

            </div>

            {{-- FORM --}}
            <form action="{{ route('checkout.store') }}"
                  method="POST">

                @csrf

                <div class="space-y-5">

                    {{-- COD --}}
                    <label
                        class="payment-card active rounded-[28px] p-6 flex justify-between items-center"
                        id="cod_card">

                        <div class="flex items-center gap-5">

                            <div class="text-4xl">
                                🚚
                            </div>

                            <div>

                                <h3 class="font-black text-xl text-[#001e40]">
                                    Thanh toán COD
                                </h3>

                                <p class="text-sm text-gray-500 mt-2">
                                    Thanh toán khi nhận hàng
                                </p>

                            </div>

                        </div>

                        <input checked
                               type="radio"
                               name="payment_method"
                               value="cod"
                               onchange="changePayment()">

                    </label>

                    {{-- MOMO --}}
                    <label
                        class="payment-card rounded-[28px] p-6 flex justify-between items-center"
                        id="momo_card">

                        <div class="flex items-center gap-5">

                            <div class="text-4xl">
                                💗
                            </div>

                            <div>

                                <h3 class="font-black text-xl text-[#001e40]">
                                    Thanh toán MoMo
                                </h3>

                                <p class="text-sm text-gray-500 mt-2">
                                    Ví điện tử MoMo
                                </p>

                            </div>

                        </div>

                        <input type="radio"
                               name="payment_method"
                               value="momo"
                               onchange="changePayment()">

                    </label>

                    {{-- VNPAY --}}
                    <label
                        class="payment-card rounded-[28px] p-6 flex justify-between items-center"
                        id="vnpay_card">

                        <div class="flex items-center gap-5">

                            <div class="text-4xl">
                                🏦
                            </div>

                            <div>

                                <h3 class="font-black text-xl text-[#001e40]">
                                    Thanh toán VNPAY
                                </h3>

                                <p class="text-sm text-gray-500 mt-2">
                                    Quét QR thanh toán
                                </p>

                            </div>

                        </div>

                        <input type="radio"
                               name="payment_method"
                               value="vnpay"
                               onchange="changePayment()">

                    </label>

                </div>

                <button type="submit"
                        class="floating-button w-full mt-10 bg-[#001e40] text-white py-5 rounded-[22px] font-black text-lg">

                    ĐẶT HÀNG →

                </button>

            </form>

        </div>

    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-4">

        <div class="glass-card order-summary rounded-[32px] p-5 sticky top-24 fade-slide overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6">

                <div>

                    <h2 class="text-[28px] font-black text-[#001e40] leading-none">
                        ĐƠN HÀNG
                    </h2>

                    <p class="text-sm text-gray-400 mt-2 leading-6">
                        Tổng quan sản phẩm thanh toán
                    </p>

                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-2xl flex-shrink-0">
                    🛒
                </div>

            </div>

            {{-- PRODUCTS --}}
            <div class="space-y-4">

                @foreach($checkoutItems as $item)

                    <div class="border border-gray-100 rounded-[28px] p-4 bg-white overflow-hidden">

                        <div class="flex gap-4 items-center">

                            {{-- IMAGE --}}
                            <div class="w-[90px] h-[90px] rounded-3xl overflow-hidden border bg-gray-50 flex-shrink-0">

                                <img src="{{ asset($item['image']) }}"
                                     class="w-full h-full object-cover">

                            </div>

                            {{-- INFO --}}
                            <div class="flex-1 min-w-0">

                                <h3 class="font-black text-[#001e40] text-[16px] leading-7 break-words">

                                    {{ $item['name'] }}

                                </h3>

                                <div class="flex items-center justify-between gap-2 mt-4 flex-wrap">

                                    <span class="bg-gray-100 text-gray-500 text-sm px-3 py-1 rounded-full flex-shrink-0">
                                        x{{ $item['quantity'] }}
                                    </span>

                                    <span class="font-black text-[#001e40] text-[16px] break-words text-right">

                                        {{ number_format($item['price']) }}đ

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            {{-- PRICE --}}
            <div class="mt-7 border-t border-gray-200 pt-6 space-y-5">

                <div class="flex items-center justify-between gap-3">

                    <span class="text-gray-500 text-lg">
                        Tạm tính
                    </span>

                    <span class="font-black text-[#001e40] text-[18px] text-right break-words">

                        {{ number_format($subtotal) }}đ

                    </span>

                </div>

                <div class="flex items-center justify-between gap-3">

                    <span class="text-gray-500 text-lg">
                        Phí vận chuyển
                    </span>

                    <span class="font-black text-[#001e40] text-[18px] text-right break-words">

                        {{ number_format($shippingFee) }}đ

                    </span>

                </div>

                <div class="flex items-center justify-between gap-3">

                    <span class="text-gray-500 text-lg">
                        Giảm giá
                    </span>

                    <span class="font-black text-red-500 text-[18px] text-right break-words">

                        -{{ number_format($discount) }}đ

                    </span>

                </div>

            </div>

            {{-- TOTAL --}}
            <div class="mt-7 bg-[#001e40] rounded-[30px] p-5 text-white overflow-hidden">

                <div class="space-y-4">

                    <div>

                        <p class="uppercase tracking-[4px] text-[11px] text-blue-200">
                            Tổng thanh toán
                        </p>

                        <p class="text-blue-100 text-sm mt-2 leading-6">
                            Đã bao gồm VAT & phí vận chuyển
                        </p>

                    </div>

                    <div class="text-[28px] md:text-[32px] font-black leading-tight break-words">

                        {{ number_format($total) }}đ

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

 </div>

<script>

function changePayment(){

    let cod =
        document.querySelector(
            'input[value="cod"]'
        );

    let momo =
        document.querySelector(
            'input[value="momo"]'
        );

    let vnpay =
        document.querySelector(
            'input[value="vnpay"]'
        );

    let codCard =
        document.getElementById(
            'cod_card'
        );

    let momoCard =
        document.getElementById(
            'momo_card'
        );

    let vnpayCard =
        document.getElementById(
            'vnpay_card'
        );

    codCard.classList.remove('active');
    momoCard.classList.remove('active');
    vnpayCard.classList.remove('active');

    if(cod.checked){

        codCard.classList.add(
            'active'
        );
    }

    if(momo.checked){

        momoCard.classList.add(
            'active'
        );
    }

    if(vnpay.checked){

        vnpayCard.classList.add(
            'active'
        );
    }

}

</script>

@endsection