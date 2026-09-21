<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác Thực OTP Khách Thuê - SmartRoom &amp; Renty</title>

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
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #070b13;
            background-image: 
                radial-gradient(at 15% 15%, rgba(16, 185, 129, 0.12) 0px, transparent 55%),
                radial-gradient(at 85% 20%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(20, 184, 166, 0.08) 0px, transparent 60%);
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-card {
            background: rgba(13, 20, 36, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .otp-box {
            letter-spacing: 0.45em;
            font-family: 'JetBrains Mono', monospace;
            text-align: center;
        }

        /* Ambient animated glow */
        .ambient-glow {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            filter: blur(130px);
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
        .toast-card.show { transform: translateX(0); }
        .toast-success { border-color: rgba(16, 185, 129, 0.5); background: rgba(6, 44, 31, 0.95); }
        .toast-error { border-color: rgba(244, 63, 94, 0.5); background: rgba(54, 10, 20, 0.95); }
        .toast-info { border-color: rgba(99, 102, 241, 0.5); background: rgba(20, 25, 60, 0.95); }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <div id="toast-container"></div>

    <!-- Background Orbs -->
    <div class="ambient-glow bg-emerald-500/20 top-[-100px] left-[15%]"></div>
    <div class="ambient-glow bg-indigo-500/20 bottom-[-80px] right-[10%]"></div>

    <!-- TOP NAVIGATION -->
    <header class="container mx-auto px-6 py-5 flex justify-between items-center relative z-20">
        <a href="{{ route('renty.user') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 via-teal-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-emerald-500/25 group-hover:scale-105 transition-all duration-300">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight flex items-center gap-1.5">
                <span class="text-white">SmartRoom</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold uppercase tracking-wider">&amp; Renty</span>
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
    <main class="container mx-auto px-4 sm:px-6 py-8 flex-grow flex items-center justify-center relative z-10">
        <div class="w-full max-w-lg">
            
            <div class="glass-card rounded-3xl p-6 sm:p-9 space-y-6">

                <!-- Header Badge & Icon -->
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-bold uppercase tracking-wider shadow-inner">
                        <i class="fa-solid fa-shield-check text-xs"></i> Bước Cuối: Xác Thực OTP
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mx-auto text-emerald-400 text-2xl shadow-inner shadow-emerald-500/20">
                        <i class="fa-solid fa-key"></i>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Xác Thực Tài Khoản <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Khách Thuê</span>
                    </h1>

                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                        Mã bảo mật gồm 6 số đã được gửi đến {{ $method === 'email' ? 'hộp thư email' : 'số điện thoại' }}:
                    </p>

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-950/80 border border-slate-800 text-emerald-300 font-mono text-xs font-bold">
                        <i class="fa-solid {{ $method === 'email' ? 'fa-envelope text-indigo-400' : 'fa-phone text-emerald-400' }}"></i>
                        <span>{{ $target }}</span>
                    </div>
                </div>

                <!-- Form verify OTP -->
                <form id="verify-form" action="{{ route('guest.verifyOtp') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- OTP Input -->
                    <div>
                        <label for="input-otp" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2.5 text-center">
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
                                class="otp-box w-full py-4 px-3 rounded-2xl bg-slate-950/80 border-2 border-slate-700/80 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 text-3xl font-bold text-emerald-300 outline-none transition-all placeholder:text-slate-700 placeholder:tracking-widest"
                            >
                        </div>
                        @error('otp')
                            <p class="text-xs text-rose-400 font-semibold mt-2.5 text-center flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation text-[11px]"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Countdown & Resend Button -->
                    <div class="flex items-center justify-between text-xs text-slate-400 px-1 pt-1">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-slate-500"></i>
                            <span>Hết hạn sau:</span>
                            <strong class="text-emerald-400 font-mono" id="countdown-timer">05:00</strong>
                        </span>
                        <button type="button" id="btn-resend-otp" onclick="resendOtp()" class="text-emerald-400 hover:text-emerald-300 underline font-semibold disabled:opacity-40 disabled:no-underline cursor-pointer transition-colors" disabled>
                            Gửi lại mã (<span id="resend-countdown">60</span>s)
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/25 active:scale-[0.98] transition-all cursor-pointer">
                        <i class="fa-solid fa-circle-check text-base"></i>
                        <span id="btn-submit-text">Xác Nhận &amp; Vào Renty Ngay</span>
                    </button>

                    <!-- Back to register -->
                    <div class="text-center pt-2">
                        <a href="{{ route('user.createUser') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-left text-[10px]"></i>
                            <span>Quay lại chỉnh sửa thông tin đăng ký</span>
                        </a>
                    </div>
                </form>

            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-900 mt-6">
        &copy; 2026 SmartRoom &amp; Renty. Tất cả quyền được bảo lưu.
    </footer>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const inputOtp = document.getElementById('input-otp');
        const btnSubmit = document.getElementById('btn-submit');
        const btnSubmitText = document.getElementById('btn-submit-text');
        const countdownTimerEl = document.getElementById('countdown-timer');
        const btnResend = document.getElementById('btn-resend-otp');
        const resendCountdownEl = document.getElementById('resend-countdown');

        // Format OTP input: only digits, max 6
        if (inputOtp) {
            inputOtp.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                if (this.value.length === 6) {
                    // Auto submit when 6 digits are typed
                    document.getElementById('verify-form').requestSubmit();
                }
            });
        }

        // Countdown timer 5 minutes
        let totalSeconds = 300;
        const timerInterval = setInterval(function() {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                if (countdownTimerEl) {
                    countdownTimerEl.textContent = "00:00 (Hết hạn)";
                    countdownTimerEl.classList.add('text-rose-400');
                }
                showToast("Mã OTP đã hết hạn. Vui lòng bấm 'Gửi lại mã'.", "error");
                return;
            }
            const mins = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const secs = String(totalSeconds % 60).padStart(2, '0');
            if (countdownTimerEl) countdownTimerEl.textContent = `${mins}:${secs}`;
        }, 1000);

        // Resend button cooldown 60 seconds
        let resendSeconds = 60;
        const resendInterval = setInterval(function() {
            resendSeconds--;
            if (resendSeconds <= 0) {
                clearInterval(resendInterval);
                if (btnResend) {
                    btnResend.disabled = false;
                    btnResend.textContent = "Gửi lại mã OTP";
                }
                return;
            }
            if (resendCountdownEl) resendCountdownEl.textContent = resendSeconds;
        }, 1000);

        // Resend OTP function
        async function resendOtp() {
            if (btnResend.disabled) return;
            btnResend.disabled = true;
            btnResend.textContent = "Đang gửi lại...";

            try {
                const response = await fetch("{{ route('guest.sendOtp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message, "success");
                    // Reset 5-minute timer
                    totalSeconds = data.expires_in || 300;
                    countdownTimerEl.classList.remove('text-rose-400');

                    // Reset 60s cooldown
                    resendSeconds = 60;
                    btnResend.innerHTML = `Gửi lại mã (<span id="resend-countdown">${resendSeconds}</span>s)`;
                    const newCountdownEl = document.getElementById('resend-countdown');
                    const newInterval = setInterval(function() {
                        resendSeconds--;
                        if (resendSeconds <= 0) {
                            clearInterval(newInterval);
                            btnResend.disabled = false;
                            btnResend.textContent = "Gửi lại mã OTP";
                            return;
                        }
                        if (newCountdownEl) newCountdownEl.textContent = resendSeconds;
                    }, 1000);

                    if (inputOtp) {
                        inputOtp.value = '';
                        inputOtp.focus();
                    }
                } else {
                    showToast(data.message || "Không thể gửi lại mã OTP. Vui lòng thử lại sau.", "error");
                    btnResend.disabled = false;
                    btnResend.textContent = "Gửi lại mã OTP";
                }
            } catch (err) {
                showToast("Lỗi kết nối mạng khi gửi lại mã OTP.", "error");
                btnResend.disabled = false;
                btnResend.textContent = "Gửi lại mã OTP";
            }
        }

        // Form submit loading state
        const verifyForm = document.getElementById('verify-form');
        if (verifyForm) {
            verifyForm.addEventListener('submit', function() {
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
                }
                if (btnSubmitText) {
                    btnSubmitText.textContent = "Đang xác thực & mở Renty...";
                }
            });
        }

        // Toast system
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            let icon = 'fa-circle-info text-indigo-400';
            if (type === 'success') icon = 'fa-circle-check text-emerald-400';
            if (type === 'error') icon = 'fa-circle-exclamation text-rose-400';
            card.innerHTML = `
                <i class="fa-solid ${icon} mt-0.5 text-base shrink-0"></i>
                <div class="flex-grow">
                    <p class="text-xs font-medium text-slate-200 leading-relaxed">${message}</p>
                </div>
            `;
            container.appendChild(card);
            setTimeout(() => card.classList.add('show'), 10);
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 400);
            }, 5000);
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
