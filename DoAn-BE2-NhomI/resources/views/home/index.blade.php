@extends('layouts.app')

@section('content')

{{-- ==================== 1. SẢN PHẨM TRENDING ==================== --}}
@if(isset($trendingProducts) && $trendingProducts->count() > 0)
<section class="max-w-[1600px] mx-auto px-6 pt-10" id="trending">
    <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
        <div class="p-6 md:p-8">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white px-5 py-2.5 rounded-full">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">
                            local_fire_department
                        </span>
                        <span class="font-black text-sm uppercase tracking-wider">
                            Trending
                        </span>
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
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:bg-white/20 hover:border-white/30 transition-all duration-300 flex flex-col flex-1 group h-full w-full">

                        {{-- Link chỉ bọc ảnh + thông tin, KHÔNG bọc nút giỏ hàng --}}
                        <a href="{{ url('/product/' . $product->product_id) }}" class="block">
                            <div class="relative h-40 md:h-52 mb-4 bg-white/5 rounded-lg flex items-center justify-center p-2">
                                <img
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                                    src="{{ asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $product->image_url)) }}" />

                                <span class="absolute top-0 left-0 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-black w-7 h-7 rounded-full flex items-center justify-center shadow-lg">
                                    #{{ $index + 1 }}
                                </span>

                                @if($product->view_count > 0)
                                <span class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[11px]">
                                        visibility
                                    </span>
                                    {{ number_format($product->view_count) }}
                                </span>
                                @endif
                            </div>

                            <h4 class="text-sm font-bold text-white line-clamp-2 mb-2 flex-1">
                                {{ $product->name }}
                            </h4>

                            <div class="mb-4">
                                <p class="text-orange-400 font-black text-lg">
                                    <span data-realtime-price data-product-id="{{ $product->product_id }}">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
                                </p>
                            </div>
                        </a>

                        {{-- Nút hành động nằm ngoài thẻ a --}}
                        <div class="flex gap-2 mt-auto">
                            <a
                                href="{{ url('/product/' . $product->product_id) }}"
                                class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-black py-2.5 rounded uppercase tracking-wider text-center">
                                Mua ngay
                            </a>

                            <form action="{{ route('cart.add') }}" method="POST" onclick="event.stopPropagation();">
                                @csrf

                                <input type="hidden" name="id" value="{{ $product->product_id }}">
                                <input type="hidden" name="quantity" value="1">

                                <button
                                    type="submit"
                                    onclick="event.stopPropagation();"
                                    class="w-10 h-10 border border-white/30 text-white rounded flex items-center justify-center hover:bg-white/10 transition-colors"
                                    title="Thêm vào giỏ hàng">
                                    <span class="material-symbols-outlined text-xl">
                                        shopping_cart
                                    </span>
                                </button>
                            </form>

                            <form action="{{ route('compare.add') }}" method="POST" onclick="event.stopPropagation();">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                <button type="submit" onclick="event.stopPropagation();" title="So sánh"
                                    class="w-10 h-10 border border-white/30 text-white rounded flex items-center justify-center hover:bg-white/10 transition-colors">
                                    <span class="material-symbols-outlined text-xl">compare_arrows</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Dot Pagination --}}
            <div class="flex items-center justify-center gap-2 mt-4" id="trending-dots"></div>
        </div>
    </div>
</section>
@endif

{{-- ==================== 2. SẢN PHẨM MỚI ==================== --}}
<section class="max-w-[1600px] mx-auto px-6 py-10 space-y-6" id="dien-thoai">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div class="flex items-center gap-8">
            <h2 class="text-2xl font-extrabold text-brand-blue uppercase tracking-tight">
                Sản phẩm mới
            </h2>

            <div class="hidden md:flex gap-6">
                <button class="text-sm font-bold border-b-2 border-brand-blue pb-2 text-brand-blue">
                    Apple
                </button>

                <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue">
                    Samsung
                </button>

                <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue">
                    Xiaomi
                </button>
            </div>
        </div>

        <a class="text-brand-blue text-sm font-bold hover:gap-2 transition-all flex items-center gap-1" href="#">
            Xem tất cả
            <span class="material-symbols-outlined text-sm">
                chevron_right
            </span>
        </a>
    </div>

    {{-- ==================== 3. Bộ lọc ==================== --}}
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-wrap items-center gap-3">

            <div class="flex items-center gap-2 mr-3">
                <span class="material-symbols-outlined text-brand-blue">
                filter_alt
                </span>

                <span class="font-semibold text-slate-800">
                    Bộ lọc
                </span>
            </div>

            {{-- Danh mục --}}
            <select name="category_id" class="h-11 min-w-[180px] rounded-xl border border-slate-300 px-4 text-sm">

                <option value="">Danh mục</option>

                 @foreach($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                    </option>
                @endforeach

            </select>

            {{-- Thương hiệu --}}
            <select name="brand_id"
                class="h-11 min-w-[180px] rounded-xl border border-slate-300 px-4 text-sm">

                <option value="">Thương hiệu</option>

                @foreach($brands as $brand)
                    <option value="{{ $brand->brand_id }}"
                        {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach

            </select>

            {{-- Giá --}}
            <label class="cursor-pointer">
                <input type="radio"
                    name="price_range"
                    value="under_10"
                    class="hidden peer"
                    {{ request('price_range') == 'under_10' ? 'checked' : '' }}>

                <span
                    class="px-4 py-2 rounded-full border border-slate-300 bg-slate-50
                    peer-checked:bg-brand-blue
                    peer-checked:text-white
                    peer-checked:border-brand-blue
                    hover:border-brand-blue transition">
                    Dưới 10tr
                </span>
            </label>

            <label class="cursor-pointer">
                <input type="radio"
                    name="price_range"
                    value="10_20"
                    class="hidden peer"
                    {{ request('price_range') == '10_20' ? 'checked' : '' }}>

                <span
                    class="px-4 py-2 rounded-full border border-slate-300 bg-slate-50
                    peer-checked:bg-brand-blue
                    peer-checked:text-white
                    peer-checked:border-brand-blue
                    hover:border-brand-blue transition">
                    10-20tr
                </span>
            </label>

            <label class="cursor-pointer">
                <input type="radio"
                    name="price_range"
                    value="20_30"
                    class="hidden peer"
                    {{ request('price_range') == '20_30' ? 'checked' : '' }}>

                <span
                    class="px-4 py-2 rounded-full border border-slate-300 bg-slate-50
                    peer-checked:bg-brand-blue
                    peer-checked:text-white
                    peer-checked:border-brand-blue
                    hover:border-brand-blue transition">
                    20-30tr
                </span>
            </label>

            <label class="cursor-pointer">
                <input type="radio"
                    name="price_range"
                    value="over_30"
                    class="hidden peer"
                    {{ request('price_range') == 'over_30' ? 'checked' : '' }}>

                <span
                    class="px-4 py-2 rounded-full border border-slate-300 bg-slate-50
                    peer-checked:bg-brand-blue
                    peer-checked:text-white
                    peer-checked:border-brand-blue
                    hover:border-brand-blue transition">
                    Trên 30tr
                </span>
            </label>

            {{-- Nút lọc --}}
            <button type="submit"
                class="h-11 px-6 rounded-xl bg-brand-blue text-white font-medium hover:opacity-90 transition">
                Áp dụng
            </button>

            {{-- Xóa bộ lọc --}}
            @if(request()->hasAny(['category_id','brand_id','price_range']))
                <a href="{{ route('home') }}"
                    class="h-11 px-5 rounded-xl border border-slate-300 flex items-center hover:bg-slate-100 transition">
                    Xóa dữ liệu bộ lọc
                </a>
            @endif

        </div>

    </form>
    {{-- Main Content --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        {{-- Cột Banner Trái --}}
        <div class="md:col-span-3 sticky top-4 hidden md:block">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-2xl group bg-brand-blue">
                @if(isset($promoProduct))
                <img
                    alt="{{ $promoProduct->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 opacity-80"
                    src="{{ asset(str_replace('public/', '', $promoProduct->image_url)) }}" />

                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-8 text-white">
                    <span class="bg-red-600 w-fit px-3 py-1 rounded-full text-[10px] font-bold mb-3 uppercase tracking-widest">
                        Hot Deal
                    </span>

                    <h3 class="text-2xl font-bold mb-2 leading-tight">
                        {{ $promoProduct->name }}
                    </h3>

                    <p class="text-xl font-light mb-6 text-slate-200">
                        <span data-realtime-price data-product-id="{{ $promoProduct->product_id }}">{{ number_format($promoProduct->base_price, 0, ',', '.') }}₫</span>
                    </p>

                    <a href="{{ url('/product/' . $promoProduct->product_id) }}" class="w-full py-3 bg-white text-brand-blue text-center font-bold rounded-xl hover:bg-brand-blue hover:text-white transition-all uppercase text-xs">
                        Mua ngay
                    </a>
                </div>
                @else
                <div class="flex items-center justify-center h-full text-white/20 font-bold">
                    BTRIS STORE
                </div>
                @endif
            </div>
        </div>

        {{-- Grid Sản phẩm --}}
        <div class="md:col-span-9 grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($newProducts as $product)
            <div class="group bg-white rounded-2xl p-4 border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">

                {{-- Ảnh sản phẩm --}}
                <a href="{{ url('/product/' . $product->product_id) }}" class="relative aspect-square mb-4 block overflow-hidden rounded-xl">
                    <img
                        alt="{{ $product->name }}"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                        src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />

                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if(isset($product->created_at) && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 7)
                            <span class="bg-emerald-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg">
                            NEW
                            </span>
                            @endif

                            @if(isset($product->is_hot) && $product->is_hot == 1)
                            <span class="bg-orange-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg">
                                HOT
                            </span>
                            @endif
                    </div>
                </a>

                {{-- Tên sản phẩm --}}
                <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 mb-3 flex-1">
                    <a href="{{ url('/product/' . $product->product_id) }}" class="hover:text-brand-blue">
                        {{ $product->name }}
                    </a>
                </h4>

                {{-- Giá + thêm giỏ hàng --}}
                <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
                    <p class="text-brand-blue font-bold text-base">
                        <span data-realtime-price data-product-id="{{ $product->product_id }}">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
                    </p>

                    <form action="{{ route('cart.add') }}" method="POST" onclick="event.stopPropagation();">
                        @csrf

                        <input type="hidden" name="id" value="{{ $product->product_id }}">
                        <input type="hidden" name="quantity" value="1">

                        <button
                            type="submit"
                            onclick="event.stopPropagation();"
                            class="w-10 h-10 bg-slate-50 text-brand-blue rounded-xl flex items-center justify-center hover:bg-brand-blue hover:text-white transition-all"
                            title="Thêm vào giỏ hàng">
                            <span class="material-symbols-outlined text-xl">
                                add_shopping_cart
                            </span>
                        </button>
                    </form>

                    <form action="{{ route('compare.add') }}" method="POST" onclick="event.stopPropagation();" class="ml-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <button type="submit" onclick="event.stopPropagation();" title="So sánh"
                            class="w-10 h-10 border border-slate-100 text-brand-blue rounded-xl flex items-center justify-center hover:bg-brand-blue hover:text-white transition-all">
                            <span class="material-symbols-outlined text-xl">compare_arrows</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="py-10">
        {{ $newProducts->links() }}
    </div>
</section>

{{-- Script & Style cho Trending Slider --}}
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .trending-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .trending-dot.active {
        width: 24px;
        background: linear-gradient(to right, #f97316, #ef4444);
    }
</style>

<script>
    (function() {
        const slider = document.getElementById('trending-slider');
        const dotsContainer = document.getElementById('trending-dots');

        if (!slider || !dotsContainer) return;

        let autoTimer = null;
        let currentPage = 0;

        function getPageCount() {
            const pageWidth = slider.clientWidth;
            return Math.max(1, Math.ceil(slider.scrollWidth / pageWidth));
        }

        function buildDots() {
            dotsContainer.innerHTML = '';

            const count = getPageCount();

            for (let i = 0; i < count; i++) {
                const btn = document.createElement('button');

                btn.className = 'trending-dot' + (i === currentPage ? ' active' : '');
                btn.setAttribute('aria-label', 'Trang ' + (i + 1));
                btn.addEventListener('click', () => goToPage(i));

                dotsContainer.appendChild(btn);
            }
        }

        function updateDots() {
            const dots = dotsContainer.querySelectorAll('.trending-dot');

            dots.forEach((d, i) => {
                d.classList.toggle('active', i === currentPage);
            });
        }

        function goToPage(page) {
            const count = getPageCount();

            currentPage = (page + count) % count;

            slider.scrollTo({
                left: currentPage * slider.clientWidth,
                behavior: 'smooth'
            });

            updateDots();
        }

        window.scrollTrending = function(direction) {
            goToPage(currentPage + direction);
        };

        const trendingSection = document.getElementById('trending');
        if (trendingSection) {
            trendingSection.addEventListener('mouseenter', stopAuto);
            trendingSection.addEventListener('mouseleave', startAuto);
        } else {
            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);
        }

        slider.addEventListener('scroll', () => {
            const page = Math.round(slider.scrollLeft / slider.clientWidth);

            if (page !== currentPage) {
                currentPage = page;
                updateDots();
            }
        });

        function startAuto() {
            autoTimer = setInterval(() => goToPage(currentPage + 1), 5000);
        }

        function stopAuto() {
            clearInterval(autoTimer);
        }

        buildDots();
        startAuto();

        window.addEventListener('resize', () => {
            buildDots();
        });
    })();
</script>

@endsection