<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng ký - B-Tris</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-container": "#003366",
                        "on-surface": "#191c1e",
                        "outline": "#737780",
                        "outline-variant": "#c3c6d1",
                        "surface-tint": "#3a5f94",
                        "background": "#001e40",
                        "primary": "#001e40",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#43474f",
                        "surface-container-low": "#f2f4f6",
                    },
                    "fontFamily": {
                        "body": ["Inter"],
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
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .main-gradient-bg {
            background: linear-gradient(135deg, #001e40 0%, #003366 100%);
        }

        .glass-panel {
            background: #ffffff;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-field {
            background-color: #f2f4f6;
            border: none;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #191c1e;
            width: 100%;
            outline: none;
            transition: all 0.2s;
        }

        .input-field:focus {
            box-shadow: 0 0 0 2px #001e40;
        }

        .input-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #001e40;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background-color: #002a5a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem;
            border-radius: 0.75rem;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 10px 20px -5px rgba(0, 42, 90, 0.5);
        }

        .btn-primary:hover {
            background-color: #001e40;
            transform: translateY(-2px);
        }

        .btn-social {
            background-color: #f2f4f6;
            color: #191c1e;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.75rem;
            border-radius: 0.75rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .btn-social:hover {
            background-color: #e0e3e5;
        }
    </style>
</head>

<body class="main-gradient-bg text-white antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        
        <!-- Left Side Content -->
        <div class="hidden lg:flex w-1/2 flex-col space-y-8 relative">
            <!-- Decorative icon background -->
            <div class="absolute -top-10 left-1/2 opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-[150px]">memory</span>
            </div>
            
            <div class="bg-primary-container p-6 rounded-xl w-64 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-blue-500/20 blur-2xl"></div>
                <img alt="B-Tris Logo" class="w-full relative z-10 drop-shadow-md"
                    src="https://lh3.googleusercontent.com/aida/ADBb0ujkfzLNdx7XZSZitQlk5uvj58AaPKD3Q4a8s-N0jif1cx4oHslaKAX8G2ZSnAHlcRzadbQdewYZKqoFk1mOb5nMlQ2IWE1LEkOPhgpQ_f3OAsi4xeTMJ3iOTa-_8eU52P20jiTjhhO_DVQY61OFzUJM8oDLw2QCxhc4jgJbee-3YfHibnbR1pzW15EedKEEkwJ2jT6xWslOUKe8XEFuUs5-rwpt-cQ8hs_cqBxpbSAhnRVFQyjHx3mj4QEwzI1P6AkPg2IpZ6OgwCA" />
            </div>

            <div class="space-y-6 mt-8">
                <h1 class="text-5xl font-black leading-tight tracking-tight">
                    CÔNG NGHỆ & <br/> ĐIỆN TỬ TRỰC <br/> TUYẾN
                </h1>
                <p class="text-blue-200 text-lg leading-relaxed max-w-md font-medium">
                    Tham gia hệ sinh thái kỹ thuật chính xác hàng đầu. Tối ưu hóa quy trình sản xuất và thiết kế với nền tảng tích hợp AI của B-Tris.
                </p>
            </div>

            <div class="flex gap-16 mt-8">
                <div>
                    <h3 class="text-4xl font-black">500+</h3>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-200 mt-2">ĐỐI TÁC DOANH NGHIỆP</p>
                </div>
                <div>
                    <h3 class="text-4xl font-black">99.9%</h3>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-200 mt-2">ĐỘ CHÍNH XÁC</p>
                </div>
            </div>
            
            <!-- Decorative icon bottom -->
            <div class="absolute bottom-0 right-0 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[100px]">precision_manufacturing</span>
            </div>
        </div>

        <!-- Right Side Register Form -->
        <div class="w-full lg:w-1/2 flex justify-center lg:justify-end">
            <div class="glass-panel p-8 md:p-10 w-full max-w-[480px]">
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-black text-primary uppercase tracking-tight">TẠO TÀI KHOẢN</h2>
                    <p class="text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mt-2">Bắt đầu hành trình kỹ thuật của bạn</p>
                </div>

                <form method="POST" action="/register" class="space-y-5">
                    @csrf
                    
                    <!-- Full Name -->
                    <div>
                        <label class="input-label" for="full_name">Họ và tên</label>
                        <input class="input-field" name="full_name" id="full_name" placeholder="Ví dụ: Nguyễn Văn A" type="text" value="{{ old('full_name') }}" />
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email & Phone Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="input-label" for="email">Email công việc</label>
                            <input class="input-field" name="email" id="email" placeholder="email@btris.vn" type="email" value="{{ old('email') }}" />
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label" for="phone">Số điện thoại</label>
                            <input class="input-field" name="phone" id="phone" placeholder="09xx xxx xxx" type="tel" value="{{ old('phone') }}" />
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="input-label" for="password">Mật khẩu bảo mật</label>
                        <div class="relative">
                            <input class="input-field pr-10" name="password" id="password" placeholder="••••••••" type="password" />
                            <span onclick="togglePassword('password', 'eyeIcon1')" id="eyeIcon1" class="material-symbols-outlined absolute right-3 top-3 text-outline cursor-pointer hover:text-primary transition-colors text-[20px]">
                                visibility
                            </span>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="input-label" for="password_confirmation">Xác nhận mật khẩu</label>
                        <input class="input-field" name="password_confirmation" id="password_confirmation" placeholder="••••••••" type="password" />
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="flex items-start gap-3 mt-4">
                        <input type="checkbox" id="terms" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary" required>
                        <label for="terms" class="text-[11px] text-on-surface-variant font-medium leading-relaxed">
                            Tôi đồng ý tuân thủ các <a href="#" class="font-bold text-primary hover:underline">Điều khoản Dịch vụ</a> và <a href="#" class="font-bold text-primary hover:underline">Chính sách Bảo mật</a> của B-Tris.
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary mt-2">
                        Bắt đầu ngay
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-on-surface-variant font-bold">
                        Đã là thành viên? <a href="/login" class="text-primary hover:underline">Đăng nhập tại đây</a>
                    </p>
                </div>

                <!-- Divider -->
                <div class="relative flex items-center py-5">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">HOẶC KẾT NỐI QUA</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Social Logins -->
                <div class="flex gap-4">
                    <a href="{{ route('google.login') }}" class="btn-social" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span style="margin-left: 8px;">Google</span>
                    </a>
                    <a href="{{ route('github.login') }}" class="btn-social" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                        <span style="margin-left: 8px;">GitHub</span>
                    </a>
                </div>

                <div class="mt-8 text-center text-[8px] font-bold text-outline uppercase tracking-[0.2em]">
                    © 2024 B-Tris Precision Engineering Ecosystem.<br/> Bản quyền được bảo lưu.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
