@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 space-y-10 pb-20">

    {{-- ===== BREADCRUMB ===== --}}
    <div class="flex items-center text-sm text-gray-500 gap-2">
        <a href="{{ url('/') }}" class="text-blue-600 hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span> Trang chủ
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-bold text-gray-800">{{ $product->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        {{-- ===== ẢNH SẢN PHẨM ===== --}}
        <div class="lg:col-span-6 sticky top-24">
            <div class="border-2 border-gray-50 p-8 rounded-3xl bg-white shadow-sm hover:shadow-md transition-shadow">
                <img src="{{ asset(str_replace('public/', '', $product->image_url)) }}"
                    class="w-full h-[450px] object-contain hover:scale-105 transition-transform duration-500"
                    alt="{{ $product->name }}">
            </div>
        </div>

        {{-- ===== THÔNG TIN CHI TIẾT ===== --}}
        <div class="lg:col-span-6 space-y-8">
            <div class="space-y-3">
                <h1 class="text-4xl font-black text-blue-900 leading-tight">
                    {{ $product->name }}
                </h1>
                <div class="flex items-center gap-4">
                    {{-- FIX: Thêm id="mainPrice" để nhảy giá --}}
                    <p id="mainPrice" class="text-3xl text-red-600 font-black">
                        {{ number_format($variants[0]->price ?? $product->base_price, 0, ',', '.') }}₫
                    </p>
                    <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Tiết kiệm 10%</span>
                </div>
            </div>

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                <p class="text-gray-600 leading-relaxed text-sm">
                    {{ $product->description }}
                </p>
            </div>

            {{-- ================= PHÂN LOẠI (VARIANTS) ================= --}}
            @php
            // Lấy danh sách cấu hình RAM/ROM duy nhất để tạo nút
            $uniqueVariants = $variants->unique(function ($v) {
            $attr = json_decode($v->attribute_values, true);
            return ($attr['RAM'] ?? '') . ($attr['ROM'] ?? '');
            });

            $colors = [];
            foreach($variants as $v) {
            $attr = json_decode($v->attribute_values, true);
            if (isset($attr['Màu sắc'])) $colors[] = $attr['Màu sắc'];
            }
            @endphp

            <div class="space-y-6">
                {{-- RAM / ROM --}}
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

                {{-- MÀU SẮC --}}
                @if(count($colors))
                <div>
                    <p class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">palette</span> Màu sắc
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @foreach(array_unique($colors) as $color)
                        <span class="border-2 border-gray-100 px-4 py-2 rounded-xl text-sm font-bold text-gray-600 bg-white">
                            {{ $color }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- ƯU ĐÃI ĐẶC BIỆT --}}
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white p-4 rounded-2xl flex items-center gap-4 shadow-lg shadow-blue-900/10">
                <span class="material-symbols-outlined text-yellow-400">workspace_premium</span>
                <p class="text-sm font-medium">Giảm thêm 1.000.000đ khi thanh toán qua ví B-Tris Pay hoặc chuyển khoản.</p>
            </div>

            {{-- ================= FORM THÊM GIỎ HÀNG ================= --}}
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-6 pt-4">
                @csrf
                <input type="hidden" name="id" value="{{ $product->product_id }}">

                {{-- FIX: Input ẩn để gửi variant_id lên server --}}
                <input type="hidden" name="variant_id" id="selectedVariantId" value="{{ $variants[0]->variant_id ?? '' }}">

                <div class="flex items-center gap-6">
                    <label class="font-black text-blue-900 text-xs uppercase tracking-widest">Số lượng:</label>
                    <div class="flex items-center border-2 border-gray-100 rounded-xl overflow-hidden w-fit bg-white shadow-sm">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepDown()"
                            class="px-5 py-2 hover:bg-gray-50 text-gray-400 transition-colors font-black text-lg">-</button>
                        <input type="number" name="quantity" value="1" min="1"
                            class="w-12 text-center border-none focus:ring-0 font-black text-blue-900 bg-transparent text-sm">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepUp()"
                            class="px-5 py-2 hover:bg-gray-50 text-gray-400 transition-colors font-black text-lg">+</button>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-900 text-white py-4 rounded-2xl font-black hover:bg-blue-800 transition-all shadow-xl active:scale-[0.98] uppercase tracking-[0.15em] text-xs">
                        MUA NGAY
                    </button>
                    <button type="submit" class="w-16 border-2 border-blue-900 text-blue-900 rounded-2xl flex items-center justify-center hover:bg-blue-50 transition-all relative group active:scale-90 shadow-sm">
                        <span class="material-symbols-outlined text-2xl">add_shopping_cart</span>
                        <div class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-blue-900 text-white text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300 whitespace-nowrap shadow-2xl z-10 uppercase">
                            Thêm vào giỏ
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-blue-900"></div>
                        </div>
                    </button>
                </div>
            </form>

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

    {{-- ===== SECTION SO SÁNH SẢN PHẨM CHỌN LỌC ===== --}}
    <section class="mt-24 bg-gray-50 rounded-3xl p-8 lg:p-12 border border-gray-100">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-2xl font-black text-blue-900 tracking-tighter uppercase">So sánh cấu hình</h2>
            <div class="h-[2px] flex-1 mx-8 bg-blue-900/10"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-0 border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-xl">
            {{-- Cột Tiêu chí --}}
            <div class="hidden md:flex flex-col bg-gray-50/50">
                <div class="h-44 flex items-center justify-center border-b border-gray-100 px-6 font-black text-gray-400 text-[10px] uppercase">Tiêu chí</div>
                <div class="flex-1 flex flex-col">
                    <div class="h-24 flex items-center px-8 border-b border-gray-100 text-xs font-black uppercase text-blue-900 bg-gray-50/30">Chipset</div>
                    <div class="h-24 flex items-center px-8 border-b border-gray-100 text-xs font-black uppercase text-blue-900">Camera</div>
                    <div class="h-24 flex items-center px-8 border-b border-gray-100 text-xs font-black uppercase text-blue-900 bg-gray-50/30">Pin & Sạc</div>
                    <div class="h-24 flex items-center px-8 text-xs font-black uppercase text-blue-900">Giá bán</div>
                </div>
            </div>
            {{-- Cột Máy hiện tại --}}
            <div class="flex flex-col border-r border-gray-100 relative bg-blue-50/5 text-center">
                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-10">
                    <span class="bg-blue-900 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">Sản phẩm này</span>
                </div>
                <div class="h-44 p-6 flex flex-col items-center justify-end gap-3 border-b border-gray-100">
                    <img src="{{ asset(str_replace('public/', '', $product->image_url)) }}" class="h-24 w-auto object-contain drop-shadow-xl" />
                    <span class="text-sm font-black text-blue-900">{{ $product->name }}</span>
                </div>
                <div class="flex-1 text-sm font-bold text-blue-900">
                    <div class="h-24 flex items-center justify-center border-b border-gray-100 bg-blue-900/5">A18 Pro (Mẫu)</div>
                    <div class="h-24 flex items-center justify-center border-b border-gray-100">48MP + 48MP</div>
                    <div class="h-24 flex items-center justify-center border-b border-gray-100 bg-blue-900/5">5.000 mAh</div>
                    <div class="h-24 flex items-center justify-center text-lg font-black text-red-600">{{ number_format($product->base_price, 0, ',', '.') }}₫</div>
                </div>
            </div>
            {{-- Cột Chọn máy so sánh --}}
            <div class="flex flex-col" id="compare-column-2">
                <div class="h-44 p-6 flex flex-col items-center justify-center gap-3 border-b border-gray-100 bg-gray-50/50">
                    <div id="select-container" class="w-full">
                        <select id="select-compare-product" class="w-full text-xs font-bold border-gray-200 rounded-xl focus:ring-blue-900 focus:border-blue-900 cursor-pointer">
                            <option value="">+ Chọn máy so sánh</option>
                            @foreach($relatedProducts as $item)
                            <option value="{{ $item->product_id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="compare-info-2" class="hidden flex-col items-center gap-2">
                        <img id="compare-img-2" class="h-20 w-auto object-contain drop-shadow-md" src="" />
                        <span id="compare-name-2" class="text-xs font-black text-center text-gray-800 line-clamp-1"></span>
                        <button onclick="resetCompare()" class="text-[9px] text-blue-600 font-bold hover:underline uppercase">Chọn máy khác</button>
                    </div>
                </div>
                <div class="flex-1 opacity-40 transition-opacity duration-500 text-center" id="compare-specs-2">
                    <div class="h-24 flex items-center justify-center border-b border-gray-100 bg-gray-50/50 text-sm font-medium spec-value" data-spec="chipset">-</div>
                    <div class="h-24 flex items-center justify-center border-b border-gray-100 text-sm font-medium spec-value" data-spec="camera">-</div>
                    <div class="h-24 flex items-center justify-center border-b border-gray-100 bg-gray-50/50 text-sm font-medium spec-value" data-spec="battery">-</div>
                    <div class="h-24 flex items-center justify-center text-lg font-bold spec-value" data-spec="price">-</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SẢN PHẨM LIÊN QUAN ===== --}}
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantBtns = document.querySelectorAll('.variant-btn');
        const selectedVariant = document.getElementById('selectedVariant');
        const mainPrice = document.getElementById('mainPrice');
        const hiddenVariantInput = document.getElementById('selectedVariantId');

        variantBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Style buttons
                variantBtns.forEach(b => {
                    b.classList.remove('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');
                    b.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                });
                this.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                this.classList.add('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');

                // Update UI
                if (selectedVariant) selectedVariant.innerText = "(" + this.dataset.value + ")";
                if (mainPrice) mainPrice.innerText = this.dataset.price;
                if (hiddenVariantInput) hiddenVariantInput.value = this.dataset.variantId;
            });
        });
    });

    // Logic So Sánh
    function resetCompare() {
        document.getElementById('select-compare-product').value = "";
        document.getElementById('compare-info-2').classList.add('hidden');
        document.getElementById('compare-info-2').classList.remove('flex');
        document.getElementById('select-container').classList.remove('hidden');
        document.getElementById('compare-specs-2').classList.add('opacity-40');
        document.querySelectorAll('#compare-specs-2 .spec-value').forEach(s => s.innerText = "-");
    }

    document.getElementById('select-compare-product').addEventListener('change', function() {
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

    function resetCompare() {
        document.getElementById('select-compare-product').value = "";
        document.getElementById('compare-info-2').classList.add('hidden');
        document.getElementById('compare-info-2').classList.remove('flex');
        document.getElementById('select-container').classList.remove('hidden');
        document.getElementById('compare-specs-2').classList.add('opacity-40');
        document.querySelectorAll('#compare-specs-2 .spec-value').forEach(s => s.innerText = "-");
    }

    document.getElementById('select-compare-product').addEventListener('change', function() {
        const id = this.value;
        if (!id) return;

        // Bắt đầu Fetch dữ liệu từ API bạn vừa kiểm tra
        fetch(`/api/compare-product/${id}`)
            .then(res => res.json())
            .then(data => {
                // 1. Hiển thị thông tin máy (Ảnh và Tên)
                document.getElementById('select-container').classList.add('hidden');
                document.getElementById('compare-info-2').classList.remove('hidden');
                document.getElementById('compare-info-2').classList.add('flex');

                document.getElementById('compare-img-2').src = data.image;
                document.getElementById('compare-name-2').innerText = data.name;

                // 2. Kích hoạt cột thông số (bỏ mờ)
                const container = document.getElementById('compare-specs-2');
                container.classList.remove('opacity-40');

                // 3. Đổ dữ liệu vào từng hàng dựa trên data-spec
                container.querySelectorAll('.spec-value').forEach(span => {
                    const type = span.dataset.spec; // chipset, camera, battery, price

                    // Kiểm tra phím tương ứng trong JSON của bạn
                    if (type === 'price') {
                        span.innerText = data.price; // Lấy từ data.price
                    } else if (data.specs && data.specs[type]) {
                        span.innerText = data.specs[type]; // Lấy từ data.specs.chipset, ...
                    }
                });
            })
            .catch(err => {
                console.error("Lỗi:", err);
                alert("Không thể tải dữ liệu so sánh!");
            });
    });
</script>
@endsection