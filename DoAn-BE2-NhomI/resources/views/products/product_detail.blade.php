@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-6 space-y-10">

        {{-- ===== BREADCRUMB ===== --}}
        <div class="text-sm text-gray-500">
            <a href="{{ url('/') }}" class="text-blue-600 hover:underline">
                Trang chủ
            </a>
            > <span class="font-bold">{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- ===== ẢNH ===== --}}
            <div class="lg:col-span-6">
                <div class="border p-6 rounded-xl bg-white">
                    <img src="{{ asset(str_replace('public/', '', $product->image_url)) }}"
                        class="w-full h-[400px] object-contain">
                </div>
            </div>


            {{-- ===== THÔNG TIN ===== --}}
            <div class="lg:col-span-6 space-y-5">

                {{-- tên --}}
                <h1 class="text-3xl font-bold text-blue-900">
                    {{ $product->name }}
                </h1>

                {{-- giá --}}
                <p class="text-3xl text-red-500 font-bold">
                    {{ number_format($product->base_price, 0, ',', '.') }}₫
                </p>

                {{-- mô tả --}}
                <p class="text-gray-600">
                    {{ $product->description }}
                </p>

                {{-- ================= VARIANTS ================= --}}
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

                {{-- RAM / ROM --}}
                @if(count($rams))
                    <div>
                        <p class="font-bold mb-2">
                            Phiên bản
                            <span id="selectedVariant" class="text-gray-500 text-sm">
                                ({{ $rams[0] ?? '' }})
                            </span>
                        </p>

                        <div class="flex flex-wrap gap-2">
                            @foreach(array_unique($rams) as $index => $ram)
                                <button class="variant-btn border px-4 py-2 rounded 
                                            {{ $index == 0 ? 'bg-blue-600 text-white border-blue-600' : '' }}"
                                    data-value="{{ $ram }}">
                                    {{ $ram }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif


                {{-- MÀU --}}
                @if(count($colors))
                    <div>
                        <p class="font-bold mb-2">Màu sắc</p>

                        <div class="flex flex-wrap gap-2">
                            @foreach(array_unique($colors) as $color)
                                <span class="border px-3 py-1 rounded">
                                    {{ $color }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif


                {{-- ƯU ĐÃI --}}
                <div class="bg-blue-900 text-white p-4 rounded">
                    Giảm thêm 1.000.000đ khi thanh toán qua ví
                </div>

                {{-- BUTTON --}}
                <div class="flex gap-4">
                    <button class="flex-1 bg-blue-600 text-white py-3 rounded font-bold">
                        MUA NGAY
                    </button>

                    <button class="w-14 border border-blue-600 text-blue-600 rounded">
                        🛒
                    </button>
                </div>

                {{-- INFO --}}
                <div class="flex gap-6 text-sm text-gray-600">
                    <span>🚚 Giao nhanh 2H</span>
                    <span>🛡️ Bảo hành 12 tháng</span>
                </div>

            </div>

        </div>

        {{-- ===== SẢN PHẨM LIÊN QUAN ===== --}}
        @if(count($relatedProducts))
            <div>
                <h2 class="text-xl font-bold mb-6">Sản phẩm liên quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4"> @foreach($relatedProducts as $item) <a
                    href="{{ url('/product/' . $item->product_id) }}" class="border rounded-xl p-3 hover:shadow bg-white">
                    <img src="{{ asset(str_replace('public/', '', $item->image_url)) }}" class="w-full h-40 object-contain">
                    <h4 class="text-sm font-bold mt-2"> {{ $item->name }} </h4>
                    <p class="text-red-500 font-bold"> {{ number_format($item->base_price, 0, ',', '.') }}₫ </p>
                </a> @endforeach </div>
            </div>
        @endif



    </div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const variantBtns = document.querySelectorAll('.variant-btn');
        const selectedVariant = document.getElementById('selectedVariant');

        variantBtns.forEach(btn => {
            btn.addEventListener('click', function () {

                // reset tất cả
                variantBtns.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                });

                // chọn cái mới
                this.classList.add('bg-blue-600', 'text-white', 'border-blue-600');

                // update text
                if (selectedVariant) {
                    selectedVariant.innerText = "(" + this.dataset.value + ")";
                }
            });
        });

    });
</script>