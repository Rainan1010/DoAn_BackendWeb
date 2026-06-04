@extends('admin.layouts.app')

@section('header_search')
<form method="GET" action="{{ route('admin.reviews.index') }}">
    <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Tìm kiếm đánh giá hoặc mã sản phẩm..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50">
    </div>
</form>
@endsection

@section('content')
{{-- Tiêu đề --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Quản lý đánh giá</h1>
        <p class="text-sm text-gray-500 mt-0.5">Xem và phê duyệt các đánh giá từ khách hàng về sản phẩm.</p>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 border-l-4 border-blue-500 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tổng đánh giá</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-yellow-400 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Chờ phê duyệt</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
        @if($stats['pending'] > 0)
            <span class="inline-block w-2 h-2 rounded-full bg-yellow-400 mt-1"></span>
        @endif
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-green-500 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Điểm trung bình</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['avg_rating'] ?? '—' }}</p>
        <div class="flex gap-0.5 mt-1">
            @for($i=1;$i<=5;$i++)
                <svg class="w-3 h-3 {{ $i <= round($stats['avg_rating']) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-red-400 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Đánh giá bị ẩn</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['hidden'] }}</p>
        @if($stats['hidden'] > 0)
            <span class="text-xs text-red-500 font-medium">Cần xem lại</span>
        @endif
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 p-4">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 flex-wrap">
        <span class="text-sm font-semibold text-gray-500">LỌC THEO:</span>
        <select name="status" onchange="this.form.submit()"
            class="text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
            <option value="all" {{ request('status','all')=='all'?'selected':'' }}>Tất cả trạng thái</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ duyệt</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Đã duyệt</option>
            <option value="hidden" {{ request('status')=='hidden'?'selected':'' }}>Đã ẩn</option>
        </select>
        <select name="rating" onchange="this.form.submit()"
            class="text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
            <option value="all" {{ request('rating','all')=='all'?'selected':'' }}>Tất cả đánh giá</option>
            @for($r=5;$r>=1;$r--)
                <option value="{{ $r }}" {{ request('rating')==$r?'selected':'' }}>{{ $r }} sao</option>
            @endfor
        </select>
        @if(request('search') || request('status') || request('rating'))
            <a href="{{ route('admin.reviews.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Xóa lọc</a>
        @endif
        <span class="ml-auto text-sm text-gray-400">Hiển thị {{ $reviews->firstItem() ?? 0 }}–{{ $reviews->lastItem() ?? 0 }} trong {{ number_format($reviews->total()) }} kết quả</span>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Review ID</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách hàng</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Đánh giá</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bình luận</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($reviews as $review)
            <tr class="hover:bg-gray-50/70 transition-colors">
                <td class="px-4 py-4 font-mono text-xs text-gray-500">#REV-{{ $review->review_id }}</td>
                <td class="px-4 py-4">
                    <a href="{{ route('product.detail', $review->product_id) }}" target="_blank" class="flex items-center gap-3 hover:bg-gray-100 p-1.5 -m-1.5 rounded-xl transition-colors group">
                        @php
                            $img = $review->product?->images?->firstWhere('is_primary',1) ?? $review->product?->images?->first();
                            $imgUrl = '';
                            if ($img) {
                                $imgUrl = $img->image_url;
                                if (!str_starts_with($imgUrl, 'http')) {
                                    $imgUrl = str_replace(['public/', '/storage/products/'], ['', '/products/'], $imgUrl);
                                    $imgUrl = asset(ltrim($imgUrl, '/'));
                                }
                            }
                        @endphp
                        @if($img)
                            <img src="{{ $imgUrl }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center"><i data-lucide="image" class="w-4 h-4 text-gray-300"></i></div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800 leading-tight group-hover:text-blue-600 transition-colors">{{ Str::limit($review->product?->name ?? '—', 22) }}</p>
                            <p class="text-xs text-gray-400">ID-{{ $review->product_id }}</p>
                        </div>
                    </a>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($review->user?->full_name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm text-gray-700 font-medium">{{ $review->user?->full_name ?? '—' }}</span>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <div class="flex gap-0.5">
                        @for($i=1;$i<=5;$i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </td>
                <td class="px-4 py-4 max-w-[160px]">
                    <p class="text-gray-500 truncate">{{ Str::limit($review->comment, 30) }}</p>
                    @if($review->images_count > 0)
                        <p class="text-xs text-blue-400 font-medium mt-0.5">+{{ $review->images_count }} hình ảnh</p>
                    @endif
                </td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap">{{ $review->created_at?->format('d/m/Y') }}</td>
                <td class="px-4 py-4">
                    @if($review->status === 'pending')
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Chờ duyệt</span>
                    @else
                        {{-- Switch button Ẩn / Hiện --}}
                        <form method="POST" action="{{ route('admin.reviews.updateStatus', $review->review_id) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $review->status === 'approved' ? 'hidden' : 'approved' }}">
                            <button type="submit" class="flex items-center gap-2 group" title="Nhấn để {{ $review->status === 'approved' ? 'ẩn' : 'hiện' }}">
                                {{-- Track --}}
                                <div class="relative w-10 h-5 rounded-full transition-colors duration-200
                                    {{ $review->status === 'approved' ? 'bg-green-500' : 'bg-gray-300' }}">
                                    {{-- Thumb --}}
                                    <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200
                                        {{ $review->status === 'approved' ? 'left-5' : 'left-0.5' }}"></div>
                                </div>
                                <span class="text-xs font-medium {{ $review->status === 'approved' ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $review->status === 'approved' ? 'Hiện' : 'Ẩn' }}
                                </span>
                            </button>
                        </form>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-center gap-1">
                        {{-- Nút Duyệt --}}
                        @if($review->status === 'pending')
                            <form method="POST" action="{{ route('admin.reviews.updateStatus', $review->review_id) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition-colors" title="Phê duyệt">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @else
                            <button disabled class="w-8 h-8 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center cursor-not-allowed" title="Chỉ duyệt được khi đang chờ duyệt">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        @endif

                        {{-- Nút Chi tiết --}}
                        <button onclick="openReviewModal({{ $review->review_id }})"
                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors" title="Xem chi tiết">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>

                        {{-- Nút Xóa --}}
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->review_id) }}"
                            onsubmit="return confirm('Xóa đánh giá #REV-{{ $review->review_id }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors" title="Xóa">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-16 text-center text-gray-400">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-30"></i>
                    <p class="text-sm">Không có đánh giá nào</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <span class="text-sm text-gray-500">
            {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} / {{ number_format($reviews->total()) }} kết quả
        </span>
        <div class="flex items-center gap-1">
            {{-- Nút Trước --}}
            @if($reviews->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 cursor-not-allowed">← Trước</span>
            @else
                <a href="{{ $reviews->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">← Trước</a>
            @endif

            {{-- Các số trang --}}
            @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                @if($page == $reviews->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0A2540] text-white text-sm font-semibold">{{ $page }}</span>
                @elseif($page == 1 || $page == $reviews->lastPage() || abs($page - $reviews->currentPage()) <= 1)
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition-colors">{{ $page }}</a>
                @elseif(abs($page - $reviews->currentPage()) == 2)
                    <span class="w-8 h-8 flex items-center justify-center text-sm text-gray-400">…</span>
                @endif
            @endforeach

            {{-- Nút Sau --}}
            @if($reviews->hasMorePages())
                <a href="{{ $reviews->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">Sau →</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-300 cursor-not-allowed">Sau →</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ===================== MODAL CHI TIẾT ===================== --}}
<div id="reviewModal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReviewModal()"></div>

    {{-- Panel --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header modal --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-900" id="modal-title">Chi tiết đánh giá</h2>
                <span id="modal-status-badge" class="px-3 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            <button onclick="closeReviewModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Loading state --}}
        <div id="modal-loading" class="flex items-center justify-center py-20">
            <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        </div>

        {{-- Content --}}
        <div id="modal-content" class="hidden">
            {{-- User info --}}
            <div class="px-6 py-5">
                <div class="flex items-start gap-4">
                    <div id="modal-avatar" class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3">
                            <p id="modal-username" class="font-semibold text-gray-900"></p>
                            <div id="modal-stars" class="flex gap-0.5"></div>
                        </div>
                        <p id="modal-date" class="text-xs text-gray-400 mt-0.5"></p>
                        <blockquote id="modal-comment" class="mt-3 text-sm text-gray-700 bg-gray-50 rounded-xl px-4 py-3 border-l-4 border-blue-200 italic leading-relaxed"></blockquote>
                    </div>
                </div>
            </div>

            {{-- Review Images --}}
            <div id="modal-images-section" class="px-6 pb-4 hidden">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Hình ảnh thực tế từ khách hàng</p>
                <div id="modal-images" class="flex gap-3 flex-wrap"></div>
            </div>

            {{-- Product info --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Sản phẩm đánh giá</p>
                <a id="modal-product-link" href="#" target="_blank" class="flex items-center gap-4 hover:bg-gray-100 p-2 -m-2 rounded-xl transition-colors group">
                    <img id="modal-product-img" src="" class="w-16 h-16 rounded-xl object-cover border border-gray-200 hidden">
                    <div id="modal-product-no-img" class="w-16 h-16 rounded-xl bg-gray-200 flex items-center justify-center hidden">
                        <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <div>
                        <p id="modal-product-name" class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors"></p>
                        <p id="modal-product-id" class="text-xs text-gray-400"></p>
                    </div>
                </a>
            </div>

            {{-- Action buttons --}}
            <div id="modal-actions" class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 justify-end">
                <form id="modal-hide-form" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="hidden">
                    <button type="submit" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Ẩn đánh giá
                    </button>
                </form>
                <form id="modal-approve-form" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#0A2540] text-white text-sm font-medium hover:bg-[#0d3060] transition-colors">
                        Phê duyệt
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===================== LIGHTBOX ===================== --}}
<div id="lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90" onclick="closeLightbox()">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <button id="lb-prev" onclick="event.stopPropagation(); lbNav(-1)" class="absolute left-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
        <i data-lucide="chevron-left" class="w-5 h-5"></i>
    </button>
    <img id="lb-img" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl object-contain shadow-2xl" onclick="event.stopPropagation()">
    <button id="lb-next" onclick="event.stopPropagation(); lbNav(1)" class="absolute right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
        <i data-lucide="chevron-right" class="w-5 h-5"></i>
    </button>
    <p id="lb-counter" class="absolute bottom-4 text-white/60 text-sm"></p>
</div>
@endsection

@push('scripts')
<script>
const starSVG = (filled) => `<svg class="w-4 h-4 ${filled ? 'text-yellow-400' : 'text-gray-200'}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;

const avatarColors = ['bg-blue-500','bg-purple-500','bg-green-500','bg-rose-500','bg-amber-500','bg-teal-500'];

function openReviewModal(id) {
    const modal = document.getElementById('reviewModal');
    modal.classList.remove('hidden');
    document.getElementById('modal-loading').classList.remove('hidden');
    document.getElementById('modal-content').classList.add('hidden');

    fetch(`/admin/reviews/${id}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('Network response was not ok');
            return r.json();
        })
        .then(data => {
            // Title & status badge
            document.getElementById('modal-title').textContent = `Chi tiết #REV-${data.review_id}`;
            const badge = document.getElementById('modal-status-badge');
            const statusMap = { approved: ['bg-green-100 text-green-700', 'Đã duyệt'], hidden: ['bg-gray-100 text-gray-500', 'Đã ẩn'], pending: ['bg-yellow-100 text-yellow-700', 'Chờ duyệt'] };
            const [cls, label] = statusMap[data.status] ?? statusMap['pending'];
            badge.className = `px-3 py-1 rounded-full text-xs font-semibold ${cls}`;
            badge.textContent = label;

            // Avatar
            const avatar = document.getElementById('modal-avatar');
            const name = data.user?.full_name ?? 'U';
            const colorIdx = name.charCodeAt(0) % avatarColors.length;
            avatar.className = `w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 ${avatarColors[colorIdx]}`;
            avatar.textContent = name.charAt(0).toUpperCase();

            // User info
            document.getElementById('modal-username').textContent = name;
            document.getElementById('modal-date').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('vi-VN') : '';
            document.getElementById('modal-comment').textContent = data.comment ?? '(Không có bình luận)';

            // Stars
            const starsEl = document.getElementById('modal-stars');
            starsEl.innerHTML = '';
            for (let i = 1; i <= 5; i++) starsEl.innerHTML += starSVG(i <= data.rating);

            // Review images
            const imagesSection = document.getElementById('modal-images-section');
            const imagesEl = document.getElementById('modal-images');
            if (data.images && data.images.length > 0) {
                const srcs = data.images.map(img => {
                    let url = img.image_url;
                    if (url.startsWith('http')) return url;
                    url = url.replace('public/', '');
                    return url.startsWith('/') ? url : '/' + url;
                });
                imagesEl.innerHTML = srcs.map((src, idx) =>
                    `<img src="${src}" onclick="openLightbox(${idx})" data-idx="${idx}" class="w-24 h-24 rounded-xl object-cover border border-gray-200 hover:scale-105 transition-transform cursor-pointer" title="Nhấn để phóng to">`
                ).join('');
                window._lbSrcs = srcs;
                imagesSection.classList.remove('hidden');
            } else {
                imagesSection.classList.add('hidden');
            }

            // Product
            const product = data.product;
            const prodImg = product?.images?.find(i => i.is_primary) ?? product?.images?.[0];
            if (prodImg) {
                let imgUrl = prodImg.image_url;
                if (!imgUrl.startsWith('http')) {
                    imgUrl = imgUrl.replace('public/', '');
                    if (!imgUrl.startsWith('/')) {
                        imgUrl = '/' + imgUrl;
                    }
                }
                document.getElementById('modal-product-img').src = imgUrl;
                document.getElementById('modal-product-img').classList.remove('hidden');
                document.getElementById('modal-product-no-img').classList.add('hidden');
            } else {
                document.getElementById('modal-product-img').classList.add('hidden');
                document.getElementById('modal-product-no-img').classList.remove('hidden');
            }
            document.getElementById('modal-product-name').textContent = product?.name ?? '—';
            document.getElementById('modal-product-id').textContent = `ID-${data.product_id}`;
            document.getElementById('modal-product-link').href = `/product-detail/${data.product_id}`;

            // Action forms
            const baseUrl = `/admin/reviews/${id}/status`;
            document.getElementById('modal-hide-form').action = baseUrl;
            document.getElementById('modal-approve-form').action = baseUrl;

            // Ẩn nút nếu đã ở trạng thái đó
            document.getElementById('modal-hide-form').style.display = data.status === 'hidden' ? 'none' : '';
            document.getElementById('modal-approve-form').style.display = data.status === 'approved' ? 'none' : '';

            document.getElementById('modal-loading').classList.add('hidden');
            document.getElementById('modal-content').classList.remove('hidden');
            lucide.createIcons();
        })
        .catch(() => {
            document.getElementById('modal-loading').innerHTML = '<p class="text-red-500 text-sm py-20 text-center">Lỗi tải dữ liệu</p>';
        });
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

// ── Lightbox ──
let _lbIdx = 0;
let _lbScale = 1;

function openLightbox(idx) {
    const srcs = window._lbSrcs || [];
    if (!srcs.length) return;
    _lbIdx = idx;
    const lb = document.getElementById('lightbox');
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    _lbScale = 1;
    _lbRender();
    lucide.createIcons();
}
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
function lbNav(dir) {
    const srcs = window._lbSrcs || [];
    _lbIdx = (_lbIdx + dir + srcs.length) % srcs.length;
    _lbScale = 1; // reset zoom khi chuyển ảnh
    _lbRender();
}
function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    _lbScale = 1;
}

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
    if (!lb) return;

    if (e.key === 'Escape') {
        if (!lb.classList.contains('hidden')) { closeLightbox(); return; }
        closeReviewModal();
    }
    
    if (!lb.classList.contains('hidden')) {
        if (e.key === 'ArrowLeft')  lbNav(-1);
        if (e.key === 'ArrowRight') lbNav(1);
    }
});
</script>
@endpush
