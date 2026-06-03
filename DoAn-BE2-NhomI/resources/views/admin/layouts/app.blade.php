<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="price-sync-url" content="{{ url('/api/prices/sync') }}" />
    <title>Admin - B-Tris</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F4F5F7;
        }

        .sidebar-scroll {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge cũ */
        }

        .sidebar-scroll::-webkit-scrollbar {
            display: none;
            /* Chrome, Edge, Safari */
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden text-gray-800">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A2540] hidden md:flex flex-col h-full">
        <!-- Logo -->
        <div class="h-20 flex items-center justify-center border-b border-white/10 px-6">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <span class="text-[#0A2540] font-black text-lg">B</span>
                </div>
                <div>
                    <p class="text-white font-black text-base leading-tight">B-Tris</p>
                    <p class="text-blue-300 text-[10px] font-medium uppercase tracking-wider">Hệ thống quản trị</p>
                </div>
            </div>
        </div>



        <nav class="sidebar-scroll flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Bảng điều khiển
            </a>

            <a href="{{ route('admin.products.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.products.*') ? 'bg-[#002B6B] text-white shadow-inner' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="box" class="w-5 h-5"></i> Sản phẩm
            </a>
            <a href="{{ route('admin.attributes.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.attributes.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="cpu" class="w-5 h-5"></i> Thuộc tính
            </a>

            <a href="{{ route('admin.brands.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.brands.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="tag" class="w-5 h-5"></i> Thương hiệu
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="boxes" class="w-5 h-5"></i> Danh mục
            </a>

            <a href="{{ route('admin.vouchers.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.vouchers.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="ticket" class="w-5 h-5"></i> Voucher
            </a>

            <a href="{{ route('admin.reviews.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.reviews.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="star" class="w-5 h-5"></i> Đánh giá
            </a>

            <a href="{{ route('admin.backups.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.backups.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="database-backup" class="w-5 h-5"></i> Sao lưu Dữ liệu
            </a>

            <a href="{{ route('admin.order-statistics.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.order-statistics.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                Thống kê đơn hàng
            </a>

            <a href="{{ route('admin.revenue_reports.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.revenue_reports.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="wallet" class="w-5 h-5"></i> Báo cáo doanh thu
            </a>

            <a href="{{ route('admin.permissions.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.permissions.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="shield-check" class="w-5 h-5"></i> Phân quyền
            </a>
            <a href="{{ route('admin.stock-logs.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.stock-logs.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="history" class="w-5 h-5"></i>
                Nhật ký kho hàng
            </a>
            <a href="{{ route('admin.login.history') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.stock-logs.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="history" class="w-5 h-5"></i>
               Lịch sử đăng nhập
            </a>
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.backups.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10' }} rounded-lg transition-colors">
                <i data-lucide="arrow-big-right" class="w-5 h-5"></i> Quay về Trang chủ
            </a>

        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 bg-white/20 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    A</div>
                <div>
                    <p class="text-sm font-bold text-white">Administrator</p>
                    <p class="text-xs text-blue-300">Quản trị viên</p>
                </div>
                <button onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" class="ml-auto text-blue-300 hover:text-white transition-colors" title="Đăng xuất">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
                <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0">
            <div class="flex-1 max-w-xl relative">
                @yield('header_search')
            </div>

        </header>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            @if(session('success'))
                <div
                    class="mb-4 p-4 bg-[#E2F6EA] text-[#0FAF62] rounded-lg font-medium text-sm flex items-center gap-2 shadow-sm border border-[#0FAF62]/20">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> {!! session('success') !!}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg font-medium text-sm flex items-center gap-2 shadow-sm border border-red-200">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i> {!! session('error') !!}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
    <script src="{{ asset('js/price-realtime.js') }}" defer></script>
    @stack('scripts')
</body>

</html>