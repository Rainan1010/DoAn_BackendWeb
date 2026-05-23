@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Đơn Hàng #' . $order->order_code)

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    .font-inter {
        font-family: 'Inter', sans-serif;
    }
    .custom-select {
        width: 100%;
        border: 1px solid #dbe2ea;
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
        color: #0A2540;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234A5568'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px;
    }
    .custom-select:focus {
        border-color: #001e40;
        background-color: white;
        box-shadow: 0 0 0 4px rgba(0, 30, 64, 0.08);
    }
    .custom-textarea {
        width: 100%;
        border: 1px solid #dbe2ea;
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
        color: #0A2540;
        min-height: 100px;
        resize: vertical;
    }
    .custom-textarea:focus {
        border-color: #001e40;
        background-color: white;
        box-shadow: 0 0 0 4px rgba(0, 30, 64, 0.08);
    }
</style>

<div class="font-inter space-y-8 pb-20" x-data="{ orderStatus: '{{ $order->order_status }}' }">

    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 gap-2">
                <a href="{{ route('admin.order-statistics.index') }}" class="hover:text-[#001e40]">Quản lý Đơn hàng</a>
                <span>›</span>
                <span class="text-[#001e40]">Chi tiết & Chỉnh sửa</span>
            </nav>

            <h1 class="text-4xl font-extrabold tracking-tight text-[#001e40] flex items-center gap-3">
                Đơn Hàng <span class="text-[#003366] font-mono font-black">{{ $order->order_code }}</span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.order-statistics.index') }}"
                class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                QUAY LẠI
            </a>

            <button type="submit" form="editOrderForm"
                class="px-6 py-3 bg-gradient-to-tr from-[#001e40] to-[#003366] text-white rounded-lg text-sm font-bold shadow-lg shadow-[#001e40]/20 hover:shadow-xl hover:from-[#002d62] hover:to-[#00478a] transition-all active:scale-95">
                CẬP NHẬT ĐƠN HÀNG
            </button>
        </div>
    </div>

    {{-- Error or Success alerts --}}
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-red-500 text-lg">⚠️</span>
                <p class="text-sm font-bold text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Main Grid --}}
    <form id="editOrderForm" action="{{ route('admin.orders.update', $order->order_id) }}" method="POST" class="grid lg:grid-cols-12 gap-8 items-start">
        @csrf
        @method('PATCH')

        {{-- LEFT COLUMN (8 Cols) --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 1. Thông tin Khách hàng & Vận chuyển --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-black text-[#001e40] flex items-center gap-3">
                        <span>👤</span> Thông tin Khách hàng & Nhận hàng
                    </h3>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1">Số điện thoại</label>
                        <p class="text-sm font-bold text-[#001e40] bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 font-mono">
                            {{ $order->user->phone ?? ($order->shippingAddress->phone ?? 'Không có') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1">Họ và tên</label>
                        <p class="text-sm font-bold text-[#001e40] bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">
                            {{ $order->user->full_name ?? ($order->shippingAddress->full_name ?? 'Khách vãng lai') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1">Email</label>
                        <p class="text-sm font-bold text-[#001e40] bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">
                            {{ $order->user->email ?? 'Không có email' }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-50 space-y-4">
                    <h4 class="text-sm font-bold text-[#001e40] flex items-center gap-2">
                        <span>📍</span> Địa chỉ nhận hàng
                    </h4>

                    @if($order->shippingAddress)
                        <div class="bg-blue-50/30 rounded-xl p-5 border border-blue-50/50 space-y-2">
                            <div class="flex justify-between items-center text-xs text-blue-800 font-bold">
                                <span>{{ strtoupper($order->shippingAddress->province === 'Showroom' ? 'Nhận tại Showroom B-Tris' : 'Giao hàng tận nơi') }}</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-mono">#{{ $order->shipping_address_id }}</span>
                            </div>

                            <p class="text-sm text-[#001e40] font-medium leading-relaxed mt-2">
                                <strong>Người nhận:</strong> {{ $order->shippingAddress->full_name }} <br>
                                <strong>Điện thoại:</strong> {{ $order->shippingAddress->phone }} <br>
                                <strong>Địa chỉ cụ thể:</strong> {{ $order->shippingAddress->street_address }}<br>
                                @if($order->shippingAddress->province !== 'Showroom')
                                    <strong>Khu vực:</strong> {{ $order->shippingAddress->ward }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->province }}
                                @endif
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Không có thông tin địa chỉ nhận hàng.</p>
                    @endif
                </div>
            </div>

            {{-- 2. Chi tiết sản phẩm trong đơn --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-[#001e40] flex items-center gap-3">
                        <span>📦</span> Danh sách sản phẩm mua
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-500">
                                <th class="px-8 py-4">Hình ảnh</th>
                                <th class="px-6 py-4">Sản phẩm</th>
                                <th class="px-6 py-4 text-right">Đơn giá</th>
                                <th class="px-6 py-4 text-center">Số lượng</th>
                                <th class="px-8 py-4 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                @php
                                    $variant = $item->variant;
                                    $product = $variant->product ?? null;
                                    $primaryImage = $product ? ($product->primaryImage ?? $product->images->first()) : null;
                                    $imageUrl = $primaryImage ? $primaryImage->image_url : 'assets/images/placeholder.png';
                                @endphp
                                <tr class="hover:bg-gray-50/40 transition-colors">
                                    <td class="px-8 py-4 w-24">
                                        <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center">
                                            @if($primaryImage)
                                                <img src="{{ asset($imageUrl) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="text-[10px] text-gray-400 font-bold uppercase text-center">No Image</div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-[#001e40] hover:text-[#003366] transition-colors">
                                            {{ $item->product_name }}
                                        </p>
                                        @if($item->variant_info)
                                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                                {{ $item->variant_info }}
                                            </span>
                                        @endif
                                        @if($variant)
                                            <p class="text-[10px] text-gray-400 font-mono mt-1">SKU: {{ $variant->sku }}</p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm font-bold text-[#001e40] text-right">
                                        {{ number_format($item->unit_price, 0, ',', '.') }} ₫
                                    </td>

                                    <td class="px-6 py-4 text-sm font-bold text-[#001e40] text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="px-8 py-4 text-sm font-black text-[#001e40] text-right">
                                        {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (4 Cols) --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- 1. Trạng thái & Xử lý --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                <h3 class="text-lg font-black text-[#001e40] border-b border-gray-100 pb-3 flex items-center gap-2">
                    <span>⚡</span> Trạng thái Đơn hàng
                </h3>

                {{-- Select Trạng thái đơn hàng --}}
                <div class="space-y-2">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Trạng thái xử lý</label>
                    <select name="order_status" x-model="orderStatus" class="custom-select font-bold">
                        <option value="pending" class="text-yellow-600 font-bold">Chờ xác nhận (Pending)</option>
                        <option value="confirmed" class="text-blue-600 font-bold">Đã xác nhận (Confirmed)</option>
                        <option value="processing" class="text-indigo-600 font-bold">Đang đóng gói (Processing)</option>
                        <option value="shipped" class="text-purple-600 font-bold">Đang vận chuyển (Shipped)</option>
                        <option value="delivered" class="text-teal-600 font-bold">Đã giao hàng / Hoàn thành (Delivered)</option>
                        <option value="cancelled" class="text-red-600 font-bold">Đã hủy đơn (Cancelled)</option>
                    </select>
                </div>

                {{-- Lý do hủy đơn (Chỉ xuất hiện khi chọn cancelled) --}}
                <div class="space-y-2" x-show="orderStatus === 'cancelled'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    <label class="block text-xs uppercase tracking-widest font-bold text-red-500">Lý do hủy đơn hàng <span class="text-red-500">*</span></label>
                    <textarea name="cancel_reason" placeholder="Vui lòng nhập lý do hủy đơn hàng..." :required="orderStatus === 'cancelled'" class="custom-textarea">{{ $order->cancel_reason }}</textarea>
                </div>

                {{-- Select Trạng thái thanh toán --}}
                <div class="space-y-2">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Trạng thái thanh toán</label>
                    <select name="payment_status" class="custom-select font-bold">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }} class="text-yellow-600 font-bold">Chưa thanh toán (Pending)</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }} class="text-green-600 font-bold">Đã thanh toán (Paid)</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }} class="text-red-600 font-bold">Đã hoàn tiền (Refunded)</option>
                    </select>
                </div>

                {{-- Ghi chú thanh toán hiện tại --}}
                <div class="text-xs bg-gray-50 p-4 rounded-xl border border-gray-100 text-gray-500 space-y-1.5 font-medium">
                    <p><strong>Phương thức:</strong> <span class="uppercase text-slate-700 font-bold font-mono">{{ $order->payment_method ?: 'COD' }}</span></p>
                    <p><strong>Ngày thanh toán:</strong> <span class="text-slate-700">{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i:s') : 'Chưa ghi nhận' }}</span></p>
                </div>
            </div>

            {{-- 2. Tóm tắt tài chính --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                <h3 class="text-lg font-black text-[#001e40] border-b border-gray-100 pb-3 flex items-center gap-2">
                    <span>💵</span> Tóm tắt thanh toán
                </h3>

                <div class="space-y-3.5 text-sm font-medium text-gray-500">
                    <div class="flex justify-between">
                        <span>Tạm tính (Subtotal)</span>
                        <span class="text-[#001e40] font-bold">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span class="text-[#001e40] font-bold">+ {{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Thuế VAT (10%)</span>
                        <span class="text-[#001e40] font-bold">+ {{ number_format($order->subtotal * 0.1, 0, ',', '.') }} ₫</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600 font-bold">
                            <span>Giảm giá Voucher</span>
                            <span>- {{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif

                    @if($order->voucher)
                        <div class="bg-green-50/50 text-green-800 text-xs p-3 rounded-xl border border-green-50 flex items-center justify-between font-bold">
                            <span>Mã Voucher đã dùng:</span>
                            <span class="bg-green-100 px-2 py-0.5 rounded-full font-mono">{{ $order->voucher->code }}</span>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-base font-black text-[#001e40]">Tổng thanh toán</span>
                        <span class="text-2xl font-black text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
