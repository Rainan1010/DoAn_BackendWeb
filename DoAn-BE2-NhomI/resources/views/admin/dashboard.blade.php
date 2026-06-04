@extends('admin.layouts.app')

@section('header_search')
<form action="{{ route('admin.dashboard') }}" method="GET" class="relative">
    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm..." 
        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] transition-all">
</form>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#0A2540] tracking-tight">Tổng quan Dashboard</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Các chỉ số hiệu suất thời gian thực cho hệ sinh thái B-Tris Tech.</p>
        </div>
        <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-gray-100 shadow-sm">
            @foreach(['24h', '7d', '30d'] as $r)
                <a href="{{ route('admin.dashboard', ['range' => $r]) }}" 
                   class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all {{ request('range', '24h') == $r ? 'bg-[#0A2540] text-white shadow-lg shadow-[#0A2540]/20' : 'text-gray-400 hover:bg-gray-50' }}">
                    {{ $r === '24h' ? '24 Giờ' : ($r === '7d' ? '7 Ngày' : '30 Ngày') }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-[#0A2540]/5 transition-all duration-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-[#0A2540]">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    </div>
                    <span class="flex items-center gap-1 text-[10px] font-black {{ $stats['revenue']['trend'] === 'up' ? 'text-[#0FAF62]' : 'text-red-500' }} bg-opacity-10 px-2 py-1 rounded-full uppercase tracking-widest">
                        <i data-lucide="trending-{{ $stats['revenue']['trend'] }}" class="w-3 h-3"></i>
                        {{ $stats['revenue']['growth'] }}%
                    </span>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng Doanh Thu</p>
                <h3 class="text-2xl font-black text-[#0A2540]">{{ number_format($stats['revenue']['total']) }} <span class="text-xs font-bold text-gray-400">VNĐ</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                <i data-lucide="dollar-sign" class="w-32 h-32 text-[#0A2540]"></i>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-[#0A2540]/5 transition-all duration-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    </div>
                    <span class="flex items-center gap-1 text-[10px] font-black text-[#0FAF62] bg-opacity-10 px-2 py-1 rounded-full uppercase tracking-widest">
                        Tỷ lệ hoàn thành {{ $stats['orders']['fulfillment_rate'] }}%
                    </span>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng Đơn Hàng</p>
                <h3 class="text-2xl font-black text-[#0A2540]">{{ number_format($stats['orders']['total']) }}</h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                <i data-lucide="shopping-bag" class="w-32 h-32 text-purple-600"></i>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-[#0A2540]/5 transition-all duration-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div class="flex -space-x-2">
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-200"></div>
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-300"></div>
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-400 flex items-center justify-center text-[8px] text-white font-bold">+</div>
                    </div>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Khách Hàng Hoạt Động</p>
                <h3 class="text-2xl font-black text-[#0A2540]">{{ number_format($stats['customers']['total']) }}</h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                <i data-lucide="users" class="w-32 h-32 text-orange-500"></i>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-[#0A2540]/5 transition-all duration-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-[#0FAF62]">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[10px] font-black text-red-500 bg-red-50 px-2 py-1 rounded-full uppercase tracking-widest">
                        Hết hàng: {{ $stats['products']['out_of_stock'] }}
                    </span>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sản phẩm hoạt động</p>
                <h3 class="text-2xl font-black text-[#0A2540]">{{ $stats['products']['total'] }}</h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                <i data-lucide="package" class="w-32 h-32 text-[#0FAF62]"></i>
            </div>
        </div>
    </div>

    <!-- Charts and Secondary Data -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest">Xu Hướng Doanh Thu</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Phân tích tăng trưởng hàng tháng cho năm tài chính hiện tại</p>
                </div>
                <button class="text-gray-400 hover:text-[#0A2540] transition-colors"><i data-lucide="more-vertical" class="w-5 h-5"></i></button>
            </div>
            <div class="h-64 relative w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
            <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest">Trạng Thái Đơn Hàng</h3>
            <p class="text-xs text-gray-400 font-medium mt-1">Phân tích chu kỳ hoạt động của đơn hàng</p>
            
            <div class="flex-1 flex flex-col items-center justify-center py-6 relative">
                <div class="relative w-44 h-44 flex items-center justify-center">
                    <canvas id="orderStatusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-3xl font-black text-[#0A2540]">{{ $orderStatus['success_rate'] }}%</p>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Thành công</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3 pt-6 border-t border-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#0A2540]"></span>
                        <span class="text-xs font-bold text-gray-500 uppercase">Đã giao hàng</span>
                    </div>
                    <span class="text-xs font-black text-[#0A2540]">{{ number_format($orderStatus['delivered']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-300"></span>
                        <span class="text-xs font-bold text-gray-500 uppercase">Đang xử lý</span>
                    </div>
                    <span class="text-xs font-black text-[#0A2540]">{{ number_format($orderStatus['processing']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-200"></span>
                        <span class="text-xs font-bold text-gray-500 uppercase">Bị trả lại</span>
                    </div>
                    <span class="text-xs font-black text-[#0A2540]">{{ number_format($orderStatus['returned']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Top Products & Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
        <!-- Top Selling Products -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-[#0A2540] uppercase tracking-widest">Sản Phẩm Bán Chạy Nhất</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Các sản phẩm mang lại doanh thu cao nhất</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-600 transition-colors">Xem tất cả</a>
            </div>
            <div class="p-0">
                @foreach($topSellingProducts as $product)
                <div class="flex items-center justify-between p-6 hover:bg-gray-50 transition-all border-b border-gray-50 last:border-0 group">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden border border-gray-100">
                            <img src="{{ $product['image'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="">
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-[#0A2540]">{{ $product['name'] }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $product['category'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-[#0A2540]">{{ number_format($product['revenue']) }} <span class="text-[10px] font-bold text-gray-400">VNĐ</span></p>
                        <p class="text-[10px] font-black text-[#0FAF62] uppercase tracking-widest">Đã bán: {{ $product['sold'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-[#0A2540] rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
            <h3 class="text-sm font-black uppercase tracking-widest mb-8 relative z-10">Hoạt Động Gần Đây</h3>
            <div class="space-y-8 relative z-10">
                @foreach($recentActivities as $activity)
                <div class="flex gap-4 relative">
                    <div class="absolute left-4 top-10 bottom-0 w-px bg-white/10 last:hidden"></div>
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="{{ $activity['icon'] }}" class="w-4 h-4 text-blue-300"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest">{{ $activity['title'] }}</h4>
                        <p class="text-xs text-blue-200 mt-1 leading-relaxed">{{ $activity['desc'] }}</p>
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mt-2">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-12 pt-8 border-t border-white/10 relative z-10">
                <button class="w-full py-4 bg-white text-[#0A2540] rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl shadow-black/20">
                    Xuất báo cáo đầy đủ
                </button>
            </div>
            
            <!-- Background Decoration -->
            <div class="absolute -right-24 -bottom-24 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tự động làm mới trang sau mỗi 30 giây để tránh gián đoạn tương tác biểu đồ
        setInterval(function() {
            if (!document.activeElement || (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA')) {
                window.location.reload();
            }
        }, 30000);

        // Map tháng viết tắt sang Tiếng Việt
        const monthTranslations = {
            'Jan': 'Thg 1', 'Feb': 'Thg 2', 'Mar': 'Thg 3', 'Apr': 'Thg 4',
            'May': 'Thg 5', 'Jun': 'Thg 6', 'Jul': 'Thg 7', 'Aug': 'Thg 8',
            'Sep': 'Thg 9', 'Oct': 'Thg 10', 'Nov': 'Thg 11', 'Dec': 'Thg 12'
        };

        // --- 1. BIỂU ĐỒ DOANH THU (LINE CHART) ---
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const rawTrends = {!! json_encode($revenueTrends) !!};
        const revenueLabels = Object.keys(rawTrends).map(m => monthTranslations[m] ?? m);
        const revenueData = Object.values(rawTrends);

        // Tạo gradient màu nền cho đường biểu diễn
        const revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 240);
        revenueGradient.addColorStop(0, 'rgba(10, 37, 64, 0.15)');
        revenueGradient.addColorStop(1, 'rgba(10, 37, 64, 0.00)');

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    borderColor: '#0A2540',
                    borderWidth: 2.5,
                    backgroundColor: revenueGradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0A2540',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0A2540',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return ' Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        border: { dash: [5, 5] },
                        grid: {
                            color: '#E5E7EB',
                            tickBorderDash: [5, 5]
                        },
                        ticks: {
                            font: { family: 'Inter', size: 10, weight: '600' },
                            color: '#9CA3AF',
                            callback: function(value) {
                                if (value >= 1e6) {
                                    return (value / 1e6) + ' triệu';
                                }
                                return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 10, weight: '600' },
                            color: '#9CA3AF'
                        }
                    }
                }
            }
        });

        // --- 2. BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (DOUGHNUT) ---
        const ctxOrder = document.getElementById('orderStatusChart').getContext('2d');
        const orderData = [
            {{ $orderStatus['delivered'] }},
            {{ $orderStatus['processing'] }},
            {{ $orderStatus['returned'] }}
        ];

        const isAllZero = orderData.every(val => val === 0);
        const chartData = isAllZero ? [1, 0, 0] : orderData; 

        new Chart(ctxOrder, {
            type: 'doughnut',
            data: {
                labels: ['Đã giao hàng', 'Đang xử lý', 'Bị trả lại'],
                datasets: [{
                    data: chartData,
                    backgroundColor: ['#0A2540', '#93C5FD', '#FEE2E2'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0A2540',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                const val = isAllZero && context.dataIndex === 0 ? 0 : orderData[context.dataIndex];
                                return ' ' + context.label + ': ' + val + ' đơn';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
