<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #001e40;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8fbff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info td {
            padding: 6px 0;
            font-size: 14px;
        }
        .order-info td.label {
            color: #64748b;
            width: 40%;
        }
        .order-info td.value {
            font-weight: bold;
            color: #0f172a;
            text-align: right;
        }
        .btn {
            display: inline-block;
            background-color: #001e40;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            margin-top: 15px;
            text-align: center;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CẢM ƠN QUÝ KHÁCH!</h1>
            <p style="margin: 5px 0 0 0; color: #93c5fd;">Đơn hàng của bạn đã được xác nhận thành công</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $order->receiver_name ?? $order->user_full_name ?? 'Khách hàng' }}</strong>,</p>
            <p>Cảm ơn quý khách đã mua sắm tại <strong>B-Tris</strong>. Chúng tôi rất vui mừng thông báo đơn hàng của quý khách đã được tiếp nhận và đang trong quá trình xử lý.</p>
            
            <p>Hóa đơn bán hàng chi tiết đã được đính kèm dưới định dạng <strong>PDF</strong> trong email này để quý khách tiện theo dõi.</p>

            <div class="order-info">
                <h3 style="margin-top: 0; color: #001e40; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Thông tin đơn hàng</h3>
                <table>
                    <tr>
                        <td class="label">Mã đơn hàng:</td>
                        <td class="value">#{{ $order->order_code ?? $order->order_id }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phương thức:</td>
                        <td class="value">{{ strtoupper($order->payment_method ?? 'COD') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tổng thanh toán:</td>
                        <td class="value" style="color: #ef4444; font-size: 16px;">{{ number_format($order->total_amount ?? 0, 0, ',', '.') }}đ</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 13px; color: #64748b;">Nếu có bất kỳ thắc mắc nào, quý khách vui lòng phản hồi email này hoặc liên hệ Hotline hỗ trợ của chúng tôi để được trợ giúp ngay lập tức.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} B-Tris. All rights reserved.<br>
            Hệ thống bán hàng công nghệ uy tín hàng đầu.
        </div>
    </div>
</body>
</html>
