@extends('layouts.app')

@section('content')

    {{-- ==================== SẢN PHẨM TRENDING ==================== --}}
    @if($trendingProducts->count() > 0)
    <section class="max-w-[1600px] mx-auto px-6" id="trending">
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
            <div class="p-6 md:p-8">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white px-5 py-2.5 rounded-full">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                            <span class="font-black text-sm uppercase tracking-wider">Trending</span>
                        </div>
                    </div>
                    
                    {{-- Nút điều hướng Slider --}}
                    <div class="flex gap-2">
                        <button onclick="scrollTrending(-1)" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button onclick="scrollTrending(1)" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>

                {{-- Product Slider --}}
                <div class="flex overflow-x-auto gap-4 snap-x snap-mandatory pb-4 hide-scrollbar" id="trending-slider">
                    @foreach($trendingProducts as $index => $product)
                        <div class="snap-start shrink-0 w-[85vw] md:w-[calc(33.333%-0.67rem)] lg:w-[calc(25%-0.75rem)]">
                            <a href="{{ url('/product/' . $product->product_id) }}" class="flex h-full w-full">
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:bg-white/20 hover:border-white/30 transition-all duration-300 flex flex-col flex-1 group h-full w-full">
                                {{-- Trending Rank Badge --}}
                                <div class="relative h-40 md:h-52 mb-4 bg-white/5 rounded-lg flex items-center justify-center p-2">
                                    <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                                        src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />

                                    <span class="absolute top-0 left-0 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-black w-7 h-7 rounded-full flex items-center justify-center shadow-lg">
                                        #{{ $index + 1 }}
                                    </span>

                                    @if($product->view_count > 0)
                                        <span class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[11px]">visibility</span>
                                            {{ number_format($product->view_count) }}
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-bold text-white line-clamp-2 mb-2 flex-1">{{ $product->name }}</h4>
                                <div class="mb-4">
                                    <p class="text-orange-400 font-black text-lg">
                                        {{ number_format($product->base_price, 0, ',', '.') }}₫</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-black py-2.5 rounded uppercase tracking-wider hover:from-orange-600 hover:to-red-600 transition-all">
                                        Mua ngay
                                    </button>
                                    <button class="w-10 h-10 border border-white/30 text-white rounded flex items-center justify-center hover:bg-white/10 transition-colors">
                                        <span class="material-symbols-outlined text-xl">shopping_cart</span>
                                    </button>
                                </div>
                            </div>
                        </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script>
        function scrollTrending(direction) {
            const slider = document.getElementById('trending-slider');
            const cardWidth = slider.firstElementChild.offsetWidth + 16; // 16px for gap-4
            slider.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
        }
    </script>
    @endif

    {{-- ==================== SẢN PHẨM MỚI ==================== --}}
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
                    <a href="{{ url('/product/' . $product->product_id) }}" class="flex h-full">
                        <div
                            class="bg-white rounded-xl p-4 border border-slate-100 hover:shadow-xl transition-shadow flex flex-col flex-1 h-full">

                            <div class="relative h-40 md:h-52 mb-4 bg-slate-50/50 rounded-lg flex items-center justify-center p-2">
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