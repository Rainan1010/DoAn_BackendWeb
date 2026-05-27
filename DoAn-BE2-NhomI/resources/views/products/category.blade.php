@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto px-6 pt-10">
    {{-- Header Danh mục với Gradient sang xịn --}}
    <div class="rounded-2xl overflow-hidden mb-12 shadow-xl" style="background: linear-gradient(135deg, #003366 0%, #004488 50%, #0f3460 100%);">
        <div class="p-8 md:p-12 text-white relative z-10">
            <nav class="flex text-slate-300 text-xs gap-2 mb-4 font-bold uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                <span>/</span>
                <span class="text-white">Danh mục</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tight mb-4">{{ $category->name }}</h1>
            <p class="text-slate-200 text-sm md:text-base max-w-2xl font-light">
                Khám phá bộ sưu tập {{ $category->name }} đỉnh cao công nghệ từ B-Tris. Tinh hoa thiết kế kỹ thuật chính xác, sang trọng vượt trội.
            </p>
        </div>
    </div>

    {{-- Grid Sản phẩm --}}
    <div class="space-y-10 mb-20">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <h2 class="text-xl font-extrabold text-brand-blue uppercase tracking-tight">Tất cả sản phẩm</h2>
            <p class="text-slate-500 text-sm font-semibold">Tìm thấy {{ $products->total() }} siêu phẩm</p>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="group bg-white rounded-2xl p-4 border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                        <a href="{{ url('/product/' . $product->product_id) }}" class="relative aspect-square mb-4 block overflow-hidden rounded-xl bg-slate-50">
                            <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                                src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                @if(isset($product->created_at) && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 7)
                                    <span class="bg-emerald-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg">NEW</span>
                                @endif
                                @if(isset($product->is_hot) && $product->is_hot == 1)
                                    <span class="bg-orange-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg">HOT</span>
                                @endif
                            </div>
                        </a>

                        <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 mb-3 flex-1">
                            <a href="{{ url('/product/' . $product->product_id) }}" class="hover:text-brand-blue">{{ $product->name }}</a>
                        </h4>

                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
                            <p class="text-brand-blue font-bold text-base"><span data-realtime-price data-product-id="{{ $product->product_id }}">{{ number_format($product->base_price, 0, ',', '.') }}₫</span></p>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->product_id }}">
                                <button type="submit" class="w-10 h-10 bg-slate-50 text-brand-blue rounded-xl flex items-center justify-center hover:bg-brand-blue hover:text-white transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="py-10 flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">inventory_2</span>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Chưa có sản phẩm nào</h3>
                <p class="text-slate-400 text-sm mb-6">Chúng tôi đang cập nhật các siêu phẩm cho danh mục này. Quay lại sau nhé!</p>
                <a href="{{ route('home') }}" class="px-6 py-2.5 bg-brand-blue text-white font-bold rounded-full text-sm hover:opacity-90 transition-all">
                    Quay lại trang chủ
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
