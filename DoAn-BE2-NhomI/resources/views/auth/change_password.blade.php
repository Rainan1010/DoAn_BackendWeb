<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đổi mật khẩu - B-Tris</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-fixed": "#ffdbca",
                        "error-container": "#ffdad6",
                        "on-tertiary-fixed": "#341100",
                        "surface-container-high": "#e6e8ea",
                        "primary-container": "#003366",
                        "on-secondary": "#ffffff",
                        "surface": "#f7f9fb",
                        "surface-container-highest": "#e0e3e5",
                        "surface-container-low": "#f2f4f6",
                        "on-primary-fixed": "#001b3c",
                        "inverse-primary": "#a7c8ff",
                        "secondary-fixed-dim": "#bac7e1",
                        "on-tertiary-container": "#d8885c",
                        "on-primary-fixed-variant": "#1f477b",
                        "on-secondary-fixed-variant": "#3b475d",
                        "surface-dim": "#d8dadc",
                        "surface-bright": "#f7f9fb",
                        "surface-container": "#eceef0",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#799dd6",
                        "on-tertiary": "#ffffff",
                        "on-surface": "#191c1e",
                        "secondary-fixed": "#d6e3fe",
                        "inverse-on-surface": "#eff1f3",
                        "outline": "#737780",
                        "surface-variant": "#e0e3e5",
                        "primary-fixed": "#d5e3ff",
                        "on-secondary-container": "#58657c",
                        "outline-variant": "#c3c6d1",
                        "surface-tint": "#3a5f94",
                        "tertiary-container": "#592300",
                        "secondary-container": "#d6e3fe",
                        "background": "#f7f9fb",
                        "on-primary": "#ffffff",
                        "tertiary-fixed-dim": "#ffb690",
                        "primary": "#001e40",
                        "on-error": "#ffffff",
                        "on-secondary-fixed": "#0f1c2f",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#43474f",
                        "surface-container-lowest": "#ffffff",
                        "inverse-surface": "#2d3133",
                        "primary-fixed-dim": "#a7c8ff",
                        "on-tertiary-fixed-variant": "#723610",
                        "secondary": "#525f75",
                        "on-background": "#191c1e",
                        "tertiary": "#381300"
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
            },
        }
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "visibility_off";
            } else {
                input.type = "password";
                icon.textContent = "visibility";
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #001e40;
        }

        .main-gradient-bg {
            background: radial-gradient(circle at 70% 30%, #003366 0%, #001e40 100%);
            position: relative;
            overflow: hidden;
        }

        .grid-overlay {
            background-image: radial-gradient(rgba(167, 200, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .tech-glow-1 {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(167, 200, 255, 0.1) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            pointer-events: none;
        }

        .tech-glow-2 {
            position: absolute;
            width: 1000px;
            height: 1000px;
            background: radial-gradient(circle, rgba(58, 95, 148, 0.08) 0%, transparent 70%);
            bottom: -200px;
            right: -200px;
            pointer-events: none;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.6);
        }

        .vibrant-btn {
            background: linear-gradient(90deg, #003366 0%, #001e40 100%);
            box-shadow: 0 10px 20px -5px rgba(0, 51, 102, 0.5);
        }

        .vibrant-btn:hover {
            background: linear-gradient(90deg, #004080 0%, #002a5a 100%);
            transform: translateY(-2px);
        }

        .image-cover {
            background: linear-gradient(rgba(0, 30, 64, 0.8), rgba(0, 30, 64, 0.8)), url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=1000');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="main-gradient-bg text-on-surface antialiased min-h-screen">
    <!-- Background Decor -->
    <div class="fixed inset-0 grid-overlay opacity-50 z-0"></div>
    <div class="tech-glow-1 z-0"></div>
    <div class="tech-glow-2 z-0"></div>
    <main class="min-h-screen flex relative z-10">
        <!-- Split Layout: Desktop Only Image Side -->
        <div class="hidden lg:flex w-1/2 image-cover items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/40 to-transparent"></div>
            <div class="relative z-10 max-w-lg space-y-8">
                <img alt="B-Tris Logo" class="w-full max-w-sm drop-shadow-[0_0_30px_rgba(167,200,255,0.5)]"
                    src="https://lh3.googleusercontent.com/aida/ADBb0ujkfzLNdx7XZSZitQlk5uvj58AaPKD3Q4a8s-N0jif1cx4oHslaKAX8G2ZSnAHlcRzadbQdewYZKqoFk1mOb5nMlQ2IWE1LEkOPhgpQ_f3OAsi4xeTMJ3iOTa-_8eU52P20jiTjhhO_DVQY61OFzUJM8oDLw2QCxhc4jgJbee-3YfHibnbR1pzW15EedKEEkwJ2jT6xWslOUKe8XEFuUs5-rwpt-cQ8hs_cqBxpbSAhnRVFQyjHx3mj4QEwzI1P6AkPg2IpZ6OgwCA" />
                <div class="space-y-4">
                    <h2 class="text-5xl font-black text-white tracking-tighter uppercase">Security Protocol</h2>
                    <p class="text-primary-fixed-dim text-lg leading-relaxed font-medium">Bảo vệ tài khoản và dữ liệu của bạn trên hệ sinh thái B-Tris.</p>
                </div>
                <div class="flex gap-4">
                    <span class="material-symbols-outlined text-white/20 text-6xl"
                        data-icon="shield_person">shield_person</span>
                    <span class="material-symbols-outlined text-white/20 text-6xl"
                        data-icon="lock_reset">lock_reset</span>
                    <span class="material-symbols-outlined text-white/20 text-6xl"
                        data-icon="verified_user">verified_user</span>
                </div>
            </div>
            <!-- Decorative Large Icons -->
            <div class="absolute -bottom-10 -right-10 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[300px] font-thin text-white"
                    data-icon="enhanced_encryption">enhanced_encryption</span>
            </div>
        </div>
        <!-- Login Section -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-[500px] space-y-10">
                <!-- Brand Mobile Header -->
                <div class="lg:hidden flex flex-col items-center text-center space-y-4">
                    <img alt="B-Tris Logo" class="h-20 w-auto drop-shadow-lg"
                        src="https://lh3.googleusercontent.com/aida/ADBb0ujkfzLNdx7XZSZitQlk5uvj58AaPKD3Q4a8s-N0jif1cx4oHslaKAX8G2ZSnAHlcRzadbQdewYZKqoFk1mOb5nMlQ2IWE1LEkOPhgpQ_f3OAsi4xeTMJ3iOTa-_8eU52P20jiTjhhO_DVQY61OFzUJM8oDLw2QCxhc4jgJbee-3YfHibnbR1pzW15EedKEEkwJ2jT6xWslOUKe8XEFuUs5-rwpt-cQ8hs_cqBxpbSAhnRVFQyjHx3mj4QEwzI1P6AkPg2IpZ6OgwCA" />
                    <div class="space-y-1">
                        <h1 class="text-3xl font-black tracking-tighter text-white uppercase">B-Tris</h1>
                        <p class="text-primary-fixed-dim text-xs font-bold tracking-widest uppercase">Hệ thống Quản trị
                        </p>
                    </div>
                </div>
                <!-- Form Card -->
                <div class="glass-panel p-8 md:p-12 rounded-[2rem]">
                    <div class="mb-10 hidden lg:block">
                        <h3 class="text-2xl font-black text-primary uppercase tracking-tight">Đổi mật khẩu</h3>
                        <p class="text-sm text-on-surface-variant font-medium mt-1">Cập nhật mật khẩu để bảo vệ tài khoản của bạn</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline font-bold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li class="font-bold text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/password/change') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Current Password -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-primary"
                                for="current_password">Mật khẩu hiện tại</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-0 pointer-events-none text-outline-variant group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl" data-icon="lock_open">lock_open</span>
                                </div>
                                <input
                                    class="w-full bg-transparent border-b-2 border-outline-variant/30 focus:border-primary focus:ring-0 transition-all pl-8 py-3 text-on-surface placeholder:text-outline-variant/50 outline-none font-medium"
                                    name="current_password" id="current_password" placeholder="Nhập mật khẩu hiện tại" type="password" required />
                                <span
                                    onclick="togglePassword('current_password', 'eyeIconCurrent')"
                                    id="eyeIconCurrent"
                                    class="material-symbols-outlined absolute right-0 top-3 text-outline cursor-pointer hover:text-primary transition-colors text-xl">
                                    visibility
                                </span>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-primary"
                                for="new_password">Mật khẩu mới</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-0 pointer-events-none text-outline-variant group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl" data-icon="lock">lock</span>
                                </div>
                                <input
                                    class="w-full bg-transparent border-b-2 border-outline-variant/30 focus:border-primary focus:ring-0 transition-all pl-8 py-3 text-on-surface placeholder:text-outline-variant/50 outline-none font-medium"
                                    name="new_password" id="new_password" placeholder="Nhập mật khẩu mới" type="password" required />
                                <span
                                    onclick="togglePassword('new_password', 'eyeIconNew')"
                                    id="eyeIconNew"
                                    class="material-symbols-outlined absolute right-0 top-3 text-outline cursor-pointer hover:text-primary transition-colors text-xl">
                                    visibility
                                </span>
                            </div>
                        </div>

                        <!-- New Password Confirm -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-primary"
                                for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-0 pointer-events-none text-outline-variant group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl" data-icon="lock_clock">lock_clock</span>
                                </div>
                                <input
                                    class="w-full bg-transparent border-b-2 border-outline-variant/30 focus:border-primary focus:ring-0 transition-all pl-8 py-3 text-on-surface placeholder:text-outline-variant/50 outline-none font-medium"
                                    name="new_password_confirmation" id="new_password_confirmation" placeholder="Nhập lại mật khẩu mới" type="password" required />
                                <span
                                    onclick="togglePassword('new_password_confirmation', 'eyeIconConfirm')"
                                    id="eyeIconConfirm"
                                    class="material-symbols-outlined absolute right-0 top-3 text-outline cursor-pointer hover:text-primary transition-colors text-xl">
                                    visibility
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            class="vibrant-btn w-full text-white py-5 rounded-2xl font-black uppercase tracking-[0.2em] text-sm transition-all duration-300 active:scale-[0.98] mt-4"
                            type="submit">
                            Xác nhận đổi mật khẩu
                        </button>
                    </form>
                    
                    <!-- Form Footer -->
                    <div class="mt-8 pt-8 border-t border-surface-container flex flex-col items-center space-y-4">
                        <a href="{{ url('/') }}" class="text-sm text-primary font-bold hover:underline">Về trang chủ</a>
                    </div>
                </div>
                <!-- Page Footer -->
                <footer class="text-center space-y-4">
                    <div class="flex justify-center gap-8">
                        <a class="text-[10px] font-bold uppercase tracking-widest text-primary-fixed/40 hover:text-primary-fixed transition-colors"
                            href="#">Chính sách bảo mật</a>
                        <a class="text-[10px] font-bold uppercase tracking-widest text-primary-fixed/40 hover:text-primary-fixed transition-colors"
                            href="#">Điều khoản dịch vụ</a>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary-fixed/30 leading-relaxed">
                        © 2024 B-Tris Precision Engineering Ecosystem.<br class="md:hidden" /> Bản quyền được bảo lưu.
                    </p>
                </footer>
            </div>
        </div>
    </main>
    <!-- Top Accent Bar -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary via-inverse-primary to-surface-tint z-50">
    </div>
    <!-- Floating Tech Symbols -->
    <div class="fixed top-12 right-12 hidden lg:block opacity-5 pointer-events-none">
        <span class="material-symbols-outlined text-[200px] font-thin text-white"
            data-icon="admin_panel_settings">admin_panel_settings</span>
    </div>
</body>
</html>
