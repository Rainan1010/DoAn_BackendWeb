<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - B-Tris</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F4F5F7; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col h-full">
        <div class="h-20 flex items-center justify-center border-b border-gray-100">
            <h2 class="text-2xl font-black text-[#0A2540]">B-Tris Admin</h2>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Bảng điều khiển
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-[#0A2540] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i data-lucide="boxes" class="w-5 h-5"></i> Danh mục
            </a>
         
            <a href="{{ route('admin.vouchers.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.vouchers.*') ? 'bg-[#0A2540] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition-colors">
                <i data-lucide="ticket" class="w-5 h-5"></i> Mã giảm giá
            </a>
        </nav>
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#0A2540] text-white rounded-full flex items-center justify-center font-bold">A</div>
                <div>
                    <p class="text-sm font-bold text-[#0A2540]">Administrator</p>
                    <p class="text-xs text-gray-500">Quản trị viên</p>
                </div>
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
            <div class="flex items-center gap-4 text-gray-500">
                <button><i data-lucide="bell" class="w-5 h-5"></i></button>
                <button><i data-lucide="settings" class="w-5 h-5"></i></button>
            </div>
        </header>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            @if(session('success'))
            <div class="mb-4 p-4 bg-[#E2F6EA] text-[#0FAF62] rounded-lg font-medium text-sm flex items-center gap-2 shadow-sm border border-[#0FAF62]/20">
                <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg font-medium text-sm flex items-center gap-2 shadow-sm border border-red-200">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
