<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác Minh Mã OTP - SmartRoom &amp; Renty</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #070b13;
            background-image: 
                radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.12) 0px, transparent 55%),
                radial-gradient(at 85% 20%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(245, 158, 11, 0.06) 0px, transparent 60%);
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-card {
            background: rgba(13, 20, 36, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .otp-box {
            letter-spacing: 0.4em;
            font-family: 'JetBrains Mono', monospace;
            text-align: center;
        }

        /* Ambient animated glow */
        .ambient-glow {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.35;
        }

        /* Toast notifications */
        #toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .toast-card {
            pointer-events: auto;
            transform: translateX(120%);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 24rem;
            border-radius: 0.875rem;
            padding: 0.875rem 1.125rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.5);
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .toast-card.show {
            transform: translateX(0);
        }
        .toast-success { border-color: rgba(16, 185, 129, 0.4); background: rgba(6, 44, 31, 0.95); }
        .toast-error { border-color: rgba(244, 63, 94, 0.4); background: rgba(54, 10, 20, 0.95); }
        .toast-info { border-color: rgba(99, 102, 241, 0.4); background: rgba(20, 25, 60, 0.95); }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <div id="toast-container"></div>

    <!-- Background Orbs -->
    <div class="ambient-glow bg-amber-500/20 top-[-100px] left-[15%]"></div>
    <div class="ambient-glow bg-indigo-500/20 bottom-[-80px] right-[10%]"></div>

    <!-- TOP NAVIGATION -->
    <header class="container mx-auto px-6 py-5 flex justify-between items-center relative z-20">
        <a href="{{ route('renty.user') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-500 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-all duration-300">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight flex items-center gap-1.5">
                <span class="text-white">SmartRoom</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold uppercase tracking-wider">Landlord</span>
            </span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('renty.user') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-all">
                <i class="fa-solid fa-compass text-slate-400"></i> Khám Phá Renty
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600/90 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i> Đăng Nhập
            </a>
        </div>
    </header>

    <!-- MAIN SECTION -->
    <main class="container mx-auto px-4 sm:px-6 py-6 lg:py-10 flex-grow relative z-10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- LEFT COLUMN: Value Proposition & Onboarding Steps -->
            <section class="flex flex-col justify-between space-y-6 lg:space-y-8 md:col-span-5 lg:col-span-5 order-2 md:order-1">
                <div>
                    <!-- Back Button -->
                    <div class="mb-5">
                        <a href="{{ route('landlord.register') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-400 hover:text-slate-100 transition-all shadow-sm group">
                            <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1 text-slate-500 group-hover:text-indigo-400"></i>
                            <span>Quay lại sửa thông tin</span>
                        </a>
                    </div>

                    <!-- Section Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-bold uppercase tracking-wider mb-4 shadow-inner">
                        <i class="fa-solid fa-shield-halved text-emerald-400"></i> Bước 2: Xác thực bảo mật OTP
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                        Chỉ 1 Bước Nữa Để <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Mở Dashboard Quản Lý</span>
                    </h1>

                    <p class="mt-4 text-sm text-slate-400 leading-relaxed">
                        Mã OTP bảo vệ tài khoản chủ trọ của bạn. Xác thực thành công sẽ tự động kích hoạt tài khoản và mở ngay hệ thống quản lý phòng trọ SmartRoom.
                    </p>
                </div>

                <!-- 3-STEP ROADMAP -->
                <div class="space-y-3">
                    <div class="glass-card rounded-2xl p-4 flex items-start gap-3.5 border-emerald-500/30 bg-emerald-500/5 transition-all">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 flex items-center justify-center font-black text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>Khởi Tạo Nhanh (60 giây)</span>
                                <span class="text-[9px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-bold">Đã xong</span>
                            </h4>
                            <p class="text-xs text-slate-400 mt-0.5">Thông tin tài khoản đã được thiết lập thành công.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-4 flex items-start gap-3.5 border-indigo-500/40 bg-indigo-500/10 transition-all">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 border border-indigo-400 text-white flex items-center justify-center font-black text-xs shrink-0 mt-0.5 shadow-md shadow-indigo-600/40">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                                <span>Xác Minh Mã OTP</span>
                                <span class="text-[9px] bg-indigo-500/20 text-indigo-300 px-1.5 py-0.5 rounded font-bold animate-pulse">Đang làm</span>
                            </h4>
                            <p class="text-xs text-slate-300 mt-0.5">Nhập mã 6 chữ số gửi về để vào thẳng Dashboard.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-4 flex items-start gap-3.5 border-slate-800 bg-slate-900/30 transition-all opacity-70">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 text-slate-500 flex items-center justify-center font-black text-xs shrink-0 mt-0.5">
                            3
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tích Xanh &amp; Nhận Tiền Tự Động</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Hoàn thiện hồ sơ pháp lý sau khi vào Dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- STATS COUNTER -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="glass-card rounded-2xl p-3.5 text-center">
                        <span class="text-xl font-black text-white block">12.5K+</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Cư dân tìm phòng</span>
                    </div>
                    <div class="glass-card rounded-2xl p-3.5 text-center">
                        <span class="text-xl font-black text-emerald-400 block">99.2%</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Tỷ lệ lấp đầy</span>
                    </div>
                    <div class="glass-card rounded-2xl p-3.5 text-center">
                        <span class="text-xl font-black text-indigo-400 block">0 VNĐ</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Phí khởi tạo</span>
                    </div>
                </div>

            </section>

            <!-- RIGHT COLUMN: Verification Card -->
            <section class="md:col-span-7 lg:col-span-7 order-1 md:order-2">
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">

                    <!-- Step indicator: 3 bước -->
                    <div class="flex items-center gap-0 border-b border-slate-800/80 pb-4">
                        <!-- Step 1: Done -->
                        <div class="flex flex-col items-center min-w-0">
                            <div class="w-7 h-7 rounded-full bg-emerald-500/20 border-2 border-emerald-500/50 flex items-center justify-center text-emerald-400 shrink-0">
                                <i class="fa-solid fa-check text-[11px]"></i>
                            </div>
                            <span class="text-[10px] font-semibold text-emerald-400/80 mt-1 whitespace-nowrap">ĐK Cơ Bản</span>
                        </div>
                        <!-- Connector 1-2 -->
                        <div class="flex-1 h-0.5 bg-emerald-500/50 mx-1.5 mt-[-14px]"></div>
                        <!-- Step 2: Active (Màu tím) -->
                        <div class="flex flex-col items-center min-w-0">
                            <div class="w-7 h-7 rounded-full bg-indigo-600 border-2 border-indigo-400 flex items-center justify-center text-white font-black text-xs shrink-0 shadow-md shadow-indigo-600/40">
                                2
                            </div>
                            <span class="text-[10px] font-bold text-indigo-300 mt-1 whitespace-nowrap">Xác Minh OTP</span>
                        </div>
                        <!-- Connector 2-3 -->
                        <div class="flex-1 h-0.5 bg-slate-700 mx-1.5 mt-[-14px]"></div>
                        <!-- Step 3: Pending (Xám mờ) -->
                        <div class="flex flex-col items-center min-w-0">
                            <div class="w-7 h-7 rounded-full bg-slate-800 border-2 border-slate-600 flex items-center justify-center text-slate-500 font-black text-xs shrink-0">
                                3
                            </div>
                            <span class="text-[10px] font-semibold text-slate-500 mt-1 whitespace-nowrap">Hoàn Thiện HS</span>
                        </div>
                    </div>

                    <!-- Title & Info -->
                    <div class="text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center mx-auto text-indigo-400 text-xl shadow-inner shadow-indigo-500/20">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h1 class="text-xl font-black text-white tracking-tight">Xác Thực Mã OTP</h1>
                        <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                            Mã bảo mật 6 số đã được gửi đến {{ $method === 'email' ? 'hộp thư email' : 'số điện thoại' }}:
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-amber-300 font-mono text-xs font-bold">
                            <i class="fa-solid {{ $method === 'email' ? 'fa-envelope text-indigo-400' : 'fa-phone text-emerald-400' }}"></i>
                            <span>{{ $target }}</span>
                        </div>
                    </div>


                    <!-- Form verify -->
                    <form id="verify-form" action="{{ route('landlord.verifyOtp') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- OTP Input -->
                        <div>
                            <label for="input-otp" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2 text-center">
                                Nhập mã xác thực 6 chữ số
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="otp" 
                                    id="input-otp" 
                                    required 
                                    maxlength="6" 
                                    pattern="[0-9]{6}" 
                                    inputmode="numeric" 
                                    autocomplete="one-time-code" 
                                    placeholder="••••••" 
                                    autofocus
                                    value="{{ old('otp') }}"
                                    class="otp-box w-full py-4 px-3 rounded-2xl bg-slate-950/80 border-2 border-slate-700/80 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 text-3xl font-bold text-amber-300 outline-none transition-all placeholder:text-slate-700 placeholder:tracking-widest"
                                >
                            </div>
                            @error('otp')
                                <p class="text-xs text-rose-400 font-semibold mt-2 text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Timer & Resend -->
                        <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-500"></i>
                                <span>Hết hạn sau:</span>
                                <strong class="text-amber-400 font-mono" id="countdown-timer">05:00</strong>
                            </span>
                            <button type="button" id="btn-resend-otp" onclick="resendOtp()" class="text-indigo-400 hover:text-indigo-300 underline font-semibold disabled:opacity-40 disabled:no-underline cursor-pointer transition-colors" disabled>
                                Gửi lại mã (<span id="resend-countdown">60</span>s)
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btn-submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/25 active:scale-[0.98] transition-all cursor-pointer">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span id="btn-submit-text">Xác Nhận &amp; Mở Dashboard Ngay</span>
                        </button>

                        <!-- Back to edit info -->
                        <div class="text-center pt-2">
                            <a href="{{ route('landlord.register') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                <span>Quay lại chỉnh sửa thông tin đăng ký</span>
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-900 mt-12">
        &copy; 2026 SmartRoom &amp; Renty. Hệ thống quản lý vận hành phòng trọ thông minh.
    </footer>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const inputOtp = document.getElementById('input-otp');
        const btnSubmit = document.getElementById('btn-submit');
        const btnSubmitText = document.getElementById('btn-submit-text');
        const btnResend = document.getElementById('btn-resend-otp');
        const resendCountdownEl = document.getElementById('resend-countdown');
        const countdownTimerEl = document.getElementById('countdown-timer');

        // Countdown hết hạn mã (5 phút)
        let expireSeconds = 300;
        const expireInterval = setInterval(() => {
            expireSeconds--;
            if (expireSeconds <= 0) {
                clearInterval(expireInterval);
                countdownTimerEl.textContent = '00:00';
                countdownTimerEl.classList.add('text-rose-400');
                showToast('Mã OTP đã hết hạn. Vui lòng nhấn nút gửi lại mã mới.', 'error');
            } else {
                const m = String(Math.floor(expireSeconds / 60)).padStart(2, '0');
                const s = String(expireSeconds % 60).padStart(2, '0');
                countdownTimerEl.textContent = `${m}:${s}`;
            }
        }, 1000);

        // Countdown cho nút Gửi lại mã (60s)
        let resendSeconds = 60;
        const resendInterval = setInterval(() => {
            resendSeconds--;
            if (resendSeconds <= 0) {
                clearInterval(resendInterval);
                btnResend.disabled = false;
                btnResend.innerHTML = '<i class="fa-solid fa-rotate-right mr-1"></i>Gửi lại mã ngay';
            } else {
                resendCountdownEl.textContent = resendSeconds;
            }
        }, 1000);


        async function resendOtp() {
            btnResend.disabled = true;
            btnResend.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Đang gửi lại...';

            try {
                const res = await fetch("{{ route('landlord.sendOtp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        method: "{{ $method }}",
                        target: "{{ $target }}"
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    showToast(data.message || 'Mã OTP mới đã được gửi!', 'success');
                    // Reset countdown timer
                    expireSeconds = 300;
                    countdownTimerEl.classList.remove('text-rose-400');
                    
                    // Reset resend countdown
                    resendSeconds = 60;
                    btnResend.disabled = true;
                    btnResend.innerHTML = 'Gửi lại mã (<span id="resend-countdown">60</span>s)';
                    setInterval(() => {
                        resendSeconds--;
                        if (resendSeconds <= 0) {
                            btnResend.disabled = false;
                            btnResend.innerHTML = '<i class="fa-solid fa-rotate-right mr-1"></i>Gửi lại mã ngay';
                        } else {
                            const span = document.getElementById('resend-countdown');
                            if (span) span.textContent = resendSeconds;
                        }
                    }, 1000);
                } else {
                    showToast(data.message || 'Không thể gửi lại mã OTP. Vui lòng thử lại.', 'error');
                    btnResend.disabled = false;
                    btnResend.innerHTML = 'Gửi lại mã ngay';
                }
            } catch (err) {
                showToast('Lỗi kết nối khi gửi lại OTP.', 'error');
                btnResend.disabled = false;
                btnResend.innerHTML = 'Gửi lại mã ngay';
            }
        }

        // Tự động submit khi nhập đủ 6 số
        if (inputOtp) {
            inputOtp.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
                if (this.value.length === 6) {
                    btnSubmitText.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Đang kích hoạt tài khoản...';
                    document.getElementById('verify-form').submit();
                }
            });
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast-card toast-${type}`;
            const iconClass = type === 'success' ? 'fa-circle-check text-emerald-400' :
                              type === 'error' ? 'fa-circle-exclamation text-rose-400' :
                              'fa-circle-info text-indigo-400';

            toast.innerHTML = `
                <i class="fa-solid ${iconClass} text-lg mt-0.5"></i>
                <div class="flex-grow">
                    <p class="text-xs font-semibold text-slate-200 leading-snug">${message}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 4500);
        }

        @if(session('success'))
            showToast(@json(session('success')), "success");
        @endif
        @if(session('error'))
            showToast(@json(session('error')), "error");
        @endif
    </script>
</body>
</html>
