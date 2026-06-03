@extends('admin.layouts.app')

@section('content')
    <style>
        .chart-btn-active {

            background: linear-gradient(135deg,
                    #001e40,
                    #003b80);

            color: white;

            box-shadow:
                0 10px 25px rgba(0, 30, 64, .2);
        }

        .chart-btn {

            background: white;

            color: #4b5563;
        }

        .chart-btn:hover {

            background: #f3f4f6;

            color: #001e40;
        }
    </style>
    @php
        $from = request('from', now()->subDays(5)->format('Y-m-d'));
        $to = request('to', now()->format('Y-m-d'));
    @endphp

    <div class="space-y-8">

        {{-- BREADCRUMB --}}
        <div class="flex items-center gap-2 text-sm">

            <span class="text-gray-400 font-semibold">
                ADMIN
            </span>

            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300">
            </i>

            <span class="text-[#001e40] font-black">
                QUẢN LÝ DOANH THU
            </span>

        </div>

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row justify-between gap-5">

            <div>

                <h1 class="text-5xl font-black text-[#001e40]">
                    Báo cáo doanh thu
                </h1>

                <p class="text-gray-400 mt-3 text-lg">
                    Thống kê doanh thu hệ thống
                </p>

            </div>

            <div class="flex gap-3">

                <form action="#" method="GET">

                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">

                    <!-- <button class="group
                                           bg-gradient-to-r
                                           from-[#001e40]
                                           to-[#003b80]
                                           hover:from-[#003366]
                                           hover:to-[#004c99]
                                           text-white
                                           px-8 py-4
                                           rounded-2xl
                                           font-black
                                           shadow-[0_10px_30px_rgba(0,30,64,0.25)]
                                           hover:shadow-[0_16px_40px_rgba(0,30,64,0.35)]
                                           transition-all duration-300
                                           flex items-center gap-3
                                           hover:-translate-y-1">

                        <i data-lucide="download" class="w-5 h-5 transition-transform
                                               group-hover:-translate-y-1"></i>

                        Xuất File Excel

                    </button> -->

                </form>

            </div>

        </div>

        {{-- FILTER --}}
        <form id="filterForm" action="{{ route('admin.revenue_reports.index') }}" method="GET"
            class="bg-white rounded-3xl p-6 shadow-sm">

            <div class="grid lg:grid-cols-4 gap-5">

                {{-- FROM --}}
                <div>

                    <label class="font-bold text-sm text-[#001e40]">
                        Từ ngày
                    </label>

                    <div class="relative mt-3">

                        <i data-lucide="calendar" class="absolute left-4 top-1/2
                                                      -translate-y-1/2
                                                      w-5 h-5 text-gray-400 z-10">
                        </i>

                        <input type="date" name="from" value="{{ $from }}" max="{{ now()->subDay()->format('Y-m-d') }}"
                            class="w-full h-[56px]
                                                       rounded-2xl
                                                       border border-gray-200
                                                       bg-white
                                                       px-12
                                                       text-[#001e40]
                                                       font-semibold
                                                       focus:ring-2
                                                       focus:ring-[#003366]
                                                       focus:border-[#003366]
                                                       outline-none" />

                    </div>

                </div>

                <div>

                    <label class="font-bold text-sm text-[#001e40]">
                        Đến ngày
                    </label>

                    <div class="relative mt-3">

                        <i data-lucide="calendar-days" class="absolute left-4 top-1/2
                                                      -translate-y-1/2
                                                      w-5 h-5 text-gray-400 z-10">
                        </i>

                        <input type="date" name="to" value="{{ $to }}" max="{{ now()->format('Y-m-d') }}" class="w-full h-[56px]
                                                       rounded-2xl
                                                       border border-gray-200
                                                       bg-white
                                                       px-12
                                                       text-[#001e40]
                                                       font-semibold
                                                       focus:ring-2
                                                       focus:ring-[#003366]
                                                       focus:border-[#003366]
                                                       outline-none" />

                    </div>

                </div>

                {{-- BTN --}}
                <div class="flex items-end">
                    <button class="group
                                       w-full
                                       bg-gradient-to-r
                                       from-[#001e40]
                                       to-[#003b80]
                                       hover:from-[#003366]
                                       hover:to-[#004c99]
                                       text-white
                                       h-[56px]
                                       rounded-2xl
                                       font-black
                                       transition-all duration-300
                                       flex items-center justify-center gap-3
                                       shadow-lg
                                       hover:-translate-y-1">

                        <i data-lucide="search" class="w-5 h-5
                                           transition-transform
                                           group-hover:scale-110"></i>

                        Lọc doanh thu

                    </button>

                </div>

                {{-- RESET --}}
                <div class="flex items-end">

                    <a href="{{ route('admin.revenue_reports.index') }}" class="group
                                           w-full
                                           h-[56px]
                                           rounded-2xl
                                           bg-gray-100
                                           hover:bg-red-50
                                           border border-gray-200
                                           hover:border-red-200
                                           text-gray-700
                                           hover:text-red-500
                                           font-black
                                           transition-all duration-300
                                           flex items-center justify-center gap-3
                                           hover:-translate-y-1">

                        <i data-lucide="rotate-ccw" class="w-5 h-5
                                               transition-transform
                                               group-hover:-rotate-180"></i>

                        Reset

                    </a>

                </div>

            </div>

        </form>
        {{-- TOTAL SYSTEM REVENUE --}}
        <div class="relative overflow-hidden
               rounded-[32px]
               bg-gradient-to-r
               from-[#001e40]
               via-[#003366]
               to-[#004c99]
               p-8
               text-white
               shadow-[0_20px_50px_rgba(0,30,64,0.25)]">

            {{-- BACKGROUND EFFECT --}}
            <div class="absolute
                   -right-10
                   -top-10
                   w-56 h-56
                   bg-white/10
                   rounded-full blur-3xl"></div>

            <div class="absolute
                   bottom-0
                   right-0
                   opacity-10
                   text-[220px]
                   leading-none
                   font-black">

                ₫

            </div>

            <div class="relative z-10">

                <div class="flex items-start justify-between flex-wrap gap-6">

                    {{-- LEFT --}}
                    <div>

                        <div class="inline-flex
                               items-center gap-2
                               bg-white/10
                               px-4 py-2
                               rounded-full">

                            <span class="w-2 h-2
                                   rounded-full
                                   bg-green-400"></span>

                            <span class="text-sm font-bold">
                                TOÀN HỆ THỐNG
                            </span>

                        </div>

                        <h2 class="text-5xl lg:text-6xl font-black mt-6">

                            {{ number_format($totalRevenueAll) }}đ

                        </h2>

                        <p class="text-blue-100 mt-4 text-lg">

                            Tổng doanh thu từ trước đến nay

                        </p>

                    </div>

                    {{-- RIGHT --}}
                    <div class="flex gap-4 flex-wrap">

                        {{-- TOTAL ORDERS --}}
                        <div class="bg-white/10
                               backdrop-blur-xl
                               rounded-3xl
                               px-6 py-5
                               min-w-[180px]">

                            <p class="text-blue-200 text-sm">
                                Tổng đơn hàng
                            </p>

                            <h3 class="text-3xl font-black mt-3">

                                {{ number_format($totalOrders) }}

                            </h3>

                        </div>

                        {{-- AVG --}}
                        <div class="bg-white/10
                               backdrop-blur-xl
                               rounded-3xl
                               px-6 py-5
                               min-w-[180px]">

                            <p class="text-blue-200 text-sm">
                                Giá trị TB
                            </p>

                            <h3 class="text-3xl font-black mt-3">

                                {{ number_format($avgOrderValueAll) }}đ

                            </h3>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        {{-- STATS --}}
        <div class="grid lg:grid-cols-4 gap-6">

            {{-- REVENUE --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border">

                <div class="flex items-center justify-between">

                    <div class="w-16 h-16 rounded-2xl
                                                                bg-blue-50
                                                                flex items-center justify-center">

                        <i data-lucide="wallet" class="w-8 h-8 text-[#003366]">
                        </i>

                    </div>

                    <span class="bg-green-100
                                                                 text-green-600
                                                                 text-sm
                                                                 px-3 py-1
                                                                 rounded-full
                                                                 font-bold">

                        +12.4%

                    </span>

                </div>

                <p class="text-gray-400 text-sm mt-6">
                    Tổng doanh thu
                </p>

                <h2 class="text-4xl font-black text-[#001e40] mt-3">

                    {{ number_format($totalRevenue) }}đ

                </h2>
                <div class="mt-4 pt-4 border-t border-gray-100">

                    <p class="text-xs text-gray-400 uppercase tracking-widest">
                        Tổng toàn hệ thống
                    </p>

                    <p class="text-lg font-black text-green-600 mt-2">

                        {{ number_format($totalRevenueAll) }}đ

                    </p>

                </div>
            </div>

            {{-- ORDERS --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border">

                <div class="flex items-center justify-between">

                    <div class="w-16 h-16 rounded-2xl
                                                                bg-indigo-50
                                                                flex items-center justify-center">

                        <i data-lucide="shopping-cart" class="w-8 h-8 text-indigo-600">
                        </i>

                    </div>

                    <span class="bg-green-100
                                                                 text-green-600
                                                                 text-sm
                                                                 px-3 py-1
                                                                 rounded-full
                                                                 font-bold">

                        +5.2%

                    </span>

                </div>

                <p class="text-gray-400 text-sm mt-6">
                    Tổng đơn hàng
                </p>

                <h2 class="text-4xl font-black text-indigo-600 mt-3">

                    {{ $totalOrders }}

                </h2>

            </div>

            {{-- SOLD --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border">

                <div class="flex items-center justify-between">

                    <div class="w-16 h-16 rounded-2xl
                                                                bg-orange-50
                                                                flex items-center justify-center">

                        <i data-lucide="package-check" class="w-8 h-8 text-orange-500">
                        </i>

                    </div>

                    <span class="bg-red-100
                                                                 text-red-500
                                                                 text-sm
                                                                 px-3 py-1
                                                                 rounded-full
                                                                 font-bold">

                        -2.1%

                    </span>

                </div>

                <p class="text-gray-400 text-sm mt-6">
                    Đã bán
                </p>

                <h2 class="text-4xl font-black text-orange-500 mt-3">

                    {{ $totalItemsSold }}

                </h2>

            </div>

            {{-- AVG --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border">

                <div class="flex items-center justify-between">

                    <div class="w-16 h-16 rounded-2xl
                                                                bg-green-50
                                                                flex items-center justify-center">

                        <i data-lucide="chart-column" class="w-8 h-8 text-green-600">
                        </i>

                    </div>

                    <span class="bg-green-100
                                                                 text-green-600
                                                                 text-sm
                                                                 px-3 py-1
                                                                 rounded-full
                                                                 font-bold">

                        +3.8%

                    </span>

                </div>

                <p class="text-gray-400 text-sm mt-6">
                    Giá trị TB đơn
                </p>

                <h2 class="text-4xl font-black text-green-600 mt-3">

                    {{ number_format($avgOrderValue) }}đ

                </h2>

            </div>

        </div>

        {{-- CHART + KPI --}}
        <div class="grid lg:grid-cols-4 gap-6">

            {{-- CHART --}}
            <div class="lg:col-span-3 bg-white rounded-3xl p-8 shadow-sm border">

                <div class="flex justify-between items-center mb-8">

                    <div>

                        <h2 class="text-3xl font-black text-[#001e40]">
                            Biểu đồ doanh thu
                        </h2>

                        <p class="text-gray-400 mt-2">
                            Dữ liệu doanh thu theo ngày
                        </p>

                    </div>

                    <div class="flex items-center gap-3 bg-gray-100 p-2 rounded-2xl">

                        {{-- BUTTON LINE --}}
                        <button id="lineBtn" type="button" onclick="changeChart('line')" class="chart-btn-active
                                   flex items-center gap-2
                                   px-5 py-3
                                   rounded-xl
                                   font-bold
                                   transition-all duration-300">

                            <i data-lucide="chart-spline" class="w-5 h-5">
                            </i>

                            Biểu đồ đường

                        </button>

                        {{-- BUTTON BAR --}}
                        <button id="barBtn" type="button" onclick="changeChart('bar')" class="chart-btn
                                   flex items-center gap-2
                                   px-5 py-3
                                   rounded-xl
                                   font-bold
                                   transition-all duration-300">

                            <i data-lucide="chart-column" class="w-5 h-5">
                            </i>

                            Biểu đồ cột

                        </button>

                    </div>

                </div>

                <div class="h-[520px] w-full">

                    <canvas id="revenueChart"></canvas>

                </div>

            </div>

            {{-- KPI --}}
            <div class="bg-[#001e40]
                                                        rounded-3xl
                                                        p-8
                                                        text-white
                                                        shadow-2xl">

                <div class="space-y-8">

                    <div>

                        <div class="bg-blue-500/20
                                                                    inline-flex
                                                                    items-center gap-2
                                                                    px-4 py-2
                                                                    rounded-full">

                            <span class="w-2 h-2 rounded-full bg-blue-300"></span>

                            <span class="text-sm font-bold">
                                CHỈ TIÊU THÁNG 10
                            </span>

                        </div>

                        <h2 class="text-6xl font-black mt-8">
                            82%
                        </h2>

                        <p class="text-blue-100 mt-5 leading-8">

                            Doanh thu đang tăng trưởng ổn định
                            và gần đạt KPI tháng.

                        </p>

                    </div>

                    <div>

                        <div class="flex justify-between mb-3">

                            <span>
                                Tiến độ
                            </span>

                            <span class="font-black">
                                8.2 tỷ / 10 tỷ
                            </span>

                        </div>

                        <div class="w-full bg-blue-900 rounded-full h-5">

                            <div class="bg-blue-400 h-5 rounded-full" style="width:82%"></div>

                        </div>

                    </div>

                    <button class="w-full py-4 rounded-2xl
                                                               bg-white/10
                                                               hover:bg-white/20
                                                               transition
                                                               font-black">

                        XEM KPI

                    </button>

                </div>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border">

            <div class="flex justify-between items-center mb-8">

                <div>

                    <h2 class="text-3xl font-black text-[#001e40]">
                        Lịch sử báo cáo
                    </h2>

                    <p class="text-gray-400 mt-2">
                        Dữ liệu doanh thu hệ thống
                    </p>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr class="border-b bg-gray-50">

                            <th class="p-5 text-left">Ngày</th>
                            <th class="p-5 text-left">Doanh thu</th>
                            <th class="p-5 text-left">Đơn hàng</th>
                            <th class="p-5 text-left">Đã bán</th>
                            <th class="p-5 text-left">TB đơn</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($reports as $report)

                            <tr class="border-b hover:bg-gray-50 transition">

                                <td class="p-5 font-semibold">
                                    {{ $report->report_date }}
                                </td>

                                <td class="p-5 font-black text-green-600">
                                    {{ number_format($report->total_revenue) }}đ
                                </td>

                                <td class="p-5">
                                    {{ $report->total_orders }}
                                </td>

                                <td class="p-5">
                                    {{ $report->total_items_sold }}
                                </td>

                                <td class="p-5 font-semibold">
                                    {{ number_format($report->avg_order_value) }}đ
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="mt-10 flex justify-center">

                <div class="flex items-center gap-2">

                    {{-- PREVIOUS --}}
                    @if ($reports->onFirstPage())

                        <span class="w-11 h-11
                                       rounded-xl
                                       flex items-center justify-center
                                       text-gray-300 bg-gray-100">

                            <i data-lucide="chevron-left" class="w-5 h-5">
                            </i>

                        </span>

                    @else

                        <a href="{{ $reports->previousPageUrl() }}" class="w-11 h-11
                                       rounded-xl
                                       flex items-center justify-center
                                       text-gray-500
                                       hover:bg-[#001e40]
                                       hover:text-white
                                       transition">

                            <i data-lucide="chevron-left" class="w-5 h-5">
                            </i>

                        </a>

                    @endif

                    {{-- PAGE NUMBERS --}}
                    @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)

                        @if ($page == $reports->currentPage())

                            <span class="w-11 h-11
                                                   rounded-xl
                                                   flex items-center justify-center
                                                   bg-[#001e40]
                                                   text-white
                                                   font-black
                                                   shadow-lg">

                                {{ $page }}

                            </span>

                        @else

                            <a href="{{ $url }}" class="w-11 h-11
                                                   rounded-xl
                                                   flex items-center justify-center
                                                   text-gray-500
                                                   font-bold
                                                   hover:bg-gray-100
                                                   transition">

                                {{ $page }}

                            </a>

                        @endif

                    @endforeach

                    {{-- NEXT --}}
                    @if ($reports->hasMorePages())

                        <a href="{{ $reports->nextPageUrl() }}" class="w-11 h-11
                                       rounded-xl
                                       flex items-center justify-center
                                       text-gray-500
                                       hover:bg-[#001e40]
                                       hover:text-white
                                       transition">

                            <i data-lucide="chevron-right" class="w-5 h-5">
                            </i>

                        </a>

                    @else

                        <span class="w-11 h-11
                                       rounded-xl
                                       flex items-center justify-center
                                       text-gray-300 bg-gray-100">

                            <i data-lucide="chevron-right" class="w-5 h-5">
                            </i>

                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        const labels =
            @json($chartData->pluck('date'));

        const revenues =
            @json($chartData->pluck('revenue'));

        const expectedRevenues =
            revenues.map(
                item => item + Math.floor(Math.random() * 30000000)
            );

        /*
        |--------------------------------------------------------------------------
        | DATE VALIDATE
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'filterForm'
            );

        form.addEventListener(
            'submit',
            function (e) {

                const fromValue =
                    document.querySelector(
                        'input[name="from"]'
                    ).value;

                const toValue =
                    document.querySelector(
                        'input[name="to"]'
                    ).value;

                const from =
                    new Date(
                        fromValue + 'T00:00:00'
                    );

                const to =
                    new Date(
                        toValue + 'T00:00:00'
                    );

                const today =
                    new Date();

                from.setHours(0, 0, 0, 0);
                to.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);

                if (from.getTime() >= to.getTime()) {

                    e.preventDefault();

                    alert(
                        'Ngày bắt đầu phải nhỏ hơn ngày kết thúc'
                    );

                    return;
                }

                if (from.getTime() > today.getTime()) {

                    e.preventDefault();

                    alert(
                        'Ngày bắt đầu không được lớn hơn hôm nay'
                    );

                    return;
                }

                if (to.getTime() > today.getTime()) {

                    e.preventDefault();

                    alert(
                        'Ngày kết thúc không được lớn hơn hôm nay'
                    );

                    return;
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | CHART
        |--------------------------------------------------------------------------
        */

        const ctx =
            document
                .getElementById('revenueChart')
                .getContext('2d');

        const gradient =
            ctx.createLinearGradient(
                0,
                0,
                0,
                500
            );

        gradient.addColorStop(
            0,
            'rgba(37,99,235,0.4)'
        );

        gradient.addColorStop(
            1,
            'rgba(37,99,235,0.02)'
        );

        let revenueChart;

        function renderChart(type = 'line') {

            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {

                type: type,

                data: {

                    labels: labels,

                    datasets: [

                        {
                            label: 'Doanh thu',

                            data: revenues,

                            borderColor: '#003b95',

                            backgroundColor:
                                type === 'line'
                                    ? gradient
                                    : '#2563eb',

                            fill:
                                type === 'line',

                            tension: 0.4,

                            borderWidth: 4,

                            pointRadius: 5,

                            pointHoverRadius: 8,

                            pointBackgroundColor:
                                '#003366',

                            pointBorderWidth: 3,

                            pointBorderColor:
                                '#fff',

                            borderRadius: 12,
                        },

                        {
                            label: 'Dự kiến',

                            data: expectedRevenues,

                            borderColor: '#93c5fd',

                            borderDash: [8, 8],

                            borderWidth: 3,

                            fill: false,

                            tension: 0.4,

                            pointRadius: 0,
                        }
                    ]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    interaction: {

                        intersect: false,

                        mode: 'index'
                    },

                    plugins: {

                        legend: {

                            labels: {

                                color: '#001e40',

                                font: {

                                    size: 14,

                                    weight: 'bold'
                                }
                            }
                        },

                        tooltip: {

                            backgroundColor:
                                '#001e40',

                            padding: 14,

                            cornerRadius: 14,

                            titleColor: '#fff',

                            bodyColor: '#fff',

                            callbacks: {

                                label: function (context) {

                                    return context.dataset.label
                                        + ': '
                                        + Number(
                                            context.raw
                                        ).toLocaleString('vi-VN')
                                        + 'đ';
                                }
                            }
                        }
                    },

                    scales: {

                        y: {

                            beginAtZero: true,

                            grid: {

                                color:
                                    'rgba(0,0,0,0.05)'
                            },

                            ticks: {

                                color: '#555',

                                callback: function (value) {

                                    return value
                                        .toLocaleString('vi-VN')
                                        + 'đ';
                                }
                            }
                        },

                        x: {

                            grid: {
                                display: false
                            },

                            ticks: {
                                color: '#555'
                            }
                        }
                    }
                }
            });
        }

        function changeChart(type) {

            const lineBtn =
                document.getElementById('lineBtn');

            const barBtn =
                document.getElementById('barBtn');

            /*
            |--------------------------------------------------------------------------
            | RESET BUTTON
            |--------------------------------------------------------------------------
            */

            lineBtn.classList.remove(
                'chart-btn-active'
            );

            barBtn.classList.remove(
                'chart-btn-active'
            );

            lineBtn.classList.add(
                'chart-btn'
            );

            barBtn.classList.add(
                'chart-btn'
            );

            /*
            |--------------------------------------------------------------------------
            | ACTIVE BUTTON
            |--------------------------------------------------------------------------
            */

            if (type === 'line') {

                lineBtn.classList.remove(
                    'chart-btn'
                );

                lineBtn.classList.add(
                    'chart-btn-active'
                );

            } else {

                barBtn.classList.remove(
                    'chart-btn'
                );

                barBtn.classList.add(
                    'chart-btn-active'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RENDER CHART
            |--------------------------------------------------------------------------
            */

            renderChart(type);

            /*
            |--------------------------------------------------------------------------
            | RELOAD ICON
            |--------------------------------------------------------------------------
            */

            lucide.createIcons();
        }
        renderChart('line');
        lucide.createIcons();
    </script>

@endsection