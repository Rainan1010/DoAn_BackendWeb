@extends('admin.layouts.app')

@section('content')

    <style>
        .dashboard-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #edf0f3;
            transition: 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            border-color: rgba(0, 51, 102, 0.2);
        }

        .table-row {
            transition: 0.25s ease;
        }

        .table-row:hover {
            background: #f5f9ff;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
        }

        .animate-fade {
            animation: fade .5s ease;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <section class="space-y-8 animate-fade">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">

            <div>

                <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">

                    <span>Admin</span>

                    <i data-lucide="chevron-right" class="w-4 h-4"></i>

                    <span class="font-semibold text-[#003366]">
                        Lịch sử đăng nhập
                    </span>

                </div>

                <h1 class="text-4xl font-black text-[#003366] tracking-tight">
                    Quản lý Lịch sử đăng nhập
                </h1>

                <p class="text-gray-500 mt-2">
                    Giám sát hoạt động truy cập và bảo mật hệ thống.
                </p>

            </div>

            <div class="flex items-center gap-3">

                <form method="GET" class="flex items-center gap-3 flex-wrap">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div class="w-64">

                        <label class="block text-sm font-medium mb-2">
                            Ngày bắt đầu
                        </label>

                        <input type="date" id="from_date" name="from_date"
                            value="{{ request('from_date', now()->toDateString()) }}"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3">

                        <p id="fromDateError"
                            class="text-red-500 text-xs mt-1 break-words {{ $errors->has('from_date') ? '' : 'hidden' }}">

                            {{ $errors->first('from_date') }}

                        </p>
                    </div>
                    <div class="w-64">

                        <label class="block text-sm font-medium mb-2">
                            Ngày kết thúc
                        </label>

                        <input type="date" id="to_date" name="to_date"
                            value="{{ request('to_date', now()->toDateString()) }}"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3">

                        <p id="toDateError"
                            class="text-red-500 text-xs mt-1 break-words {{ $errors->has('to_date') ? '' : 'hidden' }}">

                            {{ $errors->first('to_date') }}

                        </p>

                    </div>
                    <button type="submit" class="bg-[#003366] text-white px-5 py-3 rounded-2xl">
                        Lọc
                    </button>

                </form>
            </div>

        </div>

        {{-- CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            {{-- CARD --}}
            <div class="dashboard-card p-6">

                <div class="flex items-center justify-between mb-5">

                    <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">

                        <i data-lucide="log-in" class="w-7 h-7 text-[#003366]"></i>

                    </div>

                    <span class="bg-blue-100 text-[#003366] text-xs font-bold px-3 py-1 rounded-full">
                        +12%
                    </span>

                </div>

                <p class="text-gray-400 uppercase tracking-widest text-xs font-bold">
                    Tổng truy cập
                </p>

                <h2 class="text-4xl font-black text-[#003366] mt-2">
                    {{ $logs->total() }}
                </h2>

            </div>

            {{-- CARD --}}
            <div class="dashboard-card p-6">

                <div class="flex items-center justify-between mb-5">

                    <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center">

                        <i data-lucide="check-circle" class="w-7 h-7 text-green-600"></i>

                    </div>

                </div>

                <p class="text-gray-400 uppercase tracking-widest text-xs font-bold">
                    Thành công
                </p>

                <h2 class="text-4xl font-black text-[#003366] mt-2">
                    {{ \App\Models\LoginHistory::where('status', 'success')->count() }}
                </h2>

            </div>

            {{-- CARD --}}
            <div class="dashboard-card p-6">

                <div class="flex items-center justify-between mb-5">

                    <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center">

                        <i data-lucide="globe" class="w-7 h-7 text-purple-600"></i>

                    </div>

                </div>

                <p class="text-gray-400 uppercase tracking-widest text-xs font-bold">
                    Địa chỉ IP
                </p>

                <h2 class="text-4xl font-black text-[#003366] mt-2">
                    {{ \App\Models\LoginHistory::distinct('ip_address')->count() }}
                </h2>

            </div>

            {{-- CARD --}}
            <div class="dashboard-card p-6">

                <div class="flex items-center justify-between mb-5">

                    <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center">

                        <i data-lucide="triangle-alert" class="w-7 h-7 text-red-600"></i>

                    </div>

                </div>

                <p class="text-gray-400 uppercase tracking-widest text-xs font-bold">
                    Thất bại
                </p>

                <h2 class="text-4xl font-black text-[#003366] mt-2">
                    {{ \App\Models\LoginHistory::where('status', 'failed')->count() }}
                </h2>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-[28px] overflow-hidden border border-gray-100 shadow-sm">

            {{-- TABLE HEADER --}}
            <div class="glass border-b border-gray-100 p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                <div class="flex items-center gap-4">

                    <h2 class="text-xl font-black text-[#003366]">
                        Danh sách nhật ký hệ thống
                    </h2>

                    <span
                        class="bg-blue-100 text-[#003366] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1">

                        <span class="w-2 h-2 rounded-full bg-[#003366] animate-pulse"></span>

                        Real-time

                    </span>

                </div>

                {{-- FILTER --}}
                <form method="GET">

                    <input type="hidden" name="from_date" value="{{ request('from_date', now()->toDateString()) }}">

                    <input type="hidden" name="to_date" value="{{ request('to_date', now()->toDateString()) }}">

                    <select name="status" onchange="this.form.submit()"
                        class="bg-gray-100 rounded-2xl border-none px-5 py-3 text-sm text-gray-500 font-semibold">

                        <option value="">
                            Tất cả trạng thái
                        </option>

                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>
                            Thành công
                        </option>

                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                            Thất bại
                        </option>

                    </select>

                </form>

            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr class="text-gray-400 uppercase text-[11px] tracking-[2px] font-black">

                            <th class="px-6 py-5 text-left">
                                ID
                            </th>

                            <th class="px-6 py-5 text-left">
                                Người dùng
                            </th>

                            <th class="px-6 py-5 text-left">
                                Thời gian
                            </th>

                            <th class="px-6 py-5 text-left">
                                Địa chỉ IP
                            </th>

                            <th class="px-6 py-5 text-left">
                                Trạng thái
                            </th>



                        </tr>

                    </thead>

                    <tbody>

                        @forelse($logs as $log)

                                        <tr class="table-row border-t border-gray-100">

                                            {{-- ID --}}
                                            <td class="px-6 py-5">

                                                <span class="text-xs font-black text-gray-500">
                                                    #LOG-{{ $log->history_id }}
                                                </span>

                                            </td>

                                            {{-- USER --}}
                                            <td class="px-6 py-5">

                                                <div class="flex items-center gap-3">

                                                    <div
                                                        class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center font-bold text-[#003366]">

                                                        {{ strtoupper(substr($log->email, 0, 2)) }}

                                                    </div>

                                                    <div>

                                                        <h4 class="font-bold text-[#003366]">

                                                            {{ $log->user->full_name ?? 'Unknown' }}

                                                        </h4>

                                                        <p class="text-xs text-gray-400 mt-1">

                                                            {{ $log->email }}

                                                        </p>

                                                    </div>

                                                </div>

                                            </td>

                                            {{-- TIME --}}
                                            <td class="px-6 py-5">

                                                <div class="space-y-1">

                                                    <p class="font-semibold text-sm">

                                                        <span class="uppercase text-[10px] text-gray-400 mr-1">
                                                            Vào:
                                                        </span>

                                                        {{ \Carbon\Carbon::parse($log->login_time)->format('H:i:s') }}

                                                    </p>

                                                    <p class="font-semibold text-sm text-gray-500">

                                                        <span class="uppercase text-[10px] text-gray-400 mr-1">
                                                            Ra:
                                                        </span>

                                                        {{ $log->logout_time
                            ? \Carbon\Carbon::parse($log->logout_time)->format('H:i:s')
                            : '--' }}

                                                    </p>

                                                    <p class="text-[11px] text-gray-400">

                                                        {{ \Carbon\Carbon::parse($log->login_time)->format('d/m/Y') }}

                                                    </p>

                                                </div>

                                            </td>

                                            {{-- IP --}}
                                            <td class="px-6 py-5">

                                                <span class="font-mono font-bold text-sm text-gray-600">

                                                    {{ $log->ip_address }}

                                                </span>

                                            </td>

                                            {{-- STATUS --}}
                                            <td class="px-6 py-5">

                                                @if($log->status == 'success')

                                                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">

                                                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>

                                                    </div>

                                                @else

                                                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">

                                                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>

                                                    </div>

                                                @endif

                                            </td>

                                            {{-- ACTION --}}


                                        </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-10 text-gray-400">

                                    Không có dữ liệu lịch sử đăng nhập.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="bg-gray-50 border-t border-gray-100 p-6">

                {{ $logs->links() }}

            </div>

        </div>

    </section>

    <script>
        lucide.createIcons();
        const fromDate =
            document.getElementById('from_date');

        const toDate =
            document.getElementById('to_date');

        const fromError =
            document.getElementById('fromDateError');

        const toError =
            document.getElementById('toDateError');

        const today =
            new Date().toISOString().split('T')[0];
        if (!fromDate.value) {
            fromDate.value = today;
        }

        if (!toDate.value) {
            toDate.value = today;
        }
        function validateDates() {

            fromError.classList.add('hidden');
            toError.classList.add('hidden');

            if (
                fromDate.value &&
                fromDate.value > today
            ) {

                fromError.textContent =
                    'Ngày bắt đầu không được lớn hơn ngày hiện tại';

                fromError.classList.remove('hidden');
            }

            if (
                toDate.value &&
                toDate.value > today
            ) {

                toError.textContent =
                    'Ngày kết thúc không được lớn hơn ngày hiện tại';

                toError.classList.remove('hidden');
            }

            if (
                fromDate.value &&
                toDate.value &&
                toDate.value < fromDate.value
            ) {

                toError.textContent =
                    'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu';

                toError.classList.remove('hidden');
            }

        }

        fromDate.addEventListener(
            'change',
            validateDates
        );

        toDate.addEventListener(
            'change',
            validateDates
        );
    </script>

@endsection