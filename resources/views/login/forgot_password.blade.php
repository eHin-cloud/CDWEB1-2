<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Khôi Phục Mật Khẩu - SmartRoom &amp; Renty</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #070a12;
            color: #f1f5f9;
        }

        .ambient-mesh-1 {
            position: fixed;
            top: -15%;
            left: -10%;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(99, 102, 241, 0) 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .ambient-mesh-2 {
            position: fixed;
            bottom: -15%;
            right: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.14) 0%, rgba(236, 72, 153, 0) 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
        }

        .otp-box {
            letter-spacing: 0.35em;
            font-family: 'JetBrains Mono', monospace;
            text-align: center;
        }

        .form-input-shell {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-icon {
            position: absolute;
            left: 1rem;
            color: #64748b;
            font-size: 0.875rem;
            transition: color 0.2s;
            pointer-events: none;
        }

        .form-input-shell:focus-within .form-input-icon {
            color: #818cf8;
        }

        .auth-input-field {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            background: rgba(2, 6, 23, 0.7);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 0.875rem;
            color: #f8fafc;
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }

        .auth-input-field:hover {
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(2, 6, 23, 0.85);
        }

        .auth-input-field:focus {
            border-color: #6366f1;
            background: rgba(2, 6, 23, 0.95);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .auth-pwd-toggle {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.15s;
        }

        .auth-pwd-toggle:hover {
            color: #e2e8f0;
        }

        .auth-strength-track {
            display: flex;
            gap: 0.25rem;
            height: 4px;
            width: 100%;
        }

        .auth-strength-segment {
            flex: 1;
            height: 100%;
            border-radius: 4px;
            background: rgba(148, 163, 184, 0.15);
            opacity: 0.2;
            transition: background 0.3s ease, opacity 0.3s ease;
        }

        .toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-width: 420px;
            width: calc(100% - 3rem);
            pointer-events: none;
        }

        .toast-card {
            background: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.6);
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            pointer-events: auto;
            transform: translateX(120%);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast-card.show {
            transform: translateX(0);
        }

        .toast-success { border-left: 4px solid #10b981 !important; }
        .toast-error { border-left: 4px solid #ef4444 !important; }
        .toast-info { border-left: 4px solid #6366f1 !important; }

        /* ===== LIGHT MODE STYLING ===== */
        html.theme-light body, body.theme-light {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.theme-light .glass-card, body.theme-light .glass-card {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.1) !important;
            color: #0f172a !important;
        }
        html.theme-light header .text-white, body.theme-light header .text-white,
        html.theme-light h1.text-white, body.theme-light h1.text-white,
        html.theme-light h2.text-white, body.theme-light h2.text-white {
            color: #0f172a !important;
        }
        html.theme-light .text-slate-400, body.theme-light .text-slate-400 {
            color: #475569 !important;
            font-weight: 500 !important;
        }
        html.theme-light .text-slate-500, body.theme-light .text-slate-500 {
            color: #334155 !important;
            font-weight: 500 !important;
        }
        html.theme-light .text-slate-300, body.theme-light .text-slate-300 {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.theme-light .auth-input-field, body.theme-light .auth-input-field {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        html.theme-light .auth-input-field:hover, body.theme-light .auth-input-field:hover {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
        }
        html.theme-light .auth-input-field:focus, body.theme-light .auth-input-field:focus {
            border-color: #4f46e5 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
        }
        html.theme-light .auth-input-field::placeholder, body.theme-light .auth-input-field::placeholder {
            color: #64748b !important;
            opacity: 1 !important;
        }
        html.theme-light .otp-box, body.theme-light .otp-box {
            background: #ffffff !important;
            border: 2px solid #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.05) !important;
        }
        html.theme-light .otp-box:focus, body.theme-light .otp-box:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
        }
        html.theme-light .form-input-icon, body.theme-light .form-input-icon {
            color: #64748b !important;
        }
        html.theme-light .theme-toggle-button, body.theme-light .theme-toggle-button {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    <!-- Ambient glow -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <div id="toast-container" class="toast-container"></div>

    <!-- HEADER -->
    <header class="container mx-auto px-6 py-5 flex justify-between items-center relative z-20">
        <a href="{{ route('renty.user') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-all duration-300">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight flex items-center gap-1.5">
                <span class="text-white">SmartRoom</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 font-bold uppercase tracking-wider">&amp; Renty</span>
            </span>
        </a>

        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                <i class="fa-solid fa-moon" data-theme-icon></i>
            </button>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600/90 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i> Đăng Nhập
            </a>
        </div>
    </header>

    <!-- MAIN CARD -->
    <main class="container mx-auto px-4 sm:px-6 py-8 flex-grow flex items-center justify-center relative z-10">
        <div class="w-full max-w-lg">
            
            <div class="glass-card rounded-3xl p-6 sm:p-9 space-y-6">


                <div class="text-center space-y-2">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center mx-auto text-indigo-400 text-2xl shadow-inner shadow-indigo-500/20">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Khôi Phục <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">Mật Khẩu</span>
                    </h1>

                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                        @if(($step ?? 1) == 2 && !empty($email))
                            Nhập mã xác thực 6 số gửi về email và thiết lập mật khẩu mới
                        @else
                            Nhập địa chỉ email đăng ký tài khoản để nhận mã OTP xác thực
                        @endif
                    </p>
                </div>

                <!-- Alert Errors / Success -->
                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-3 shadow-lg shadow-rose-500/5">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-base mt-0.5 shrink-0"></i>
                        <div class="flex-grow">
                            <p class="font-bold text-rose-200">Không thể thực hiện!</p>
                            <p class="text-[11px] text-rose-300 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div id="session-success-alert" class="p-3.5 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-start gap-3 shadow-lg shadow-emerald-500/5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-base mt-0.5 shrink-0"></i>
                        <div class="flex-grow">
                            <p class="font-bold text-emerald-200">Thành công!</p>
                            <p class="text-[11px] text-emerald-300 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs">
                        <div class="font-bold mb-1 text-rose-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i> Vui lòng kiểm tra lại:
                        </div>
                        <ul class="list-disc pl-5 space-y-1 text-[11px]">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(($step ?? 1) == 1 || empty($email))
                    <!-- BƯỚC 1: NHẬP EMAIL ĐỂ NHẬN OTP -->
                    <form action="{{ route('password.sendOtp', [], false) }}" method="POST" class="space-y-4" onsubmit="handleSendOtpSubmit(this)">
                        @csrf
                        <div>
                            <label for="input-email" class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Địa chỉ Email tài khoản <span class="text-rose-400 font-bold">*</span>
                            </label>
                            <div class="form-input-shell">
                                <i class="fa-solid fa-envelope form-input-icon"></i>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="input-email" 
                                    required 
                                    placeholder="user@example.com" 
                                    value="{{ old('email') }}"
                                    class="auth-input-field @error('email') border-rose-500 ring-2 ring-rose-500/20 @enderror"
                                    autocomplete="email"
                                    autofocus
                                >
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-[10px]"></i>
                                Hệ thống sẽ gửi mã bảo mật 6 số qua email này để xác nhận danh tính của bạn.
                            </p>
                        </div>

                        <button 
                            type="submit" 
                            id="btn-send-otp"
                            class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                        >
                            <i id="btn-send-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                            <span id="btn-send-text">Gửi Mã Xác Thực OTP</span>
                            <i id="btn-send-icon" class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                @else
                    <!-- BƯỚC 2: QUY TRÌNH 2 GIAI ĐOẠN (XÁC THỰC OTP TRƯỚC -> THIẾT LẬP MẬT KHẨU SAU) -->
                    
                    <!-- GIAI ĐOẠN 2A: CHỈ NHẬP VÀ XÁC THỰC MÃ OTP -->
                    <div id="stage-otp" class="space-y-4">
                        <!-- THÔNG TIN EMAIL ĐANG NHẬN OTP -->
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-indigo-500/20 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-300">
                                <i class="fa-solid fa-envelope-circle-check text-indigo-400"></i>
                                <span>Gửi tới: <strong class="text-indigo-300 font-mono">{{ $email }}</strong></span>
                            </div>
                            <a href="{{ route('password.forgot') }}?reset=1" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold underline hover:no-underline transition-all">
                                Đổi email
                            </a>
                        </div>

                        <div>
                            <label for="input-otp" class="block text-xs font-semibold text-slate-300 mb-1.5 text-center">
                                Nhập mã OTP 6 số xác thực <span class="text-rose-400 font-bold">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="input-otp" 
                                maxlength="6" 
                                inputmode="numeric" 
                                placeholder="••••••" 
                                class="otp-box w-full py-3.5 px-4 rounded-xl bg-slate-950/80 border-2 border-indigo-500/40 focus:border-indigo-500 text-2xl font-bold text-indigo-300 outline-none transition-all placeholder:text-slate-700"
                                oninput="onOtpInputChange(this)"
                                onkeydown="if(event.key==='Enter'){event.preventDefault();verifyOtpAndProceed();}"
                                autofocus
                            >
                            <p id="otp-error-msg" class="hidden text-xs text-rose-400 font-semibold mt-1.5 text-center"></p>

                            <!-- Timer & Resend -->
                            <div class="flex items-center justify-between text-xs text-slate-400 mt-2.5 px-1">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-slate-500"></i>
                                    <span>Hết hạn:</span>
                                    <strong class="text-amber-400 font-mono" id="countdown-timer">05:00</strong>
                                </span>
                                <button type="button" id="btn-resend-otp" onclick="resendForgotOtp()" class="text-indigo-400 hover:text-indigo-300 underline font-semibold disabled:opacity-40 disabled:no-underline cursor-pointer transition-colors" disabled>
                                    Gửi lại mã (<span id="resend-countdown">60</span>s)
                                </button>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            id="btn-verify-otp"
                            onclick="verifyOtpAndProceed()"
                            class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                        >
                            <i id="btn-verify-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                            <span id="btn-verify-text">Xác Thực Mã OTP &amp; Tiếp Tục</span>
                            <i id="btn-verify-icon" class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                    <!-- GIAI ĐOẠN 2B: THIẾT LẬP MẬT KHẨU MỚI (CHỈ XUẤT HIỆN SAU KHI OTP ĐÃ HỢP LỆ) -->
                    <div id="stage-password" class="hidden space-y-4">
                        <form id="form-reset-password" action="{{ route('password.resetWithOtp', [], false) }}" method="POST" class="space-y-4" novalidate onsubmit="return handleResetPasswordSubmit(event)">
                            @csrf
                            <input type="hidden" name="otp" id="hidden-otp-input" value="{{ old('otp') }}">

                            <!-- Mật khẩu mới -->
                            <div>
                                <label for="input-password" class="block text-xs font-semibold text-slate-300 mb-1.5">
                                    Mật khẩu mới <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <div class="form-input-shell">
                                    <i class="fa-solid fa-lock form-input-icon"></i>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="input-password" 
                                        required 
                                        minlength="6" 
                                        placeholder="Tối thiểu 6 ký tự" 
                                        class="auth-input-field pr-10"
                                        oninput="evalPasswordStrength(this.value); clearPasswordError('input-password')"
                                        autocomplete="new-password"
                                    >
                                    <button type="button" onclick="togglePasswordVisibility('input-password', this)" class="auth-pwd-toggle" title="Hiện / Ẩn mật khẩu">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                                <p id="password-error-msg" class="hidden text-xs text-rose-400 font-semibold mt-1"></p>

                                <!-- Thanh đo độ mạnh mật khẩu -->
                                <div id="pwd-strength-box" class="mt-2 space-y-1">
                                    <div class="auth-strength-track">
                                        <div class="auth-strength-segment"></div>
                                        <div class="auth-strength-segment"></div>
                                        <div class="auth-strength-segment"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span id="pwd-strength-label" class="font-bold text-slate-500 uppercase tracking-wider">Độ mạnh: Chưa nhập</span>
                                        <span class="text-slate-500">Nên có chữ hoa, số &amp; ký tự đặc biệt</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Xác nhận mật khẩu mới -->
                            <div>
                                <label for="input-password-confirmation" class="block text-xs font-semibold text-slate-300 mb-1.5">
                                    Xác nhận mật khẩu mới <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <div class="form-input-shell">
                                    <i class="fa-solid fa-lock-open form-input-icon"></i>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="input-password-confirmation" 
                                        required 
                                        minlength="6" 
                                        placeholder="Nhập lại mật khẩu mới" 
                                        class="auth-input-field pr-10"
                                        oninput="clearPasswordError('input-password-confirmation')"
                                        autocomplete="new-password"
                                    >
                                    <button type="button" onclick="togglePasswordVisibility('input-password-confirmation', this)" class="auth-pwd-toggle" title="Hiện / Ẩn mật khẩu">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                                <p id="password-confirmation-error-msg" class="hidden text-xs text-rose-400 font-semibold mt-1"></p>
                            </div>

                            <!-- Submit button -->
                            <button 
                                type="submit" 
                                id="btn-submit-reset"
                                class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                            >
                                <i id="btn-reset-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                                <i id="btn-reset-icon" class="fa-solid fa-circle-check"></i>
                                <span id="btn-reset-text">Xác Nhận &amp; Đặt Lại Mật Khẩu</span>
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Footer Back to Login Button -->
                <div class="pt-4 border-t border-slate-800/80 space-y-3 text-center">
                    <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white font-semibold text-xs transition-all flex items-center justify-center gap-2 group shadow-sm">
                        <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                        <span>Quay lại trang đăng nhập</span>
                    </a>
                    <p class="text-xs text-slate-400">
                        Nhớ lại mật khẩu rồi? 
                        <a href="{{ route('login') }}" class="font-bold text-indigo-400 hover:text-indigo-300 ml-1 transition-colors underline underline-offset-2">
                            Đăng nhập ngay
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-900 mt-6">
        &copy; 2026 SmartRoom &amp; Renty. Nền tảng quản lý nhà trọ và kết nối khách thuê số 1.
    </footer>

    <!-- INTERACTIVE SCRIPTS -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            } else {
                input.type = 'password';
                if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
            }
        }

        function handleSendOtpSubmit(form) {
            const btn = document.getElementById('btn-send-otp');
            const spinner = document.getElementById('btn-send-spinner');
            const text = document.getElementById('btn-send-text');
            const icon = document.getElementById('btn-send-icon');
            if (btn) { btn.disabled = true; btn.classList.add('opacity-80', 'cursor-not-allowed'); }
            if (spinner) spinner.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (text) text.textContent = 'Đang gửi mã OTP...';
        }

        function evalPasswordStrength(pwd) {
            const box = document.getElementById('pwd-strength-box');
            const label = document.getElementById('pwd-strength-label');
            const segments = box ? box.querySelectorAll('.auth-strength-segment') : [];
            if (!box || !label || segments.length === 0) return;

            segments.forEach(s => { s.style.background = 'rgba(148, 163, 184, 0.2)'; s.style.opacity = '0.3'; });

            if (!pwd || pwd.length === 0) {
                label.textContent = 'Độ mạnh: Chưa nhập';
                label.className = 'font-bold text-slate-500 uppercase tracking-wider';
                return;
            }

            const hasUpper = /[A-Z]/.test(pwd);
            const hasNumber = /[0-9]/.test(pwd);
            const hasSpecial = /[^A-Za-z0-9]/.test(pwd);
            const isOnlyLower = /^[a-z]+$/.test(pwd);

            if (pwd.length >= 8 && hasUpper && hasNumber && hasSpecial) {
                segments.forEach(s => { s.style.background = '#10b981'; s.style.opacity = '1'; });
                label.textContent = 'Độ mạnh: Rất mạnh';
                label.className = 'font-bold text-emerald-400 uppercase tracking-wider';
            } else if (pwd.length >= 6 && !isOnlyLower && (hasUpper || hasNumber)) {
                segments[0].style.background = '#f59e0b'; segments[0].style.opacity = '1';
                segments[1].style.background = '#f59e0b'; segments[1].style.opacity = '1';
                label.textContent = 'Độ mạnh: Trung bình';
                label.className = 'font-bold text-amber-400 uppercase tracking-wider';
            } else {
                segments[0].style.background = '#ef4444'; segments[0].style.opacity = '1';
                label.textContent = 'Độ mạnh: Yếu';
                label.className = 'font-bold text-rose-400 uppercase tracking-wider';
            }
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast-card toast-${type}`;
            const icon = type === 'success' ? 'fa-circle-check text-emerald-400' :
                         type === 'error' ? 'fa-circle-exclamation text-rose-400' :
                         'fa-circle-info text-indigo-400';
            toast.innerHTML = `
                <i class="fa-solid ${icon} text-lg mt-0.5"></i>
                <div class="flex-grow">
                    <p class="text-xs font-semibold leading-snug toast-message" style="color: #e2e8f0 !important;">${message}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white text-xs" style="color: #94a3b8;">
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

        @if(($step ?? 1) == 2 && !empty($email))
            // Countdown 5 phút hết hạn OTP
            let expireSeconds = {{ max(0, ($expiresAt ?? (time() + 300)) - time()) }};
            if (expireSeconds <= 0) expireSeconds = 300;
            const countdownTimerEl = document.getElementById('countdown-timer');

            const expireInterval = setInterval(() => {
                expireSeconds--;
                if (expireSeconds <= 0) {
                    clearInterval(expireInterval);
                    if (countdownTimerEl) {
                        countdownTimerEl.textContent = '00:00';
                        countdownTimerEl.classList.add('text-rose-400');
                    }
                    showToast('Mã OTP đã hết hạn. Vui lòng bấm gửi lại mã mới.', 'error');
                } else if (countdownTimerEl) {
                    const m = String(Math.floor(expireSeconds / 60)).padStart(2, '0');
                    const s = String(expireSeconds % 60).padStart(2, '0');
                    countdownTimerEl.textContent = `${m}:${s}`;
                }
            }, 1000);

            // Countdown 60s cho nút gửi lại
            let resendSec = 60;
            const btnResend = document.getElementById('btn-resend-otp');
            const resendCountdownEl = document.getElementById('resend-countdown');
            const resendInterval = setInterval(() => {
                resendSec--;
                if (resendSec <= 0) {
                    clearInterval(resendInterval);
                    if (btnResend) {
                        btnResend.disabled = false;
                        btnResend.textContent = 'Gửi lại mã OTP ngay';
                    }
                } else if (resendCountdownEl) {
                    resendCountdownEl.textContent = resendSec;
                }
            }, 1000);

            async function resendForgotOtp() {
                if (btnResend) btnResend.disabled = true;
                try {
                    const res = await fetch("{{ route('password.resendOtp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message || 'Mã OTP mới đã được gửi lại vào email!', 'success');
                        expireSeconds = 300;
                        if (btnResend) btnResend.textContent = 'Đã gửi lại thành công!';
                    } else {
                        showToast(data.message || 'Không thể gửi lại mã OTP. Vui lòng thử lại.', 'error');
                        if (btnResend) btnResend.disabled = false;
                    }
                } catch (e) {
                    showToast('Lỗi mạng kết nối khi gửi lại OTP.', 'error');
                    if (btnResend) btnResend.disabled = false;
                }
            }

            function onOtpInputChange(el) {
                el.value = el.value.replace(/[^0-9]/g, '');
                const errEl = document.getElementById('otp-error-msg');
                if (errEl) errEl.classList.add('hidden');
                el.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/30');
                if (el.value.length === 6) {
                    verifyOtpAndProceed();
                }
            }

            async function verifyOtpAndProceed() {
                const otpInput = document.getElementById('input-otp');
                const errEl = document.getElementById('otp-error-msg');
                const btn = document.getElementById('btn-verify-otp');
                const spinner = document.getElementById('btn-verify-spinner');
                const text = document.getElementById('btn-verify-text');
                const icon = document.getElementById('btn-verify-icon');

                const otpVal = otpInput ? otpInput.value.trim() : '';

                if (!otpVal) {
                    if (errEl) {
                        errEl.textContent = 'Vui lòng nhập đủ 6 chữ số mã OTP xác thực.';
                        errEl.classList.remove('hidden');
                    }
                    if (otpInput) {
                        otpInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
                        otpInput.focus();
                    }
                    showToast('Vui lòng nhập mã OTP 6 số đã được gửi tới email.', 'error');
                    return;
                }

                if (otpVal.length < 6) {
                    if (errEl) {
                        errEl.textContent = 'Mã OTP phải gồm đủ 6 chữ số (bạn mới nhập ' + otpVal.length + '/6 số).';
                        errEl.classList.remove('hidden');
                    }
                    if (otpInput) {
                        otpInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
                        otpInput.focus();
                    }
                    showToast('Vui lòng nhập đủ 6 chữ số của mã OTP.', 'error');
                    return;
                }

                if (btn) { btn.disabled = true; btn.classList.add('opacity-75', 'cursor-not-allowed'); }
                if (spinner) spinner.classList.remove('hidden');
                if (icon) icon.classList.add('hidden');
                if (text) text.textContent = 'Đang kiểm tra mã OTP...';

                try {
                    const res = await fetch("{{ route('password.verifyOtp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        body: JSON.stringify({ otp: otpVal })
                    });

                    const data = await res.json();

                    if (data.success) {
                        const hiddenOtp = document.getElementById('hidden-otp-input');
                        if (hiddenOtp) hiddenOtp.value = otpVal;

                        const sessionSuccessAlert = document.getElementById('session-success-alert');
                        if (sessionSuccessAlert) sessionSuccessAlert.remove();

                        const stageOtp = document.getElementById('stage-otp');
                        const stagePassword = document.getElementById('stage-password');
                        if (stageOtp) stageOtp.classList.add('hidden');
                        if (stagePassword) stagePassword.classList.remove('hidden');

                        showToast('Xác thực OTP thành công! Vui lòng thiết lập mật khẩu mới.', 'success');

                        const pwdInput = document.getElementById('input-password');
                        if (pwdInput) setTimeout(() => pwdInput.focus(), 150);
                    } else {
                        const msg = data.message || 'Mã xác thực OTP không chính xác. Vui lòng kiểm tra lại.';
                        if (errEl) {
                            errEl.textContent = msg;
                            errEl.classList.remove('hidden');
                        }
                        if (otpInput) {
                            otpInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
                            otpInput.focus();
                        }
                        showToast(msg, 'error');
                    }
                } catch (e) {
                    showToast('Lỗi kết nối khi xác thực OTP. Vui lòng thử lại.', 'error');
                } finally {
                    if (btn) { btn.disabled = false; btn.classList.remove('opacity-75', 'cursor-not-allowed'); }
                    if (spinner) spinner.classList.add('hidden');
                    if (icon) icon.classList.remove('hidden');
                    if (text) text.textContent = 'Xác Thực Mã OTP & Tiếp Tục';
                }
            }

            function resetEmailStep() {
                window.location.href = "{{ route('password.forgot') }}?reset=1";
            }

            function backToOtpStage() {
                const stageOtp = document.getElementById('stage-otp');
                const stagePassword = document.getElementById('stage-password');
                if (stagePassword) stagePassword.classList.add('hidden');
                if (stageOtp) stageOtp.classList.remove('hidden');
                const otpInput = document.getElementById('input-otp');
                if (otpInput) setTimeout(() => otpInput.focus(), 100);
            }

            function clearPasswordError(id) {
                const errEl = document.getElementById(id === 'input-password' ? 'password-error-msg' : 'password-confirmation-error-msg');
                if (errEl) errEl.classList.add('hidden');
                const input = document.getElementById(id);
                if (input) input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/30');
            }

            function handleResetPasswordSubmit(e) {
                const pwd = document.getElementById('input-password');
                const pwdConf = document.getElementById('input-password-confirmation');
                const pwdErr = document.getElementById('password-error-msg');
                const confErr = document.getElementById('password-confirmation-error-msg');

                const pwdVal = pwd ? pwd.value : '';
                const confVal = pwdConf ? pwdConf.value : '';

                if (!pwdVal || pwdVal.length < 6) {
                    e.preventDefault();
                    if (pwdErr) {
                        pwdErr.textContent = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
                        pwdErr.classList.remove('hidden');
                    }
                    if (pwd) {
                        pwd.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
                        pwd.focus();
                    }
                    showToast('Vui lòng nhập mật khẩu mới tối thiểu 6 ký tự.', 'error');
                    return false;
                }

                if (pwdVal !== confVal) {
                    e.preventDefault();
                    if (confErr) {
                        confErr.textContent = 'Xác nhận mật khẩu không trùng khớp với mật khẩu mới.';
                        confErr.classList.remove('hidden');
                    }
                    if (pwdConf) {
                        pwdConf.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
                        pwdConf.focus();
                    }
                    showToast('Mật khẩu xác nhận không trùng khớp.', 'error');
                    return false;
                }

                const btn = document.getElementById('btn-submit-reset');
                const spinner = document.getElementById('btn-reset-spinner');
                const icon = document.getElementById('btn-reset-icon');
                const text = document.getElementById('btn-reset-text');
                if (btn) { btn.disabled = true; btn.classList.add('opacity-75', 'cursor-not-allowed'); }
                if (spinner) spinner.classList.remove('hidden');
                if (icon) icon.classList.add('hidden');
                if (text) text.textContent = 'Đang lưu mật khẩu mới...';

                return true;
            }

            document.addEventListener('DOMContentLoaded', function() {
                @if($errors->has('password') || $errors->has('password_confirmation'))
                    const sessionSuccessAlert = document.getElementById('session-success-alert');
                    if (sessionSuccessAlert) sessionSuccessAlert.remove();

                    const stageOtp = document.getElementById('stage-otp');
                    const stagePassword = document.getElementById('stage-password');
                    if (stageOtp && stagePassword) {
                        stageOtp.classList.add('hidden');
                        stagePassword.classList.remove('hidden');
                    }
                @endif
            });
        @endif
    </script>
</body>
</html>
