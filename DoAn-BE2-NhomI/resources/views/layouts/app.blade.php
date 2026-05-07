<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>B-Tris Precision Tech | Thiết Bị Công Nghệ Cao Cấp</title>
    
    {{-- Scripts & Fonts --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    {{-- Lucide Icons (Dành cho trang Admin của Bảo) --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-low": "#f2f4f6",
                        "surface": "#f7f9fb",
                        "error": "#ba1a1a",
                        "brand-blue": "#003366"
                    },
                    "borderRadius": { "full": "0.75rem" },
                    "fontFamily": { "body": ["Inter"] }
                },
            }
        }
    </script>

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glass-nav { backdrop-filter: blur(20px); background-color: rgba(0, 51, 102, 0.9); }
        html { scroll-behavior: smooth; }
        /* Hiệu ứng ẩn hiện mượt cho Flash Message */
        .flash-message { transition: all 0.5s ease; }
    </style>
</head>

<body class="bg-surface text-on-surface font-body">

    {{-- NAVIGATION --}}
    <nav class="fixed top-0 w-full z-50 glass-nav shadow-lg tracking-tight">
        <div class="flex justify-between items-center px-6 h-20 max-w-full mx-auto">
            <div class="flex items-center gap-8">
                <a class="flex items-center" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo/logo.jpg') }}" alt="B-Tris Logo" class="h-12 w-auto object-contain" />
                </a>
                <div class="hidden md:flex gap-8">
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#dien-thoai">Điện thoại</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#laptop">Laptop</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#khuyen-mai">Khuyến mãi</a>
                </div>
            </div>

            {{-- SEARCH BOX --}}
            <div class="flex items-center gap-6 flex-1 max-w-xl mx-12">
                <div class="relative w-full" id="search-container">
                    <input id="search-input" class="w-full bg-white/20 border-none rounded-full py-2.5 px-6 text-sm text-white placeholder-slate-200 focus:ring-2 focus:ring-white/50 transition-all" placeholder="Tìm kiếm siêu phẩm..." type="text" />
                    <span class="material-symbols-outlined absolute right-4 top-2.5 text-white/90">search</span>
                    <div id="search-results" class="absolute w-full bg-white mt-2 rounded-xl shadow-2xl overflow-hidden hidden z-[100]"></div>
                </div>
            </div>

            <div class="flex items-center gap-6 text-white">
                {{-- GIỎ HÀNG (DYNAMC) --}}
                <a href="{{ route('cart.index') }}" class="hover:opacity-80 transition-all active:scale-95 relative group">
                    <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                    
                    @php
                        $totalQuantity = collect(session('cart', []))->sum('quantity');
                    @endphp

                    @if($totalQuantity > 0)
                        <span class="absolute -top-1 -right-2 bg-red-600 text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold border-2 border-[#003366]">
                            {{ $totalQuantity }}
                        </span>
                    @endif

                    {{-- Tooltip Header --}}
                    <div class="absolute top-full mt-3 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-white text-brand-blue text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300 shadow-2xl z-[100] border border-gray-100 uppercase">
                        Xem giỏ hàng
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-4 border-transparent border-b-white"></div>
                    </div>
                </a>

                {{-- USER AUTH --}}
                @auth
                    <div class="relative group">
                        <div onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer bg-white/10 px-3 py-1.5 rounded-full hover:bg-white/20 transition-all">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ asset(Auth::user()->avatar_url) }}" class="w-7 h-7 rounded-full object-cover border border-white/50">
                            @else
                                <span class="material-symbols-outlined text-2xl">account_circle</span>
                            @endif
                            <span class="font-bold text-sm">{{ Auth::user()->full_name }}</span>
                        </div>

                        <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white text-[#0A2540] rounded-xl shadow-2xl p-2 font-bold text-sm">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100 rounded-lg">Hồ sơ cá nhân</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 rounded-lg border-b border-gray-50 mb-1">Đơn mua của tôi</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 rounded-lg">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-slate-300 font-bold">
                        <span class="material-symbols-outlined">login</span> Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="pt-24 min-h-screen">
        
        {{-- FLASH MESSAGES --}}
        <div class="max-w-[1600px] mx-auto px-6">
            @if(session('success'))
                <div class="flash-message bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg shadow-sm mb-6 flex justify-between items-center" role="alert">
                    <p class="font-bold">{{ session('success') }}</p>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-message bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-6 flex justify-between items-center" role="alert">
                    <p class="font-bold">{{ session('error') }}</p>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">✕</button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-brand-blue text-white pt-16 pb-8 px-6 mt-20">
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="space-y-6">
                <h2 class="text-2xl font-black tracking-tighter">B-TRIS</h2>
                <p class="text-slate-300 text-sm leading-relaxed">Hệ sinh thái kỹ thuật chính xác B-Tris. Tinh hoa công nghệ trong tầm tay bạn.</p>
            </div>
            {{-- Thêm các cột khác của Bảo ở đây --}}
        </div>
        <div class="max-w-[1600px] mx-auto pt-8 border-t border-white/10 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2026 B-TRIS NHÓM I. DESIGNED FOR EXCELLENCE.</p>
        </div>
    </footer>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/search.js') }}"></script>
    <script>
        // Init Lucide
        lucide.createIcons();

        // Dropdown User
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown && !e.target.closest('.group') && !e.target.closest('#dropdownMenu')) {
                dropdown.classList.add('hidden');
            }
        });

        // Tự động ẩn Flash Message sau 5 giây
        setTimeout(() => {
            document.querySelectorAll('.flash-message').forEach(el => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>