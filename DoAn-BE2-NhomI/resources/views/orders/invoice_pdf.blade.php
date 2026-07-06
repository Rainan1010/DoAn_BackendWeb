<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn {{ $order->order_code ?? $order->order_id }}</title>

```
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #111827;
        margin: 0;
        padding: 24px;
    }

    .header {
        border-bottom: 2px solid #001e40;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }

    .brand {
        font-size: 24px;
        font-weight: bold;
        color: #001e40;
    }

    .title {
        text-align: right;
        font-size: 22px;
        font-weight: bold;
        color: #001e40;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .info td {
        width: 50%;
        vertical-align: top;
        padding: 8px 16px 8px 0;
    }

    .box-title {
        font-weight: bold;
        color: #001e40;
        text-transform: uppercase;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 6px;
        margin-bottom: 8px;
    }

    .line {
        margin-bottom: 6px;
    }

    .label {
        color: #6b7280;
    }

    .value {
        font-weight: bold;
    }

    .items th {
        background: #001e40;
        color: white;
        padding: 10px 8px;
        text-align: left;
        font-size: 11px;
    }

    .items td {
        border-bottom: 1px solid #e5e7eb;
        padding: 10px 8px;
    }

    .text-right {
        text-align: right;
    }

    .summary {
        width: 46%;
        margin-left: auto;
        margin-top: 24px;
    }

    .summary td {
        padding: 7px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .summary .total td {
        font-size: 15px;
        font-weight: bold;
        color: #001e40;
        border-bottom: none;
        padding-top: 10px;
    }

    .footer {
        margin-top: 36px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        color: #6b7280;
        font-size: 11px;
    }
</style>

</head>

<body>
    @php
        /*
        |--------------------------------------------------------------------------
        | Chuẩn hóa số tiền hiển thị trên hóa đơn
        |--------------------------------------------------------------------------
        | Ưu tiên lấy dữ liệu đã lưu trong bảng orders.
        | Nếu subtotal bị thiếu, hệ thống tự tính lại từ danh sách sản phẩm.
        | Nếu tax_amount bị thiếu, hệ thống tự tính VAT 10% theo subtotal.
        */


    $subtotal = $order->subtotal ?? 0;

    if ($subtotal <= 0 && isset($items)) {
        $subtotal = collect($items)->sum(function ($item) {
            $quantity = $item->quantity ?? 1;
            $price = $item->unit_price ?? $item->price ?? 0;

            return $quantity * $price;
        });
    }

    $shippingFee = $order->shipping_fee ?? 0;
    $discountAmount = $order->discount_amount ?? 0;

    $voucherCode = $order->voucher_code
        ?? $order->coupon_code
        ?? $order->discount_code
        ?? null;

    $taxAmount = $order->tax_amount ?? round($subtotal * 0.10);

    $totalAmount = $order->total_amount
        ?? ($subtotal + $shippingFee + $taxAmount - $discountAmount);
@endphp

<table class="header">
    <tr>
        <td>
            <div class="brand">B-Tris</div>
            <div>Hệ thống bán hàng công nghệ</div>
        </td>
        <td class="title">HÓA ĐƠN</td>
    </tr>
</table>

<table class="info">
    <tr>
        <td>
            <div class="box-title">Thông tin đơn hàng</div>

            <div class="line">
                <span class="label">Mã đơn:</span>
                <span class="value">{{ $order->order_code ?? $order->order_id }}</span>
            </div>

            <div class="line">
                <span class="label">Ngày đặt:</span>
                <span class="value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
            </div>

            <div class="line">
                <span class="label">Thanh toán:</span>
                <span class="value">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
            </div>

            <div class="line">
                <span class="label">Trạng thái thanh toán:</span>
                <span class="value">{{ $order->payment_status ?? 'pending' }}</span>
            </div>

            <div class="line">
                <span class="label">Trạng thái đơn:</span>
                <span class="value">{{ $order->order_status ?? 'pending' }}</span>
            </div>
        </td>

        <td>
            <div class="box-title">Thông tin khách hàng</div>

            <div class="line">
                <span class="label">Khách hàng:</span>
                <span class="value">{{ $order->receiver_name ?? $order->user_full_name ?? 'Khách hàng' }}</span>
            </div>

            <div class="line">
                <span class="label">Email:</span>
                <span class="value">{{ $order->user_email ?? 'Không có' }}</span>
            </div>

            <div class="line">
                <span class="label">SĐT:</span>
                <span class="value">{{ $order->phone ?? 'Không có' }}</span>
            </div>

            <div class="line">
                <span class="label">Địa chỉ:</span>
                <span class="value">
                    {{ $order->street_address ?? '' }}
                    {{ !empty($order->ward) ? ', ' . $order->ward : '' }}
                    {{ !empty($order->district) ? ', ' . $order->district : '' }}
                    {{ !empty($order->province) ? ', ' . $order->province : '' }}
                </span>
            </div>
        </td>
    </tr>
</table>

<div class="box-title" style="margin-top: 20px;">Chi tiết sản phẩm</div>

<table class="items">
    <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>SKU</th>
            <th class="text-right">SL</th>
            <th class="text-right">Đơn giá</th>
            <th class="text-right">Thành tiền</th>
        </tr>
    </thead>

    <tbody>
        @forelse($items as $item)
            @php
                $quantity = $item->quantity ?? 1;
                $price = $item->unit_price ?? $item->price ?? 0;
                $lineTotal = $quantity * $price;
            @endphp

            <tr>
                <td>{{ $item->product_name ?? $item->product_name_snapshot ?? 'Sản phẩm' }}</td>
                <td>{{ $item->sku ?? 'N/A' }}</td>
                <td class="text-right">{{ $quantity }}</td>
                <td class="text-right">{{ number_format($price, 0, ',', '.') }}đ</td>
                <td class="text-right">{{ number_format($lineTotal, 0, ',', '.') }}đ</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; color:#6b7280;">
                    Không có sản phẩm trong đơn hàng.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="summary">
    <tr>
        <td>Tạm tính</td>
        <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
    </tr>

    <tr>
        <td>Phí vận chuyển</td>
        <td class="text-right">{{ number_format($shippingFee, 0, ',', '.') }}đ</td>
    </tr>

    <tr>
        <td>Mã giảm giá</td>
        <td class="text-right">{{ $voucherCode ?: 'Không áp dụng' }}</td>
    </tr>

    <tr>
        <td>Giảm giá</td>
        <td class="text-right">-{{ number_format($discountAmount, 0, ',', '.') }}đ</td>
    </tr>

    <tr>
        <td>VAT 10%</td>
        <td class="text-right">{{ number_format($taxAmount, 0, ',', '.') }}đ</td>
    </tr>

    <tr class="total">
        <td>Tổng thanh toán</td>
        <td class="text-right">{{ number_format($totalAmount, 0, ',', '.') }}đ</td>
    </tr>
</table>

<div class="footer">
    Cảm ơn quý khách đã mua hàng tại B-Tris.<br>
    Hóa đơn này được tạo tự động từ hệ thống.
</div>
```

</body>
</html>
