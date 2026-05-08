@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 space-y-10 pb-20">

    {{-- ===== 1. BREADCRUMB ===== --}}
    <div class="flex items-center text-sm text-gray-500 gap-2">
        <a href="{{ url('/') }}" class="text-blue-600 hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span> Trang chủ
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-bold text-gray-800">{{ $product->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        {{-- ===== 2. ẢNH SẢN PHẨM ===== --}}
        <div class="lg:col-span-6 sticky top-24">
            <div class="border-2 border-gray-50 p-8 rounded-3xl bg-white shadow-sm hover:shadow-md transition-shadow">
                <img src="{{ asset(str_replace('public/', '', $product->image_url)) }}"
                    class="w-full h-[450px] object-contain hover:scale-105 transition-transform duration-500 cursor-zoom-in"
                    alt="{{ $product->name }}"
                    onclick="openLightbox(['{{ asset(str_replace('public/', '', $product->image_url)) }}'], 0)">
            </div>
        </div>

        {{-- ===== 3. THÔNG TIN CHI TIẾT ===== --}}
        <div class="lg:col-span-6 space-y-8">
            <div class="space-y-3">
                <h1 class="text-4xl font-black text-blue-900 leading-tight">{{ $product->name }}</h1>
                <div class="flex items-center gap-4">
                    {{-- Giá sẽ nhảy theo biến thể nhờ JS ở dưới --}}
                    <p id="mainPrice" class="text-3xl text-red-600 font-black">
                        {{ number_format($variants[0]->price ?? $product->base_price, 0, ',', '.') }}₫
                    </p>
                    <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Tiết kiệm 10%</span>
                </div>
            </div>

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                <p class="text-gray-600 leading-relaxed text-sm">{{ $product->description }}</p>
            </div>

            {{-- PHÂN LOẠI BIẾN THỂ (RAM/ROM) --}}
            @php
                $uniqueVariants = $variants->unique(function ($v) {
                    $attr = json_decode($v->attribute_values, true);
                    return ($attr['RAM'] ?? '') . ($attr['ROM'] ?? '');
                });
            @endphp

            <div class="space-y-6">
                @if(count($uniqueVariants))
                <div>
                    <p class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">memory</span> Phiên bản
                        <span id="selectedVariant" class="text-gray-400 font-medium text-xs">
                            @php $firstAttr = json_decode($uniqueVariants->first()->attribute_values, true); @endphp
                            ({{ ($firstAttr['RAM'] ?? '') . ' ' . ($firstAttr['ROM'] ?? '') }})
                        </span>
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($uniqueVariants as $index => $v)
                            @php $attr = json_decode($v->attribute_values, true); @endphp
                            <button type="button"
                                class="variant-btn border-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all
                                {{ $index == 0 ? 'bg-blue-900 text-white border-blue-900 shadow-md' : 'bg-white text-gray-500 border-gray-100 hover:border-blue-200' }}"
                                data-value="{{ $attr['RAM'] ?? '' }} {{ $attr['ROM'] ?? '' }}"
                                data-price="{{ number_format($v->price, 0, ',', '.') }}₫"
                                data-variant-id="{{ $v->variant_id }}">
                                {{ $attr['RAM'] ?? '' }} {{ $attr['ROM'] ?? '' }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- FORM GIỎ HÀNG (BỔ SUNG NÚT & TOOLTIP) --}}
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-6 pt-4">
                @csrf
                <input type="hidden" name="id" value="{{ $product->product_id }}">
                <input type="hidden" name="variant_id" id="selectedVariantId" value="{{ $variants[0]->variant_id ?? '' }}">
                
                {{-- CHỌN SỐ LƯỢNG --}}
                <div class="flex items-center gap-6">
                    <label class="font-black text-blue-900 text-xs uppercase tracking-widest">Số lượng:</label>
                    <div class="flex items-center border-2 border-gray-100 rounded-xl overflow-hidden w-fit bg-white shadow-sm">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepDown()" class="px-5 py-2 hover:bg-gray-50 text-gray-400 font-black transition-colors">-</button>
                        <input type="number" name="quantity" value="1" min="1" class="w-12 text-center border-none focus:ring-0 font-black text-blue-900 bg-transparent">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepUp()" class="px-5 py-2 hover:bg-gray-50 text-gray-400 font-black transition-colors">+</button>
                    </div>
                </div>

                {{-- CÁC NÚT HÀNH ĐỘNG --}}
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-900 text-white py-4 rounded-2xl font-black hover:bg-blue-800 transition-all shadow-xl uppercase tracking-widest text-xs active:scale-[0.98]">
                        MUA NGAY
                    </button>

                    {{-- BIỂU TƯỢNG GIỎ HÀNG VỚI TOOLTIP --}}
                    <div class="relative group">
                        <button type="submit" class="w-16 h-full border-2 border-blue-900 text-blue-900 rounded-2xl flex items-center justify-center hover:bg-blue-50 transition-all active:scale-90">
                            <span class="material-symbols-outlined text-2xl">add_shopping_cart</span>
                        </button>
                        
                        <div class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-[#003366] text-white text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300 whitespace-nowrap shadow-2xl z-10 uppercase tracking-tighter">
                            Thêm vào giỏ hàng
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#003366]"></div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- CHÍNH SÁCH --}}
            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                <div class="flex items-center gap-3 text-xs font-bold text-gray-500">
                    <span class="material-symbols-outlined text-blue-600">local_shipping</span> Giao nhanh toàn quốc
                </div>
                <div class="flex items-center gap-3 text-xs font-bold text-gray-500">
                    <span class="material-symbols-outlined text-blue-600">verified_user</span> Bảo hành chính hãng 12T
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 4. SO SÁNH CẤU HÌNH ===== --}}
    <section class="mt-24 bg-gray-50 rounded-3xl p-8 border">
        <h2 class="text-2xl font-black text-blue-900 uppercase mb-8">So sánh cấu hình</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 border rounded-2xl overflow-hidden bg-white shadow-xl">
             <div class="hidden md:flex flex-col bg-gray-50/50">
                <div class="h-44 border-b flex items-center justify-center font-black text-gray-400 text-[10px] uppercase">Tiêu chí</div>
                <div class="flex-1 flex flex-col">
                    <div class="h-24 flex items-center px-8 border-b text-xs font-black text-blue-900 bg-gray-50/30 uppercase">Chipset</div>
                    <div class="h-24 flex items-center px-8 border-b text-xs font-black text-blue-900 uppercase">Camera</div>
                    <div class="h-24 flex items-center px-8 border-b text-xs font-black text-blue-900 bg-gray-50/30 uppercase">Pin</div>
                    <div class="h-24 flex items-center px-8 text-xs font-black text-blue-900 uppercase">Giá bán</div>
                </div>
            </div>
            <div class="flex flex-col border-r text-center">
                <div class="h-44 p-6 flex flex-col items-center justify-end border-b">
                    <img src="{{ asset(str_replace('public/', '', $product->image_url)) }}" class="h-24 object-contain mb-2" />
                    <span class="text-sm font-black text-blue-900">{{ $product->name }}</span>
                </div>
                <div class="flex-1 text-sm font-bold text-blue-900">
                    <div class="h-24 border-b flex items-center justify-center bg-blue-900/5">A-Series Precision</div>
                    <div class="h-24 border-b flex items-center justify-center">Pro Camera System</div>
                    <div class="h-24 border-b flex items-center justify-center bg-blue-900/5">All-day Battery</div>
                    <div class="h-24 flex items-center justify-center text-lg font-black text-red-600">{{ number_format($product->base_price, 0, ',', '.') }}₫</div>
                </div>
            </div>
            <div class="flex flex-col" id="compare-column-2">
                <div class="h-44 p-6 flex items-center justify-center border-b bg-gray-50/50">
                    <div id="select-container" class="w-full">
                        <select id="select-compare-product" class="w-full text-xs font-bold border-gray-200 rounded-xl focus:ring-blue-900">
                            <option value="">+ Chọn máy so sánh</option>
                            @foreach($relatedProducts as $item)
                                <option value="{{ $item->product_id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="compare-info-2" class="hidden flex-col items-center gap-2">
                        <img id="compare-img-2" class="h-20 object-contain" src="" />
                        <span id="compare-name-2" class="text-xs font-black text-center text-gray-800 line-clamp-1"></span>
                        <button onclick="resetCompare()" class="text-[9px] text-blue-600 font-bold uppercase">Chọn lại</button>
                    </div>
                </div>
                <div class="flex-1 opacity-40 text-center" id="compare-specs-2">
                    <div class="h-24 border-b flex items-center justify-center bg-gray-50/50 text-sm spec-value" data-spec="chipset">-</div>
                    <div class="h-24 border-b flex items-center justify-center text-sm spec-value" data-spec="camera">-</div>
                    <div class="h-24 border-b flex items-center justify-center bg-gray-50/50 text-sm spec-value" data-spec="battery">-</div>
                    <div class="h-24 flex items-center justify-center text-lg font-bold spec-value" data-spec="price">-</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 5. CÙNG DÒNG SẢN PHẨM ===== --}}
    @if(count($relatedProducts))
        <div class="pt-12 border-t border-gray-100">
            <h2 class="text-2xl font-black text-blue-900 mb-8 uppercase tracking-tight">Cùng dòng sản phẩm</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($relatedProducts as $item)
                    <a href="{{ url('/product/' . $item->product_id) }}" class="group border border-gray-100 rounded-2xl p-4 hover:shadow-2xl hover:-translate-y-1 transition-all bg-white flex flex-col">
                        <div class="aspect-square mb-4 overflow-hidden rounded-xl">
                            <img src="{{ asset(str_replace('public/', '', $item->image_url)) }}" 
                                 class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" 
                                 alt="{{ $item->name }}">
                        </div>
                        <h4 class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-blue-900 transition-colors"> {{ $item->name }} </h4>
                        <p class="text-red-500 font-black text-sm mt-2"> {{ number_format($item->base_price, 0, ',', '.') }}₫ </p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== 6. ĐÁNH GIÁ SẢN PHẨM ===== --}}
    <div class="mt-10 bg-white p-8 rounded-3xl border shadow-sm">
        <h2 class="text-xl font-black text-blue-900 mb-6 border-b pb-4 uppercase tracking-tight">Đánh giá từ khách hàng</h2>
        @if(count($reviews) > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b pb-6 last:border-0">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-900">
                                {{ substr($review->user->full_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">{{ $review->user->full_name ?? 'Người dùng ẩn danh' }}</h4>
                                <div class="flex items-center text-yellow-400 text-[10px]">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' {{ $i <= $review->rating ? 1 : 0 }};">star</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed italic">"{{ $review->comment }}"</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-10 text-gray-400 text-sm font-medium">Sản phẩm này chưa có đánh giá nào.</p>
        @endif
    </div>
</div>

{{-- LIGHTBOX --}}
<div id="lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/95 backdrop-blur-md" onclick="closeLightbox()">
    <button class="absolute top-6 right-6 text-white hover:rotate-90 transition-transform">
        <span class="material-symbols-outlined text-4xl">close</span>
    </button>
    <img id="lb-img" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl object-contain shadow-2xl">
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Xử lý Biến thể & Nhảy giá ---
        const variantBtns = document.querySelectorAll('.variant-btn');
        const selectedVariantLabel = document.getElementById('selectedVariant');
        const mainPrice = document.getElementById('mainPrice');
        const hiddenVariantInput = document.getElementById('selectedVariantId');

        variantBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Reset style tất cả button
                variantBtns.forEach(b => {
                    b.classList.remove('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');
                    b.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                });
                // Active button được chọn
                this.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                this.classList.add('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');

                // Cập nhật thông tin hiển thị và input ẩn
                if (selectedVariantLabel) selectedVariantLabel.innerText = "(" + this.dataset.value + ")";
                if (mainPrice) mainPrice.innerText = this.dataset.price;
                if (hiddenVariantInput) hiddenVariantInput.value = this.dataset.variantId;
            });
        });

        // --- 2. So sánh AJAX ---
        const selectCompare = document.getElementById('select-compare-product');
        if(selectCompare) {
            selectCompare.addEventListener('change', function() {
                const id = this.value;
                if (!id) return;
                fetch(`/api/compare-product/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('select-container').classList.add('hidden');
                        document.getElementById('compare-info-2').classList.remove('hidden');
                        document.getElementById('compare-info-2').classList.add('flex');
                        document.getElementById('compare-img-2').src = data.image;
                        document.getElementById('compare-name-2').innerText = data.name;

                        const container = document.getElementById('compare-specs-2');
                        container.classList.remove('opacity-40');
                        container.querySelectorAll('.spec-value').forEach(span => {
                            const type = span.dataset.spec;
                            span.innerText = (type === 'price') ? data.price : data.specs[type];
                        });
                    });
            });
        }
    });

    // --- 3. Các hàm hỗ trợ ---
    function resetCompare() {
        document.getElementById('select-compare-product').value = "";
        document.getElementById('compare-info-2').classList.add('hidden');
        document.getElementById('compare-info-2').classList.remove('flex');
        document.getElementById('select-container').classList.remove('hidden');
        document.getElementById('compare-specs-2').classList.add('opacity-40');
        document.querySelectorAll('#compare-specs-2 .spec-value').forEach(s => s.innerText = "-");
    }

    window.openLightbox = function(srcs, idx) {
        document.getElementById('lb-img').src = srcs[idx];
        document.getElementById('lightbox').classList.replace('hidden', 'flex');
    };
    window.closeLightbox = function() {
        document.getElementById('lightbox').classList.replace('flex', 'hidden');
    };
</script>
@endpush