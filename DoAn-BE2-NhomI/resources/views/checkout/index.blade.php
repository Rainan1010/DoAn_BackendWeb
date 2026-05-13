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
        #f6f9ff 0%,
        #edf4ff 40%,
        #ffffff 100%);
}

.checkout-wrapper{
    animation:fadeUp .7s ease;
}

.glass-card{
    background:rgba(255,255,255,.88);
    backdrop-filter:blur(16px);

    border:1px solid rgba(255,255,255,.7);

    box-shadow:
        0 15px 40px rgba(0,0,0,.05);
}

.checkout-input{
    width:100%;

    border:none;

    border:1px solid #dbe2ea;

    background:#f8fafc;

    padding:16px 18px;

    border-radius:18px;

    transition:.35s;

    outline:none;

    font-size:15px;
}

.checkout-input:focus{

    border-color:#001e40;

    background:white;

    transform:translateY(-2px);

    box-shadow:
        0 0 0 5px rgba(0,30,64,.08);
}

.delivery-card{

    border:1px solid #dbe2ea;

    transition:.35s;

    cursor:pointer;

    position:relative;

    overflow:hidden;

    background:white;
}

.delivery-card::before{

    content:'';

    position:absolute;

    inset:0;

    background:
        linear-gradient(135deg,
        rgba(0,30,64,.04),
        transparent);

    opacity:0;

    transition:.35s;
}

.delivery-card:hover::before{
    opacity:1;
}

.delivery-card:hover{

    transform:
        translateY(-5px);

    border-color:#001e40;

    box-shadow:
        0 15px 30px rgba(0,30,64,.08);
}

.delivery-card.active{

    border-color:#001e40;

    background:#eef5ff;
}

.cart-item{
    transition:.35s;
}

.cart-item:hover{

    transform:
        translateX(5px);
}

.floating-button{

    transition:.35s;

    background:
        linear-gradient(135deg,
        #001e40,
        #003f8a);
}

.floating-button:hover{

    transform:
        translateY(-4px) scale(1.01);

    box-shadow:
        0 15px 35px rgba(0,30,64,.25);
}

.sidebar-step{
    transition:.3s;
}

.sidebar-step:hover{
    transform:translateX(5px);
}

.address-card{

    transition:.35s;

    border:1px solid #e5e7eb;

    background:white;
}

.address-card:hover{

    border-color:#001e40;

    transform:translateY(-2px);

    box-shadow:
        0 12px 25px rgba(0,0,0,.05);
}

.order-summary{

    background:
        linear-gradient(180deg,
        rgba(255,255,255,.95),
        rgba(248,250,252,.95));
}

.fade-slide{
    animation:fadeSlide .5s ease;
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

@keyframes fadeSlide{

    from{
        opacity:0;
        transform:translateY(15px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

<div class="max-w-7xl mx-auto py-10 px-5 checkout-wrapper">

    <div class="grid lg:grid-cols-12 gap-8">

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

                    <div class="sidebar-step bg-blue-50 border-l-4 border-[#001e40] rounded-3xl p-5 flex items-center gap-4">

                        <div class="text-3xl">
                            👤
                        </div>

                        <div>

                            <p class="uppercase tracking-[3px] text-xs font-black text-[#001e40]">
                                Bước 1
                            </p>

                            <p class="font-bold text-[#001e40] mt-1">
                                Thông tin giao hàng
                            </p>

                        </div>

                    </div>

                    <div class="sidebar-step bg-white rounded-3xl p-5 flex items-center gap-4 border border-gray-100 text-gray-400">

                        <div class="text-3xl">
                            💳
                        </div>

                        <div>

                            <p class="uppercase tracking-[3px] text-xs font-black">
                                Bước 2
                            </p>

                            <p class="font-bold mt-1">
                                Thanh toán
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- MAIN --}}
        <div class="lg:col-span-6">

            <form action="{{ route('checkout.saveInformation') }}" method="POST">

                @csrf

                <div class="glass-card rounded-[32px] p-8 fade-slide">

                    <div class="flex items-center justify-between mb-10">

                        <div>

                            <h1 class="text-4xl font-black text-[#001e40]">
                                THÔNG TIN NHẬN HÀNG
                            </h1>

                            <p class="text-gray-400 mt-2">
                                Điền đầy đủ thông tin để tiếp tục thanh toán
                            </p>

                        </div>

                        <div class="hidden md:flex w-16 h-16 rounded-2xl bg-blue-50 items-center justify-center text-3xl">
                            🚚
                        </div>

                    </div>

                    {{-- DELIVERY --}}
                    <div class="mb-10">

                        <label class="delivery-card active rounded-[28px] p-6 flex gap-5 items-start" id="delivery_home_card">

                            <input type="radio"
                                   checked
                                   name="delivery_type"
                                   value="home"
                                   onchange="toggleDelivery()">

                            <div>

                                <h3 class="font-black text-xl text-[#001e40]">
                                    Giao hàng tận nơi
                                </h3>

                                <p class="text-gray-500 text-sm mt-2 leading-6">
                                    Giao nhanh tận nơi an toàn và tiện lợi
                                </p>

                            </div>

                        </label>

                    </div>

                    {{-- USER INFO --}}
                    <div class="grid md:grid-cols-2 gap-6 mb-8">

                        <div>

                            <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                Họ và tên
                            </label>

                            <input type="text"
                                   name="full_name"
                                  value="{{ $oldInfo['full_name'] ?? auth()->user()->full_name }}"
                                   class="checkout-input">

                        </div>

                        <div>

                            <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                Số điện thoại
                            </label>

                            <input type="text"
                                   name="phone"
                                   value="{{ $oldInfo['phone'] ?? auth()->user()->phone }}"
                                   class="checkout-input">

                        </div>

                    </div>

                    {{-- SHIPPING AREA --}}
                    <div id="shippingArea">

                        <div class="mb-8">

                            <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-4">
                                Chọn địa chỉ
                            </label>

                            <div class="grid md:grid-cols-2 gap-5">

                                {{-- SAVED --}}
                                <label class="delivery-card active rounded-[28px] p-5 flex gap-4 items-start" id="saved_address_card">

                                    <input type="radio"
                                          {{ ($oldInfo['address_type'] ?? 'saved') == 'saved' ? 'checked' : '' }}
                                           name="address_type"
                                           value="saved"
                                           onchange="toggleAddressType()">

                                    <div>

                                        <h3 class="font-black text-lg text-[#001e40]">
                                            Địa chỉ đã lưu
                                        </h3>

                                        <p class="text-gray-500 text-sm mt-1">
                                            Sử dụng địa chỉ đã lưu trước đó
                                        </p>

                                    </div>

                                </label>

                                {{-- NEW --}}
                                <label class="delivery-card rounded-[28px] p-5 flex gap-4 items-start" id="new_address_card">
<input type="radio"
       name="address_type"
       value="new"

       {{ ($oldInfo['address_type'] ?? '') == 'new'
            ? 'checked'
            : '' }}

       onchange="toggleAddressType()">

                                    <div>

                                        <h3 class="font-black text-lg text-[#001e40]">
                                            Địa chỉ mới
                                        </h3>

                                        <p class="text-gray-500 text-sm mt-1">
                                            Thêm địa chỉ giao hàng mới
                                        </p>

                                    </div>

                                </label>

                            </div>

                        </div>

                        {{-- SAVED ADDRESS --}}
                        <div id="savedAddressArea" class="space-y-4">

                            @if(count($addresses) > 0)

                                @foreach($addresses as $address)

                                    <label class="address-card rounded-[28px] p-5 flex gap-4 cursor-pointer">

                                        <input type="radio"
                                               name="shipping_address_id"
                                               value="{{ $address->address_id }}"
                                               {{ ($oldInfo['shipping_address_id'] ?? '') == $address->address_id ? 'checked' : '' }}>

                                        <div>

                                            <div class="flex items-center gap-3 flex-wrap">

                                                <h3 class="font-black text-[#001e40]">
                                                    {{ $address->full_name }}
                                                </h3>

                                                <span class="text-sm text-gray-500">
                                                    {{ $address->phone }}
                                                </span>

                                            </div>

                                            <p class="text-gray-600 mt-2 leading-7">
                                                {{ $address->street_address }},
                                                {{ $address->ward }},
                                                {{ $address->district }},
                                                {{ $address->province }}
                                            </p>

                                        </div>

                                    </label>

                                @endforeach

                            @else

                                <div class="bg-yellow-50 border border-yellow-300 text-yellow-700 px-5 py-5 rounded-2xl">
                                    Bạn chưa có địa chỉ đã lưu. Hãy chọn địa chỉ mới.
                                </div>

                            @endif

                        </div>

                        {{-- NEW ADDRESS --}}
                        <div id="newAddressArea" class="hidden mt-8 fade-slide">

                            <div class="grid md:grid-cols-3 gap-6">

                                <div>

                                    <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                        Tỉnh / Thành
                                    </label>

                                   <select id="province"
        name="province"
        class="checkout-input"
        data-selected="{{ $oldInfo['province'] ?? '' }}">
                                        <option value="">Chọn tỉnh</option>
                                    </select>

                                </div>

                                <div>

                                    <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                        Quận / Huyện
                                    </label>

                                   <select id="district"
        name="district"
        class="checkout-input"
        data-selected="{{ $oldInfo['district'] ?? '' }}">
                                        <option value="">Chọn quận</option>
                                    </select>

                                </div>

                                <div>

                                    <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                        Phường / Xã
                                    </label>

                                  <select id="ward"
        name="ward"
        class="checkout-input"
        data-selected="{{ $oldInfo['ward'] ?? '' }}">
                                        <option value="">Chọn phường</option>
                                    </select>

                                </div>

                            </div>

                            <div class="mt-8">

                                <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                                    Địa chỉ cụ thể
                                </label>

                                <input type="text"
                                       id="street_address"
                                       name="street_address"
                                       class="checkout-input"
                                       placeholder="Số nhà, tên đường...">

                            </div>

                        </div>

                    </div>

                    {{-- NOTE --}}
                    <div class="mt-8">

                        <label class="block text-xs uppercase tracking-[3px] font-black text-gray-500 mb-3">
                            Ghi chú đơn hàng
                        </label>

                        <textarea name="note"
                                  rows="5"
                                  class="checkout-input"
                                  placeholder="Ví dụ: Giao giờ hành chính..."></textarea>

                    </div>

                    {{-- BUTTON --}}
                    <button type="submit"
                            class="floating-button w-full text-white py-5 rounded-[22px] font-black text-lg mt-10">

                        TIẾP TỤC THANH TOÁN →

                    </button>

                </div>

            </form>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-3">

            <div class="glass-card order-summary rounded-[32px] p-8 sticky top-24 fade-slide">

                <h2 class="text-3xl font-black text-[#001e40] mb-8">
                    ĐƠN HÀNG
                </h2>

                @foreach($checkoutItems as $item)

                    <div class="cart-item flex gap-4 mb-6">

                        <img src="{{ asset($item['image']) }}"
                             class="w-24 h-24 object-cover rounded-2xl border">

                        <div class="flex-1">

                            <h3 class="font-black text-[#001e40] leading-6">
                                {{ $item['name'] }}
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                x{{ $item['quantity'] }}
                            </p>

                            <p class="font-black text-xl text-[#001e40] mt-3">
                                {{ number_format($item['price']) }}đ
                            </p>

                        </div>

                    </div>

                @endforeach

                <div class="border-t pt-6 mt-6 space-y-5">

                    <div class="flex justify-between text-gray-500">

                        <span>Tạm tính</span>

                        <span>
                            {{ number_format($subtotal) }}đ
                        </span>

                    </div>

                    <div class="flex justify-between text-gray-500">

                        <span>Phí ship</span>

                        <span>
                            {{ number_format($shippingFee) }}đ
                        </span>

                    </div>

                    <div class="border-t pt-5 flex justify-between items-center">

                        <span class="text-xl font-black">
                            Tổng tiền
                        </span>

                        <span class="text-3xl font-black text-[#001e40]">
                            {{ number_format($total) }}đ
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function toggleDelivery(){

    let shippingArea =
        document.getElementById(
            'shippingArea'
        );

    let homeCard =
        document.getElementById(
            'delivery_home_card'
        );

    shippingArea.style.display =
        'block';

    homeCard.classList.add(
        'active'
    );
}

function toggleAddressType(){

    let saved =
        document.querySelector(
            'input[name="address_type"][value="saved"]'
        );

    let savedArea =
        document.getElementById(
            'savedAddressArea'
        );

    let newArea =
        document.getElementById(
            'newAddressArea'
        );

    let savedCard =
        document.getElementById(
            'saved_address_card'
        );

    let newCard =
        document.getElementById(
            'new_address_card'
        );

    if(saved.checked){

        savedArea.style.display =
            'block';

        newArea.style.display =
            'none';

        savedCard.classList.add(
            'active'
        );

        newCard.classList.remove(
            'active'
        );

        document.querySelectorAll(
            'input[name="shipping_address_id"]'
        ).forEach(item=>{

            item.disabled = false;

        });

    }
    else{

        newArea.style.display =
            'block';

        savedArea.style.display =
            'none';

        newCard.classList.add(
            'active'
        );

        savedCard.classList.remove(
            'active'
        );

        document.querySelectorAll(
            'input[name="shipping_address_id"]'
        ).forEach(item=>{

            item.disabled = true;

        });

    }
}

const province =
    document.getElementById('province');

const district =
    document.getElementById('district');

const ward =
    document.getElementById('ward');

async function loadProvinces(){

    const response =
        await fetch(
            'https://provinces.open-api.vn/api/p/'
        );

    const data =
        await response.json();

    data.forEach(item=>{

        let option =
            new Option(
                item.name,
                item.name
            );

        /*
        |--------------------------------------------------------------------------
        | SELECT OLD PROVINCE
        |--------------------------------------------------------------------------
        */
        if(
            province.dataset.selected
            ==
            item.name
        ){
            option.selected = true;
        }

        province.options[
            province.options.length
        ] = option;

    });

}

    const response =
        await fetch(
            'https://provinces.open-api.vn/api/p/'
        );

    const data =
        await response.json();

    data.forEach(item=>{

        province.options[
            province.options.length
        ] = new Option(
            item.name,
            item.name
        );

    });
}

async function loadDistricts(name){

    district.length = 1;

    ward.length = 1;

    const response =
        await fetch(
            'https://provinces.open-api.vn/api/p/'
        );

    const provinces =
        await response.json();

    const provinceData =
        provinces.find(
            p=>p.name == name
        );

    if(!provinceData) return;

    const districtResponse =
        await fetch(
            `https://provinces.open-api.vn/api/p/${provinceData.code}?depth=2`
        );

    const data =
        await districtResponse.json();

    data.districts.forEach(item=>{

        district.options[
            district.options.length
        ] = new Option(
            item.name,
            item.name
        );

    });
}

async function loadWards(name){

    ward.length = 1;

    const response =
        await fetch(
            'https://provinces.open-api.vn/api/d/'
        );

    const districts =
        await response.json();

    const districtData =
        districts.find(
            d=>d.name == name
        );

    if(!districtData) return;

    const wardResponse =
        await fetch(
            `https://provinces.open-api.vn/api/d/${districtData.code}?depth=2`
        );

    const data =
        await wardResponse.json();

    data.wards.forEach(item=>{

        ward.options[
            ward.options.length
        ] = new Option(
            item.name,
            item.name
        );

    });
}

province.onchange = function(){

    loadDistricts(this.value);
}

district.onchange = function(){

    loadWards(this.value);
}

/*
|--------------------------------------------------------------------------
| RESTORE OLD ADDRESS
|--------------------------------------------------------------------------
*/
window.addEventListener(

    'load',

    async function(){

        let selectedProvince =
            province.dataset.selected;

        let selectedDistrict =
            district.dataset.selected;

        let selectedWard =
            ward.dataset.selected;

        /*
        |--------------------------------------------------------------------------
        | PROVINCE
        |--------------------------------------------------------------------------
        */
        if(selectedProvince){

            province.value =
                selectedProvince;

            await loadDistricts(
                selectedProvince
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DISTRICT
        |--------------------------------------------------------------------------
        */
        if(selectedDistrict){

            district.value =
                selectedDistrict;

            await loadWards(
                selectedDistrict
            );

        }

        /*
        |--------------------------------------------------------------------------
        | WARD
        |--------------------------------------------------------------------------
        */
        if(selectedWard){

            ward.value =
                selectedWard;
        }

    }
);

</script>

@endsection

@extends('layouts.app')

@section('content')

<style>

.payment-card{

    background:rgba(255,255,255,.92);

    backdrop-filter:blur(14px);

    border:1px solid rgba(255,255,255,.7);

    box-shadow:
        0 15px 35px rgba(0,0,0,.05);
}

.method-card{

    border:1px solid #e5e7eb;

    transition:.35s;

    cursor:pointer;

    background:white;
}

.method-card:hover{

    transform:translateY(-4px);

    border-color:#001e40;

    box-shadow:
        0 15px 30px rgba(0,30,64,.08);
}

.method-card.active{

    border-color:#001e40;

    background:#eff6ff;
}

.place-order-btn{

    background:
        linear-gradient(135deg,
        #001e40,
        #004aad);

    transition:.35s;
}

.place-order-btn:hover{

    transform:
        translateY(-3px) scale(1.01);

    box-shadow:
        0 18px 35px rgba(0,30,64,.25);
}

</style>

<div class="max-w-7xl mx-auto py-10 px-5">

    <div class="grid lg:grid-cols-12 gap-8">

        {{-- LEFT --}}
        <div class="lg:col-span-8">

            <div class="payment-card rounded-[32px] p-8">

                <div class="flex items-center justify-between mb-10">

                    <div>

                        <h1 class="text-4xl font-black text-[#001e40]">
                            THANH TOÁN
                        </h1>

                        <p class="text-gray-400 mt-2">
                            Xác nhận đơn hàng và chọn phương thức thanh toán
                        </p>

                    </div>

                    <div class="hidden md:flex w-16 h-16 rounded-2xl bg-blue-50 items-center justify-center text-3xl">
                        💳
                    </div>

                </div>

                {{-- INFO --}}
                <div class="bg-slate-50 rounded-[28px] p-6 mb-8 border border-slate-100">

                    <div class="flex items-center justify-between mb-5">

                        <h2 class="text-2xl font-black text-[#001e40]">
                            Thông tin nhận hàng
                        </h2>

                        <a href="{{ route('checkout') }}"
                           class="text-[#001e40] font-bold hover:underline">
                            Chỉnh sửa
                        </a>

                    </div>

                    <div class="space-y-3 text-gray-700 leading-7">

                        <p>
                            <span class="font-black text-[#001e40]">
                                Người nhận:
                            </span>

                            {{ $info['full_name'] ?? '' }}
                        </p>

                        <p>
                            <span class="font-black text-[#001e40]">
                                Số điện thoại:
                            </span>

                            {{ $info['phone'] ?? '' }}
                        </p>

                        <p>
                            <span class="font-black text-[#001e40]">
                                Địa chỉ:
                            </span>

                            {{ $info['street_address'] ?? '' }},
                            {{ $info['ward'] ?? '' }},
                            {{ $info['district'] ?? '' }},
                            {{ $info['province'] ?? '' }}
                        </p>

                    </div>

                </div>

                <form action="{{ route('checkout.store') }}" method="POST">

                    @csrf

                    {{-- METHODS --}}
                    <div>

                        <h2 class="text-2xl font-black text-[#001e40] mb-6">
                            Phương thức thanh toán
                        </h2>

                        <div class="space-y-5">

                            {{-- COD --}}
                            <label class="method-card active rounded-[28px] p-6 flex items-center gap-5" id="cod_card">

                                <input type="radio"
                                       checked
                                       name="payment_method"
                                       value="cod"
                                       onchange="togglePaymentMethod()">

                                <div class="text-4xl">
                                    💵
                                </div>

                                <div>

                                    <h3 class="font-black text-xl text-[#001e40]">
                                        Thanh toán khi nhận hàng
                                    </h3>

                                    <p class="text-gray-500 mt-1">
                                        Thanh toán bằng tiền mặt khi nhận hàng
                                    </p>

                                </div>

                            </label>

                            {{-- BANK --}}
                            <label class="method-card rounded-[28px] p-6 flex items-center gap-5" id="bank_card">

                                <input type="radio"
                                       name="payment_method"
                                       value="banking"
                                       onchange="togglePaymentMethod()">

                                <div class="text-4xl">
                                    🏦
                                </div>

                                <div>

                                    <h3 class="font-black text-xl text-[#001e40]">
                                        Chuyển khoản ngân hàng
                                    </h3>

                                    <p class="text-gray-500 mt-1">
                                        Thanh toán online qua tài khoản ngân hàng
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button type="submit"
                            class="place-order-btn w-full text-white py-5 rounded-[24px] font-black text-lg mt-10">

                        ĐẶT HÀNG NGAY →

                    </button>

                </form>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-4">

            <div class="payment-card rounded-[32px] p-8 sticky top-24">

                <h2 class="text-3xl font-black text-[#001e40] mb-8">
                    TÓM TẮT ĐƠN
                </h2>

                @foreach($checkoutItems as $item)

                    <div class="flex gap-4 mb-6 pb-6 border-b border-slate-100">

                        <img src="{{ asset($item['image']) }}"
                             class="w-24 h-24 rounded-2xl object-cover border">

                        <div class="flex-1">

                            <h3 class="font-black text-[#001e40] leading-6">
                                {{ $item['name'] }}
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                x{{ $item['quantity'] }}
                            </p>

                            <p class="text-xl font-black text-[#001e40] mt-3">
                                {{ number_format($item['price']) }}đ
                            </p>

                        </div>

                    </div>

                @endforeach

                <div class="space-y-5 mt-6">

                    <div class="flex justify-between text-gray-500">

                        <span>Tạm tính</span>

                        <span>
                            {{ number_format($subtotal) }}đ
                        </span>

                    </div>

                    <div class="flex justify-between text-gray-500">

                        <span>Phí vận chuyển</span>

                        <span>
                            {{ number_format($shippingFee) }}đ
                        </span>

                    </div>

                    <div class="border-t pt-5 flex justify-between items-center">

                        <span class="text-xl font-black">
                            Tổng tiền
                        </span>

                        <span class="text-3xl font-black text-[#001e40]">
                            {{ number_format($total) }}đ
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function togglePaymentMethod(){

    let cod =
        document.querySelector(
            'input[value="cod"]'
        );

    let codCard =
        document.getElementById(
            'cod_card'
        );

    let bankCard =
        document.getElementById(
            'bank_card'
        );

    if(cod.checked){

        codCard.classList.add(
            'active'
        );

        bankCard.classList.remove(
            'active'
        );

    }
    else{

        bankCard.classList.add(
            'active'
        );

        codCard.classList.remove(
            'active'
        );

    }
}

</script>

@endsection
