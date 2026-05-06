@extends('layouts.app')

@section('content')
<main class="pt-10 pb-20 max-w-7xl mx-auto px-6 lg:px-8 font-body">
    <nav class="flex items-center gap-2 text-xs uppercase tracking-widest text-on-surface-variant mb-8 font-label">
        <a class="hover:text-primary transition-colors" href="/">Trang chủ</a>
        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
        <span class="text-primary font-bold">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-surface-container-lowest rounded-xl overflow-hidden aspect-[4/5] flex items-center justify-center p-8 relative shadow-sm">
                <div class="absolute top-6 left-6 flex flex-col gap-2 z-10">
                    @if($product->is_new)
                        <span class="bg-primary text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">New Arrival</span>
                    @endif
                    @if($product->is_hot)
                        <span class="bg-tertiary-container text-on-tertiary-container text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">Hot Sale</span>
                    @endif
                </div>
                
                {{-- Ảnh chính --}}
                @php $primaryImg = $images->where('is_primary', 1)->first() ?? $images->first(); @endphp
                <img alt="{{ $product->name }}" 
                     class="w-full h-full object-contain mix-blend-multiply transition-all duration-500 transform hover:scale-105" 
                     src="{{ asset(str_replace('public/', '', $primaryImg->image_url ?? 'images/products/default.png')) }}"/>
            </div>

            <div class="grid grid-cols-4 gap-4">
                @foreach($images as $img)
                <div class="aspect-square bg-surface-container-lowest rounded-lg border {{ $img->is_primary ? 'border-2 border-primary' : 'border-outline-variant/30' }} overflow-hidden cursor-pointer hover:border-primary transition-all p-2 flex items-center justify-center group">
                    <img alt="Thumbnail" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform" 
                         src="{{ asset(str_replace('public/', '', $img->image_url)) }}"/>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 flex flex-col gap-8">
            <div>
                <h1 class="text-4xl font-black text-primary tracking-tight leading-tight mb-4 uppercase">{{ $product->name }}</h1>
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex text-tertiary-fixed-dim">
                        @for($i = 0; $i < 5; $i++)
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                        @endfor
                    </div>
                    <span class="text-on-surface-variant text-sm font-label">(1,248 nhận xét)</span>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-primary leading-none">{{ number_format($product->base_price, 0, ',', '.') }}₫</div>
                    <div class="text-on-surface-variant line-through text-sm">
                        {{ number_format($product->base_price * 1.15, 0, ',', '.') }}₫ 
                        <span class="text-error font-bold ml-2">-15%</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-xs font-bold uppercase tracking-widest text-on-surface flex items-center justify-between">
                    Cấu hình khả dụng
                    <span class="text-[10px] text-on-surface-variant font-normal">Click để chọn</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($variants as $variant)
                        @php $attrs = json_decode($variant->attribute_values, true); @endphp
                        <button class="py-3 px-2 rounded-md border border-outline-variant bg-surface-container-lowest text-on-surface text-xs font-bold hover:border-primary hover:bg-primary/5 transition-all text-center">
                            {{ $attrs['RAM'] ?? '' }} {{ $attrs['ROM'] ?? '' }}
                            <div class="text-[10px] opacity-60">{{ number_format($variant->price, 0, ',', '.') }}₫</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-xs font-bold uppercase tracking-widest text-on-surface">Màu sắc lựa chọn</label>
                <div class="flex flex-wrap gap-4">
                    <button class="group flex items-center gap-3 pr-4 pl-1 py-1 rounded-full border-2 border-primary bg-primary-fixed/20">
                        <div class="w-6 h-6 rounded-full bg-slate-800 border border-white/20"></div>
                        <span class="text-xs font-bold">Titan Đen</span>
                    </button>
                    <button class="group flex items-center gap-3 pr-4 pl-1 py-1 rounded-full border border-outline-variant">
                        <div class="w-6 h-6 rounded-full bg-slate-200 border border-black/5"></div>
                        <span class="text-xs font-bold">Titan Trắng</span>
                    </button>
                </div>
            </div>

            <div class="bg-primary-container p-4 rounded-xl text-white flex items-center gap-4 border border-primary-container/20 overflow-hidden relative">
                <div class="z-10 flex-1">
                    <p class="text-[10px] uppercase tracking-widest font-bold opacity-80 mb-1">Ưu đãi độc quyền</p>
                    <p class="text-sm font-medium">Trả góp 0% qua thẻ tín dụng B-Tris Pay.</p>
                </div>
                <span class="material-symbols-outlined text-4xl opacity-20 absolute -right-2 -bottom-2" style="font-variation-settings: 'opsz' 48;">payments</span>
            </div>

            <div class="flex gap-4 items-center">
                <button class="flex-1 bg-gradient-to-r from-[#003366] to-[#004a8f] text-white py-5 rounded-md font-bold text-sm tracking-widest uppercase hover:shadow-lg active:scale-[0.98] transition-all">
                    MUA NGAY
                </button>
                <button class="p-5 rounded-md border-2 border-primary-container text-primary-container hover:bg-primary-container hover:text-white active:scale-95 transition-all group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">shopping_cart</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mt-24">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-2xl font-black text-primary tracking-tighter uppercase">Thông số kỹ thuật</h2>
            <div class="h-[2px] flex-1 mx-8 bg-surface-container-high"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-2">
            @php $specs = json_decode($product->specs, true); @endphp
            @if($specs)
                @foreach($specs as $key => $value)
                <div class="group border-b border-surface-container py-4 flex justify-between items-center px-4 hover:bg-surface-container-low transition-colors">
                    <span class="text-on-surface-variant text-sm font-medium">{{ $key }}</span>
                    <span class="text-on-surface text-sm font-bold">{{ $value }}</span>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="mt-24 bg-white rounded-2xl p-8 border border-slate-100 shadow-sm">
        <h2 class="text-2xl font-black text-primary tracking-tighter uppercase mb-6">Đặc điểm nổi bật</h2>
        <div class="prose max-w-none text-slate-600 leading-relaxed">
            {!! $product->description !!}
        </div>
    </div>
</main>
@endsection