<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Xác nhận mã OTP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#001e40",
                        "primary-fixed-dim": "#a7c8ff",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#43474f",
                        "surface-container": "#eceef0",
                    },
                    "fontFamily": {
                        "body": ["Inter"],
                    }
                },
            },
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

        .otp-input {
            width: 3rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 1px solid #e0e3e5;
            border-radius: 0.5rem;
            background-color: transparent;
            color: #001e40;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: #001e40;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 30, 64, 0.2);
        }
        
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="main-gradient-bg text-on-surface antialiased min-h-screen flex items-center justify-center">
    <!-- Background Decor -->
    <div class="fixed inset-0 grid-overlay opacity-50 z-0 pointer-events-none"></div>

    <div class="w-full max-w-[450px] space-y-8 relative z-10 p-6">
        <!-- OTP Card -->
        <div class="glass-panel p-8 md:p-10 rounded-[2rem] text-center">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img alt="B-Tris Logo" class="h-16 w-auto" src="{{ asset('images/logo/logo.jpg') }}" onerror="this.src='https://lh3.googleusercontent.com/aida/ADBb0ujkfzLNdx7XZSZitQlk5uvj58AaPKD3Q4a8s-N0jif1cx4oHslaKAX8G2ZSnAHlcRzadbQdewYZKqoFk1mOb5nMlQ2IWE1LEkOPhgpQ_f3OAsi4xeTMJ3iOTa-_8eU52P20jiTjhhO_DVQY61OFzUJM8oDLw2QCxhc4jgJbee-3YfHibnbR1pzW15EedKEEkwJ2jT6xWslOUKe8XEFuUs5-rwpt-cQ8hs_cqBxpbSAhnRVFQyjHx3mj4QEwzI1P6AkPg2IpZ6OgwCA'" />
            </div>

            <h2 class="text-2xl font-black text-primary uppercase tracking-tight mb-2">Xác nhận mã OTP</h2>
            <p class="text-sm text-on-surface-variant font-medium mb-8">Vui lòng nhập mã xác thực đã được gửi đến email của bạn</p>

            @if(session('error'))
                <div class="text-red-600 text-sm mb-4 font-bold">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->has('otp'))
                <div class="text-red-600 text-sm mb-4 font-bold">
                    {{ $errors->first('otp') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-8">
                @csrf
                <!-- OTP Inputs -->
                <div class="flex justify-center gap-2">
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" autofocus required>
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" required>
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" required>
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" required>
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" required>
                    <input type="number" name="otp[]" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="vibrant-btn w-full text-white py-4 rounded-xl font-black uppercase tracking-[0.2em] text-sm transition-all duration-300 active:scale-[0.98]">
                    Xác nhận
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="mt-6 text-sm font-bold">
                <span class="text-on-surface-variant uppercase text-xs">Không nhận được mã?</span><br>
                <button id="resendBtn" class="text-primary hover:underline uppercase tracking-wide mt-1 disabled:opacity-50" disabled onclick="resendOTP()">
                    Gửi lại mã <span id="timer">(01:59)</span>
                </button>
            </div>

            <!-- Back to Login -->
            <div class="mt-8 pt-6 border-t border-surface-container">
                <a href="{{ route('login') }}" class="text-primary font-black hover:underline uppercase tracking-widest text-[11px] flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Quay lại đăng nhập
                </a>
            </div>
        </div>

        <!-- Page Footer -->
        <footer class="text-center space-y-4">
            <div class="flex justify-center gap-8">
                <a class="text-[10px] font-bold uppercase tracking-widest text-primary-fixed-dim/50 hover:text-primary-fixed-dim transition-colors" href="#">Chính sách bảo mật</a>
                <a class="text-[10px] font-bold uppercase tracking-widest text-primary-fixed-dim/50 hover:text-primary-fixed-dim transition-colors" href="#">Điều khoản dịch vụ</a>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary-fixed-dim/40 leading-relaxed">
                © 2024 B-Tris Precision Engineering Ecosystem.<br /> Bản quyền được bảo lưu.
            </p>
        </footer>
    </div>

    <!-- Top Accent Bar -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary via-[#a7c8ff] to-[#3a5f94] z-50"></div>
    
    <div class="fixed top-12 right-12 hidden lg:block opacity-5 pointer-events-none">
        <span class="material-symbols-outlined text-[200px] font-thin text-white" data-icon="shield_with_heart">shield_with_heart</span>
    </div>

    <script>
        function moveToNext(input, event) {
            // Prevent non-numeric
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value.length > 1) {
                input.value = input.value.slice(0, 1);
            }

            if (input.value.length === 1) {
                let next = input.nextElementSibling;
                if (next && next.tagName === 'INPUT') {
                    next.focus();
                }
            }
        }

        // Handle backspace
        document.querySelectorAll('.otp-input').forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !input.value) {
                    let prev = input.previousElementSibling;
                    if (prev && prev.tagName === 'INPUT') {
                        prev.focus();
                    }
                }
            });
        });

        // Timer logic
        let timeLeft = 119; // 1:59
        let timerInterval;

        function updateTimer() {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            document.getElementById('timer').innerText = `(${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')})`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('timer').innerText = '';
                document.getElementById('resendBtn').disabled = false;
            } else {
                timeLeft--;
            }
        }

        function startTimer() {
            document.getElementById('resendBtn').disabled = true;
            timeLeft = 119;
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }

        startTimer();

        function resendOTP() {
            document.getElementById('resendBtn').disabled = true;
            document.getElementById('timer').innerText = '...';
            
            $.ajax({
                url: '{{ route("otp.resend") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        startTimer();
                    } else {
                        alert(response.message);
                        document.getElementById('resendBtn').disabled = false;
                        document.getElementById('timer').innerText = '';
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                    document.getElementById('resendBtn').disabled = false;
                    document.getElementById('timer').innerText = '';
                }
            });
        }
    </script>
</body>
</html>
