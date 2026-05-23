@extends('admin.layouts.app')

@section('title', 'Tạo Đơn Hàng Mới')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    .font-inter {
        font-family: 'Inter', sans-serif;
    }
    .custom-input {
        width: 100%;
        border: 1px solid #dbe2ea;
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
        color: #0A2540;
    }
    .custom-input:focus {
        border-color: #001e40;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 30, 64, 0.08);
    }
    .delivery-tab {
        border: 1px solid #dbe2ea;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .delivery-tab.active {
        border-color: #001e40;
        background: #eef5ff;
        box-shadow: 0 4px 12px rgba(0, 30, 64, 0.05);
    }
</style>

<div class="font-inter space-y-8 pb-20" x-data="createOrder()">

    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 gap-2">
                <a href="{{ route('admin.order-statistics.index') }}" class="hover:text-[#001e40]">Quản lý Đơn hàng</a>
                <span>›</span>
                <span class="text-[#001e40]">Tạo Đơn Hàng Mới</span>
            </nav>

            <h1 class="text-4xl font-extrabold tracking-tight text-[#001e40]">
                Tạo Đơn Hàng Mới
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.order-statistics.index') }}"
                class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-50 transition-colors">
                HỦY BỎ
            </a>

            <button type="button" @click="submitOrderForm()"
                class="px-6 py-3 bg-gradient-to-tr from-[#001e40] to-[#003366] text-white rounded-lg text-sm font-bold shadow-lg shadow-[#001e40]/20 hover:shadow-xl transition-all active:scale-95">
                LƯU ĐƠN HÀNG
            </button>
        </div>
    </div>

    {{-- Main Grid --}}
    <form id="orderForm" action="{{ route('admin.orders.store') }}" method="POST" class="grid lg:grid-cols-12 gap-8 items-start">
        @csrf

        {{-- Hidden Fields for Form Submit --}}
        <input type="hidden" name="user_id" x-model="customer.user_id">
        <input type="hidden" name="voucher_id" x-model="voucher.voucher_id">
        <input type="hidden" name="discount_amount" x-model="discount">
        <input type="hidden" name="shipping_fee" x-model="shippingFee">

        {{-- LEFT COLUMN (8 Cols) --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 1. Khách hàng & Vận chuyển --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-black text-[#001e40] flex items-center gap-3">
                        <span>👤</span> Thông tin Khách hàng & Vận chuyển
                    </h3>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="relative">
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" x-model="customer.phone" @input.debounce.300ms="searchCustomer()" required class="custom-input" placeholder="Nhập SĐT khách hàng...">
                        
                        {{-- Search Results Dropdown --}}
                        <div x-show="searchResults.length > 0" @click.away="searchResults = []"
                            class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden divide-y divide-gray-50 max-h-60 overflow-y-auto">
                            <template x-for="user in searchResults" :key="user.user_id">
                                <div @click="selectCustomer(user)" class="p-3 hover:bg-blue-50/50 cursor-pointer transition-colors flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-xs text-[#001e40]" x-text="user.full_name"></p>
                                        <p class="text-[10px] text-gray-500" x-text="user.phone + ' | ' + user.email"></p>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Chọn</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Họ và tên</label>
                        <input type="text" name="full_name" x-model="customer.full_name" placeholder="Họ và tên khách hàng" required class="custom-input">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Email</label>
                        <input type="email" name="email" x-model="customer.email" placeholder="Email khách hàng (nếu có)" class="custom-input">
                    </div>
                </div>

                {{-- Tab Phương thức nhận hàng --}}
                <div class="space-y-3">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Phương thức giao nhận</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div @click="setDeliveryType('home')" :class="deliveryType === 'home' ? 'active' : ''"
                            class="delivery-tab rounded-xl p-5 flex items-center gap-4">
                            <input type="radio" name="delivery_type" value="home" :checked="deliveryType === 'home'" class="hidden">
                            <div class="text-2xl">🏠</div>
                            <div>
                                <h4 class="font-bold text-sm text-[#001e40]">Giao hàng tận nơi</h4>
                                <p class="text-xs text-gray-500 mt-1">Giao hàng nhanh tận địa chỉ khách hàng</p>
                            </div>
                        </div>

                        <div @click="setDeliveryType('store')" :class="deliveryType === 'store' ? 'active' : ''"
                            class="delivery-tab rounded-xl p-5 flex items-center gap-4">
                            <input type="radio" name="delivery_type" value="store" :checked="deliveryType === 'store'" class="hidden">
                            <div class="text-2xl">🏪</div>
                            <div>
                                <h4 class="font-bold text-sm text-[#001e40]">Nhận tại cửa hàng</h4>
                                <p class="text-xs text-gray-500 mt-1">Khách tự nhận tại Showroom B-Tris</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Giao hàng tận nơi --}}
                <div x-show="deliveryType === 'home'" class="grid md:grid-cols-3 gap-6 pt-4 border-t border-gray-50">
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Tỉnh / Thành phố</label>
                        <select id="province" name="province" x-model="customer.province" @change="onProvinceChange()" class="custom-input">
                            <option value="">-- Chọn Tỉnh / Thành --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Quận / Huyện</label>
                        <select id="district" name="district" x-model="customer.district" @change="onDistrictChange()" class="custom-input">
                            <option value="">-- Chọn Quận / Huyện --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Phường / Xã</label>
                        <select id="ward" name="ward" x-model="customer.ward" class="custom-input">
                            <option value="">-- Chọn Phường / Xã --</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Địa chỉ cụ thể</label>
                        <input type="text" name="street_address" x-model="customer.street_address" placeholder="Số nhà, tên đường..." class="custom-input">
                    </div>
                </div>

                {{-- Nhận tại cửa hàng --}}
                <div x-show="deliveryType === 'store'" class="pt-4 border-t border-gray-50 space-y-4">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Chọn Showroom nhận hàng</label>
                    <div class="grid md:grid-cols-2 gap-4">
                        <template x-for="store in stores" :key="store.name">
                            <label class="border rounded-xl p-4 flex gap-3 items-start cursor-pointer hover:bg-slate-50 transition-colors"
                                :class="pickupStore === store.address ? 'border-[#001e40] bg-blue-50/20' : 'border-gray-200'">
                                <input type="radio" name="pickup_store" :value="store.address" x-model="pickupStore" class="mt-1 accent-[#001e40]">
                                <div>
                                    <h5 class="font-bold text-sm text-[#001e40]" x-text="store.name"></h5>
                                    <p class="text-xs text-gray-500 mt-1" x-text="store.address"></p>
                                    <p class="text-[10px] text-amber-600 font-bold mt-1" x-text="'Mở cửa: ' + store.hours"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 2. Danh sách Sản phẩm --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-black text-[#001e40] flex items-center gap-3">
                        <span>📦</span> Danh sách Sản phẩm
                    </h3>
                    <button type="button" @click="showProductModal = true"
                        class="flex items-center gap-2 px-4 py-2 bg-[#001e40] hover:bg-[#002d5a] text-white rounded-lg text-xs font-bold transition-all active:scale-95 shadow-sm">
                        ➕ THÊM SẢN PHẨM
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3">Sản phẩm</th>
                                <th class="px-4 py-3">Đơn giá</th>
                                <th class="px-4 py-3 text-center">Số lượng</th>
                                <th class="px-4 py-3 text-right">Thành tiền</th>
                                <th class="px-4 py-3 text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, index) in orderItems" :key="index">
                                <tr>
                                    {{-- Info --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-slate-50 rounded-lg border flex-shrink-0 flex items-center justify-center overflow-hidden">
                                                <img :src="item.image" class="object-cover w-full h-full" onerror="this.src='/images/default-product.png'">
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-sm text-[#001e40]" x-text="item.name"></h5>
                                                <p class="text-xs text-gray-500 mt-0.5" x-text="item.variant_name"></p>
                                                <span class="text-[10px] font-bold text-[#58657c]" x-text="'SKU: ' + item.sku"></span>
                                            </div>
                                        </div>
                                        {{-- Hidden fields for form submit --}}
                                        <input type="hidden" :name="'items['+index+'][variant_id]'" :value="item.variant_id">
                                        <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                                        <input type="hidden" :name="'items['+index+'][price]'" :value="item.price">
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="px-4 py-4 font-bold text-sm text-[#001e40]" x-text="formatPrice(item.price)"></td>

                                    {{-- Qty Controls --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center bg-slate-50 rounded-lg p-1 border w-fit mx-auto">
                                            <button type="button" @click="decreaseQty(index)" class="w-8 h-8 flex items-center justify-center hover:bg-white rounded text-[#001e40] font-black">-</button>
                                            <span class="w-10 text-center font-bold text-sm text-[#001e40]" x-text="item.quantity"></span>
                                            <button type="button" @click="increaseQty(index)" class="w-8 h-8 flex items-center justify-center hover:bg-white rounded text-[#001e40] font-black">+</button>
                                        </div>
                                    </td>

                                    {{-- Subtotal --}}
                                    <td class="px-4 py-4 text-right font-black text-sm text-[#001e40]" x-text="formatPrice(item.price * item.quantity)"></td>

                                    {{-- Delete Button --}}
                                    <td class="px-4 py-4 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="orderItems.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    Chưa có sản phẩm nào được chọn. Vui lòng bấm "Thêm sản phẩm" ở trên!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Ghi chú nội bộ --}}
                <div class="pt-6 border-t border-gray-100">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Ghi chú đơn hàng nội bộ</label>
                    <textarea name="cancel_reason" rows="3" class="custom-input" placeholder="Nhập ghi chú cho nhân viên vận hành (Không hiển thị với khách)..."></textarea>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (4 Cols) --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- 3. Thanh toán & Voucher --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-black text-[#001e40]">
                        Thanh toán & Voucher
                    </h3>
                </div>

                {{-- Chọn Voucher --}}
                <div class="space-y-2">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Mã giảm giá (Voucher)</label>
                    <select class="custom-input font-bold text-[#001e40]" @change="selectVoucher($el.value)">
                        <option value="">-- Áp dụng Voucher --</option>
                        <template x-for="v in vouchers" :key="v.voucher_id">
                            <option :value="v.voucher_id" x-text="v.code + ' (Giảm ' + (v.type === 'percent' ? parseFloat(v.value)+'%' : formatPrice(v.value)) + ')'"
                                :disabled="calculatedSubtotal < v.min_order_value"></option>
                        </template>
                    </select>
                </div>

                {{-- Phương thức thanh toán --}}
                <div class="space-y-3">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500">Phương thức thanh toán</label>
                    <div class="space-y-2">
                        <label class="border rounded-xl p-4 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors"
                            :class="paymentMethod === 'cod' ? 'border-[#001e40] bg-blue-50/20' : 'border-gray-200'">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">🚚</span>
                                <span class="font-bold text-sm text-[#001e40]">Thanh toán COD</span>
                            </div>
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="accent-[#001e40]">
                        </label>

                        <label class="border rounded-xl p-4 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors"
                            :class="paymentMethod === 'vnpay' ? 'border-[#001e40] bg-blue-50/20' : 'border-gray-200'">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">🏦</span>
                                <span class="font-bold text-sm text-[#001e40]">Thanh toán VNPAY</span>
                            </div>
                            <input type="radio" name="payment_method" value="vnpay" x-model="paymentMethod" class="accent-[#001e40]">
                        </label>

                        <label class="border rounded-xl p-4 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors"
                            :class="paymentMethod === 'momo' ? 'border-[#001e40] bg-blue-50/20' : 'border-gray-200'">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">💗</span>
                                <span class="font-bold text-sm text-[#001e40]">Thanh toán MoMo</span>
                            </div>
                            <input type="radio" name="payment_method" value="momo" x-model="paymentMethod" class="accent-[#001e40]">
                        </label>
                    </div>
                </div>

                {{-- Trạng thái thanh toán --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Trạng thái thanh toán</label>
                    <select name="payment_status" class="custom-input font-bold text-[#001e40]">
                        <option value="pending" class="text-amber-600 font-bold">Chưa thanh toán (Pending)</option>
                        <option value="paid" class="text-green-600 font-bold">Đã thanh toán (Paid)</option>
                        <option value="refunded" class="text-red-600 font-bold">Đã hoàn tiền (Refunded)</option>
                    </select>
                </div>

                {{-- Trạng thái đơn hàng --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Trạng thái đơn hàng</label>
                    <select name="order_status" class="custom-input font-bold text-[#001e40]">
                        <option value="pending">Chờ xác nhận (Pending)</option>
                        <option value="confirmed" selected>Đã xác nhận (Confirmed)</option>
                        <option value="processing">Đang xử lý (Processing)</option>
                        <option value="shipped">Đang giao (Shipped)</option>
                        <option value="delivered">Đã giao / Hoàn thành (Delivered)</option>
                        <option value="cancelled">Đã hủy (Cancelled)</option>
                    </select>
                </div>
            </div>

            {{-- 4. Tóm tắt Đơn hàng --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-black text-[#001e40]">Tóm tắt đơn hàng</h3>
                </div>

                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Tạm tính (Sản phẩm)</span>
                        <span class="font-bold text-[#001e40]" x-text="formatPrice(calculatedSubtotal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>VAT (10%)</span>
                        <span class="font-bold text-[#001e40]" x-text="formatPrice(calculatedSubtotal * 0.1)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span class="font-bold text-[#001e40]" x-text="formatPrice(shippingFee)"></span>
                    </div>
                    <div class="flex justify-between text-green-600" x-show="discount > 0">
                        <span>Đã giảm (Voucher)</span>
                        <span class="font-black" x-text="'-' + formatPrice(discount)"></span>
                    </div>
                </div>

                <div class="bg-[#001e40] rounded-xl p-5 text-white mt-4 space-y-2">
                    <p class="uppercase tracking-widest text-[10px] text-blue-200">Tổng thanh toán</p>
                    <h3 class="text-3xl font-black" x-text="formatPrice(calculatedTotal)"></h3>
                    <p class="text-blue-100 text-[10px] mt-1">Đã bao gồm VAT và phí ship</p>
                </div>

                <button type="button" @click="submitOrderForm()"
                    class="w-full py-4 bg-gradient-to-tr from-[#001e40] to-[#003366] text-white rounded-xl font-bold text-md shadow-md hover:shadow-lg transition-all active:scale-[0.99] mt-6">
                    LƯU ĐƠN HÀNG →
                </button>
            </div>
        </div>

        {{-- PRODUCT SELECTION MODAL --}}
        <div x-show="showProductModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm">
            <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[85vh]">
                
                {{-- Modal Header --}}
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-xl font-black text-[#001e40]">➕ Chọn sản phẩm & biến thể</h3>
                    <button type="button" @click="showProductModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">×</button>
                </div>

                {{-- Modal Content (Scrollable) --}}
                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Select Product --}}
                        <div>
                            <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Chọn sản phẩm</label>
                            <select x-model="modalSelectedProductId" @change="onModalProductChange()" class="custom-input">
                                <option value="">-- Chọn sản phẩm --</option>
                                <template x-for="p in products" :key="p.product_id">
                                    <option :value="p.product_id" x-text="p.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Select Variant --}}
                        <div>
                            <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">Chọn biến thể</label>
                            <select x-model="modalSelectedVariantId" @change="onModalVariantChange()" class="custom-input" :disabled="!modalSelectedProductId">
                                <option value="">-- Chọn biến thể --</option>
                                <template x-for="v in modalVariants" :key="v.variant_id">
                                    <option :value="v.variant_id" x-text="formatVariantText(v)"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Selected Item Details --}}
                    <div x-show="modalSelectedVariant" class="bg-slate-50 p-6 rounded-xl border flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex gap-4 items-start flex-1 min-w-0">
                            {{-- Product Image in Modal --}}
                            <div class="w-20 h-20 bg-white rounded-xl border flex-shrink-0 flex items-center justify-center overflow-hidden p-2">
                                <img :src="modalProductImage" class="object-contain w-full h-full" onerror="this.src='/images/default-product.png'">
                            </div>

                            <div class="min-w-0">
                                <h4 class="font-bold text-md text-[#001e40] truncate" x-text="modalSelectedProduct?.name"></h4>
                                <p class="text-sm text-gray-600 mt-1" x-text="'Biến thể: ' + modalSelectedVariantText"></p>
                                <div class="flex gap-4 mt-2 flex-wrap">
                                    <span class="text-xs font-semibold text-gray-500" x-text="'SKU: ' + modalSelectedVariant?.sku"></span>
                                    <span class="text-xs font-semibold text-gray-500" x-text="'Kho hàng: ' + modalSelectedVariant?.stock_quantity"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div>
                                <p class="text-xs text-gray-500">Đơn giá</p>
                                <p class="text-xl font-black text-[#001e40]" x-text="formatPrice(modalSelectedVariant?.price)"></p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Số lượng</label>
                                <input type="number" x-model="modalQty" min="1" :max="modalSelectedVariant?.stock_quantity" class="custom-input w-24">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 bg-slate-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showProductModal = false"
                        class="px-5 py-2.5 bg-white border rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50">
                        ĐÓNG
                    </button>
                    <button type="button" @click="addSelectedProductToOrder()" :disabled="!modalSelectedVariant"
                        class="px-5 py-2.5 bg-[#001e40] hover:bg-[#002d5a] text-white text-xs font-bold rounded-xl disabled:opacity-50">
                        THÊM VÀO ĐƠN HÀNG
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- Script for Alpine.js --}}
<script>
    function createOrder() {
        return {
            products: @json($products),
            shippingFees: @json($shippingFees),
            vouchers: @json($vouchers),
            stores: [
                { name: 'B-Tris Flagship Store - Quận 1', address: '123 Lê Lợi, Phường Bến Thành, Quận 1, TP. HCM', hours: '08:30 - 21:30' },
                { name: 'B-Tris Experience Center - Quận 7', address: '456 Nguyễn Lương Bằng, Phường Tân Phú, Quận 7, TP. HCM', hours: '09:00 - 22:00' },
                { name: 'B-Tris Tech Hub - Hoàn Kiếm', address: '10 Lý Thường Kiệt, Hoàn Kiếm, HN', hours: '08:30 - 21:30' },
                { name: 'B-Tris Đà Nẵng - Hải Châu', address: '88 Nguyễn Văn Linh, Hải Châu, ĐN', hours: '08:30 - 21:30' }
            ],

            searchResults: [],
            customer: {
                user_id: '',
                full_name: '',
                phone: '',
                email: '',
                province: '',
                district: '',
                ward: '',
                street_address: ''
            },

            deliveryType: 'home',
            pickupStore: '123 Lê Lợi, Phường Bến Thành, Quận 1, TP. HCM',
            shippingFee: 30000,

            orderItems: [],

            voucher: {
                voucher_id: '',
                code: '',
                type: 'fixed',
                value: 0,
                min_order_value: 0,
                max_discount: null
            },
            discount: 0,

            paymentMethod: 'cod',

            // Modal state
            showProductModal: false,
            modalSelectedProductId: '',
            modalVariants: [],
            modalSelectedVariantId: '',
            modalSelectedProduct: null,
            modalSelectedVariant: null,
            modalSelectedVariantText: '',
            modalQty: 1,

            async init() {
                await this.loadProvinces();
            },

            setDeliveryType(type) {
                this.deliveryType = type;
                this.updateShippingFee();
            },

            searchCustomer() {
                if (!this.customer.phone) {
                    this.searchResults = [];
                    return;
                }
                fetch(`{{ route('admin.orders.search-user') }}?search=${encodeURIComponent(this.customer.phone)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.searchResults = data;
                    });
            },

            async selectCustomer(user) {
                this.customer.user_id = user.user_id;
                this.customer.full_name = user.full_name;
                this.customer.phone = user.phone;
                this.customer.email = user.email;
                
                if (user.address) {
                    this.customer.province = user.address.province;
                    this.customer.street_address = user.address.street_address;
                    
                    await this.loadDistricts(user.address.province);
                    this.customer.district = user.address.district;
                    
                    await this.loadWards(user.address.district);
                    this.customer.ward = user.address.ward;
                } else {
                    this.customer.province = '';
                    this.customer.district = '';
                    this.customer.ward = '';
                    this.customer.street_address = '';
                }
                
                this.searchResults = [];
                this.updateShippingFee();
            },

            updateShippingFee() {
                if (this.deliveryType === 'store') {
                    this.shippingFee = 0;
                    return;
                }
                if (!this.customer.province) {
                    this.shippingFee = 30000;
                    return;
                }

                fetch("{{ route('shipping.fee') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        province: this.customer.province
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.shippingFee = parseFloat(data.fee) || 30000;
                })
                .catch(err => {
                    this.shippingFee = 30000;
                });
            },

            async loadProvinces() {
                const response = await fetch('https://provinces.open-api.vn/api/p/');
                const data = await response.json();
                const provinceSelect = document.getElementById('province');
                if (!provinceSelect) return;
                
                provinceSelect.length = 1;
                
                data.forEach(item => {
                    let option = new Option(item.name, item.name);
                    provinceSelect.add(option);
                });
            },

            async loadDistricts(provinceName) {
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');
                if (!districtSelect || !wardSelect) return;

                districtSelect.length = 1;
                wardSelect.length = 1;

                if (!provinceName) return;

                const response = await fetch('https://provinces.open-api.vn/api/p/');
                const provinces = await response.json();
                const provinceData = provinces.find(p => p.name == provinceName);
                if (!provinceData) return;

                const districtResponse = await fetch(`https://provinces.open-api.vn/api/p/${provinceData.code}?depth=2`);
                const data = await districtResponse.json();
                data.districts.forEach(item => {
                    let option = new Option(item.name, item.name);
                    districtSelect.add(option);
                });
            },

            async loadWards(districtName) {
                const wardSelect = document.getElementById('ward');
                if (!wardSelect) return;

                wardSelect.length = 1;

                if (!districtName) return;

                const response = await fetch('https://provinces.open-api.vn/api/d/');
                const districts = await response.json();
                const districtData = districts.find(d => d.name == districtName);
                if (!districtData) return;

                const wardResponse = await fetch(`https://provinces.open-api.vn/api/d/${districtData.code}?depth=2`);
                const data = await wardResponse.json();
                data.wards.forEach(item => {
                    let option = new Option(item.name, item.name);
                    wardSelect.add(option);
                });
            },

            async onProvinceChange() {
                this.updateShippingFee();
                await this.loadDistricts(this.customer.province);
            },

            async onDistrictChange() {
                await this.loadWards(this.customer.district);
            },

            // Modal functions
            onModalProductChange() {
                this.modalSelectedVariantId = '';
                this.modalSelectedVariant = null;
                if (!this.modalSelectedProductId) {
                    this.modalVariants = [];
                    this.modalSelectedProduct = null;
                    return;
                }
                this.modalSelectedProduct = this.products.find(p => p.product_id == this.modalSelectedProductId);
                this.modalVariants = this.modalSelectedProduct ? this.modalSelectedProduct.variants : [];
            },

            onModalVariantChange() {
                if (!this.modalSelectedVariantId) {
                    this.modalSelectedVariant = null;
                    return;
                }
                this.modalSelectedVariant = this.modalVariants.find(v => v.variant_id == this.modalSelectedVariantId);
                this.modalQty = 1;
                this.modalSelectedVariantText = this.formatVariantText(this.modalSelectedVariant);
            },

            formatVariantText(v) {
                if (!v) return '';
                let text = '';
                if (typeof v.attribute_values === 'string') {
                    try {
                        const parsed = JSON.parse(v.attribute_values);
                        text = Object.values(parsed).join(' - ');
                    } catch(e) {
                        text = v.attribute_values;
                    }
                } else if (typeof v.attribute_values === 'object') {
                    text = Object.values(v.attribute_values).join(' - ');
                }
                return text || 'Mặc định';
            },

            formatPrice(val) {
                const n = Number(val);
                if (isNaN(n)) return '0 ₫';
                return n.toLocaleString('vi-VN') + ' ₫';
            },

            get modalProductImage() {
                if (!this.modalSelectedProduct) return '/images/default-product.png';
                const imgs = this.modalSelectedProduct.images || [];
                const primary = imgs.find(img => img.is_primary == 1);
                const url = primary ? primary.image_url : (imgs.length > 0 ? imgs[0].image_url : 'images/default-product.png');
                
                const cleanUrl = url.replace('public/', '');
                return cleanUrl.startsWith('/') ? cleanUrl : '/' + cleanUrl;
            },

            addSelectedProductToOrder() {
                if (!this.modalSelectedVariant) return;

                const imageUrl = this.modalProductImage;

                // Check duplicate
                const existIndex = this.orderItems.findIndex(item => item.variant_id == this.modalSelectedVariant.variant_id);
                if (existIndex > -1) {
                    this.orderItems[existIndex].quantity += parseInt(this.modalQty);
                } else {
                    this.orderItems.push({
                        variant_id: this.modalSelectedVariant.variant_id,
                        sku: this.modalSelectedVariant.sku,
                        name: this.modalSelectedProduct.name,
                        variant_name: this.modalSelectedVariantText,
                        price: parseFloat(this.modalSelectedVariant.price),
                        quantity: parseInt(this.modalQty),
                        image: imageUrl
                    });
                }

                // Reset modal
                this.showProductModal = false;
                this.modalSelectedProductId = '';
                this.modalSelectedVariantId = '';
                this.modalSelectedProduct = null;
                this.modalSelectedVariant = null;
                this.modalVariants = [];
            },

            increaseQty(index) {
                this.orderItems[index].quantity++;
            },

            decreaseQty(index) {
                if (this.orderItems[index].quantity > 1) {
                    this.orderItems[index].quantity--;
                }
            },

            removeItem(index) {
                this.orderItems.splice(index, 1);
            },

            selectVoucher(voucherId) {
                if (!voucherId) {
                    this.voucher = { voucher_id: '', code: '', type: 'fixed', value: 0, min_order_value: 0, max_discount: null };
                    return;
                }
                const v = this.vouchers.find(item => item.voucher_id == voucherId);
                if (v) {
                    this.voucher = v;
                }
            },

            // Computed values
            get calculatedSubtotal() {
                return this.orderItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            },

            get calculatedTotal() {
                const sub = this.calculatedSubtotal;
                const vat = sub * 0.1;
                const ship = this.shippingFee;
                
                // Calculate discount
                let disc = 0;
                if (this.voucher.voucher_id && sub >= this.voucher.min_order_value) {
                    if (this.voucher.type === 'percent') {
                        disc = sub * (parseFloat(this.voucher.value) / 100);
                        if (this.voucher.max_discount) {
                            disc = Math.min(disc, parseFloat(this.voucher.max_discount));
                        }
                    } else {
                        disc = Math.min(parseFloat(this.voucher.value), sub);
                    }
                }
                this.discount = disc;

                let tot = sub + vat + ship - disc;
                return tot < 0 ? 0 : tot;
            },

            submitOrderForm() {
                if (this.orderItems.length === 0) {
                    alert('Vui lòng chọn ít nhất một sản phẩm trước khi lưu đơn hàng!');
                    return;
                }
                if (!this.customer.full_name || !this.customer.phone) {
                    alert('Vui lòng điền đầy đủ Họ tên và Số điện thoại khách hàng!');
                    return;
                }
                if (this.deliveryType === 'home' && !this.customer.province) {
                    alert('Vui lòng chọn Tỉnh / Thành phố giao hàng!');
                    return;
                }

                // Submit Form
                document.getElementById('orderForm').submit();
            }
        };
    }
</script>
@endsection
