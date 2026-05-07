@extends('layouts.app')

@section('content')
<section class="max-w-[1600px] mx-auto px-6 py-10 space-y-6" id="dien-thoai">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div class="flex items-center gap-8">
            <h2 class="text-2xl font-extrabold text-brand-blue uppercase tracking-tight">Sản phẩm mới</h2>
            <div class="hidden md:flex gap-6">
                <button class="text-sm font-bold border-b-2 border-brand-blue pb-2 text-brand-blue">Apple</button>
                <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue transition-colors">Samsung</button>
                <button class="text-sm font-semibold text-slate-500 pb-2 hover:text-brand-blue transition-colors">Xiaomi</button>
            </div>
        </div>
        <a class="text-brand-blue text-sm font-bold hover:gap-2 transition-all flex items-center gap-1" href="#">
            Xem tất cả <span class="material-symbols-outlined text-sm">chevron_right</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

        <!-- Cột Banner Trái: Khắc phục lỗi giãn kịch -->
        <div class="md:col-span-3 sticky top-4 hidden md:block">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-2xl group bg-brand-blue">
                @if(isset($promoProduct))
                <img alt="{{ $promoProduct->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 opacity-80"
                    src="{{ asset(str_replace('public/', '', $promoProduct->image_url)) }}" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-8 text-white">
                    <span class="bg-red-600 w-fit px-3 py-1 rounded-full text-[10px] font-bold mb-3 uppercase tracking-widest">Hot Deal</span>
                    <h3 class="text-2xl font-bold mb-2 leading-tight">{{ $promoProduct->name }}</h3>
                    <p class="text-xl font-light mb-6 text-slate-200">{{ number_format($promoProduct->base_price, 0, ',', '.') }}₫</p>
                    <a href="{{ url('/product/' . $promoProduct->product_id) }}"
                        class="w-full py-3 bg-white text-brand-blue text-center font-bold rounded-xl hover:bg-brand-blue hover:text-white transition-all uppercase text-xs">
                        Mua ngay
                    </a>
                </div>
                @else
                <!-- Fallback khi không có sp promo -->
                <div class="flex items-center justify-center h-full text-white/20 font-bold">BTRIS STORE</div>
                @endif
            </div>
        </div>

        <!-- Danh sách sản phẩm phải -->
        <div class="md:col-span-9 grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($newProducts as $product)
            <div class="group bg-white rounded-2xl p-4 border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                <a href="{{ url('/product/' . $product->product_id) }}" class="relative aspect-square mb-4 block overflow-hidden rounded-xl">
                    <img alt="{{ $product->name }}"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                        src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />

                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if(isset($product->created_at) && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 7)
                            <span class="bg-emerald-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg shadow-sm w-fit">NEW</span>
                            @endif
                            @if($product->is_hot == 1)
                            <span class="bg-orange-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg shadow-sm w-fit">HOT</span>
                            @endif
                    </div>
                </a>

                <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 mb-3 flex-1">
                    <a href="{{ url('/product/' . $product->product_id) }}" class="hover:text-brand-blue transition-colors">
                        {{ $product->name }}
                    </a>
                </h4>

                <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
                    <div>
                        <p class="text-xs text-slate-400 line-through">45.000.000₫</p>
                        <p class="text-brand-blue font-bold text-base leading-none">
                            {{ number_format($product->base_price, 0, ',', '.') }}₫
                        </p>
                    </div>

                    <form action="{{ route('cart.add') }}" method="POST" class="relative group">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->product_id }}">

                        <button type="submit" class="w-10 h-10 bg-slate-50 text-brand-blue rounded-xl flex items-center justify-center hover:bg-brand-blue hover:text-white transition-all shadow-sm">
                            <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
                        </button>

                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 bg-[#0A2540] text-white text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300 whitespace-nowrap shadow-xl">
                            Thêm vào giỏ
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0A2540]"></div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination (Nếu bạn dùng paginate) -->
    <div class="py-10">
        {{ $newProducts->links() }}
    </div>
</section>
@endsection