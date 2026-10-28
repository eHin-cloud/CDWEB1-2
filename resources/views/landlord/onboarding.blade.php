<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng Ký Tài Khoản Chủ Trọ - SmartRoom & Renty</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
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
            background: radial-gradient(circle, rgba(16, 185, 129, 0.14) 0%, rgba(16, 185, 129, 0) 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.68);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
        }

        .glass-panel-subtle {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
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

        .onboarding-input {
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

        .onboarding-input:hover {
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(2, 6, 23, 0.85);
        }

        .onboarding-input:focus {
            border-color: #6366f1;
            background: rgba(2, 6, 23, 0.95);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .onboarding-textarea {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            background: rgba(2, 6, 23, 0.7);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 0.875rem;
            color: #f8fafc;
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            resize: none;
        }

        .onboarding-textarea:hover {
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(2, 6, 23, 0.85);
        }

        .onboarding-textarea:focus {
            border-color: #6366f1;
            background: rgba(2, 6, 23, 0.95);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .chip-button {
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(71, 85, 105, 0.5);
            color: #94a3b8;
            font-size: 0.6875rem;
            font-weight: 600;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }

        .chip-button:hover {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.4);
            color: #c7d2fe;
            transform: translateY(-1px);
        }

        .otp-pill {
            padding: 0.25rem 0.65rem;
            border-radius: 0.5rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.28);
            color: #6ee7b7;
            font-size: 0.6875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .otp-pill:hover {
            background: rgba(16, 185, 129, 0.22);
            border-color: rgba(16, 185, 129, 0.5);
            color: #a7f3d0;
            transform: scale(1.02);
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

        .auth-strength-container {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
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
            max-width: 400px;
            width: calc(100% - 3rem);
        }

        .toast-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-left-width: 4px;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.4);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast-card.show {
            transform: translateX(0);
        }

        .toast-success { border-left-color: #10b981; }
        .toast-error { border-left-color: #ef4444; }
        .toast-info { border-left-color: #6366f1; }

        .radio-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border-width: 2px;
            border-color: #475569;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }
        .radio-dot {
            border-radius: 9999px;
            transform: scale(0);
            opacity: 0;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
        }
        .verification-option {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(51, 65, 85, 0.4);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .verification-option:hover {
            background: rgba(30, 41, 59, 0.7);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .verification-option:has(input:checked) {
            background: rgba(30, 41, 59, 0.95) !important;
            box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.35);
        }
        .verification-option:has(input:checked) .radio-dot,
        .verification-option input:checked ~ div .radio-dot {
            transform: scale(1) !important;
            opacity: 1 !important;
        }
        .verification-option:has(input[value="phone"]:checked) .radio-indicator,
        .verification-option input[value="phone"]:checked ~ div.radio-indicator {
            border-color: #10b981 !important;
        }
        .verification-option:has(input[value="phone"]:checked) {
            border-color: rgba(16, 185, 129, 0.5) !important;
        }
        .verification-option:has(input[value="phone"]:checked) .radio-dot,
        .verification-option input[value="phone"]:checked ~ div .radio-dot {
            background-color: #10b981 !important;
        }
        .verification-option:has(input[value="email"]:checked) .radio-indicator,
        .verification-option input[value="email"]:checked ~ div.radio-indicator {
            border-color: #818cf8 !important;
        }
        .verification-option:has(input[value="email"]:checked) {
            border-color: rgba(99, 102, 241, 0.5) !important;
        }
        .verification-option:has(input[value="email"]:checked) .radio-dot,
        .verification-option input[value="email"]:checked ~ div .radio-dot {
            background-color: #818cf8 !important;
        }
        .email-hint-transition {
            transition: opacity 0.25s ease, max-height 0.25s ease, transform 0.25s ease, margin 0.25s ease;
            overflow: hidden;
        }
        .email-hint-hidden {
            opacity: 0;
            max-height: 0;
            margin-top: 0 !important;
            transform: translateY(-4px);
            pointer-events: none;
        }
        .email-hint-visible {
            opacity: 1;
            max-height: 48px;
            margin-top: 0.375rem !important;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* ===== LIGHT MODE STYLING CHO ĐĂNG KÝ CHỦ TRỌ ===== */
        html.theme-light body, body.theme-light {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.theme-light header .text-white, body.theme-light header .text-white {
            color: #0f172a !important;
        }
        html.theme-light .theme-toggle-button, body.theme-light .theme-toggle-button {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
        }
        html.theme-light .glass-panel, body.theme-light .glass-panel {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.1) !important;
            color: #0f172a !important;
        }
        html.theme-light .glass-panel-subtle, body.theme-light .glass-panel-subtle {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.05) !important;
            color: #0f172a !important;
        }
        html.theme-light h1.text-white, body.theme-light h1.text-white,
        html.theme-light h2.text-white, body.theme-light h2.text-white,
        html.theme-light h4.text-white, body.theme-light h4.text-white,
        html.theme-light .glass-panel-subtle .text-white, body.theme-light .glass-panel-subtle .text-white {
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
        html.theme-light .onboarding-input, body.theme-light .onboarding-input,
        html.theme-light .onboarding-textarea, body.theme-light .onboarding-textarea {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        html.theme-light .onboarding-input:hover, body.theme-light .onboarding-input:hover,
        html.theme-light .onboarding-textarea:hover, body.theme-light .onboarding-textarea:hover {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
        }
        html.theme-light .onboarding-input:focus, body.theme-light .onboarding-input:focus,
        html.theme-light .onboarding-textarea:focus, body.theme-light .onboarding-textarea:focus {
            border-color: #4f46e5 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
        }
        html.theme-light .onboarding-input::placeholder, body.theme-light .onboarding-input::placeholder,
        html.theme-light .onboarding-textarea::placeholder, body.theme-light .onboarding-textarea::placeholder {
            color: #64748b !important;
            opacity: 1 !important;
        }
        html.theme-light .form-input-icon, body.theme-light .form-input-icon {
            color: #64748b !important;
        }
        html.theme-light .verification-option, body.theme-light .verification-option {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
        html.theme-light .verification-option:hover, body.theme-light .verification-option:hover {
            border-color: #94a3b8 !important;
        }
        html.theme-light .verification-option span.text-white, body.theme-light .verification-option span.text-white {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.theme-light .bg-slate-900\/60, body.theme-light .bg-slate-900\/60 {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
        html.theme-light .border-slate-800, body.theme-light .border-slate-800 {
            border-color: #e2e8f0 !important;
        }
        html.theme-light .auth-pwd-toggle, body.theme-light .auth-pwd-toggle {
            color: #64748b !important;
        }
        html.theme-light .auth-pwd-toggle:hover, body.theme-light .auth-pwd-toggle:hover {
            color: #0f172a !important;
        }
        html.theme-light .ambient-mesh-1, html.theme-light .ambient-mesh-2 {
            opacity: 0.3 !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    <!-- Ambient glowing backgrounds -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>

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
            <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                <i class="fa-solid fa-moon" data-theme-icon></i>
            </button>
            <a href="{{ route('renty.user') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-all">
                <i class="fa-solid fa-compass text-slate-400"></i> Khám Phá Renty
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600/90 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i> Đăng Nhập
            </a>
        </div>
    </header>

    <!-- MAIN SECTION -->
    <main class="container mx-auto px-4 sm:px-6 py-6 lg:py-8 flex-grow relative z-10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
            
            <!-- LEFT COLUMN: Value Proposition & Onboarding Steps -->
            <section class="flex flex-col justify-between space-y-6 lg:col-span-5 order-2 lg:order-1">
                <div class="space-y-5">
                    <!-- Back Button -->
                    <div>
                        <a href="{{ route('login') }}" onclick="clearLandlordDraft()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-400 hover:text-white transition-all shadow-sm group">
                            <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1 text-slate-500 group-hover:text-indigo-400"></i>
                            <span>Quay lại đăng nhập</span>
                        </a>
                    </div>

                    <!-- Section Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/25 text-indigo-300 text-xs font-bold uppercase tracking-wider shadow-inner">
                        <i class="fa-solid fa-crown text-amber-400"></i> Cổng Đối Tác Vận Hành Cơ Sở
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                        Số Hóa Cơ Sở Lưu Trú, <br>
                        <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-indigo-400 bg-clip-text text-transparent">Vận Hành &amp; Đón Khách Renty</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                        SmartRoom giúp chủ trọ và đơn vị lưu trú chuyển đổi số toàn diện chỉ trong 60 giây: Quản lý sơ đồ phòng trực quan, chốt số điện nước online và thanh toán VietQR tự động.
                    </p>

                    <!-- 3-STEP ROADMAP -->
                    <div class="space-y-3 pt-1">
                        <div class="glass-panel-subtle rounded-2xl p-3.5 flex items-start gap-3.5 hover:border-indigo-500/30 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-md shadow-emerald-500/20 mt-0.5">
                                1
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Khởi Tạo Nhanh (60 giây)</h4>
                                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Đăng ký thông tin quản trị cơ bản để nhận bàn giao Dashboard quản lý cơ sở ngay lập tức.</p>
                            </div>
                        </div>

                        <div class="glass-panel-subtle rounded-2xl p-3.5 flex items-start gap-3.5 hover:border-indigo-500/30 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-md shadow-indigo-500/20 mt-0.5">
                                2
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Số Hóa Cơ Sở &amp; Phòng Trọ</h4>
                                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Thiết lập sơ đồ tòa nhà, buồng phòng, đơn giá điện nước và quản lý hợp đồng cư dân trực tuyến.</p>
                            </div>
                        </div>

                        <div class="glass-panel-subtle rounded-2xl p-3.5 flex items-start gap-3.5 hover:border-indigo-500/30 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-md shadow-amber-500/20 mt-0.5">
                                3
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Tích Xanh &amp; Thu Tiền Tự Động</h4>
                                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Gắn huy hiệu Tích Xanh uy tín, ưu tiên hiển thị trên Renty và nhận tiền thuê qua VietQR tiện lợi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATS COUNTER -->
                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="glass-panel-subtle rounded-2xl p-3 text-center border border-slate-800/80">
                        <span class="text-lg sm:text-xl font-black text-white block">12.5K+</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Khách tìm phòng</span>
                    </div>
                    <div class="glass-panel-subtle rounded-2xl p-3 text-center border border-slate-800/80">
                        <span class="text-lg sm:text-xl font-black text-emerald-400 block">99.2%</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Tỷ lệ lấp đầy</span>
                    </div>
                    <div class="glass-panel-subtle rounded-2xl p-3 text-center border border-slate-800/80">
                        <span class="text-lg sm:text-xl font-black text-indigo-400 block">0 VNĐ</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mt-0.5 block">Phí khởi tạo</span>
                    </div>
                </div>

            </section>

            <!-- RIGHT COLUMN: Registration Form Card -->
            <section class="lg:col-span-7 order-1 lg:order-2 flex flex-col justify-center">
                <div class="glass-panel rounded-3xl p-6 sm:p-8 lg:p-9 relative overflow-hidden border border-indigo-500/20 shadow-2xl">
                    
                    <!-- Form Header -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-house-chimney-user"></i> Cổng Đăng Ký Cơ Sở Đối Tác
                            </span>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/25 text-indigo-300">
                                Miễn Phí Khởi Tạo
                            </span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 tracking-tight">Đăng Ký Tài Khoản Cơ Sở</h2>
                        <p class="text-xs text-slate-400 mt-1">Chỉ 60 giây — điền thông tin cơ bản để vào Dashboard ngay. Hồ sơ pháp lý hoàn thiện sau trong Dashboard.</p>

                        <!-- STEPPER 3 BƯỚC PHẲNG (FLEXBOX CHUẨN, KHÔNG DÙNG MARGIN ÂM) -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                            <!-- Bước 1: Đang làm -->
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-600 border border-indigo-400 flex items-center justify-center text-white font-bold text-[11px] shadow-md shadow-indigo-600/40 shrink-0">
                                    1
                                </div>
                                <span class="text-xs font-bold text-indigo-300 whitespace-nowrap">ĐK Cơ Bản</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-indigo-500/40 rounded-full mx-1"></div>
                            <!-- Bước 2 -->
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 font-bold text-[11px] shrink-0">
                                    2
                                </div>
                                <span class="text-xs font-medium text-slate-400 whitespace-nowrap">Xác Minh OTP</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-slate-800 rounded-full mx-1"></div>
                            <!-- Bước 3 -->
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-500 font-bold text-[11px] shrink-0">
                                    3
                                </div>
                                <span class="hidden sm:inline text-xs font-medium text-slate-500 whitespace-nowrap">Dashboard</span>
                            </div>
                        </div>
                    </div>

                    <!-- ERROR ALERT -->
                    @if($errors->any())
                        <div class="mb-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-3.5 text-xs text-rose-200">
                            <div class="flex items-center gap-2 font-bold mb-1 text-rose-300">
                                <i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra lại thông tin:
                            </div>
                            <ul class="list-disc pl-5 space-y-0.5 text-[11px]">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- ONBOARDING FORM -->
                    <form id="landlord-onboarding-form" method="POST" action="{{ route('landlord.register.store', [], false) }}" class="space-y-4" novalidate>
                        @csrf

                        <!-- KHỐI 1: CÁC TRƯỜNG ĐĂNG KÝ -->
                        <div class="rounded-2xl border border-indigo-500/20 bg-slate-900/60 p-4 sm:p-5 space-y-4">
                            
                            <!-- 1. Tài khoản đăng nhập & Họ và tên chủ cơ sở -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="input-username">
                                        Tài khoản đăng nhập <span class="text-rose-400 font-bold">*</span>
                                    </label>
                                    <div class="form-input-shell">
                                        <i class="fa-solid fa-circle-user form-input-icon"></i>
                                        <input type="text" name="username" id="input-username" value="{{ old('username', $regData['username'] ?? '') }}" required maxlength="50" placeholder="admin_chutro" class="onboarding-input font-mono @error('username') border-rose-500 ring-2 ring-rose-500/20 @enderror" autocomplete="username" onblur="validateLandlordUsernameBlur(this)">
                                    </div>
                                    <div id="landlord-username-feedback" class="mt-1 text-[11px] {{ $errors->has('username') ? 'text-rose-400 font-semibold block' : 'hidden' }}">
                                        @error('username')
                                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}</span>
                                        @else
                                            <span id="landlord-username-msg" class="flex items-center gap-1.5"></span>
                                        @enderror
                                    </div>
                                    @if(!$errors->has('username'))
                                        <p id="landlord-username-hint" class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-info text-[9px]"></i>
                                            3–50 ký tự, chữ/số/gạch dưới (đăng nhập bằng Username, SĐT hoặc Email)
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="input-fullname">
                                        Họ và tên chủ cơ sở <span class="text-rose-400 font-bold">*</span>
                                    </label>
                                    <div class="form-input-shell">
                                        <i class="fa-solid fa-user-tie form-input-icon"></i>
                                        <input type="text" name="full_name" id="input-fullname" value="{{ old('full_name', $regData['full_name'] ?? '') }}" required maxlength="120" placeholder="Nguyễn Văn Hoàng" class="onboarding-input @error('full_name') border-rose-500 ring-2 ring-rose-500/20 @enderror" autocomplete="name">
                                    </div>
                                    @error('full_name')
                                        <p class="text-xs text-rose-400 font-semibold mt-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- 2 & 3: Số điện thoại & Email -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="input-phone">
                                        Số điện thoại liên hệ <span class="text-rose-400 font-bold">*</span>
                                    </label>
                                    <div class="form-input-shell">
                                        <i class="fa-solid fa-phone form-input-icon"></i>
                                        <input type="tel" name="phone" id="input-phone" value="{{ old('phone', $regData['phone'] ?? '') }}" required maxlength="10" pattern="^0[0-9]{9}$" placeholder="0988123456" class="onboarding-input @error('phone') border-rose-500 ring-2 ring-rose-500/20 @enderror" autocomplete="tel" oninput="validateLandlordPhoneInput(this)" onblur="validateLandlordPhoneBlur(this)">
                                    </div>
                                    <div id="landlord-phone-feedback" class="mt-1 text-[11px] {{ $errors->has('phone') ? 'text-rose-400 font-semibold block' : 'hidden' }}">
                                        @error('phone')
                                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}</span>
                                        @else
                                            <span id="landlord-phone-msg" class="flex items-center gap-1.5"></span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="input-email">
                                        Địa chỉ Email (tùy chọn)
                                    </label>
                                    <div class="form-input-shell">
                                        <i class="fa-solid fa-envelope form-input-icon"></i>
                                        <input type="email" name="email" id="input-email" value="{{ old('email', $regData['email'] ?? '') }}" placeholder="chutro@example.com" class="onboarding-input @error('email') border-rose-500 ring-2 ring-rose-500/20 @enderror" autocomplete="email" oninput="handleLandlordEmailInput(this)" onblur="handleLandlordEmailBlur(this)">
                                    </div>
                                    <div id="landlord-email-feedback" class="mt-1 text-[11px] {{ $errors->has('email') ? 'text-rose-400 font-semibold block' : 'hidden' }}">
                                        @error('email')
                                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}</span>
                                        @else
                                            <span id="landlord-email-msg" class="flex items-center gap-1.5"></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Phương thức nhận mã OTP (Segmented Control gọn gàng) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-300">
                                        Phương thức nhận OTP xác thực <span class="text-rose-400 font-bold">*</span>
                                    </label>
                                    <span class="text-[10px] text-slate-400 font-medium">Chọn 1 trong 2 kênh</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 p-1.5 rounded-2xl bg-slate-950/70 border border-slate-800" id="verification-method-group">
                                    <label class="verification-option relative flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-transparent cursor-pointer transition-all duration-200" for="method-phone" id="landlord-pill-phone">
                                        <input type="radio" name="verification_method" id="method-phone" value="phone" {{ old('verification_method', $regData['verification_method'] ?? 'phone') == 'phone' ? 'checked' : '' }} class="hidden" onchange="onLandlordMethodChange('phone')">
                                        <div class="radio-indicator w-4 h-4">
                                            <div class="radio-dot w-2 h-2"></div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-phone text-emerald-400 text-xs"></i>
                                            <span class="text-xs font-bold text-white">Qua SĐT</span>
                                        </div>
                                    </label>
                                    <label class="verification-option relative flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-transparent cursor-pointer transition-all duration-200" for="method-email" id="landlord-pill-email">
                                        <input type="radio" name="verification_method" id="method-email" value="email" {{ old('verification_method', $regData['verification_method'] ?? '') == 'email' ? 'checked' : '' }} class="hidden" onchange="onLandlordMethodChange('email')">
                                        <div class="radio-indicator w-4 h-4">
                                            <div class="radio-dot w-2 h-2"></div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-envelope text-indigo-400 text-xs"></i>
                                            <span class="text-xs font-bold text-white">Qua Email</span>
                                        </div>
                                    </label>
                                </div>
                                <div id="landlord-email-method-hint" class="email-hint-transition text-[11px] text-amber-400/90 flex items-center gap-1.5 email-hint-hidden" aria-live="polite">
                                    <i id="landlord-email-method-hint-icon" class="fa-solid fa-circle-info text-[10px] shrink-0 text-amber-400"></i>
                                    <span id="landlord-email-method-hint-text">Vui lòng nhập địa chỉ email ở trên để kích hoạt phương thức nhận OTP qua Email.</span>
                                </div>
                                @error('verification_method')
                                    <p class="text-xs text-rose-400 font-semibold mt-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- 4. Mật khẩu đăng nhập -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="input-password">
                                    Mật khẩu đăng nhập <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <div class="form-input-shell">
                                    <i class="fa-solid fa-lock form-input-icon"></i>
                                    <input type="password" name="password" id="input-password" value="{{ old('password', $regData['password'] ?? '') }}" required minlength="6" maxlength="100" oninput="evalPasswordStrength(this.value)" placeholder="Tối thiểu 6 ký tự" class="onboarding-input pr-10 @error('password') border-rose-500 ring-2 ring-rose-500/20 @enderror" autocomplete="new-password">
                                    <button type="button" onclick="togglePasswordVisibility('input-password', this)" class="auth-pwd-toggle" title="Hiện / Ẩn mật khẩu" aria-label="Hiện / Ẩn mật khẩu">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-rose-400 font-semibold mt-1.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                                    </p>
                                @enderror
                                <!-- Strength bar + label -->
                                <div id="pwd-strength-box" class="auth-strength-container mt-2">
                                    <div class="flex items-center gap-2">
                                        <div class="auth-strength-track flex-1">
                                            <div class="auth-strength-segment"></div>
                                            <div class="auth-strength-segment"></div>
                                            <div class="auth-strength-segment"></div>
                                        </div>
                                        <span id="pwd-strength-label" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Độ mạnh: Chưa nhập</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú hoàn thiện hồ sơ sau -->
                        <div class="rounded-xl border border-indigo-500/20 bg-indigo-500/5 p-3 flex items-start gap-2.5 text-xs text-slate-400">
                            <i class="fa-solid fa-circle-info text-indigo-400 shrink-0 mt-0.5"></i>
                            <span>Sau khi xác minh OTP, bạn vào thẳng Dashboard để thiết lập cơ sở, phòng và tiện ích. Hồ sơ pháp lý có thể hoàn thiện sau.</span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-emerald-600 hover:from-indigo-500 hover:to-emerald-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer mt-1">
                            <span>Đăng Ký Cơ Sở &amp; Nhận Mã OTP</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>

                        <!-- Footer Links & Back to Login Button -->
                        <div class="pt-3 border-t border-slate-800/80 space-y-3 text-center">
                            <a href="{{ route('login') }}" onclick="clearLandlordDraft()" class="w-full py-2.5 px-4 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white font-semibold text-xs transition-all flex items-center justify-center gap-2 group shadow-sm">
                                <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                                <span>Quay lại trang đăng nhập</span>
                            </a>
                            <div class="flex items-center justify-center gap-3 text-xs text-slate-400 flex-wrap">
                                <span>Đã có tài khoản? <a href="{{ route('login') }}" onclick="clearLandlordDraft()" class="font-bold text-indigo-400 hover:text-indigo-300 ml-0.5 underline underline-offset-2">Đăng nhập</a></span>
                                <span class="text-slate-600">•</span>
                                <span>Tìm phòng? <a href="{{ route('user.createUser') }}" class="font-bold text-emerald-400 hover:text-emerald-300 ml-0.5 underline underline-offset-2">Đăng ký khách thuê</a></span>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- OTP verification is now handled by a dedicated page (landlord/verify.blade.php) -->

    <!-- FOOTER -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-900 mt-12">
        &copy; 2026 SmartRoom &amp; Renty. Nền tảng quản lý nhà trọ và kết nối khách thuê số 1.
    </footer>

    <!-- INTERACTIVE SCRIPTS -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
        }

        function evalPasswordStrength(pwd) {
            const box = document.getElementById('pwd-strength-box');
            const label = document.getElementById('pwd-strength-label');
            const segments = box ? box.querySelectorAll('.auth-strength-segment') : [];
            if (!box || !label || segments.length === 0) return;

            // Reset all segments
            segments.forEach(s => { s.style.background = 'rgba(148, 163, 184, 0.2)'; s.style.opacity = '0.3'; });

            if (!pwd || pwd.length === 0) {
                label.textContent = 'Độ mạnh: Chưa nhập';
                label.className = 'text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap';
                return;
            }

            const hasUpper = /[A-Z]/.test(pwd);
            const hasNumber = /[0-9]/.test(pwd);
            const hasSpecial = /[^A-Za-z0-9]/.test(pwd);
            const isOnlyLower = /^[a-z]+$/.test(pwd);

            if (pwd.length >= 8 && hasUpper && hasNumber && hasSpecial) {
                // Mạnh — xanh lá (có chữ hoa + số + ký tự đặc biệt, từ 8 ký tự trở lên)
                segments.forEach(s => { s.style.background = '#10b981'; s.style.opacity = '1'; });
                label.textContent = 'Độ mạnh: Mạnh';
                label.className = 'text-[10px] font-bold text-emerald-400 uppercase tracking-wider whitespace-nowrap';
            } else if (pwd.length >= 6 && !isOnlyLower && (hasUpper || hasNumber)) {
                // Trung bình — vàng (từ 6 ký tự trở lên, có chữ hoa hoặc số)
                segments[0].style.background = '#f59e0b';
                segments[0].style.opacity = '1';
                segments[1].style.background = '#f59e0b';
                segments[1].style.opacity = '1';
                segments[2].style.background = 'rgba(148, 163, 184, 0.2)';
                segments[2].style.opacity = '0.2';
                label.textContent = 'Độ mạnh: Trung bình';
                label.className = 'text-[10px] font-bold text-amber-400 uppercase tracking-wider whitespace-nowrap';
            } else {
                // Yếu — đỏ (dưới 6 ký tự hoặc chỉ chữ thường)
                segments[0].style.background = '#ef4444';
                segments[0].style.opacity = '1';
                segments[1].style.background = 'rgba(148, 163, 184, 0.2)';
                segments[1].style.opacity = '0.2';
                segments[2].style.background = 'rgba(148, 163, 184, 0.2)';
                segments[2].style.opacity = '0.2';
                label.textContent = 'Độ mạnh: Yếu';
                label.className = 'text-[10px] font-bold text-rose-400 uppercase tracking-wider whitespace-nowrap';
            }
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

        function clearLandlordDraft() {
            ['input-username', 'input-fullname', 'input-phone', 'input-email', 'input-password'].forEach(id => {
                sessionStorage.removeItem('landlord_reg_' + id);
            });
            sessionStorage.removeItem('landlord_reg_method');
        }

        @if(!empty($isReset))
            clearLandlordDraft();
        @endif

        // =========================================================
        // KIỂM TRA USERNAME, SĐT VÀ EMAIL REAL-TIME KHI BLUR (CHỐNG TRÙNG)
        // =========================================================
        function validateLandlordUsernameBlur(input) {
            const val = input.value.trim();
            const feedback = document.getElementById('landlord-username-feedback');
            const msg = document.getElementById('landlord-username-msg');
            const hint = document.getElementById('landlord-username-hint');
            if (!feedback || !msg) return;

            if (!val) {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Vui lòng nhập tài khoản đăng nhập (3–50 ký tự)';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (hint) hint.classList.add('hidden');
                return;
            }

            if (!/^[a-zA-Z0-9_-]{3,50}$/.test(val)) {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Tên tài khoản chỉ gồm chữ cái, số, gạch dưới (3–50 ký tự)';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                if (hint) hint.classList.add('hidden');
                return;
            }

            // Gọi API kiểm tra trùng Username real-time
            fetch('/api/auth/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ type: 'username', value: val })
            })
            .then(res => res.json())
            .then(data => {
                if (data.available === false) {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> ' + data.message;
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                    if (hint) hint.classList.add('hidden');
                } else {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-emerald-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-check text-[10px]"></i> ' + data.message;
                    input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                    if (hint) hint.classList.remove('hidden');
                }
            })
            .catch(() => {});
        }
        function validateLandlordPhoneInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            const val = input.value;
            const feedback = document.getElementById('landlord-phone-feedback');
            const msg = document.getElementById('landlord-phone-msg');
            if (!feedback || !msg) return;

            if (val.length > 0 && val[0] !== '0') {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Số điện thoại phải bắt đầu bằng số 0';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
            } else if (val.length < 10) {
                feedback.className = 'hidden';
                input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20', 'border-emerald-500', 'ring-2', 'ring-emerald-500/20');
            } else if (val.length === 10) {
                validateLandlordPhoneBlur(input);
            }
        }

        function validateLandlordPhoneBlur(input) {
            const val = input.value.trim();
            const feedback = document.getElementById('landlord-phone-feedback');
            const msg = document.getElementById('landlord-phone-msg');
            if (!feedback || !msg) return;

            if (!val) {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Vui lòng nhập số điện thoại (10 chữ số)';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                return;
            }

            if (!/^0[0-9]{9}$/.test(val)) {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Số điện thoại phải gồm đúng 10 chữ số bắt đầu bằng số 0';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                return;
            }

            // Gọi API kiểm tra trùng SĐT real-time
            fetch('/api/auth/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ phone: val })
            })
            .then(res => res.json())
            .then(data => {
                if (data.available === false) {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> ' + data.message;
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                } else {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-emerald-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-check text-[10px]"></i> ' + data.message;
                    input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                }
            })
            .catch(() => {});
        }

        let landlordEmailDebounceTimer = null;

        function isLandlordEmailValidFormat(email) {
            return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.trim());
        }

        function updateLandlordEmailMethodState() {
            const emailInput = document.getElementById('input-email');
            const pillEmail = document.getElementById('landlord-pill-email');
            const pillPhone = document.getElementById('landlord-pill-phone');
            const radioEmail = document.getElementById('method-email');
            const radioPhone = document.getElementById('method-phone');
            const hint = document.getElementById('landlord-email-method-hint');
            const hintText = document.getElementById('landlord-email-method-hint-text');
            const hintIcon = document.getElementById('landlord-email-method-hint-icon');

            if (!emailInput || !pillEmail || !radioEmail) return;
            const val = emailInput.value.trim();
            const isValid = isLandlordEmailValidFormat(val);

            if (isValid) {
                // Email đúng định dạng hợp lệ: Enable phương thức nhận qua Email
                radioEmail.disabled = false;
                pillEmail.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                pillEmail.removeAttribute('title');

                // Ẩn tooltip mượt mà
                if (hint) {
                    hint.className = 'email-hint-transition text-[11px] text-amber-400/90 flex items-center gap-1.5 email-hint-hidden';
                }
            } else {
                // Email chưa hợp lệ hoặc rỗng: Disable phương thức nhận qua Email
                const wasChecked = radioEmail.checked;
                radioEmail.disabled = true;
                pillEmail.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');

                // Cập nhật nội dung tooltip chính xác
                if (!val) {
                    if (hintText) hintText.textContent = 'Vui lòng nhập địa chỉ email ở trên để kích hoạt phương thức nhận OTP qua Email.';
                    if (hintIcon) hintIcon.className = 'fa-solid fa-circle-info text-[10px] shrink-0 text-amber-400';
                    if (hint) hint.className = 'email-hint-transition text-[11px] text-amber-400/90 flex items-center gap-1.5 email-hint-visible';
                    pillEmail.setAttribute('title', 'Vui lòng nhập địa chỉ email để dùng phương thức này');
                } else {
                    if (hintText) hintText.textContent = 'Định dạng email chưa hợp lệ';
                    if (hintIcon) hintIcon.className = 'fa-solid fa-circle-exclamation text-[10px] shrink-0 text-rose-400';
                    if (hint) hint.className = 'email-hint-transition text-[11px] text-rose-400/90 flex items-center gap-1.5 email-hint-visible';
                    pillEmail.setAttribute('title', 'Định dạng email chưa hợp lệ');
                }

                // AUTO-SWITCH: Nếu đang chọn "Qua Email" mà email bị sửa/xóa không hợp lệ -> Tự chuyển về SĐT
                if (wasChecked) {
                    if (radioPhone) {
                        radioPhone.checked = true;
                        if (typeof onLandlordMethodChange === 'function') {
                            onLandlordMethodChange('phone');
                        }

                        // Hiệu ứng highlight mượt trên ô SĐT
                        if (pillPhone) {
                            pillPhone.classList.add('ring-2', 'ring-emerald-400/60');
                            setTimeout(() => pillPhone.classList.remove('ring-2', 'ring-emerald-400/60'), 600);
                        }

                        // Thông báo nhỏ
                        if (typeof showToast === 'function') {
                            showToast('Đã chuyển sang xác minh qua SĐT vì email không hợp lệ', 'info');
                        }
                    }
                }
            }
        }

        function handleLandlordEmailInput(input) {
            // Lắng nghe trực tiếp sự kiện input với debounce 300ms
            clearTimeout(landlordEmailDebounceTimer);
            landlordEmailDebounceTimer = setTimeout(() => {
                updateLandlordEmailMethodState();
            }, 300);
        }

        function handleLandlordEmailBlur(input) {
            clearTimeout(landlordEmailDebounceTimer);
            updateLandlordEmailMethodState();
            const val = input.value.trim();
            const feedback = document.getElementById('landlord-email-feedback');
            const msg = document.getElementById('landlord-email-msg');
            if (!feedback || !msg) return;

            if (!val) {
                feedback.className = 'hidden';
                input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20', 'border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                return;
            }

            if (!isLandlordEmailValidFormat(val)) {
                feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> Địa chỉ email không đúng định dạng hợp lệ';
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                return;
            }

            // Gọi API kiểm tra trùng Email real-time
            fetch('/api/auth/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ email: val })
            })
            .then(res => res.json())
            .then(data => {
                if (data.available === false) {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-rose-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-exclamation text-[10px]"></i> ' + data.message;
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                } else {
                    feedback.className = 'mt-1 text-[11px] font-semibold text-emerald-400 block';
                    msg.innerHTML = '<i class="fa-solid fa-circle-check text-[10px]"></i> ' + data.message;
                    input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                }
            })
            .catch(() => {});
        }

        function onLandlordMethodChange(method) {
            const emailInput = document.getElementById('input-email');
            if (method === 'email') {
                if (emailInput && !emailInput.value.trim()) {
                    emailInput.focus();
                    showToast('Vui lòng nhập email để nhận mã OTP xác minh', 'info');
                }
            }
        }

        // Client-side validation for required fields
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('landlord-onboarding-form') || document.querySelector('form[action="{{ route('landlord.register.store', [], false) }}"]');
            if (!form) return;

            updateLandlordEmailMethodState();

            const requiredFields = [
                { id: 'input-username', label: 'Tài khoản đăng nhập' },
                { id: 'input-fullname', label: 'Họ và tên chủ trọ' },
                { id: 'input-phone', label: 'Số điện thoại' },
                { id: 'input-password', label: 'Mật khẩu đăng nhập' },
            ];

            // Tự động kiểm tra độ mạnh mật khẩu nếu đã có giá trị sẵn từ session
            const pwdInput = document.getElementById('input-password');
            if (pwdInput && pwdInput.value) {
                evalPasswordStrength(pwdInput.value);
            }

            @if(empty($isReset))
            // Hỗ trợ lưu nháp vào sessionStorage để giữ dữ liệu tuyệt đối khi back/reload
            const formFields = ['input-username', 'input-fullname', 'input-phone', 'input-email', 'input-password'];
            formFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const saved = sessionStorage.getItem('landlord_reg_' + id);
                    if (!el.value && saved) {
                        el.value = saved;
                        if (id === 'input-password') evalPasswordStrength(saved);
                        if (id === 'input-email') updateLandlordEmailMethodState();
                    }
                    el.addEventListener('input', () => {
                        sessionStorage.setItem('landlord_reg_' + id, el.value);
                    });
                }
            });

            const savedMethod = sessionStorage.getItem('landlord_reg_method');
            if (savedMethod && !form.querySelector('input[name="verification_method"]:checked')) {
                const radio = form.querySelector(`input[name="verification_method"][value="${savedMethod}"]`);
                if (radio) radio.checked = true;
            }
            form.querySelectorAll('input[name="verification_method"]').forEach(r => {
                r.addEventListener('change', () => {
                    sessionStorage.setItem('landlord_reg_method', r.value);
                });
            });
            @else
            // Khi reset: chỉ lắng nghe input để lưu nháp cho phiên mới
            const formFields = ['input-username', 'input-fullname', 'input-phone', 'input-email', 'input-password'];
            formFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', () => {
                        sessionStorage.setItem('landlord_reg_' + id, el.value);
                    });
                }
            });
            form.querySelectorAll('input[name="verification_method"]').forEach(r => {
                r.addEventListener('change', () => {
                    sessionStorage.setItem('landlord_reg_method', r.value);
                });
            });
            @endif

            form.addEventListener('submit', function(e) {
                // Helper: add inline error below a field
                function addInlineError(el, message) {
                    if (!el) return;
                    const wrapper = el.closest('.form-input-shell')?.parentElement || el.parentElement;
                    if (!wrapper) return;
                    const err = document.createElement('p');
                    err.className = 'inline-field-error';
                    err.style.cssText = 'color:#f87171;font-size:11px;font-weight:600;margin-top:4px;display:flex;align-items:center;gap:4px;';
                    err.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>' + message;
                    wrapper.appendChild(err);
                }

                // Clear all previous inline errors and highlights
                form.querySelectorAll('.inline-field-error').forEach(el => el.remove());
                requiredFields.forEach(f => {
                    const el = document.getElementById(f.id);
                    if (el) { el.style.borderColor = ''; el.style.boxShadow = ''; }
                });
                ['input-username', 'input-email', 'input-phone'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) { el.style.borderColor = ''; el.style.boxShadow = ''; }
                });
                const vmGroup = document.getElementById('verification-method-group');
                if (vmGroup) {
                    vmGroup.querySelectorAll('.verification-option').forEach(opt => {
                        opt.style.borderColor = ''; opt.style.boxShadow = '';
                    });
                }

                let hasError = false;

                // Check required text fields
                requiredFields.forEach(f => {
                    const el = document.getElementById(f.id);
                    if (el && !el.value.trim()) {
                        hasError = true;
                        el.style.borderColor = '#ef4444';
                        el.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                        addInlineError(el, 'Vui lòng nhập ' + f.label.toLowerCase());
                    }
                });

                // Check username format
                const userEl = document.getElementById('input-username');
                if (userEl && userEl.value.trim() && !/^[a-zA-Z0-9_-]{3,50}$/.test(userEl.value.trim())) {
                    hasError = true;
                    userEl.style.borderColor = '#ef4444';
                    userEl.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                    addInlineError(userEl, 'Tên tài khoản 3–50 ký tự, chỉ gồm chữ cái, số, gạch dưới');
                }

                // Check phone format
                const phoneEl = document.getElementById('input-phone');
                if (phoneEl && phoneEl.value.trim() && !/^0\d{9}$/.test(phoneEl.value.trim())) {
                    hasError = true;
                    phoneEl.style.borderColor = '#ef4444';
                    phoneEl.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                    addInlineError(phoneEl, 'Số điện thoại phải gồm đúng 10 chữ số (bắt đầu bằng 0)');
                }

                // Check verification method radio
                const verificationSelected = form.querySelector('input[name="verification_method"]:checked');
                if (!verificationSelected && vmGroup) {
                    hasError = true;
                    vmGroup.querySelectorAll('.verification-option').forEach(opt => {
                        opt.style.borderColor = '#ef4444';
                        opt.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.15)';
                    });
                    const errP = document.createElement('p');
                    errP.className = 'inline-field-error';
                    errP.style.cssText = 'color:#f87171;font-size:11px;font-weight:600;margin-top:6px;display:flex;align-items:center;gap:4px;';
                    errP.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>Vui lòng chọn phương thức xác minh';
                    vmGroup.parentElement.appendChild(errP);
                }

                // If verification method is email, check email field
                if (verificationSelected && verificationSelected.value === 'email') {
                    const emailEl = document.getElementById('input-email');
                    if (emailEl && !emailEl.value.trim()) {
                        hasError = true;
                        emailEl.style.borderColor = '#ef4444';
                        emailEl.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                        addInlineError(emailEl, 'Bạn chọn xác minh Email, vui lòng nhập email');
                    } else if (emailEl && !/\S+@\S+\.\S+/.test(emailEl.value.trim())) {
                        hasError = true;
                        emailEl.style.borderColor = '#ef4444';
                        emailEl.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                        addInlineError(emailEl, 'Địa chỉ email không đúng định dạng');
                    }
                }

                if (hasError) {
                    e.preventDefault();
                    const firstErr = form.querySelector('.inline-field-error');
                    if (firstErr) {
                        firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }
                // Tất cả hợp lệ → submit form → server gửi OTP → chuyển sang verify.blade.php
            });

            // Remove error highlight + inline error when user starts typing
            function clearFieldError(el) {
                if (!el) return;
                el.style.borderColor = '';
                el.style.boxShadow = '';
                const wrapper = el.closest('.form-input-shell')?.parentElement || el.parentElement;
                if (wrapper) {
                    const err = wrapper.querySelector('.inline-field-error');
                    if (err) err.remove();
                }
            }
            requiredFields.forEach(f => {
                const el = document.getElementById(f.id);
                if (el) {
                    el.addEventListener('input', function() {
                        if (this.value.trim()) clearFieldError(this);
                    });
                }
            });
            // Also clear on email/phone input
            ['input-email', 'input-phone'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() {
                        if (this.value.trim()) clearFieldError(this);
                    });
                }
            });

            // Radio change: clear verification method error
            const radios = form.querySelectorAll('input[name="verification_method"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const vmGroup = document.getElementById('verification-method-group');
                    if (vmGroup) {
                        vmGroup.querySelectorAll('.verification-option').forEach(opt => {
                            opt.style.borderColor = '';
                            opt.style.boxShadow = '';
                        });
                        const err = vmGroup.parentElement.querySelector('.inline-field-error');
                        if (err) err.remove();
                    }
                });
            });

            // Khởi tạo trạng thái radio nhận OTP theo email hiện tại
            updateLandlordEmailMethodState();
        });

        @if(session('success'))
            showToast(@json(session('success')), "success");
        @endif
        @if(session('error'))
            showToast(@json(session('error')), "error");
        @endif
    </script>
</body>
</html>
