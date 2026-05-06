@extends('layouts.app') {{-- Hoặc header/footer của Bảo --}}

@section('content')
<main class="pt-24 pb-20 px-6 max-w-7xl mx-auto">
    <header class="mb-12">
        <span class="label-md uppercase tracking-[0.05em] text-on-surface-variant font-medium">Session: {{ session()->getId() }}</span>
        <h1 class="text-5xl font-black tracking-tight text-primary mt-2">Your Workspace.</h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <!-- Cart Items Column -->
        <div class="lg:col-span-8 space-y-8">
            @if(count($cart) > 0)
                @foreach($cart as $id => $details)
                <div class="bg-surface-container-lowest p-6 rounded-md group transition-all duration-300">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="w-full md:w-48 h-48 bg-surface-container-low rounded-md overflow-hidden relative">
                            <img src="{{ $details['image'] }}" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                        </div>
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="text-xl font-bold text-primary tracking-tight">{{ $details['name'] }}</h3>
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button class="text-outline hover:text-error transition-colors">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="flex justify-between items-end mt-6">
                                <div class="flex items-center border-b border-outline-variant/30 pb-1">
                                    <button class="w-8 h-8 flex items-center justify-center text-primary"><span class="material-symbols-outlined">remove</span></button>
                                    <input class="w-12 text-center bg-transparent border-none font-bold text-primary" value="{{ $details['quantity'] }}" readonly>
                                    <button class="w-8 h-8 flex items-center justify-center text-primary"><span class="material-symbols-outlined">add</span></button>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-on-surface-variant uppercase font-medium">Subtotal</div>
                                    <div class="text-2xl font-black text-primary">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}₫</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="p-12 text-center bg-surface-container rounded-md">
                    <p class="text-slate-500 uppercase font-black tracking-widest">Giỏ hàng đang trống</p>
                    <a href="/" class="mt-4 inline-block text-primary font-bold border-b-2 border-primary">Tiếp tục mua sắm</a>
                </div>
            @endif
        </div>

        <!-- Summary Column -->
        <div class="lg:col-span-4 sticky top-24">
            <div class="bg-surface-container p-8 rounded-md shadow-sm">
                <h2 class="text-2xl font-black text-primary tracking-tight mb-8">Order Summary</h2>
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-on-surface-variant">
                        <span class="text-sm font-medium">Configuration Subtotal</span>
                        <span class="font-bold text-primary">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-on-surface-variant">
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
                <button class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary py-5 rounded-md font-black uppercase tracking-[0.2em] text-sm shadow-xl">
                    Initialize Checkout
                </button>
            </div>
        </div>
    </div>
</main>
@endsection