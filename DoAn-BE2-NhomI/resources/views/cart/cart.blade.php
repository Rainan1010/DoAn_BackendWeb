@extends('layouts.app')

@section('content')
<main class="pt-24 pb-20 px-6 max-w-7xl mx-auto" x-data="{ 
    showDeleteModal: false, 
    deleteId: '', 
    deleteName: '' 
}">
    <header class="mb-12">
        <h1 class="text-5xl font-black tracking-tight text-primary mt-2">
            Giỏ hàng của bạn.
        </h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        {{-- DANH SÁCH SẢN PHẨM --}}
        <div class="lg:col-span-8 space-y-8">
            @if(count($cart) > 0)
                @foreach($cart as $id => $details)
                    @php
                        $isSelected = isset($selectedCartIds) && in_array($id, $selectedCartIds);
                        $itemSubtotal = $details['price'] * $details['quantity'];
                    @endphp

                    <div class="bg-surface-container-lowest p-6 rounded-md group transition-all duration-300 border shadow-sm
                        {{ $isSelected ? 'border-brand-blue shadow-lg ring-2 ring-brand-blue/10' : 'border-transparent hover:border-outline-variant/20' }}">
                        
                        <div class="flex flex-col md:flex-row gap-8">
                            {{-- NÚT CHỌN SẢN PHẨM --}}
                            <div class="flex items-start pt-2">
                                <form action="{{ route('cart.toggleSelect') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="id" value="{{ $id }}">

                                    <button 
                                        type="submit"
                                        title="Chọn sản phẩm này để thanh toán"
                                        class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all
                                        {{ $isSelected ? 'border-brand-blue bg-brand-blue' : 'border-gray-300 bg-white hover:border-brand-blue' }}"
                                    >
                                        @if($isSelected)
                                            <span class="w-3 h-3 bg-white rounded-full block"></span>
                                        @endif
                                    </button>
                                </form>
                            </div>

                            {{-- HÌNH SẢN PHẨM --}}
                            <div class="w-full md:w-48 h-48 bg-surface-container-low rounded-md overflow-hidden relative border border-outline-variant/10">
                                <img 
                                    src="{{ asset(str_replace('public/', '', $details['image'] ?? 'images/default-product.png')) }}"
                                    class="w-full h-full object-contain p-4 transition-all duration-500 group-hover:scale-105"
                                    onerror='this.src="{{ asset("images/default-product.png") }}";'
                                    alt="{{ $details['name'] ?? 'Sản phẩm' }}"
                                >
                            </div>

                            {{-- THÔNG TIN SẢN PHẨM --}}
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-primary tracking-tight">
                                                {{ $details['name'] }}
                                            </h3>

                                            <p class="mt-2 text-sm text-on-surface-variant">
                                                Đơn giá:
                                                <span class="font-bold text-primary">
                                                    {{ number_format($details['price'], 0, ',', '.') }}₫
                                                </span>
                                            </p>

                                            @if($isSelected)
                                                <div class="inline-flex items-center gap-2 mt-3 px-3 py-1 rounded-full bg-brand-blue/10 text-brand-blue text-xs font-black uppercase tracking-widest">
                                                    <span class="material-symbols-outlined text-sm">
                                                        check_circle
                                                    </span>
                                                    Đã chọn
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-2 mt-3 px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest">
                                                    Chưa chọn
                                                </div>
                                            @endif
                                        </div>

                                        {{-- NÚT XOÁ --}}
                                        <button 
                                            type="button" 
                                            @click="
                                                showDeleteModal = true; 
                                                deleteId = '{{ $id }}'; 
                                                deleteName = @js($details['name'])
                                            "
                                            class="text-outline hover:text-error transition-colors p-2 rounded-full hover:bg-red-50"
                                            title="Xóa sản phẩm"
                                        >
                                            <span class="material-symbols-outlined text-red-500">
                                                delete
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-6 mt-6">
                                    {{-- NÚT TĂNG GIẢM SỐ LƯỢNG --}}
                                    <div>
                                        <div class="text-[10px] text-on-surface-variant uppercase font-black tracking-widest mb-2">
                                            Số lượng
                                        </div>

                                        <div class="flex items-center bg-surface-container-high rounded-lg p-1 border border-outline-variant/10 w-fit">
                                            {{-- GIẢM SỐ LƯỢNG --}}
                                            <form action="{{ route('cart.update') }}" method="POST">
                                                @csrf

                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <input type="hidden" name="quantity" value="{{ $details['quantity'] - 1 }}">

                                                <button 
                                                    type="submit" 
                                                    class="w-10 h-10 flex items-center justify-center text-primary hover:bg-white rounded-md transition-all
                                                    {{ $details['quantity'] <= 1 ? 'opacity-20 pointer-events-none' : '' }}"
                                                    {{ $details['quantity'] <= 1 ? 'disabled' : '' }}
                                                >
                                                    <span class="material-symbols-outlined">
                                                        remove
                                                    </span>
                                                </button>
                                            </form>

                                            <span class="w-12 text-center font-black text-primary">
                                                {{ $details['quantity'] }}
                                            </span>

                                            {{-- TĂNG SỐ LƯỢNG --}}
                                            <form action="{{ route('cart.update') }}" method="POST">
                                                @csrf

                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <input type="hidden" name="quantity" value="{{ $details['quantity'] + 1 }}">

                                                <button 
                                                    type="submit" 
                                                    class="w-10 h-10 flex items-center justify-center text-primary hover:bg-white rounded-md transition-all"
                                                >
                                                    <span class="material-symbols-outlined">
                                                        add
                                                    </span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- TẠM TÍNH TỪNG SẢN PHẨM --}}
                                    <div class="text-left sm:text-right">
                                        <div class="text-[10px] text-on-surface-variant uppercase font-black tracking-widest">
                                            Subtotal
                                        </div>

                                        <div class="text-2xl font-black text-primary">
                                            {{ number_format($itemSubtotal, 0, ',', '.') }}₫
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- GIỎ HÀNG TRỐNG --}}
                <div class="p-20 text-center bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant/20">
                    <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">
                        shopping_cart_off
                    </span>

                    <p class="text-slate-500 uppercase font-black tracking-widest block">
                        Giỏ hàng của bạn đang trống
                    </p>

                    <a 
                        href="{{ url('/') }}" 
                        class="mt-6 inline-block bg-primary text-white px-8 py-3 rounded-full font-bold hover:bg-on-primary-fixed-variant transition-all"
                    >
                        QUAY LẠI CỬA HÀNG
                    </a>
                </div>
            @endif
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="lg:col-span-4 sticky top-24">
            <div class="bg-surface-container p-8 rounded-md shadow-sm border border-outline-variant/10">
                <h2 class="text-2xl font-black text-primary tracking-tight mb-8">
                    Sản phẩm đã chọn
                </h2>

                @if(count($cart) > 0)
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-on-surface-variant border-b border-outline-variant/10 pb-4">
                            <span class="text-sm font-medium">
                                Số sản phẩm đã chọn
                            </span>

                            <span class="font-bold text-primary">
                                {{ count($selectedCartIds ?? []) }}
                            </span>
                        </div>

                        <div class="flex justify-between text-on-surface-variant border-b border-outline-variant/10 pb-4">
                            <span class="text-sm font-medium">
                                Tạm tính sản phẩm
                            </span>

                            <span class="font-bold text-primary">
                                {{ number_format($subtotal, 0, ',', '.') }}₫
                            </span>
                        </div>

                        <div class="flex justify-between text-on-surface-variant border-b border-outline-variant/10 pb-4">
                            <span class="text-sm font-medium">
                                Phí vận chuyển
                            </span>

                            <span class="font-bold text-primary">
                                {{ number_format($shipping, 0, ',', '.') }}₫
                            </span>
                        </div>

                        <div class="flex justify-between text-on-surface-variant">
                            <span class="text-sm font-medium">
                                VAT 10%
                            </span>

                            <span class="font-bold text-primary">
                                {{ number_format($tax, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-baseline mb-8 pt-6 border-t border-outline-variant/10">
                        <span class="text-lg font-black text-primary">
                            Tổng thanh toán
                        </span>

                        <span class="text-3xl font-black text-primary">
                            {{ number_format($total, 0, ',', '.') }}₫
                        </span>
                    </div>

                  @if(count($selectedCartIds ?? []) > 0)
                        <form action="{{ route('checkout') }}"
                        method="GET">

                            <button type="submit" class="w-full bg-brand-blue text-white py-5 rounded-md font-black uppercase tracking-[0.2em] text-sm shadow-xl hover:bg-[#002244] hover:shadow-2xl transition-all active:scale-[0.98]">
                                Đặt hàng
                            </button>
                        </form>
                    @else
                        <button 
                            disabled
                            class="w-full bg-gray-300 text-gray-500 py-5 rounded-md font-black uppercase tracking-[0.2em] text-sm cursor-not-allowed"
                        >
                            Chọn sản phẩm
                        </button>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-on-surface-variant text-sm mb-6">
                            Chưa có sản phẩm nào trong giỏ hàng.
                        </p>

                        <button 
                            disabled
                            class="w-full bg-gray-300 text-gray-500 py-5 rounded-md font-black uppercase tracking-[0.2em] text-sm cursor-not-allowed"
                        >
                            Giỏ hàng trống
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL XÁC NHẬN XOÁ --}}
    <div 
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-outline-variant/20">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">
                        warning
                    </span>
                </div>

                <h3 class="text-2xl font-black text-primary mb-2 uppercase tracking-tight">
                    Xác nhận xoá?
                </h3>

                <p class="text-on-surface-variant text-sm leading-relaxed px-4">
                    Bảo có chắc chắn muốn xóa 
                    <span class="font-bold text-red-600" x-text="deleteName"></span> 
                    khỏi giỏ hàng này không?
                </p>
            </div>

            <div class="flex p-4 gap-3 bg-gray-50 border-t border-gray-100">
                <button 
                    type="button"
                    @click="showDeleteModal = false"
                    class="flex-1 py-3.5 text-xs font-black text-secondary uppercase tracking-widest hover:bg-white rounded-xl transition-all border border-outline-variant/20"
                >
                    HỦY BỎ
                </button>

                <form action="{{ route('cart.remove') }}" method="POST" class="flex-1">
                    @csrf

                    <input type="hidden" name="id" :value="deleteId">

                    <button 
                        type="submit" 
                        class="w-full py-3.5 text-xs font-black text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition-all uppercase tracking-widest"
                    >
                        XÁC NHẬN XÓA
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection