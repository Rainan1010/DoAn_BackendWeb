@extends('layouts.app')

@section('content')
<main class="pt-24 pb-20 px-6 max-w-7xl mx-auto" x-data="{ 
    showDeleteModal: false, 
    deleteId: '', 
    deleteName: '' 
}">
    <header class="mb-12">
        <h1 class="text-5xl font-black tracking-tight text-primary mt-2">Giỏ hàng của bạn.</h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-8 space-y-8">
            @if(count($cart) > 0)
            @foreach($cart as $id => $details)
            <div class="bg-surface-container-lowest p-6 rounded-md group transition-all duration-300 border border-transparent hover:border-outline-variant/20 shadow-sm">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- 1. FIX LỖI HÌNH --}}
                    <div class="w-full md:w-48 h-48 bg-surface-container-low rounded-md overflow-hidden relative border border-outline-variant/10">
                        <img src="{{ asset(str_replace('public/', '', $details['image'])) }}"
                            class="w-full h-full object-contain p-4 transition-all duration-500 group-hover:scale-105"
                            onerror='this.src="{{ asset("images/default-product.png") }}";'>
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="text-xl font-bold text-primary tracking-tight">{{ $details['name'] }}</h3>

                                {{-- 3. NÚT XOÁ (Mở Modal) --}}
                                <button type="button" @click="showDeleteModal = true; deleteId = '{{ $id }}'; deleteName = '{{ $details['name'] }}'"
                                    class="text-outline hover:text-error transition-colors p-2">
                                    <span class="material-symbols-outlined text-red-500">delete</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-end mt-6">
                            {{-- 2. XỬ LÝ NÚT + và - --}}
                            <div class="flex items-center bg-surface-container-high rounded-lg p-1 border border-outline-variant/10">
                                {{-- Giảm số lượng --}}
                                <form action="{{ route('cart.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="hidden" name="quantity" value="{{ $details['quantity'] - 1 }}">
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center text-primary hover:bg-white rounded-md transition-all {{ $details['quantity'] <= 1 ? 'opacity-20 pointer-events-none' : '' }}">
                                        <span class="material-symbols-outlined">remove</span>
                                    </button>
                                </form>

                                <span class="w-12 text-center font-black text-primary">{{ $details['quantity'] }}</span>

                                {{-- Tăng số lượng --}}
                                <form action="{{ route('cart.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="hidden" name="quantity" value="{{ $details['quantity'] + 1 }}">
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center text-primary hover:bg-white rounded-md transition-all">
                                        <span class="material-symbols-outlined">add</span>
                                    </button>
                                </form>
                            </div>

                            <div class="text-right">
                                <div class="text-[10px] text-on-surface-variant uppercase font-black tracking-widest">Subtotal</div>
                                <div class="text-2xl font-black text-primary">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}₫</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="p-20 text-center bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant/20">
                <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">shopping_cart_off</span>
                <p class="text-slate-500 uppercase font-black tracking-widest block">Giỏ hàng của bạn đang trống</p>
                <a href="{{ url('/') }}" class="mt-6 inline-block bg-primary text-white px-8 py-3 rounded-full font-bold hover:bg-on-primary-fixed-variant transition-all">
                    QUAY LẠI CỬA HÀNG
                </a>
            </div>
            @endif
        </div>

        <div class="lg:col-span-4 sticky top-24">
            <div class="bg-surface-container p-8 rounded-md shadow-sm border border-outline-variant/10">
                <h2 class="text-2xl font-black text-primary tracking-tight mb-8">Order Summary</h2>
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-on-surface-variant border-b border-outline-variant/10 pb-4">
                        <span class="text-sm font-medium">Configuration Subtotal</span>
                        <span class="font-bold text-primary">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-on-surface-variant border-b border-outline-variant/10 pb-4">
                        <span class="text-sm font-medium">Precision Shipping</span>
                        <span class="font-bold text-primary">{{ number_format($shipping, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-on-surface-variant">
                        <span class="text-sm font-medium">Calculated Tax (10%)</span>
                        <span class="font-bold text-primary">{{ number_format($tax, 0, ',', '.') }}₫</span>
                    </div>
                </div>
                <div class="flex justify-between items-baseline mb-8">
                    <span class="text-lg font-black text-primary">Total Investment</span>
                    <span class="text-3xl font-black text-primary">{{ number_format($total, 0, ',', '.') }}₫</span>
                </div>
                <button class="w-full bg-brand-blue text-white py-5 rounded-md font-black uppercase tracking-[0.2em] text-sm shadow-xl hover:bg-[#002244] hover:shadow-2xl transition-all active:scale-[0.98]">
                    Đặt hàng
                </button>
            </div>
        </div>
    </div>

    {{-- 4. CUSTOM MODAL XÁC NHẬN XÓA (GIỮA MÀN HÌNH) --}}
    <div x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm"
        style="display: none;">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-outline-variant/20">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">warning</span>
                </div>
                <h3 class="text-2xl font-black text-primary mb-2 uppercase tracking-tight">Xác nhận dọn dẹp?</h3>
                <p class="text-on-surface-variant text-sm leading-relaxed px-4">
                    Bảo có chắc chắn muốn xóa <span class="font-bold text-red-600" x-text="deleteName"></span> khỏi giỏ hàng này không?
                </p>
            </div>

            <div class="flex p-4 gap-3 bg-gray-50 border-t border-gray-100">
                <button @click="showDeleteModal = false"
                    class="flex-1 py-3.5 text-xs font-black text-secondary uppercase tracking-widest hover:bg-white rounded-xl transition-all border border-outline-variant/20">
                    HỦY BỎ
                </button>

                <form action="{{ route('cart.remove') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="id" :value="deleteId">
                    <button type="submit" class="w-full py-3.5 text-xs font-black text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition-all uppercase tracking-widest">
                        XÁC NHẬN XÓA
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection