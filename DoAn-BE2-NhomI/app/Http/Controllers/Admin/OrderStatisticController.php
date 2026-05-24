<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderStatisticController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        $totalOrders = DB::table('orders')->count();

        // Đơn hàng đang chờ duyệt thực sự (chỉ trạng thái pending)
        $pendingOrders = DB::table('orders')
            ->where('order_status', 'pending')
            ->count();

        $completedOrders = DB::table('orders')
            ->whereIn('order_status', ['delivered'])
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereIn('order_status', ['cancelled', 'canceled'])
            ->count();

        // Doanh thu thực tế là tổng dòng tiền từ các đơn đã thanh toán thành công (paid)
        $totalRevenue = DB::table('orders')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Doanh thu thực tế hôm nay
        $todayRevenue = DB::table('orders')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $todayNewOrders = DB::table('orders')
            ->whereDate('created_at', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | QUERY DANH SÁCH ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $recentOrdersQuery = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.user_id')
            ->select(
                'orders.order_id',
                'orders.user_id',
                'orders.shipping_address_id',
                'orders.voucher_id',
                'orders.order_code',
                'orders.subtotal',
                'orders.shipping_fee',
                'orders.discount_amount',
                'orders.total_amount',
                'orders.payment_method',
                'orders.payment_status',
                'orders.order_status',
                'orders.cancel_reason',
                'orders.paid_at',
                'orders.created_at',
                'users.full_name as customer_name',
                'users.email as customer_email'
            );

        if ($request->filled('search')) {
            $keyword = $request->search;

            $recentOrdersQuery->where(function ($query) use ($keyword) {
                $query->where('orders.order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('users.full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.email', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status')) {
            $recentOrdersQuery->where('orders.order_status', $request->status);
        }

        if ($request->filled('date_filter') && $request->date_filter !== 'all') {
            $filter = $request->date_filter;
            if ($filter === 'today') {
                $recentOrdersQuery->whereDate('orders.created_at', today());
            } elseif ($filter === 'yesterday') {
                $recentOrdersQuery->whereDate('orders.created_at', today()->subDay());
            } elseif ($filter === '7days') {
                $recentOrdersQuery->where('orders.created_at', '>=', today()->subDays(7));
            } elseif ($filter === '30days') {
                $recentOrdersQuery->where('orders.created_at', '>=', today()->subDays(30));
            } elseif ($filter === 'custom' && $request->filled('custom_date')) {
                $recentOrdersQuery->whereDate('orders.created_at', $request->custom_date);
            }
        }

        // Nhận tham số số bản ghi hiển thị (per_page)
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        // Sắp xếp đơn hàng theo ID giảm dần và phân trang thực tế
        $recentOrders = $recentOrdersQuery
            ->orderBy('orders.order_id', 'desc')
            ->limit($perPage)
            ->get();

        return view('admin.order_statistics.index', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'todayRevenue',
            'todayNewOrders',
            'recentOrders'
        ));
    }

    public function create()
    {
        $products = \App\Models\Product::with(['variants', 'images'])
            ->where('is_active', 1)
            ->get();

        $shippingFees = \App\Models\ShippingFee::orderBy('province', 'asc')->get();

        $vouchers = \App\Models\Voucher::where('is_active', 1)
            ->where(function($query) {
                $query->whereNull('end_at')
                      ->orWhere('end_at', '>=', now());
            })
            ->get();

        return view('admin.order_statistics.create', compact('products', 'shippingFees', 'vouchers'));
    }

    public function searchUser(Request $request)
    {
        $search = $request->input('search');
        if (empty($search)) {
            return response()->json([]);
        }

        $users = \App\Models\User::where('role', 'user')
            ->where(function($query) use ($search) {
                $query->where('phone', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get();

        $results = [];
        foreach ($users as $user) {
            $defaultAddress = \App\Models\ShippingAddress::where('user_id', $user->user_id)
                ->orderByDesc('is_default')
                ->orderByDesc('address_id')
                ->first();

            $results[] = [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $defaultAddress ? [
                    'province' => $defaultAddress->province,
                    'district' => $defaultAddress->district,
                    'ward' => $defaultAddress->ward,
                    'street_address' => $defaultAddress->street_address,
                ] : null
            ];
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'delivery_type' => 'required|in:home,store',
            'province' => 'required_if:delivery_type,home|nullable|string|max:255',
            'district' => 'required_if:delivery_type,home|nullable|string|max:255',
            'ward' => 'required_if:delivery_type,home|nullable|string|max:255',
            'street_address' => 'required_if:delivery_type,home|nullable|string|max:255',
            'payment_method' => 'required|in:cod,vnpay,momo',
            'payment_status' => 'required|in:pending,paid,refunded',
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $userId = $request->user_id;
            if (empty($userId)) {
                $existUser = \App\Models\User::where('phone', $request->phone)->first();
                if ($existUser) {
                    $userId = $existUser->user_id;
                } else {
                    $user = \App\Models\User::create([
                        'full_name' => $request->full_name,
                        'phone' => $request->phone,
                        'email' => $request->email ?? 'guest-' . time() . '@nhomi.com',
                        'password_hash' => bcrypt('nhomi123'),
                        'role' => 'user',
                        'is_active' => 1,
                    ]);
                    $userId = $user->user_id;
                }
            }

            $shippingAddressId = null;
            if ($request->delivery_type == 'home') {
                $address = \App\Models\ShippingAddress::create([
                    'user_id' => $userId,
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'province' => $request->province,
                    'district' => $request->district,
                    'ward' => $request->ward,
                    'street_address' => $request->street_address ?? 'Nhận tại nhà',
                    'is_default' => 0,
                ]);
                $shippingAddressId = $address->address_id;
            } else {
                $showroomAddress = \App\Models\ShippingAddress::create([
                    'user_id' => $userId,
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'province' => 'Showroom',
                    'district' => 'Showroom',
                    'ward' => 'Showroom',
                    'street_address' => $request->pickup_store ?? 'Nhận tại cửa hàng B-Tris',
                    'is_default' => 0,
                ]);
                $shippingAddressId = $showroomAddress->address_id;
            }

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $variant = \App\Models\ProductVariant::with('product')
                    ->where('variant_id', $item['variant_id'])
                    ->lockForUpdate()
                    ->first();
                if (!$variant) {
                    throw new \Exception('Biến thể sản phẩm không tồn tại!');
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    throw new \Exception('Sản phẩm ' . ($variant->product->name ?? '') . ' không đủ tồn kho!');
                }

                $variant->decrement('stock_quantity', $item['quantity']);

                $unitPrice = $item['price'];
                $qty = $item['quantity'];
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;

                $variantInfo = null;
                if (is_array($variant->attribute_values)) {
                    $variantInfo = implode(' - ', $variant->attribute_values);
                } elseif (is_string($variant->attribute_values)) {
                    $decoded = json_decode($variant->attribute_values, true);
                    $variantInfo = is_array($decoded) ? implode(' - ', $decoded) : $variant->attribute_values;
                }

                $orderItemsData[] = [
                    'variant_id' => $variant->variant_id,
                    'product_name' => $variant->product->name ?? 'Sản phẩm',
                    'variant_info' => $variantInfo,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingFee = $request->delivery_type == 'home' ? ($request->shipping_fee ?? 30000) : 0;
            $discount = $request->discount_amount ?? 0;
            $voucherId = $request->voucher_id;

            if ($voucherId) {
                $voucher = DB::table('vouchers')
                    ->where('voucher_id', $voucherId)
                    ->lockForUpdate()
                    ->first();
                
                if (!$voucher) {
                    throw new \Exception('Voucher không tồn tại!');
                }

                $now = now();
                $isValid = $voucher->is_active
                    && (!$voucher->end_at || $now->lte($voucher->end_at))
                    && ($voucher->usage_limit === null || $voucher->used_count < $voucher->usage_limit)
                    && ($voucher->min_order_value === null || $subtotal >= $voucher->min_order_value);

                if (!$isValid) {
                    throw new \Exception("Mã giảm giá [{$voucher->code}] đã hết lượt sử dụng hoặc không hợp lệ!");
                }

                DB::table('vouchers')->where('voucher_id', $voucherId)->increment('used_count');
            }

            $totalAmount = $subtotal + $shippingFee + ($subtotal * 0.1) - $discount;
            if ($totalAmount < 0) {
                $totalAmount = 0;
            }

            $order = \App\Models\Order::create([
                'user_id' => $userId,
                'shipping_address_id' => $shippingAddressId,
                'voucher_id' => $voucherId,
                'order_code' => 'ORD-ADM-' . time(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'order_status' => $request->order_status,
                'cancel_reason' => $request->cancel_reason,
                'paid_at' => $request->payment_status == 'paid' ? now() : null,
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->order_id;
                \App\Models\OrderItem::create($itemData);
            }

            \App\Models\Payment::create([
                'order_id' => $order->order_id,
                'gateway' => $request->payment_method,
                'transaction_id' => 'ADM-' . strtoupper($request->payment_method) . '-' . time(),
                'amount' => $totalAmount,
                'status' => $request->payment_status == 'paid' ? 'success' : 'pending',
                'paid_at' => $request->payment_status == 'paid' ? now() : null,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.order-statistics.index')
                ->with('success', 'Đơn hàng mới đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $order = \App\Models\Order::with(['items.variant.product', 'shippingAddress', 'user'])->find($id);
        if (!$order) {
            return redirect()
                ->route('admin.order-statistics.index')
                ->with('error', 'Đơn hàng không tồn tại!');
        }

        return view('admin.order_statistics.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'cancel_reason' => 'required_if:order_status,cancelled|nullable|string|max:500',
        ], [
            'cancel_reason.required_if' => 'Vui lòng nhập lý do huỷ đơn hàng.',
        ]);

        DB::beginTransaction();

        try {
            $order = \App\Models\Order::with('items')->findOrFail($id);
            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            $oldPaymentStatus = $order->payment_status;
            $newPaymentStatus = $request->payment_status;

            // 1. Xử lý hoàn kho khi hủy đơn hoặc trừ kho khi khôi phục đơn
            if ($newStatus == 'cancelled' && $oldStatus != 'cancelled') {
                // Hoàn kho
                foreach ($order->items as $item) {
                    \App\Models\ProductVariant::where('variant_id', $item->variant_id)
                        ->increment('stock_quantity', $item->quantity);
                }
                // Hoàn trả lượt dùng Voucher nếu có
                if ($order->voucher_id) {
                    DB::table('vouchers')->where('voucher_id', $order->voucher_id)->decrement('used_count');
                }
            } elseif ($oldStatus == 'cancelled' && $newStatus != 'cancelled') {
                // Khôi phục đơn từ đã hủy -> kiểm tra tồn kho trước khi trừ (đã được khóa bi quan để tránh Race Condition)
                foreach ($order->items as $item) {
                    $variant = \App\Models\ProductVariant::where('variant_id', $item->variant_id)
                        ->lockForUpdate()
                        ->first();
                    if (!$variant || $variant->stock_quantity < $item->quantity) {
                        throw new \Exception("Không thể khôi phục đơn hàng vì biến thể sản phẩm [ID: {$item->variant_id}] không đủ tồn kho!");
                    }
                    $variant->decrement('stock_quantity', $item->quantity);
                }
                // Trừ lại lượt dùng Voucher nếu có
                if ($order->voucher_id) {
                    DB::table('vouchers')->where('voucher_id', $order->voucher_id)->increment('used_count');
                }
            }

            // 2. Cập nhật trạng thái thanh toán và paid_at
            $paidAt = $order->paid_at;
            if ($newPaymentStatus == 'paid' && $oldPaymentStatus != 'paid') {
                $paidAt = now();
                // Cập nhật hoặc tạo mới bản ghi payment
                \App\Models\Payment::updateOrCreate(
                    ['order_id' => $order->order_id],
                    [
                        'status' => 'success',
                        'paid_at' => $paidAt,
                        'amount' => $order->total_amount,
                        'gateway' => $order->payment_method ?: 'cod',
                        'transaction_id' => 'ADM-UPD-' . strtoupper($order->payment_method ?: 'cod') . '-' . time()
                    ]
                );
            } elseif ($newPaymentStatus != 'paid' && $oldPaymentStatus == 'paid') {
                $paidAt = null;
                \App\Models\Payment::where('order_id', $order->order_id)
                    ->update([
                        'status' => 'pending',
                        'paid_at' => null
                    ]);
            }

            // 3. Lưu thông tin đơn hàng
            $order->update([
                'order_status' => $newStatus,
                'payment_status' => $newPaymentStatus,
                'cancel_reason' => $newStatus == 'cancelled' ? $request->cancel_reason : null,
                'paid_at' => $paidAt,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.order-statistics.index')
                ->with('success', "Đơn hàng #{$order->order_code} đã được cập nhật thành công!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function confirm($id)
    {
        DB::beginTransaction();
        try {
            $order = \App\Models\Order::findOrFail($id);
            if ($order->order_status !== 'pending') {
                return redirect()
                    ->route('admin.order-statistics.index')
                    ->with('error', 'Chỉ có thể xác nhận đơn hàng đang ở trạng thái Chờ xác nhận!');
            }

            $order->update([
                'order_status' => 'confirmed'
            ]);

            DB::commit();

            return redirect()
                ->route('admin.order-statistics.index')
                ->with('success', "Đơn hàng #{$order->order_code} đã được xác nhận thành công!");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->route('admin.order-statistics.index')
                ->with('error', 'Lỗi xác nhận đơn hàng: ' . $e->getMessage());
        }
    }
}