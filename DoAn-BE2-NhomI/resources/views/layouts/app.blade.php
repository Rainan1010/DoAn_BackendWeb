<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>B-Tris Precision Tech | Thiết Bị Công Nghệ Cao Cấp</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-low": "#f2f4f6",
                        "surface": "#f7f9fb",
                        "on-primary-fixed-variant": "#1f477b",
                        "on-secondary-fixed-variant": "#3b475d",
                        "on-secondary-container": "#58657c",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#d8885c",
                        "on-primary-fixed": "#001b3c",
                        "on-primary-container": "#799dd6",
                        "on-background": "#191c1e",
                        "error-container": "#ffdad6",
                        "on-secondary": "#ffffff",
                        "secondary-fixed": "#d6e3fe",
                        "tertiary-fixed-dim": "#ffb690",
                        "inverse-surface": "#2d3133",
                        "primary-container": "#003366",
                        "on-surface-variant": "#43474f",
                        "surface-variant": "#e0e3e5",
                        "secondary": "#525f75",
                        "outline-variant": "#c3c6d1",
                        "surface-container-high": "#e6e8ea",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#592300",
                        "surface-container-highest": "#e0e3e5",
                        "surface-bright": "#f7f9fb",
                        "tertiary-fixed": "#ffdbca",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-fixed": "#341100",
                        "tertiary": "#381300",
                        "primary-fixed": "#d5e3ff",
                        "background": "#f7f9fb",
                        "on-surface": "#191c1e",
                        "surface-dim": "#d8dadc",
                        "primary": "#001e40",
                        "surface-tint": "#3a5f94",
                        "secondary-container": "#d6e3fe",
                        "inverse-primary": "#a7c8ff",
                        "error": "#ba1a1a",
                        "primary-fixed-dim": "#a7c8ff",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed-variant": "#723610",
                        "on-primary": "#ffffff",
                        "outline": "#737780",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-fixed-dim": "#bac7e1",
                        "on-secondary-fixed": "#0f1c2f",
                        "brand-blue": "#003366"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "fontFamily": {
                        "headline": ["Inter"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }

        .precision-gradient {
            background: linear-gradient(135deg, #001e40 0%, #003366 100%);
        }

        .glass-nav {
            backdrop-filter: blur(20px);
            background-color: rgba(0, 51, 102, 0.9);
        }

        html {
            scroll-behavior: smooth;
        }

        .category-tab-active {
            border-bottom: 3px solid #003366;
            color: #003366;
        }
    </style>
</head>

<body class="bg-surface text-on-surface font-body selection:bg-primary-fixed selection:text-on-primary-fixed">

    <nav class="fixed top-0 w-full z-50 glass-nav shadow-lg font-['Inter'] tracking-tight">
        <div class="flex justify-between items-center px-6 h-20 max-w-full mx-auto">
            <div class="flex items-center gap-8">
                <a class="flex items-center" href="#">
                    <img src="{{ asset('images/logo/logo.jpg') }}" alt="B-Tris Logo"
                        class="h-12 w-auto object-contain" /> </a>
                <div class="hidden md:flex gap-8">
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#dien-thoai">Điện
                        thoại</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#laptop">Máy tính
                        xách tay</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#phu-kien">Phụ
                        kiện</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#khuyen-mai">Khuyến
                        mãi</a>
                    <a class="text-white hover:text-slate-300 transition-colors font-semibold" href="#dich-vu">Dịch
                        vụ</a>
                </div>
            </div>
            <div class="flex items-center gap-6 flex-1 max-w-xl mx-12">
                <div class="relative w-full" id="search-container">
                    <input id="search-input"
                        class="w-full bg-white/20 border-none rounded-full py-2.5 px-6 text-sm text-white placeholder-slate-200 focus:ring-2 focus:ring-white/50 transition-all"
                        placeholder="Tìm kiếm sản phẩm công nghệ..." type="text" autocomplete="off" />
                    <span class="material-symbols-outlined absolute right-4 top-2.5 text-white/90">search</span>

                    <div id="search-results"
                        class="absolute w-full bg-white mt-2 rounded-xl shadow-2xl overflow-hidden hidden z-[100] text-on-surface">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-6 text-white">
                <button class="hover:opacity-80 transition-opacity active:scale-95 duration-150 relative">
                    <span class="material-symbols-outlined text-3xl" data-icon="shopping_cart">shopping_cart</span>
                    <span
                        class="absolute -top-1 -right-2 bg-error text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold border-2 border-[#003366]">3</span>
                </button>
                @if(Auth::check())

                    <div class="relative">

                        {{-- ===== CLICK USER ===== --}}
                        <div onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer">

                            {{-- AVATAR --}}
                            @if(Auth::user()->avatar_url)
                                <img src="{{ asset(str_replace('public/', '', Auth::user()->avatar_url)) }}"
                                    class="w-8 h-8 rounded-full object-cover">
                            @else
                                {{-- ICON MẶC ĐỊNH --}}
                                <span class="material-symbols-outlined text-3xl">
                                    account_circle
                                </span>
                            @endif

                            {{-- TÊN USER --}}
                            <span class="font-semibold">
                                {{ Auth::user()->full_name }}
                            </span>
                        </div>

                        {{-- ===== DROPDOWN ===== --}}
                        <div id="dropdownMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white text-black rounded-xl shadow-lg p-2">

                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 rounded">
                                Tài khoản
                            </a>

                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 rounded">
                                Đơn mua
                            </a>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-100 rounded">
                                    Đăng xuất
                                </button>
                            </form>

                        </div>

                    </div>

                @else

                    {{-- ===== CHƯA LOGIN ===== --}}
                    <a href="{{ url('/login') }}">
                        <span class="material-symbols-outlined">account_circle</span> Đăng nhập
                    </a>

                @endif
            </div>
        </div>
    </nav>

    <main class="pt-24 space-y-12 pb-12">
        @yield('content')
    </main>

    <footer class="bg-[#003366] text-white pt-16 pb-8 px-6 font-['Inter']">
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="space-y-6">
                <h2 class="text-2xl font-black tracking-tighter">B-TRIS</h2>
                <p class="text-slate-300 text-sm leading-relaxed max-w-xs">
                    Nền tảng thương mại điện tử công nghệ hàng đầu, mang đến những thiết bị tinh hoa nhất thế giới cho
                    người dùng Việt.
                </p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"
                        href="#"><span class="material-symbols-outlined text-xl">public</span></a>
                    <a class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"
                        href="#"><span class="material-symbols-outlined text-xl">mail</span></a>
                    <a class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"
                        href="#"><span class="material-symbols-outlined text-xl">rss_feed</span></a>
                </div>
            </div>
            <div class="space-y-6">
                <h3 class="text-sm font-black uppercase tracking-widest">MUA SẮM</h3>
                <ul class="space-y-4">
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">SẢN PHẨM MỚI</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">KHUYẾN MÃI</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">BÁN CHẠY NHẤT</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">HỆ THỐNG CỬA HÀNG</a></li>
                </ul>
            </div>
            <div class="space-y-6">
                <h3 class="text-sm font-black uppercase tracking-widest">HỖ TRỢ</h3>
                <ul class="space-y-4">
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">VỀ CHÚNG TÔI</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">CHÍNH SÁCH BẢO MẬT</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">ĐIỀU KHOẢN DỊCH VỤ</a></li>
                    <li><a class="text-slate-300 hover:text-white transition-colors text-sm uppercase font-bold"
                            href="#">LIÊN HỆ</a></li>
                </ul>
            </div>
            <div class="space-y-6">
                <h3 class="text-sm font-black uppercase tracking-widest">BẢN TIN</h3>
                <p class="text-slate-300 text-sm leading-relaxed">Đăng ký để nhận tin tức công nghệ mới nhất và ưu đãi
                    độc quyền từ B-Tris.</p>
                <div class="relative border-b border-white/30 pb-2 flex items-center">
                    <input
                        class="bg-transparent border-none p-0 w-full text-white placeholder-slate-400 focus:ring-0 text-sm"
                        placeholder="Email của bạn" type="email" />
                    <button class="text-white hover:translate-x-1 transition-transform"><span
                            class="material-symbols-outlined">arrow_forward</span></button>
                </div>
            </div>
        </div>
        <div
            class="max-w-[1600px] mx-auto pt-8 border-t border-white/10 flex flex-col md:row justify-between items-center gap-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2024 B-TRIS. BẢN QUYỀN ĐÃ ĐƯỢC
                BẢO HỘ.</p>
            <div class="flex gap-8 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <span>DESIGNED FOR EXCELLENCE</span>
                <span>POWERED BY PRECISE TECH</span>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/search.js') }}"></script>
    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }

        // click ngoài → đóng
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('dropdownMenu');
            if (!e.target.closest('.relative')) {
                dropdown?.classList.add('hidden');
            }
        });
    </script>
</body>

</html>