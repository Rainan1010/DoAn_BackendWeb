@extends('admin.layouts.app')

@section('title', 'Nhập kho mới')

@section('content')
<div class="p-8 max-w-7xl mx-auto">

    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-6">
        <a href="{{ route('admin.inventory-logs.index') }}" class="hover:text-blue-900">
            Quản lý kho
        </a>
        <span>›</span>
        <span class="text-blue-900">Nhập kho mới</span>
    </nav>

    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700">
            <p class="font-bold mb-2">Vui lòng kiểm tra lại thông tin:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.inventory-logs.store') }}" method="POST" id="importStockForm">
        @csrf

        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-3xl font-black tracking-tighter text-blue-900 uppercase">
                    Nhập kho mới
                </h2>
                <p class="text-gray-500 mt-1">
                    Cập nhật số lượng sản phẩm mới vào hệ thống vận hành.
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.inventory-logs.index') }}"
                   class="px-6 py-2.5 rounded-md border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i data-lucide="x" class="w-5 h-5"></i>
                    Hủy
                </a>

                <button type="submit"
                    class="px-6 py-2.5 rounded-md bg-gradient-to-br from-[#001e40] to-[#003366] text-white font-bold text-sm shadow-lg shadow-blue-900/20 hover:shadow-blue-900/40 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Xác nhận nhập kho
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8 items-start">

            <div class="col-span-12 lg:col-span-8 space-y-6">

                <section class="bg-white p-8 rounded-md shadow-sm border-b-2 border-[#001e40]/5">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded bg-blue-50 text-blue-900 flex items-center justify-center font-bold text-sm">
                            01
                        </span>
                        <h3 class="text-lg font-bold text-blue-950">
                            Thông tin sản phẩm
                        </h3>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Sản phẩm
                            </label>

                            <div class="relative">
                                <select
                                    name="variant_id"
                                    id="variantSelect"
                                    required
                                    class="w-full appearance-none bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4 transition-all cursor-pointer">

                                    <option value="">-- Chọn sản phẩm cần nhập kho --</option>

                                    @foreach($variants as $variant)
                                        @php
                                            $attrs = [];

                                            if (!empty($variant->attribute_values)) {
                                                $attrs = json_decode($variant->attribute_values, true);

                                                if (!is_array($attrs)) {
                                                    $attrs = [];
                                                }
                                            }

                                            $attrText = count($attrs) > 0
                                                ? implode(' / ', $attrs)
                                                : 'Mặc định';

                                            $image = $variant->image_url
                                                ? asset(str_replace('public/', '', $variant->image_url))
                                                : asset('images/products/default.png');
                                        @endphp

                                        <option
                                            value="{{ $variant->variant_id }}"
                                            data-product-name="{{ $variant->product_name }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-stock="{{ $variant->stock_quantity }}"
                                            data-attrs="{{ $attrText }}"
                                            data-image="{{ $image }}"
                                            data-price="{{ $variant->price }}"
                                            {{ old('variant_id') == $variant->variant_id ? 'selected' : '' }}>
                                            {{ $variant->product_name }} - SKU: {{ $variant->sku }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    ▼
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                SKU sản phẩm
                            </label>

                            <input
                                id="skuPreviewInput"
                                type="text"
                                readonly
                                class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 text-sm py-3 px-4 font-bold text-[#003366]"
                                value="">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Tồn kho hiện tại
                            </label>

                            <input
                                id="stockPreviewInput"
                                type="text"
                                readonly
                                class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 text-sm py-3 px-4 font-bold text-[#003366]"
                                value="">
                        </div>
                    </div>
                </section>

                <section class="bg-white p-8 rounded-md shadow-sm border-b-2 border-[#001e40]/5">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded bg-blue-50 text-blue-900 flex items-center justify-center font-bold text-sm">
                            02
                        </span>
                        <h3 class="text-lg font-bold text-blue-950">
                            Chi tiết nhập kho
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Số lượng nhập
                            </label>

                            <input
                                name="quantity"
                                id="quantityInput"
                                class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4 font-bold"
                                type="number"
                                min="1"
                                value="{{ old('quantity', 1) }}"
                                required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Giá nhập / đơn vị
                            </label>

                            <div class="relative">
                                <input
                                    name="import_price"
                                    id="importPriceInput"
                                    class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4 pr-12 font-bold"
                                    type="number"
                                    min="0"
                                    value="{{ old('import_price', 0) }}">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] font-bold">
                                    VND
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Thành tiền
                            </label>

                            <div id="totalPricePreview"
                                 class="w-full bg-slate-50 border-0 border-b-2 border-slate-100 text-sm py-3 px-4 font-black text-blue-900 text-lg">
                                0đ
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white p-8 rounded-md shadow-sm border-b-2 border-[#001e40]/5">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded bg-blue-50 text-blue-900 flex items-center justify-center font-bold text-sm">
                            03
                        </span>
                        <h3 class="text-lg font-bold text-blue-950">
                            Thông tin nhà cung cấp
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Nhà cung cấp
                            </label>

                            <input
                                name="supplier_name"
                                class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4"
                                placeholder="Tên đơn vị cung cấp..."
                                type="text"
                                value="{{ old('supplier_name') }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Mã hóa đơn tham chiếu
                            </label>

                            <input
                                name="reference_code"
                                class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4"
                                placeholder="Ví dụ: INV-2026-001"
                                type="text"
                                value="{{ old('reference_code') }}">
                        </div>
                    </div>
                </section>

                <section class="bg-white p-8 rounded-md shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded bg-blue-50 text-blue-900 flex items-center justify-center font-bold text-sm">
                            04
                        </span>
                        <h3 class="text-lg font-bold text-blue-950">
                            Ghi chú
                        </h3>
                    </div>

                    <textarea
                        name="note"
                        class="w-full bg-[#f2f4f6] border-0 border-b-2 border-gray-200 focus:border-[#001e40] focus:ring-0 text-sm py-3 px-4 resize-none"
                        placeholder="Lý do nhập kho, ghi chú đặc biệt cho lô hàng..."
                        rows="4">{{ old('note') }}</textarea>
                </section>
            </div>

            <div class="col-span-12 lg:col-span-4 space-y-6 sticky top-24">
                <div class="bg-[#001e40] text-white p-8 rounded-md shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-[#003366] opacity-20 rounded-full blur-3xl"></div>

                    <h4 class="text-xs font-black uppercase tracking-[0.2em] opacity-60 mb-6">
                        Xem trước kho hàng
                    </h4>

                    <div class="flex items-start gap-4 mb-8">
                        <img
                            id="variantImagePreview"
                            class="w-20 h-20 rounded-md object-cover border-2 border-white/10"
                            src="{{ asset('images/products/default.png') }}"
                            alt="Product image">

                        <div>
                            <p id="variantNamePreview" class="text-sm font-bold leading-tight">
                                Chưa chọn sản phẩm
                            </p>

                            <p id="variantAttrPreview" class="text-xs opacity-70 mt-1">
                                Vui lòng chọn sản phẩm
                            </p>

                            <div id="variantSkuPreview"
                                 class="mt-3 inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-400/20 rounded text-[10px] font-black uppercase">
                                SKU: ---
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">
                                    Tồn kho hiện tại
                                </p>

                                <p class="text-2xl font-black">
                                    <span id="currentStockPreview">0</span>
                                    <span class="text-xs font-normal opacity-70 uppercase ml-1 tracking-normal">
                                        đơn vị
                                    </span>
                                </p>
                            </div>

                            <i data-lucide="package" class="w-8 h-8 opacity-30"></i>
                        </div>

                        <div class="flex items-center justify-center -my-2 relative z-10">
                            <div class="bg-[#d6e3fe] text-[#001e40] w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-4 border-[#001e40]">
                                +
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10 border-dashed">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">
                                    Số lượng nhập mới
                                </p>

                                <p class="text-2xl font-black text-[#d6e3fe]">
                                    + <span id="importQuantityPreview">1</span>
                                </p>
                            </div>

                            <i data-lucide="inbox" class="w-8 h-8 text-[#d6e3fe] opacity-40"></i>
                        </div>

                        <div class="pt-4 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-widest opacity-60">
                                    Dự kiến tồn sau nhập
                                </span>

                                <span id="afterStockPreview" class="text-3xl font-black text-white">
                                    1
                                </span>
                            </div>

                            <div class="mt-4 h-2 w-full bg-white/10 rounded-full overflow-hidden">
                                <div id="stockProgressBar" class="h-full bg-[#d6e3fe]" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#e6e8ea]/50 p-6 rounded-md border border-gray-200">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i>
                        Lưu ý vận hành
                    </h5>

                    <ul class="space-y-3">
                        <li class="flex gap-3 text-xs text-gray-500 leading-relaxed">
                            <span class="w-1 h-1 rounded-full bg-blue-900 mt-1.5 shrink-0"></span>
                            Kiểm tra kỹ số IMEI/Serial khi hàng về kho thực tế.
                        </li>

                        <li class="flex gap-3 text-xs text-gray-500 leading-relaxed">
                            <span class="w-1 h-1 rounded-full bg-blue-900 mt-1.5 shrink-0"></span>
                            Giá nhập sẽ ảnh hưởng trực tiếp đến biên lợi nhuận và định giá bán lẻ.
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const variantSelect = document.getElementById('variantSelect');
    const quantityInput = document.getElementById('quantityInput');
    const importPriceInput = document.getElementById('importPriceInput');

    const skuPreviewInput = document.getElementById('skuPreviewInput');
    const stockPreviewInput = document.getElementById('stockPreviewInput');

    const variantImagePreview = document.getElementById('variantImagePreview');
    const variantNamePreview = document.getElementById('variantNamePreview');
    const variantAttrPreview = document.getElementById('variantAttrPreview');
    const variantSkuPreview = document.getElementById('variantSkuPreview');

    const currentStockPreview = document.getElementById('currentStockPreview');
    const importQuantityPreview = document.getElementById('importQuantityPreview');
    const afterStockPreview = document.getElementById('afterStockPreview');
    const stockProgressBar = document.getElementById('stockProgressBar');
    const totalPricePreview = document.getElementById('totalPricePreview');

    function formatMoney(value) {
        value = Number(value || 0);
        return value.toLocaleString('vi-VN') + 'đ';
    }

    function updatePreview() {
        const option = variantSelect.options[variantSelect.selectedIndex];

        if (!option || !option.value) {
            skuPreviewInput.value = '';
            stockPreviewInput.value = '';

            variantNamePreview.textContent = 'Chưa chọn sản phẩm';
            variantAttrPreview.textContent = 'Vui lòng chọn sản phẩm';
            variantSkuPreview.textContent = 'SKU: ---';

            currentStockPreview.textContent = '0';
            importQuantityPreview.textContent = quantityInput.value || '0';
            afterStockPreview.textContent = quantityInput.value || '0';
            totalPricePreview.textContent = formatMoney(0);
            stockProgressBar.style.width = '10%';

            return;
        }

        const productName = option.dataset.productName || 'Sản phẩm';
        const sku = option.dataset.sku || 'N/A';
        const stock = Number(option.dataset.stock || 0);
        const attrs = option.dataset.attrs || 'Mặc định';
        const image = option.dataset.image;

        const quantity = Number(quantityInput.value || 0);
        const importPrice = Number(importPriceInput.value || 0);
        const afterStock = stock + quantity;
        const totalPrice = quantity * importPrice;

        skuPreviewInput.value = sku;
        stockPreviewInput.value = stock + ' đơn vị';

        variantNamePreview.textContent = productName;
        variantAttrPreview.textContent = attrs;
        variantSkuPreview.textContent = 'SKU: ' + sku;

        if (image) {
            variantImagePreview.src = image;
        }

        currentStockPreview.textContent = stock;
        importQuantityPreview.textContent = quantity;
        afterStockPreview.textContent = afterStock;
        totalPricePreview.textContent = formatMoney(totalPrice);

        let percent = Math.min(100, Math.max(10, afterStock));
        stockProgressBar.style.width = percent + '%';
    }

    variantSelect.addEventListener('change', updatePreview);
    quantityInput.addEventListener('input', updatePreview);
    importPriceInput.addEventListener('input', updatePreview);

    updatePreview();
});
</script>
@endsection