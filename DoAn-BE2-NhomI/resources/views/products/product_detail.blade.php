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
                        <p class="text-3xl text-red-600 font-black">
                            {{ number_format($product->base_price, 0, ',', '.') }}₫
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
                    $rams = [];
                    $colors = [];
                @endphp

                @foreach($variants as $v)
                    @php
                        $attr = json_decode($v->attribute_values, true);
                        if (isset($attr['RAM']) && isset($attr['ROM'])) {
                            $rams[] = $attr['RAM'] . ' ' . $attr['ROM'];
                        }
                        if (isset($attr['Màu sắc'])) {
                            $colors[] = $attr['Màu sắc'];
                        }
                    @endphp
                @endforeach

                <div class="space-y-6">
                    {{-- RAM / ROM --}}
                    @if(count($rams))
                        <div>
                            <p class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">memory</span> Phiên bản
                                <span id="selectedVariant" class="text-gray-400 font-medium text-xs">
                                    ({{ array_unique($rams)[0] ?? '' }})
                                </span>
                            </p>

                            <div class="flex flex-wrap gap-3">
                                @foreach(array_unique($rams) as $index => $ram)
                                    <button class="variant-btn border-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all
                                                {{ $index == 0 ? 'bg-blue-900 text-white border-blue-900 shadow-md' : 'bg-white text-gray-500 border-gray-100 hover:border-blue-200' }}"
                                            data-value="{{ $ram }}">
                                        {{ $ram }}
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

                    {{-- CHỌN SỐ LƯỢNG --}}
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

                    {{-- CÁC NÚT HÀNH ĐỘNG --}}
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-900 text-white py-4 rounded-2xl font-black hover:bg-blue-800 transition-all shadow-xl active:scale-[0.98] uppercase tracking-[0.15em] text-xs">
                            MUA NGAY
                        </button>

                        <button type="submit" class="w-16 border-2 border-blue-900 text-blue-900 rounded-2xl flex items-center justify-center hover:bg-blue-50 transition-all relative group active:scale-90 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">add_shopping_cart</span>
                            
                            {{-- Tooltip lời nhắc nhỏ --}}
                            <div class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-blue-900 text-white text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300 whitespace-nowrap shadow-2xl z-10 uppercase">
                                Thêm vào giỏ
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-blue-900"></div>
                            </div>
                        </button>
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
        document.addEventListener('DOMContentLoaded', function () {
            const variantBtns = document.querySelectorAll('.variant-btn');
            const selectedVariant = document.getElementById('selectedVariant');

            variantBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    // reset tất cả style của button
                    variantBtns.forEach(b => {
                        b.classList.remove('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');
                        b.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                    });

                    // apply style cho button được chọn
                    this.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                    this.classList.add('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');

                    // cập nhật text hiển thị phiên bản
                    if (selectedVariant) {
                        selectedVariant.innerText = "(" + this.dataset.value + ")";
                    }
                });
            });
        });
    </script>
@endsection