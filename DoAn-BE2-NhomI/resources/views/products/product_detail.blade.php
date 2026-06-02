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

        {{-- ===== FIX BIẾN THIẾU + ẢNH SẢN PHẨM ===== --}}
        @php
            $relatedProducts = $relatedProducts ?? collect();
            $reviews = $reviews ?? collect();

            $productImage = $product->image_url ?? null;

            if (!$productImage && isset($images) && count($images) > 0) {
                $primaryImg = $images->where('is_primary', 1)->first() ?? $images->first();

                if ($primaryImg) {
                    $productImage = $primaryImg->image_url
                        ?? $primaryImg->url
                        ?? $primaryImg->image
                        ?? $primaryImg->path
                        ?? null;
                }
            }

            $productImage = $productImage ?: 'images/products/default.png';
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start" data-realtime-price-root data-product-id="{{ $product->product_id }}">

            {{-- ===== ẢNH SẢN PHẨM ===== --}}
            <div class="lg:col-span-6 sticky top-24 space-y-6">
                <div class="border-2 border-gray-50 p-8 rounded-3xl bg-white shadow-sm hover:shadow-md transition-shadow">
                    <img id="mainProductImage" src="{{ asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $productImage)) }}"
                         class="w-full h-[450px] object-contain hover:scale-105 transition-transform duration-500"
                         alt="{{ $product->name }}">
                </div>

                {{-- DANH SÁCH ẢNH NHỎ (THUMBNAILS) --}}
                @if(isset($images) && count($images) > 0)
                <div class="grid grid-cols-5 gap-4">
                    @foreach($images as $img)
                        @php 
                            $imgUrl = asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $img->image_url)); 
                            $isCurrentPrimary = ($img->is_primary || ($img->image_url == $productImage) || (str_replace('public/', '', $img->image_url) == str_replace('public/', '', $productImage)));
                        @endphp
                        <div onclick="changeMainImage(this, '{{ $imgUrl }}')"
                             class="thumbnail-item aspect-square bg-white rounded-2xl border-2 {{ $isCurrentPrimary ? 'border-blue-900 shadow-md' : 'border-gray-100 hover:border-blue-200' }} overflow-hidden cursor-pointer transition-all p-2 flex items-center justify-center group shadow-sm">
                            <img alt="Thumbnail" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300" 
                                 src="{{ $imgUrl }}"/>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>


            {{-- ===== THÔNG TIN CHI TIẾT ===== --}}
            <div class="lg:col-span-6 space-y-8">

                <div class="space-y-3">
                    <h1 class="text-4xl font-black text-blue-900 leading-tight">
                        {{ $product->name }}
                    </h1>
                    <div class="flex items-center gap-4">
                        {{-- Giá sẽ nhảy theo biến thể nhờ JS ở dưới --}}
                        @php
                            $firstVariantForPrice = $variants->first();
                            $mainDisplayPrice = \App\Services\ProductPriceService::effectiveVariantPrice(
                                $firstVariantForPrice,
                                (float) $product->base_price
                            );
                        @endphp
                        <p id="mainPrice" class="text-3xl text-red-600 font-black" data-realtime-price data-product-id="{{ $product->product_id }}">
                            {{ number_format($mainDisplayPrice, 0, ',', '.') }}₫
                        </p>
                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Tiết kiệm 10%</span>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    <p class="text-gray-600 leading-relaxed text-sm">
                        {{ $product->description }}
                    </p>
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
                                @php
                                    $attr = json_decode($v->attribute_values, true);
                                    $variantDisplayPrice = \App\Services\ProductPriceService::effectiveVariantPrice($v, (float) $product->base_price);
                                @endphp
                                <button type="button"
                                    class="variant-btn border-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all
                                    {{ $index == 0 ? 'bg-blue-900 text-white border-blue-900 shadow-md' : 'bg-white text-gray-500 border-gray-100 hover:border-blue-200' }}"
                                    data-value="{{ $attr['RAM'] ?? '' }} {{ $attr['ROM'] ?? '' }}"
                                    data-price="{{ number_format($variantDisplayPrice, 0, ',', '.') }}₫"
                                    data-price-value="{{ $variantDisplayPrice }}"
                                    data-variant-id="{{ $v->variant_id }}">
                                    {{ $attr['RAM'] ?? '' }} {{ $attr['ROM'] ?? '' }}
                                </button>
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
                    <input type="hidden" name="variant_id" id="selectedVariantId" value="{{ $variants[0]->variant_id ?? '' }}">

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
                        <img src="{{ asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $productImage)) }}" class="h-24 object-contain mb-2" />
                        <span class="text-sm font-black text-blue-900">{{ $product->name }}</span>
                    </div>
                    <div class="flex-1 text-sm font-bold text-blue-900">
                        <div class="h-24 border-b flex items-center justify-center bg-blue-900/5">A-Series Precision</div>
                        <div class="h-24 border-b flex items-center justify-center">Pro Camera System</div>
                        <div class="h-24 border-b flex items-center justify-center bg-blue-900/5">All-day Battery</div>
                        <div class="h-24 flex items-center justify-center text-lg font-black text-red-600"><span data-realtime-price data-product-id="{{ $product->product_id }}">{{ number_format($product->base_price, 0, ',', '.') }}₫</span></div>
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

        {{-- ===== SẢN PHẨM LIÊN QUAN ===== --}}
        @if(count($relatedProducts))
            <div class="pt-12 border-t border-gray-100">
                <h2 class="text-2xl font-black text-blue-900 mb-8 uppercase tracking-tight">Cùng dòng sản phẩm</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($relatedProducts as $item)
                        @php
                            $itemImage = $item->image_url
                                ?? $item->image
                                ?? $item->thumbnail
                                ?? $item->path
                                ?? 'images/products/default.png';
                        @endphp

                        <a href="{{ url('/product/' . $item->product_id) }}" class="group border border-gray-100 rounded-2xl p-4 hover:shadow-2xl hover:-translate-y-1 transition-all bg-white flex flex-col">
                            <div class="aspect-square mb-4 overflow-hidden rounded-xl">
                                <img src="{{ asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $itemImage)) }}"
                                     class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" 
                                     alt="{{ $item->name }}">
                            </div>
                            <h4 class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-blue-900 transition-colors"> {{ $item->name }} </h4>
                            <p class="text-red-500 font-black text-sm mt-2"><span data-realtime-price data-product-id="{{ $item->product_id }}">{{ number_format($item->base_price, 0, ',', '.') }}₫</span></p>
                        </a>
                    @endforeach
                </div>
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

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
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
                                #star-rating label:hover ~ label {
                                    color: #facc15;
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
                            <input type="file" name="images[]" multiple accept="image/*" onchange="validateReviewImages(this)" class="w-full text-sm text-gray-500
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
        // --- 1. Xử lý Biến thể & Nhảy giá ---
        const variantBtns = document.querySelectorAll('.variant-btn');
        const selectedVariantLabel = document.getElementById('selectedVariant');
        const mainPrice = document.getElementById('mainPrice');
        const hiddenVariantInput = document.getElementById('selectedVariantId');

        variantBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                variantBtns.forEach(b => {
                    b.classList.remove('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');
                    b.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                });

                this.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                this.classList.add('bg-blue-900', 'text-white', 'border-blue-900', 'shadow-md');

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

    function resetCompare() {
        document.getElementById('select-compare-product').value = "";
        document.getElementById('compare-info-2').classList.add('hidden');
        document.getElementById('compare-info-2').classList.remove('flex');
        document.getElementById('select-container').classList.remove('hidden');
        document.getElementById('compare-specs-2').classList.add('opacity-40');
        document.querySelectorAll('#compare-specs-2 .spec-value').forEach(s => s.innerText = "-");
    }

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
        if (e.key === 'ArrowLeft') lbNav(-1);
        if (e.key === 'ArrowRight') lbNav(1);
    });

    function changeMainImage(el, url) {
        const mainImg = document.getElementById('mainProductImage');
        if (mainImg) {
            mainImg.src = url;
        }

        // Bỏ active border của toàn bộ thumbnails
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('border-blue-900', 'shadow-md');
            item.classList.add('border-gray-100', 'hover:border-blue-200');
        });

        // Thêm active border cho thumbnail được click
        el.classList.remove('border-gray-100', 'hover:border-blue-200');
        el.classList.add('border-blue-900', 'shadow-md');
    }

    window.validateReviewImages = function(input) {
        const files = Array.from(input.files);
        let hasInvalidFile = false;
        
        files.forEach(file => {
            if (!file.type.startsWith('image/')) {
                hasInvalidFile = true;
            }
        });
        
        if (hasInvalidFile) {
            alert('Vui lòng chỉ chọn các file hình ảnh (jpeg, png, jpg, gif, webp...). Các file không hợp lệ đã bị loại bỏ.');
            input.value = ''; // Reset input
        }
    }
</script>
@endpush