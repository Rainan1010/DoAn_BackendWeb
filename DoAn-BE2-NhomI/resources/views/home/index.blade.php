@extends('layouts.app')

@section('content')

    <section class="max-w-[1600px] mx-auto px-6 space-y-6" id="dien-thoai">
        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
            <div class="flex items-center gap-8">
                <h2 class="text-xl font-bold text-brand-blue uppercase tracking-tight">Sản phẩm mới</h2>
                <div class="hidden md:flex gap-6">
                    <button class="text-sm font-bold category-tab-active pb-2">Apple</button>
                    <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue">Samsung</button>
                    <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue">Xiaomi</button>
                    <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue">Google</button>
                </div>
            </div>
            <a class="text-brand-blue text-xs font-bold hover:underline flex items-center gap-1" href="#">Xem tất cả <span
                    class="material-symbols-outlined text-sm">chevron_right</span></a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-3">
                <div class="relative rounded-xl overflow-hidden h-full group">
                    <img alt="Phone Promo"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC5Yrwgbu1xQj8yUsW99S16HFfIGR_2_saZBI2FHypSxM8Npf8X15JK2d_7h99tuup0kvAnSbkY6HFBWOPelwMDw5bvTF2T7kc4egoUGSC0QOyJ60FvI8zHLe8xME4jcyx33T7OlyEc_ydfrVyuQfHu9wXRkxkQ67JAKkyM_KSeRlS9n3qEE4ohYza7LToaJr2_PuWN6fYrgGRJZYKubD2h78rNExSxAoXfsfshUT4xztJPCxV_KE7iY4CrWxVEGAKrd5uywo0IGvLe" />
                    <div class="absolute inset-0 bg-brand-blue/40 flex flex-col justify-end p-6 text-white">
                        <p class="text-sm font-bold">Giá từ</p>
                        <h3 class="text-3xl font-black mb-4">12.990.000₫</h3>
                        <button
                            class="w-full py-2 bg-white text-brand-blue font-black rounded-lg uppercase text-xs tracking-wider">Mua
                            ngay</button>
                    </div>
                </div>
            </div>

            <div class="md:col-span-9 grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($newProducts as $product)
                    <a href="{{ url('/product/' . $product->product_id) }}">
                        <div
                            class="bg-white rounded-xl p-4 border border-slate-100 hover:shadow-xl transition-shadow flex flex-col">

                            <div class="relative aspect-square mb-4">
                                <img alt="{{ $product->name }}" class="w-full h-full object-contain"
                                    src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />

                                {{-- Nhãn NEW: Tự động hiện nếu tạo trong vòng 7 ngày --}}
                                @if(isset($product->created_at) && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 7)
                                    <span
                                        class="absolute top-0 left-0 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded">NEW</span>
                                @endif

                                {{-- Nhãn HOT: Hiện nếu is_hot = 1 trong DB --}}
                                @if($product->is_hot == 1)
                                    <span
                                        class="absolute top-0 right-0 bg-error text-white text-[10px] font-bold px-2 py-0.5 rounded">HOT</span>
                                @endif
                            </div>

                            <h4 class="text-sm font-bold text-slate-800 line-clamp-2 mb-2 flex-1">{{ $product->name }}</h4>
                            <div class="mb-4">
                                <p class="text-brand-blue font-black text-lg">
                                    {{ number_format($product->base_price, 0, ',', '.') }}₫</p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    class="flex-1 bg-brand-blue text-white text-[10px] font-black py-2.5 rounded uppercase tracking-wider">Mua
                                    ngay</button>
                                <button
                                    class="w-10 h-10 border border-brand-blue text-brand-blue rounded flex items-center justify-center hover:bg-brand-blue/5">
                                    <span class="material-symbols-outlined text-xl">shopping_cart</span>
                                </button>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection