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
            <div class="mt-10">
                <h2 class="text-xl font-bold mb-6">Sản phẩm liên quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4"> @foreach($relatedProducts as $item) <a
                    href="{{ url('/product/' . $item->product_id) }}" class="border rounded-xl p-3 hover:shadow bg-white">
                    <img src="{{ asset(str_replace('public/', '', $item->image_url)) }}" class="w-full h-40 object-contain">
                    <h4 class="text-sm font-bold mt-2"> {{ $item->name }} </h4>
                    <p class="text-red-500 font-bold"> {{ number_format($item->base_price, 0, ',', '.') }}₫ </p>
                </a> @endforeach </div>
            </div>
        @endif

        {{-- ===== ĐÁNH GIÁ SẢN PHẨM ===== --}}
        <div class="mt-10 mb-10 bg-white p-6 rounded-xl border">
            <h2 class="text-xl font-bold mb-6 border-b pb-4">Đánh giá sản phẩm</h2>
            
            @if(count($reviews) > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b pb-6 last:border-0 last:pb-0">
                            <div class="flex items-center gap-4 mb-3">
                                @if($review->user && $review->user->avatar_url)
                                    <img src="{{ asset(str_replace('public/', '', $review->user->avatar_url)) }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-lg">
                                        {{ substr($review->user->full_name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $review->user->full_name ?? 'Người dùng ẩn danh' }}</h4>
                                    <div class="flex items-center text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                            @else
                                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 0;">star</span>
                                            @endif
                                        @endfor
                                        <span class="text-xs text-gray-400 ml-2">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 mt-2 text-sm">{{ $review->comment }}</p>
                            
                            @if($review->images && count($review->images) > 0)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @php
                                        $imageUrls = $review->images->map(function($img) {
                                            return "'" . asset(str_replace('public/', '', $img->image_url)) . "'";
                                        })->implode(',');
                                    @endphp
                                    @foreach($review->images as $idx => $img)
                                        <img src="{{ asset(str_replace('public/', '', $img->image_url)) }}" 
                                             onclick="openLightbox([{{ $imageUrls }}], {{ $idx }})"
                                             class="w-20 h-20 object-cover rounded-lg border cursor-pointer hover:opacity-80 transition-opacity" title="Nhấn để phóng to">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-gray-500">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            @endif

            {{-- Thông báo --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Đánh Giá --}}
            @auth
                <div class="mt-8 pt-6 border-t">
                    <h3 class="font-bold mb-4">Viết đánh giá của bạn</h3>
                    <form action="{{ route('product.review.store', $product->product_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá sao</label>
                            
                            <div class="flex flex-row-reverse justify-end items-center space-x-1 space-x-reverse" id="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" class="hidden peer" required />
                                <label for="star5" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 peer-hover:text-yellow-400 transition-colors">
                                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">star</span>
                                </label>

                                <input type="radio" id="star4" name="rating" value="4" class="hidden peer" />
                                <label for="star4" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 peer-hover:text-yellow-400 transition-colors">
                                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">star</span>
                                </label>

                                <input type="radio" id="star3" name="rating" value="3" class="hidden peer" />
                                <label for="star3" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 peer-hover:text-yellow-400 transition-colors">
                                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">star</span>
                                </label>

                                <input type="radio" id="star2" name="rating" value="2" class="hidden peer" />
                                <label for="star2" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 peer-hover:text-yellow-400 transition-colors">
                                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">star</span>
                                </label>

                                <input type="radio" id="star1" name="rating" value="1" class="hidden peer" />
                                <label for="star1" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 peer-hover:text-yellow-400 transition-colors">
                                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">star</span>
                                </label>
                            </div>
                            
                            <style>
                                /* Để hover 1 sao thì các sao bên trái nó cũng sáng theo */
                                #star-rating label:hover ~ label {
                                    color: #facc15; /* tailwind yellow-400 */
                                }
                                #star-rating input:checked ~ label {
                                    color: #facc15;
                                }
                            </style>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nhận xét</label>
                            <textarea name="comment" rows="3" class="w-full border-gray-300 rounded-md shadow-sm p-2 border" placeholder="Mời bạn chia sẻ cảm nhận về sản phẩm..." required></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thêm hình ảnh (Tuỳ chọn)</label>
                            <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer
                            "/>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">Gửi đánh giá</button>
                    </form>
                </div>
            @else
                <div class="mt-8 pt-6 border-t text-center">
                    <p class="text-gray-600">Vui lòng <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">đăng nhập</a> để viết đánh giá.</p>
                </div>
            @endauth
        </div>

    </div>

    {{-- ===================== LIGHTBOX ===================== --}}
    <div id="lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
        <button id="lb-prev" onclick="event.stopPropagation(); lbNav(-1)" class="absolute left-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <img id="lb-img" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl object-contain shadow-2xl" onclick="event.stopPropagation()">
        <button id="lb-next" onclick="event.stopPropagation(); lbNav(1)" class="absolute right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
        <p id="lb-counter" class="absolute bottom-4 text-white/60 text-sm"></p>
    </div>

@endsection
@push('scripts')
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

    // ── Lightbox ──
    let _lbIdx = 0;
    let _lbScale = 1;
    window._lbSrcs = [];

    window.openLightbox = function(srcs, idx) {
        window._lbSrcs = srcs;
        _lbIdx = idx;
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        _lbScale = 1;
        _lbRender();
    };

    function _lbRender() {
        const srcs = window._lbSrcs || [];
        const img = document.getElementById('lb-img');
        img.src = srcs[_lbIdx];
        img.style.transform = `scale(${_lbScale})`;
        img.style.transition = 'transform 0.15s ease';
        document.getElementById('lb-counter').textContent = `${_lbIdx + 1} / ${srcs.length}`;
        document.getElementById('lb-prev').style.display = srcs.length <= 1 ? 'none' : '';
        document.getElementById('lb-next').style.display = srcs.length <= 1 ? 'none' : '';
    }

    window.lbNav = function(dir) {
        const srcs = window._lbSrcs || [];
        if (!srcs.length) return;
        _lbIdx = (_lbIdx + dir + srcs.length) % srcs.length;
        _lbScale = 1;
        _lbRender();
    };

    window.closeLightbox = function() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        _lbScale = 1;
    };

    // Wheel zoom
    document.getElementById('lightbox').addEventListener('wheel', e => {
        if (document.getElementById('lightbox').classList.contains('hidden')) return;
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.15 : 0.15;
        _lbScale = Math.min(5, Math.max(0.5, _lbScale + delta));
        const img = document.getElementById('lb-img');
        img.style.transform = `scale(${_lbScale})`;
    }, { passive: false });

    document.addEventListener('keydown', e => {
        const lb = document.getElementById('lightbox');
        if (!lb || lb.classList.contains('hidden')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft')  lbNav(-1);
        if (e.key === 'ArrowRight') lbNav(1);
    });
</script>
@endpush