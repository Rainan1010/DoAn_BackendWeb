@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto px-6 pt-10">
    {{-- Header Khuyến mãi với Gradient Rực lửa siêu Hot --}}
    <div class="rounded-2xl overflow-hidden mb-12 shadow-xl" style="background: linear-gradient(135deg, #ba1a1a 0%, #ef4444 50%, #f97316 100%);">
        <div class="p-8 md:p-12 text-white relative z-10">
            <nav class="flex text-white/80 text-xs gap-2 mb-4 font-bold uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                <span>/</span>
                <span class="text-white">Khuyến mãi</span>
            </nav>
            <div class="flex items-center gap-2 bg-white/20 w-fit px-3 py-1.5 rounded-full mb-4 backdrop-blur-sm">
                <span class="material-symbols-outlined text-lg animate-bounce" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                <span class="font-black text-[10px] uppercase tracking-wider">Hot Deals Chỉ Có Tại B-Tris</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tight mb-4">Siêu Khuyến Mãi</h1>
            <p class="text-white/90 text-sm md:text-base max-w-2xl font-light">
                Chào đón các ưu đãi công nghệ lớn nhất trong năm. Các dòng sản phẩm cao cấp chính xác từ B-Tris đang giảm giá kịch sàn với số lượng giới hạn. Mua ngay kẻo lỡ!
            </p>
        </div>
    </div>

    {{-- Grid Sản phẩm --}}
    <div class="space-y-10 mb-20">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <h2 class="text-xl font-extrabold text-brand-blue uppercase tracking-tight">Sản phẩm khuyến mãi bán chạy</h2>
            <p class="text-slate-500 text-sm font-semibold">Tìm thấy {{ $products->total() }} siêu phẩm ưu đãi</p>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="group bg-white rounded-2xl p-4 border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                        <a href="{{ url('/product/' . $product->product_id) }}" class="relative aspect-square mb-4 block overflow-hidden rounded-xl bg-slate-50">
                            <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                                src="{{ asset(str_replace('public/', '', $product->image_url)) }}" />
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                <span class="bg-red-600 text-white text-[9px] font-black px-2 py-1 rounded-lg uppercase tracking-wider">HOT DEAL</span>
                                @if(isset($product->created_at) && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 7)
                                    <span class="bg-emerald-500 text-white text-[9px] font-bold px-2 py-1 rounded-lg">NEW</span>
                                @endif
                            </div>
                        </a>

                        <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 mb-3 flex-1">
                            <a href="{{ url('/product/' . $product->product_id) }}" class="hover:text-brand-blue">{{ $product->name }}</a>
                        </h4>

                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
                            <p class="text-red-600 font-black text-base">{{ number_format($product->base_price, 0, ',', '.') }}₫</p>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->product_id }}">
                                <button type="submit" class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all active:scale-95">
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
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">percent</span>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Hiện chưa có chương trình ưu đãi nào mới</h3>
                <p class="text-slate-400 text-sm mb-6">Đang chuẩn bị cập nhật đợt khuyến mãi tiếp theo. Hãy quay lại sau ít phút nhé!</p>
                <a href="{{ route('home') }}" class="px-6 py-2.5 bg-brand-blue text-white font-bold rounded-full text-sm hover:opacity-90 transition-all">
                    Quay lại trang chủ
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
