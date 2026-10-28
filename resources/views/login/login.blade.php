@php
    $page = $page ?? 'login';
    $isAdminPage = Auth::check() && (in_array($page, ['list', 'read', 'update']) || ($page === 'create' && Auth::user()->isAdmin()));
    $titles = [
        'login' => 'Đăng Nhập - SmartRoom & Renty',
        'create' => 'Đăng Ký - SmartRoom & Renty',
        'list' => 'Danh Sách Quản Trị Viên - SmartRoom & Renty',
        'read' => 'Chi Tiết Quản Trị Viên - SmartRoom & Renty',
        'update' => 'Sửa Thông Tin Quản Trị Viên - SmartRoom & Renty',
    ];
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $titles[$page] ?? $titles['login'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                } 
            } 
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @if($isAdminPage)
        <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @endif
    
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        /* Toast Notifications */
        .toast-container{position:fixed;top:1.25rem;right:1.25rem;z-index:99999;display:flex;flex-direction:column;gap:.625rem;max-width:380px;width:calc(100% - 2.5rem);pointer-events:none}
        .toast-card{background:#0f172a;border:1px solid #1e293b;border-left:3px solid #10b981;padding:.875rem 1rem;border-radius:.875rem;box-shadow:0 20px 40px -10px rgba(0,0,0,.6);display:flex;align-items:flex-start;gap:.75rem;pointer-events:auto;transform:translateX(120%);opacity:0;transition:all .35s cubic-bezier(.16,1,.3,1)}
        .toast-card.show{transform:translateX(0);opacity:1}
        .toast-success{border-left-color:#10b981}.toast-error{border-left-color:#ef4444}.toast-info{border-left-color:#6366f1}
        .toast-card h4{color:#ffffff;font-size:.8125rem;font-weight:700}
        .toast-card p{color:#94a3b8;font-size:.75rem;font-weight:500;line-height:1.4}

        /* Phối nền Bên Xanh Bên Đen */
        .split-ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background: #060911;
            overflow: hidden;
        }
        .glow-sphere-left-green {
            position: absolute;
            top: 50%;
            left: -12%;
            transform: translateY(-50%);
            width: 850px;
            height: 1000px;
            border-radius: 9999px;
            background: radial-gradient(circle at 45% 50%, rgba(16, 185, 129, 0.35) 0%, rgba(5, 150, 105, 0.18) 40%, rgba(4, 47, 46, 0.06) 70%, transparent 85%);
            filter: blur(75px);
        }
        .glow-sphere-right-dark {
            position: absolute;
            top: 50%;
            right: -12%;
            transform: translateY(-50%);
            width: 850px;
            height: 1000px;
            border-radius: 9999px;
            background: radial-gradient(circle at 55% 50%, rgba(79, 70, 229, 0.18) 0%, rgba(30, 27, 75, 0.25) 40%, rgba(15, 23, 42, 0.9) 70%, transparent 85%);
            filter: blur(80px);
        }

        /* Cyber-Grid Blueprint Texture */
        .cyber-grid-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 26px 26px;
            mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, #000 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, #000 30%, transparent 80%);
        }

        /* Thẻ Card Kính Mờ (Glass Luxe - Khung viền bao quanh toàn diện) */
        .saas-card {
            background: rgba(13, 19, 33, 0.88);
            backdrop-filter: blur(28px) saturate(190%);
            -webkit-backdrop-filter: blur(28px) saturate(190%);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.05), 0 30px 80px -20px rgba(0, 0, 0, 0.8), inset 0 1px 0 0 rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
        }
        .saas-card.role-guest-mode {
            border-color: rgba(16, 185, 129, 0.45);
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.25), 0 30px 80px -20px rgba(16, 185, 129, 0.2), inset 0 1px 0 0 rgba(16, 185, 129, 0.3);
        }
        .saas-card.role-landlord-mode {
            border-color: rgba(129, 140, 248, 0.5);
            box-shadow: 0 0 0 1px rgba(129, 140, 248, 0.25), 0 30px 80px -20px rgba(99, 102, 241, 0.25), inset 0 1px 0 0 rgba(129, 140, 248, 0.35);
        }

        /* Linear Style Segmented Controls */
        .segmented-control {
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 4px;
            background: rgba(2, 6, 23, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            gap: 4px;
        }
        .segmented-btn {
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: 1px solid transparent;
        }
        .segmented-btn:hover {
            color: #cbd5e1;
        }
        .segmented-btn.active.role-guest-active {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.3) 100%);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.25);
        }
        .segmented-btn.active.role-landlord-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(124, 58, 237, 0.3) 100%);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.25);
        }

        /* Subtle Method Toggle (Password vs OTP) */
        .sub-tab-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .sub-tab-btn:hover {
            color: #cbd5e1;
        }
        .sub-tab-btn.active {
            background: #1e293b;
            color: #f8fafc;
        }

        /* Full Screen OTP Animation Transition */
        .auth-screen-container {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 480px;
        }
        .auth-screen-pane {
            width: 100%;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease, filter 0.35s ease;
            will-change: transform, opacity, filter;
        }
        .auth-screen-pane.screen-visible {
            opacity: 1;
            transform: translateX(0);
            filter: blur(0);
            pointer-events: auto;
            position: relative;
        }
        .auth-screen-pane.screen-exit-left {
            opacity: 0;
            transform: translateX(-40px);
            filter: blur(4px);
            pointer-events: none;
            position: absolute;
            inset: 0;
        }
        .auth-screen-pane.screen-enter-right {
            opacity: 0;
            transform: translateX(40px);
            filter: blur(4px);
            pointer-events: none;
            position: absolute;
            inset: 0;
        }

        /* 6 Digit Box Styling with Staggered Entrance */
        @keyframes otpBoxPopIn {
            0% {
                opacity: 0;
                transform: translateY(22px) scale(0.78);
            }
            65% {
                opacity: 1;
                transform: translateY(-4px) scale(1.08);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .otp-digit-box {
            width: 44px;
            height: 52px;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            background: #020617;
            border: 1px solid #1e293b;
            border-radius: 12px;
            color: #f8fafc;
            outline: none;
            opacity: 0;
            transform: translateY(22px) scale(0.78);
            transition: 
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease,
                transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        /* Khi màn hình OTP trượt xong hoàn toàn, kích hoạt 6 ô xuất hiện lần lượt từ từ */
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box {
            animation: otpBoxPopIn 0.42s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        /* Độ trễ xuất hiện từng ô tuần tự (Staggered cascading delays) */
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(1) { animation-delay: 0.06s; }
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(2) { animation-delay: 0.18s; }
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(3) { animation-delay: 0.30s; }
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(4) { animation-delay: 0.42s; }
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(5) { animation-delay: 0.54s; }
        .auth-screen-pane.screen-visible.digits-revealed .otp-digit-box:nth-child(6) { animation-delay: 0.66s; }

        .otp-digit-box:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 1px #10b981, 0 0 18px -2px rgba(16, 185, 129, 0.45) !important;
            background: #050b18 !important;
            transform: translateY(-3px) scale(1.06) !important;
        }
        .otp-digit-box.filled {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }
        .otp-digit-box.has-error {
            border-color: #f43f5e !important;
            box-shadow: 0 0 0 1px #f43f5e !important;
        }
        html.theme-light .otp-digit-box {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        html.theme-light .otp-digit-box:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 1px #10b981, 0 0 12px -2px rgba(16, 185, 129, 0.25);
            background: #f8fafc;
        }

        /* Clean SaaS Inputs */
        .saas-input {
            width: 100%;
            background: #020617;
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }
        .saas-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1, 0 0 16px -4px rgba(99, 102, 241, 0.3);
            background: #050b18;
        }
        .saas-input.role-guest-focus:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 1px #10b981, 0 0 16px -4px rgba(16, 185, 129, 0.3);
        }
        .saas-input.has-error {
            border-color: #f43f5e !important;
            box-shadow: 0 0 0 1px #f43f5e !important;
        }

        /* Password Strength Meter (Linear Minimalist) */
        .strength-bar-track { display: flex; gap: 4px; height: 3px; margin-top: 6px; }
        .strength-bar-seg { flex: 1; background: #1e293b; border-radius: 9999px; transition: background 0.3s ease; }
        .strength-weak .strength-bar-seg:nth-child(1) { background: #f43f5e; }
        .strength-medium .strength-bar-seg:nth-child(1),
        .strength-medium .strength-bar-seg:nth-child(2) { background: #f59e0b; }
        .strength-strong .strength-bar-seg { background: #10b981; }

        /* ========================================================= */
        /* COMPREHENSIVE BALANCED LIGHT MODE (GIAO DIỆN SÁNG CHUẨN ĐẸP) */
        /* ========================================================= */
        html.theme-light body, body.theme-light {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* 1. Backdrop & Hiệu ứng phát quang nhẹ nhàng */
        html.theme-light .split-ambient-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 50%, #f1f5f9 100%) !important;
        }
        html.theme-light .glow-sphere-left-green {
            background: radial-gradient(circle at 45% 50%, rgba(16, 185, 129, 0.14) 0%, rgba(5, 150, 105, 0.05) 45%, transparent 75%) !important;
            filter: blur(70px) !important;
        }
        html.theme-light .glow-sphere-right-dark {
            background: radial-gradient(circle at 55% 50%, rgba(99, 102, 241, 0.12) 0%, rgba(79, 70, 229, 0.04) 45%, transparent 75%) !important;
            filter: blur(70px) !important;
        }
        html.theme-light .cyber-grid-overlay {
            background-image: radial-gradient(rgba(15, 23, 42, 0.06) 1px, transparent 1px) !important;
        }

        /* 2. Top Header */
        html.theme-light header {
            background: rgba(255, 255, 255, 0.88) !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        }
        html.theme-light header a,
        html.theme-light header button {
            border-color: #e2e8f0 !important;
            color: #475569 !important;
            background: #ffffff !important;
        }
        html.theme-light header a:hover,
        html.theme-light header button:hover {
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            background: #f8fafc !important;
        }

        /* 3. Master Card (.saas-card) */
        html.theme-light .saas-card {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.6) !important;
        }
        html.theme-light .saas-card.role-guest-mode {
            border-color: rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 20px 45px -12px rgba(16, 185, 129, 0.12), 0 0 0 1px rgba(16, 185, 129, 0.25) !important;
        }
        html.theme-light .saas-card.role-landlord-mode {
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 20px 45px -12px rgba(99, 102, 241, 0.12), 0 0 0 1px rgba(99, 102, 241, 0.25) !important;
        }

        /* 4. Cột Trái: Hero Showcase */
        html.theme-light .auth-hero-showcase {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light .auth-hero-showcase h1,
        html.theme-light .auth-hero-showcase h2,
        html.theme-light .auth-hero-showcase h3,
        html.theme-light .auth-hero-showcase .text-white {
            color: #0f172a !important;
        }
        html.theme-light .auth-hero-showcase p,
        html.theme-light .auth-hero-showcase .text-slate-300,
        html.theme-light .auth-hero-showcase .text-slate-300\/90 {
            color: #475569 !important;
        }
        html.theme-light .auth-hero-showcase .text-slate-400 {
            color: #64748b !important;
        }
        html.theme-light .auth-hero-showcase .bg-slate-900\/60,
        html.theme-light .auth-hero-showcase .bg-slate-900\/40 {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.04) !important;
        }
        html.theme-light .auth-hero-showcase .border-slate-800\/80,
        html.theme-light .auth-hero-showcase .border-t {
            border-color: #e2e8f0 !important;
        }
        html.theme-light .auth-hero-showcase .border-slate-900 {
            border-color: #ffffff !important;
        }
        html.theme-light .auth-hero-showcase .text-slate-200 {
            color: #0f172a !important;
        }
        html.theme-light #hero-pill-badge {
            background: rgba(99, 102, 241, 0.08) !important;
            border-color: rgba(99, 102, 241, 0.25) !important;
        }
        html.theme-light #hero-role-tag {
            color: #4f46e5 !important;
        }

        /* 5. Cột Phải: Form & Inputs */
        html.theme-light #auth-right-col {
            background: #ffffff !important;
        }
        html.theme-light #auth-heading,
        html.theme-light #auth-right-col h1,
        html.theme-light #auth-right-col h2 {
            color: #0f172a !important;
        }
        html.theme-light #auth-subheading {
            color: #64748b !important;
        }
        html.theme-light #auth-right-col label.text-slate-300,
        html.theme-light #auth-right-col .text-slate-300,
        html.theme-light #auth-right-col .text-slate-200 {
            color: #334155 !important;
        }
        html.theme-light #auth-right-col .text-slate-400 {
            color: #64748b !important;
        }
        html.theme-light #auth-right-col .text-slate-500 {
            color: #64748b !important;
        }
        html.theme-light #auth-right-col .border-slate-800\/80,
        html.theme-light #auth-right-col .border-slate-800\/60,
        html.theme-light #auth-right-col .border-slate-800 {
            border-color: #e2e8f0 !important;
        }

        /* Segmented control chuyển đổi vai trò Khách thuê vs Chủ trọ */
        html.theme-light .segmented-control {
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light .segmented-btn {
            color: #64748b !important;
        }
        html.theme-light .segmented-btn.active.role-guest-active {
            background: #ffffff !important;
            color: #059669 !important;
            border-color: rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 2px 6px -1px rgba(16, 185, 129, 0.15) !important;
        }
        html.theme-light .segmented-btn.active.role-landlord-active {
            background: #ffffff !important;
            color: #4f46e5 !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 2px 6px -1px rgba(99, 102, 241, 0.15) !important;
        }

        /* Method Toggle (Mật khẩu vs OTP) */
        html.theme-light #auth-pane-signin .bg-slate-950\/60 {
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light .sub-tab-btn {
            color: #64748b !important;
        }
        html.theme-light .sub-tab-btn.active {
            background: #ffffff !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        }

        /* Ô nhập liệu Input */
        html.theme-light .saas-input {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light .saas-input::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light .saas-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
            background: #ffffff !important;
        }
        html.theme-light .saas-input.has-error {
            border-color: #f43f5e !important;
            background: #fff1f2 !important;
        }

        /* Box tiện ích đăng nhập OTP */
        html.theme-light #form-signin-otp .bg-slate-900\/60 {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        /* Form đăng ký: Radio kênh nhận OTP */
        html.theme-light #signup-verification-group label {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
        }
        html.theme-light #signup-verification-group label:hover {
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
        }

        /* Hộp đăng ký cơ sở đối tác chủ trọ */
        html.theme-light #landlord-portal-box {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%) !important;
            border-color: #dbeafe !important;
        }
        html.theme-light #landlord-portal-box .text-slate-200 {
            color: #1e3a8a !important;
        }
        html.theme-light #landlord-portal-box .text-slate-400 {
            color: #475569 !important;
        }

        /* 6. Màn hình OTP chuyên biệt (Screen 2) */
        html.theme-light #auth-screen-otp {
            background: #ffffff !important;
        }
        html.theme-light #auth-screen-otp h2 {
            color: #0f172a !important;
        }
        html.theme-light #auth-screen-otp .bg-slate-900 {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light #auth-screen-otp .bg-slate-950 {
            background: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
        }
        html.theme-light #screen-otp-target-text {
            color: #047857 !important;
        }
        html.theme-light #auth-screen-otp .from-blue-950\/40 {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%) !important;
            border-color: #bae6fd !important;
            box-shadow: 0 4px 12px -2px rgba(186, 230, 253, 0.4) !important;
        }
        html.theme-light #auth-screen-otp .from-blue-950\/40 .text-white {
            color: #0c4a6e !important;
        }
        html.theme-light #auth-screen-otp .from-blue-950\/40 .text-slate-300\/90 {
            color: #334155 !important;
        }
        html.theme-light #auth-screen-otp .from-blue-950\/40 .text-cyan-200 {
            color: #0284c7 !important;
        }
        html.theme-light .otp-digit-box {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light .otp-digit-box:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
            background: #ffffff !important;
        }

        /* 7. Footer & Micro Badges */
        html.theme-light footer {
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light .text-slate-700 {
            color: #cbd5e1 !important;
        }

        /* 8. Admin Shell Overrides (Khi vào các trang admin /list, /read, /update) */
        html.theme-light #admin-shell {
            background: #f8fafc !important;
            color: #0f172a !important;
        }
        html.theme-light #admin-shell .bg-slate-900\/40,
        html.theme-light #admin-shell .bg-slate-900\/35,
        html.theme-light #admin-shell .bg-slate-900\/30 {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05), 0 2px 6px -2px rgba(0, 0, 0, 0.03) !important;
        }
        html.theme-light #admin-shell h1,
        html.theme-light #admin-shell h2,
        html.theme-light #admin-shell h3 {
            color: #0f172a !important;
        }
        html.theme-light #admin-shell .bg-gradient-to-r.from-white.to-slate-400 {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }
        html.theme-light #admin-shell .text-slate-100,
        html.theme-light #admin-shell .text-slate-200 {
            color: #0f172a !important;
        }
        html.theme-light #admin-shell .text-slate-300,
        html.theme-light #admin-shell .text-slate-400 {
            color: #475569 !important;
        }
        html.theme-light #admin-shell .text-slate-500 {
            color: #64748b !important;
        }
        html.theme-light #admin-shell .border-slate-800,
        html.theme-light #admin-shell .border-slate-800\/80,
        html.theme-light #admin-shell .border-slate-800\/40,
        html.theme-light #admin-shell .divide-slate-800\/40 > :not([hidden]) ~ :not([hidden]) {
            border-color: #e2e8f0 !important;
        }
        html.theme-light #admin-shell input,
        html.theme-light #admin-shell select {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light #admin-shell input::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light #admin-shell table thead tr {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light #admin-shell table thead th {
            color: #475569 !important;
        }
        html.theme-light #admin-shell table tbody tr:hover {
            background: #f1f5f9 !important;
        }

        /* 9. Animated Brand Typography in Light Mode */
        html.theme-light .animated-brand-smartroom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 35%, #2563eb 70%, #059669 100%) !important;
            background-size: 250% 250% !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            filter: drop-shadow(0 1px 2px rgba(99, 102, 241, 0.2)) !important;
        }
        html.theme-light .animated-brand-amp {
            color: #7c3aed !important;
        }

        /* Hero Showcase Responsive Display (Tránh xung đột với class .hidden !important toàn cục) */
        @media (max-width: 1023px) {
            .auth-hero-showcase {
                display: none !important;
            }
        }
        @media (min-width: 1024px) {
            .auth-hero-showcase {
                display: flex !important;
            }
        }

        /* DYNAMIC ANIMATED BRAND TYPOGRAPHY (CHỮ & LOGO ĐỘNG SANG TRỌNG) */
        @keyframes brandGradientFlow {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes brandAmpPulse {
            0%, 100% {
                opacity: 0.6;
                transform: scale(0.96);
                filter: drop-shadow(0 0 0px transparent);
            }
            50% {
                opacity: 1;
                transform: scale(1.1);
                filter: drop-shadow(0 0 8px rgba(192, 132, 252, 0.7));
            }
        }

        @keyframes brandTileGlow {
            0%, 100% {
                box-shadow: 0 8px 22px -4px rgba(99, 102, 241, 0.5), 0 0 0 2px rgba(129, 140, 248, 0.4);
                border-color: rgba(165, 180, 252, 0.45);
            }
            50% {
                box-shadow: 0 8px 22px -4px rgba(16, 185, 129, 0.45), 0 0 0 2px rgba(52, 211, 153, 0.45);
                border-color: rgba(110, 231, 183, 0.45);
            }
        }

        .animated-brand-smartroom {
            background: linear-gradient(135deg, #a5b4fc 0%, #c084fc 25%, #818cf8 50%, #e879f9 75%, #a5b4fc 100%);
            background-size: 250% 250%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: brandGradientFlow 4s ease-in-out infinite;
            filter: drop-shadow(0 0 10px rgba(168, 85, 247, 0.35));
            font-weight: 800;
            letter-spacing: -0.02em;
            display: inline-block;
            will-change: background-position;
        }

        .animated-brand-amp {
            display: inline-block;
            color: #c084fc;
            animation: brandAmpPulse 2.8s ease-in-out infinite;
            margin: 0 2px;
            font-weight: 700;
            will-change: transform, opacity;
        }

        .animated-brand-renty {
            background: linear-gradient(135deg, #34d399 0%, #2dd4bf 25%, #10b981 50%, #38bdf8 75%, #34d399 100%);
            background-size: 250% 250%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: brandGradientFlow 4s ease-in-out infinite reverse;
            filter: drop-shadow(0 0 10px rgba(45, 212, 191, 0.35));
            font-weight: 800;
            letter-spacing: -0.02em;
            display: inline-block;
            will-change: background-position;
        }

        .animated-brand-tile {
            animation: brandTileGlow 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#090d16] text-slate-100 min-h-screen {{ $isAdminPage ? 'overflow-hidden font-sans' : 'flex flex-col justify-between overflow-x-hidden' }} selection:bg-indigo-500 selection:text-white">

    <!-- SPLIT AMBIENT BACKDROP: BÊN XANH BÊN ĐEN -->
    <div class="split-ambient-bg" aria-hidden="true">
        <div class="glow-sphere-left-green"></div>
        <div class="glow-sphere-right-dark"></div>
    </div>
    <!-- CYBER-GRID BLUEPRINT TEXTURE -->
    <div class="cyber-grid-overlay" aria-hidden="true"></div>

    <!-- TOAST CONTAINER -->
    <div id="toast-container" class="toast-container"></div>

    @if($isAdminPage)
        @include('admin.partials.sidebar')
        <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
            @if($page === 'list')
                @include('login.partials.admin_list')
            @elseif($page === 'read')
                <main class="container mx-auto px-6 py-12 flex-grow relative z-10">
                    <div class="max-w-xl mx-auto bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl">
                        @include('login.partials.admin_detail')
                    </div>
                </main>
            @elseif($page === 'create' || $page === 'update')
                <main class="container mx-auto px-6 py-12 flex-grow relative z-10">
                    <div class="max-w-xl mx-auto bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl">
                        @include('login.partials.admin_form')
                    </div>
                </main>
            @endif
        </div>
    @else
        <!-- TOP BRAND BAR -->
        <header class="container mx-auto px-6 py-5 flex justify-between items-center relative z-20">
            <a href="{{ route('renty.user') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-teal-500 flex items-center justify-center text-white shadow-sm ring-1 ring-indigo-400/30 animated-brand-tile group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-hotel text-xs"></i>
                </div>
                <span class="text-sm font-bold tracking-tight text-white flex items-center gap-1">
                    <span class="animated-brand-smartroom">SmartRoom</span>
                    <span class="animated-brand-amp">&amp;</span>
                    <span class="animated-brand-renty">Renty</span>
                </span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('renty.user') }}" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Trang chủ</span>
                </a>
                <button type="button" onclick="toggleThemeMode()" class="w-8 h-8 rounded-lg border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon text-xs" data-theme-icon></i>
                </button>
            </div>
        </header>

        <!-- MAIN TWO-COLUMN STAGE (MASTER GLASS CARD BAO QUANH CẢ 2 DIV) -->
        <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-6 lg:py-10 relative z-10">
            <div class="w-full max-w-5xl mx-auto">
                
                <!-- SAAS AUTH MASTER CARD (BAO QUANH TOÀN BỘ CẢ 2 CỘT) -->
                <div id="auth-card" class="saas-card role-guest-mode shadow-2xl relative overflow-hidden">
                    <!-- TOP HAIRLINE ACCENT CHẠY TOÀN BỘ ĐỈNH KHUNG (TÍM SMARTOOM & NGỌC RENTY) -->
                    <div id="card-top-hairline" class="absolute top-0 inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-indigo-500 via-purple-500 to-emerald-400/80 to-transparent transition-all duration-500"></div>

                    <div class="flex flex-col lg:flex-row items-stretch">
                        
                        <!-- CỘT TRÁI: HERO SHOWCASE (NẰM BÊN TRONG KHUNG BAO QUANH) -->
                        <!-- CỘT TRÁI: HERO SHOWCASE (NẰM BÊN TRONG KHUNG BAO QUANH) -->
                        <div class="auth-hero-showcase flex-col justify-between p-6 lg:p-7 lg:w-[53%] border-b lg:border-b-0 lg:border-r border-slate-800/80 bg-slate-950/30">
                            
                            <!-- NHÓM NỘI DUNG CHÍNH (GẮN KẾT CHẶT CHẼ, KHÔNG BỊ GIÃN KHOẢNG TRỐNG) -->
                            <div class="space-y-3.5">
                                <!-- Top: Pill & Headline -->
                                <div class="space-y-3">
                                    <div id="hero-pill-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/15 border border-indigo-500/35 w-max transition-all duration-300">
                                        <span id="hero-pill-dot" class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                                        <span id="hero-role-tag" class="text-xs font-semibold text-indigo-300 tracking-wide uppercase">Hệ sinh thái SmartRoom &amp; Renty</span>
                                    </div>

                                    <h1 class="text-2xl xl:text-3xl font-extrabold text-white tracking-tight leading-tight">
                                        Tìm phòng minh bạch.<br>
                                        <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-indigo-400 bg-clip-text text-transparent">Vận hành cơ sở chuẩn số hóa.</span>
                                    </h1>
                                    <p class="text-xs text-slate-300/90 leading-relaxed max-w-lg">
                                        Nền tảng kết nối trực tiếp Khách thuê và Chủ nhà trọ thông minh với quy trình xác thực CCCD gắn chip (KYC), hợp đồng điện tử pháp lý và tự động hóa hóa đơn VietQR 24/7.
                                    </p>
                                </div>

                                <!-- Middle: 3 Bento Features -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-white/5 hover:border-emerald-500/30 transition-all duration-300 group">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-2.5 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-shield-halved text-xs"></i>
                                        </div>
                                        <h3 class="text-xs font-bold text-white mb-1">100% KYC</h3>
                                        <p class="text-[11px] text-slate-400 leading-normal">
                                            Kiểm duyệt thực tế, hoàn cọc an toàn.
                                        </p>
                                    </div>

                                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-white/5 hover:border-indigo-500/30 transition-all duration-300 group">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 mb-2.5 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-bolt-lightning text-xs"></i>
                                        </div>
                                        <h3 class="text-xs font-bold text-white mb-1">Chốt Số IoT</h3>
                                        <p class="text-[11px] text-slate-400 leading-normal">
                                            Điện nước tự động &amp; gạch nợ VietQR.
                                        </p>
                                    </div>

                                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-white/5 hover:border-teal-500/30 transition-all duration-300 group">
                                        <div class="w-8 h-8 rounded-lg bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-400 mb-2.5 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-file-signature text-xs"></i>
                                        </div>
                                        <h3 class="text-xs font-bold text-white mb-1">Hợp Đồng Số</h3>
                                        <p class="text-[11px] text-slate-400 leading-normal">
                                            Ký kết điện tử bảo vệ pháp lý.
                                        </p>
                                    </div>
                                </div>

                                <!-- THẺ HỆ THỐNG LIÊN THÔNG (LẤP ĐẦY KHOẢNG TRỐNG, CÂN ĐỐI VỚI CỘT PHẢI) -->
                                <div class="p-3.5 rounded-xl bg-slate-900/40 border border-slate-800/80 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0">
                                            <i class="fa-solid fa-server text-[11px]"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-xs truncate">Hệ thống liên thông 24/7</div>
                                            <div class="text-[11px] text-slate-400 truncate">Hợp đồng điện tử • Gạch nợ VietQR • SmartLock IoT</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-[10px] font-semibold text-emerald-400 shrink-0 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Online 99.9%
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom: Live Metrics Counters & Avatars (NẰM DƯỚI ĐÁY CÂN ĐỐI) -->
                            <div class="space-y-3 pt-3.5 border-t border-slate-800/80 mt-3.5">
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <div class="text-lg xl:text-xl font-black text-white tracking-tight flex items-center gap-1">
                                            <span>5.000+</span>
                                            <span class="text-emerald-400 text-xs"><i class="fa-solid fa-arrow-trend-up text-[10px]"></i></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Phòng xác thực</div>
                                    </div>
                                    <div>
                                        <div class="text-lg xl:text-xl font-black text-white tracking-tight">
                                            98.8%
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Tỷ lệ hài lòng</div>
                                    </div>
                                    <div>
                                        <div class="text-lg xl:text-xl font-black text-white tracking-tight flex items-center gap-1">
                                            <span>24/7</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Hỗ trợ vận hành</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5">
                                    <div class="flex -space-x-1.5">
                                        <div class="w-6 h-6 rounded-full bg-emerald-600 border-2 border-slate-900 flex items-center justify-center text-[9px] font-bold text-white">TN</div>
                                        <div class="w-6 h-6 rounded-full bg-indigo-600 border-2 border-slate-900 flex items-center justify-center text-[9px] font-bold text-white">LH</div>
                                        <div class="w-6 h-6 rounded-full bg-teal-600 border-2 border-slate-900 flex items-center justify-center text-[9px] font-bold text-white">MK</div>
                                        <div class="w-6 h-6 rounded-full bg-amber-600 border-2 border-slate-900 flex items-center justify-center text-[9px] font-bold text-white">+15k</div>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Hơn <span class="text-slate-200 font-semibold">15.000+</span> cư dân &amp; chủ cơ sở tin dùng.
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- CỘT PHẢI: FORM ĐĂNG NHẬP / ĐĂNG KÝ / MÀN HÌNH XÁC THỰC OTP CHUYÊN BIỆT -->
                        <div class="p-6 sm:p-7 lg:w-[47%] relative overflow-hidden flex flex-col justify-between" id="auth-right-col">
                            <div class="auth-screen-container">

                                <!-- MÀN HÌNH 1: FORM CHÍNH (ĐĂNG NHẬP / ĐĂNG KÝ) -->
                                <div id="auth-screen-main" class="auth-screen-pane screen-visible">

                    <!-- 2. CARD HEADER -->
                    <div class="mb-3.5">
                        <h1 id="auth-heading" class="text-xl font-bold text-white tracking-tight">Đăng nhập tài khoản</h1>
                        <p id="auth-subheading" class="text-xs text-slate-400 mt-1">
                            Hệ sinh thái kết nối Khách thuê và Quản trị cơ sở lưu trú thông minh
                        </p>
                    </div>

                    <!-- 3. ERROR / SUCCESS NOTIFICATIONS -->
                    @if(session('error'))
                        <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs flex items-start gap-2.5 mb-3 text-rose-300">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-xs mt-0.5 shrink-0"></i>
                            <div class="leading-relaxed">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs flex items-start gap-2.5 mb-3 text-emerald-300">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-xs mt-0.5 shrink-0"></i>
                            <div class="leading-relaxed">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <!-- 4. PANE: ĐĂNG NHẬP (MẬT KHẨU HOẶC OTP) -->
                    <div id="auth-pane-signin" class="space-y-3">

                        <!-- Sub-Toggle Phương Thức: Mật Khẩu vs OTP -->
                        <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-800/80">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Phương thức</span>
                            <div class="flex items-center gap-1 bg-slate-950/60 p-1 border border-slate-800/60 rounded-lg">
                                <button type="button" id="method-btn-password" onclick="switchLoginMethod('password')" class="sub-tab-btn active">
                                    Mật khẩu
                                </button>
                                <button type="button" id="method-btn-otp" onclick="switchLoginMethod('otp')" class="sub-tab-btn">
                                    Mã OTP
                                </button>
                            </div>
                        </div>

                        <!-- A. FORM ĐĂNG NHẬP MẬT KHẨU -->
                        <form id="form-signin-password" action="{{ route('user.authUser', [], false) }}" method="POST" class="space-y-3" onsubmit="return handleSigninSubmit(event)">
                            @csrf
                            <input type="hidden" name="auth_role" id="auth-role-input" value="{{ old('auth_role', 'guest') }}">
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1" for="signin-login">
                                    Tên tài khoản, Email hoặc Số điện thoại
                                </label>
                                <input type="text" 
                                       name="login" 
                                       id="signin-login" 
                                       class="saas-input {{ session('error') ? 'has-error' : '' }}" 
                                       placeholder="Tên tài khoản, email hoặc số điện thoại" 
                                       value="{{ old('login') }}"
                                       autocomplete="username"
                                       oninput="clearSigninError('signin-login')"
                                       onblur="validateSigninLoginBlur(this)">
                                <p id="signin-login-error" class="hidden text-[11px] text-rose-400 font-medium mt-1">
                                    <span id="signin-login-error-text">Vui lòng nhập tài khoản đăng nhập</span>
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-slate-300" for="signin-password">
                                        Mật khẩu
                                    </label>
                                    <a href="{{ route('password.forgot') }}" class="text-[11px] text-slate-400 hover:text-white transition-colors">
                                        Quên mật khẩu?
                                    </a>
                                </div>
                                <div class="relative">
                                    <input type="password" 
                                           name="password" 
                                           id="signin-password" 
                                           class="saas-input pr-10 {{ session('error') ? 'has-error' : '' }}" 
                                           placeholder="••••••••"
                                           autocomplete="current-password"
                                           oninput="clearSigninError('signin-password')"
                                           onblur="validateSigninPasswordBlur(this)">
                                    <button type="button" onclick="togglePasswordVisibility('signin-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs p-1" title="Ẩn/hiện mật khẩu" aria-label="Ẩn/hiện mật khẩu">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                                <p id="signin-password-error" class="hidden text-[11px] text-rose-400 font-medium mt-1">
                                    <span id="signin-password-error-text">Vui lòng nhập mật khẩu</span>
                                </p>
                                @if(session('error'))
                                    <p class="text-[11px] text-rose-400 font-medium mt-1" id="login-error-message">
                                        {{ session('error') }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center pt-0.5">
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 select-none">
                                    <input type="checkbox" name="remember" id="signin-remember" checked class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0 cursor-pointer">
                                    <span>Ghi nhớ đăng nhập trên thiết bị này</span>
                                </label>
                            </div>

                            <button type="submit" id="btn-submit-signin" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950/40 transition-all duration-200 flex items-center justify-center gap-2 mt-2.5">
                                <i id="signin-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                                <span id="btn-submit-signin-text">Đăng nhập</span>
                                <i class="fa-solid fa-arrow-right text-[10px] opacity-80"></i>
                            </button>
                        </form>

                        <!-- B. FORM ĐĂNG NHẬP MÃ OTP SIÊU TỐC -->
                        <div id="form-signin-otp" class="hidden space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1" for="otp-login-input">
                                    Tên tài khoản, Email hoặc Số điện thoại đã đăng ký
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="otp-login-input" 
                                           class="saas-input pl-9" 
                                           placeholder="Tên tài khoản, email hoặc số điện thoại"
                                           autocomplete="username">
                                    <i class="fa-solid fa-user-shield absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                </div>
                                <p id="otp-login-input-error" class="hidden text-[11px] text-rose-400 font-medium mt-1">
                                    <span id="otp-login-input-error-text">Vui lòng nhập tên tài khoản, số điện thoại hoặc email</span>
                                </p>
                            </div>

                            <!-- BOX TIỆN ÍCH OTP -->
                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-300 space-y-1">
                                <div class="flex items-center gap-2 font-semibold text-emerald-400">
                                    <i class="fa-solid fa-bolt text-xs"></i>
                                    <span>Đăng nhập nhanh không cần mật khẩu</span>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Hệ thống sẽ tự động gửi mã OTP và liên kết 1 chạm về Email hoặc Số điện thoại liên kết với tài khoản của bạn.
                                </p>
                            </div>

                            <div class="flex items-center pt-0.5">
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 select-none">
                                    <input type="checkbox" id="otp-remember" checked class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0 cursor-pointer">
                                    <span>Ghi nhớ đăng nhập trên thiết bị này</span>
                                </label>
                            </div>

                            <button type="button" 
                                    id="btn-send-login-otp" 
                                    onclick="handleSendLoginOtp()" 
                                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950/40 transition-all duration-200 flex items-center justify-center gap-2 mt-2.5">
                                <i id="otp-send-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                                <span id="otp-send-btn-text">Gửi mã OTP xác nhận</span>
                                <i class="fa-solid fa-arrow-right text-[10px] opacity-80" id="otp-send-arrow"></i>
                            </button>
                        </div>

                    </div>

                    <!-- 5. PANE: ĐĂNG KÝ (KHÁCH THUÊ) - CÂN ĐỐI 2 CỘT TỔNG THỂ -->
                    <div id="auth-pane-signup" class="hidden">
                        <form id="form-signup-guest" action="{{ route('user.postUser', [], false) }}" method="POST" class="space-y-3" onsubmit="return handleSignupSubmit(event)">
                            @csrf
                            
                            <!-- Hàng 1: Tên tài khoản & Họ tên (Grid 2 cột) -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1" for="signup-username">
                                        Tên tài khoản <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" 
                                           name="username" 
                                           id="signup-username" 
                                           required
                                           minlength="3"
                                           maxlength="50"
                                           class="saas-input @error('username') has-error @enderror" 
                                           placeholder="nguyenvana" 
                                           value="{{ old('username') }}"
                                           onblur="validateSignupUsernameBlur(this)">
                                    <div id="signup-username-feedback" class="mt-1 text-[11px] {{ $errors->has('username') ? 'text-rose-400 block' : 'hidden' }}">
                                        @error('username')
                                            <span>{{ $message }}</span>
                                        @else
                                            <span id="signup-username-msg"></span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1" for="signup-name">
                                        Họ và tên <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" name="name" id="signup-name" required class="saas-input @error('name') has-error @enderror" placeholder="Nguyễn Văn A" value="{{ old('name') }}">
                                    @error('name')
                                        <p class="mt-1 text-[11px] text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hàng 2: Số điện thoại & Email (Grid 2 cột) -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1" for="signup-phone">
                                        Số điện thoại <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           id="signup-phone" 
                                           required
                                           maxlength="10" 
                                           pattern="^0[0-9]{9}$" 
                                           inputmode="numeric" 
                                           class="saas-input @error('phone') has-error @enderror" 
                                           placeholder="0901234567" 
                                           value="{{ old('phone') }}"
                                           oninput="validatePhoneInput(this)"
                                           onblur="validatePhoneBlur(this)">
                                    <div id="signup-phone-feedback" class="mt-1 text-[11px] {{ $errors->has('phone') ? 'text-rose-400 block' : 'hidden' }}">
                                        @error('phone')
                                            <span>{{ $message }}</span>
                                        @else
                                            <span id="signup-phone-msg"></span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1" for="signup-email">
                                        Email
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="signup-email" 
                                           class="saas-input @error('email') has-error @enderror" 
                                           placeholder="user@example.com" 
                                           value="{{ old('email') }}"
                                           oninput="handleEmailInput(this)"
                                           onblur="handleEmailBlur(this)">
                                    <div id="signup-email-feedback" class="mt-1 text-[11px] {{ $errors->has('email') ? 'text-rose-400 block' : 'hidden' }}">
                                        @error('email')
                                            <span>{{ $message }}</span>
                                        @else
                                            <span id="signup-email-msg"></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Hàng 3: Kênh nhận OTP kích hoạt -->
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">
                                    Kênh nhận OTP kích hoạt
                                </label>
                                <div class="grid grid-cols-2 gap-2" id="signup-verification-group">
                                    <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-800 bg-slate-950/60 cursor-pointer text-xs text-slate-300" for="signup-method-phone" id="signup-pill-phone">
                                        <input type="radio" name="verification_method" id="signup-method-phone" value="phone" {{ old('verification_method', 'phone') === 'phone' ? 'checked' : '' }} onchange="onSignupMethodChange('phone')" class="accent-indigo-500">
                                        <span>Qua tin nhắn SĐT</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-800 bg-slate-950/60 cursor-pointer text-xs text-slate-300" for="signup-method-email" id="signup-pill-email">
                                        <input type="radio" name="verification_method" id="signup-method-email" value="email" {{ old('verification_method') === 'email' ? 'checked' : '' }} onchange="onSignupMethodChange('email')" class="accent-indigo-500">
                                        <span>Qua Email</span>
                                    </label>
                                </div>
                                <div id="email-method-hint" class="text-[11px] text-amber-400/90 mt-1 hidden" aria-live="polite">
                                    <span id="email-method-hint-text">Vui lòng nhập email trước để nhận mã qua email</span>
                                </div>
                            </div>

                            <!-- Hàng 4: Mật khẩu & Strength Bar -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-slate-300" for="signup-password">Mật khẩu</label>
                                    <span id="signup-pwd-status" class="text-[10px] text-slate-500">Tối thiểu 6 ký tự</span>
                                </div>
                                <div class="relative">
                                    <input type="password" name="password" id="signup-password" oninput="evalPasswordStrength(this.value)" required class="saas-input pr-10 @error('password') has-error @enderror" placeholder="••••••••">
                                    <button type="button" onclick="togglePasswordVisibility('signup-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs p-1" title="Ẩn/hiện mật khẩu" aria-label="Ẩn/hiện mật khẩu">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                                <div id="signup-strength-box" class="strength-bar-track">
                                    <div class="strength-bar-seg"></div>
                                    <div class="strength-bar-seg"></div>
                                    <div class="strength-bar-seg"></div>
                                </div>
                            </div>

                            <!-- Hàng 5: Checkbox Điều khoản -->
                            <div class="pt-0.5">
                                <label class="flex items-start gap-2 cursor-pointer text-[11px] text-slate-400 leading-tight">
                                    <input type="checkbox" id="signup-terms" required checked class="rounded border-slate-700 bg-slate-900 text-indigo-600 mt-0.5 cursor-pointer">
                                    <span>Tôi đồng ý với Điều khoản dịch vụ và Chính sách bảo mật</span>
                                </label>
                            </div>

                            <!-- Hàng 6: Nút Submit Đăng ký -->
                            <button type="submit" id="btn-submit-signup" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-sm transition-all mt-2.5">
                                Tiếp tục &amp; nhận mã OTP
                            </button>
                        </form>
                    </div>

                    <!-- 6. CARD FOOTER SWITCHER (ĐĂNG KÝ KHÁCH THUÊ) -->
                    <div class="mt-2.5 text-center text-xs text-slate-400">
                        <div id="footer-switch-to-signup">
                            Chưa có tài khoản?
                            <button type="button" onclick="switchAuthMode('signup')" class="text-emerald-400 hover:text-emerald-300 font-semibold ml-1 transition-colors underline underline-offset-2">
                                Đăng ký
                            </button>
                        </div>
                        <div id="footer-switch-to-signin" class="hidden">
                            Đã có tài khoản?
                            <button type="button" onclick="switchAuthMode('signin')" class="text-emerald-400 hover:text-emerald-300 font-semibold ml-1 transition-colors underline underline-offset-2">
                                Quay lại đăng nhập
                            </button>
                        </div>
                    </div>

                    <!-- 7. PANE: ĐỐI TÁC CHỦ TRỌ (ẨN Ở FORM ĐĂNG KÝ ĐỂ TỐI ƯU DIỆN TÍCH) -->
                    <div id="landlord-portal-box" class="mt-2.5 p-3 rounded-xl bg-slate-950/70 border border-slate-800 text-center relative overflow-hidden group {{ $page === 'create' ? 'hidden' : '' }}">
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-teal-500/10 pointer-events-none"></div>
                        <p class="text-xs text-slate-200 font-bold flex items-center justify-center gap-1.5 relative z-10">
                            <i class="fa-solid fa-hotel text-indigo-400 text-xs"></i>
                            <span>Bạn muốn vận hành cơ sở lưu trú trên SmartRoom?</span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed relative z-10">
                            Đăng ký cơ sở, chốt điện nước tự động, thanh toán VietQR và xác minh hồ sơ KYC Tích Xanh.
                        </p>
                        <a href="{{ route('landlord.register', ['reset' => 1]) }}" onclick="['input-username', 'input-fullname', 'input-phone', 'input-email', 'input-password'].forEach(id => sessionStorage.removeItem('landlord_reg_' + id)); sessionStorage.removeItem('landlord_reg_method');" class="inline-flex items-center justify-center gap-1.5 w-full mt-2 py-2 px-3 rounded-lg bg-gradient-to-r from-indigo-600 via-indigo-500 to-teal-600 hover:from-indigo-500 hover:to-teal-500 text-white text-xs font-semibold shadow-md transition-all relative z-10">
                            <span>Đăng ký cơ sở đối tác</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- 8. MICRO-TRUST SECURITY BADGES -->
                    <div class="mt-auto pt-3 border-t border-slate-800/60 flex items-center justify-center gap-4 text-[11px] text-slate-500">
                        <span class="inline-flex items-center gap-1.5 hover:text-slate-400 transition-colors">
                            <i class="fa-solid fa-shield-halved text-emerald-400/80 text-[10px]"></i>
                            <span>AES-256</span>
                        </span>
                        <span class="text-slate-700">•</span>
                        <span class="inline-flex items-center gap-1.5 hover:text-slate-400 transition-colors">
                            <i class="fa-solid fa-qrcode text-indigo-400/80 text-[10px]"></i>
                            <span>VietQR 2.0</span>
                        </span>
                        <span class="text-slate-700">•</span>
                        <span class="inline-flex items-center gap-1.5 hover:text-slate-400 transition-colors">
                            <i class="fa-solid fa-circle-check text-teal-400/80 text-[10px]"></i>
                            <span>KYC Tích Xanh</span>
                        </span>
                    </div>

                                </div> <!-- Đóng #auth-screen-main -->

                                <!-- MÀN HÌNH 2: XÁC THỰC MÃ OTP CHUYÊN BIỆT (FULL SCREEN SLIDE TRANSITION) -->
                                <div id="auth-screen-otp" class="auth-screen-pane screen-enter-right">
                                    
                                    <!-- Top: Nút quay lại & Badge bảo mật -->
                                    <div class="flex items-center justify-between gap-2 pb-4 border-b border-slate-800/80">
                                        <button type="button" 
                                                onclick="exitOtpScreen()" 
                                                class="inline-flex items-center gap-2 text-xs font-medium text-slate-400 hover:text-white transition-colors group">
                                            <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform"></i>
                                            <span>Quay lại đăng nhập</span>
                                        </button>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-[11px] text-slate-400">
                                            <i class="fa-solid fa-lock text-emerald-400 text-[10px]"></i>
                                            <span>Bảo mật 2FA</span>
                                        </div>
                                    </div>

                                    <!-- Center Body: Icon, Heading, Badge người nhận, 6 Digit Box -->
                                    <div class="py-5 space-y-5 text-center my-auto">
                                        <div class="flex flex-col items-center">
                                            <div id="screen-otp-icon-box" class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400 text-2xl shadow-xl shadow-emerald-950/40 mb-3.5">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </div>
                                            <h2 class="text-xl font-bold text-white tracking-tight">Xác thực mã OTP</h2>
                                            <p class="text-xs text-slate-400 mt-1.5">
                                                Hệ thống đã gửi mã xác nhận 6 chữ số đến
                                            </p>
                                            <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-950 border border-slate-800 text-xs font-mono text-emerald-300 shadow-sm max-w-[280px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                                                <span id="screen-otp-target-text" class="truncate font-semibold"></span>
                                            </div>
                                        </div>

                                        <!-- HỘP NHẮC XÁC MINH 1 NHẤP QUA EMAIL -->
                                        <div class="p-3 rounded-2xl bg-gradient-to-r from-blue-950/40 via-indigo-950/50 to-purple-950/40 border border-indigo-500/30 text-left flex items-start gap-3 shadow-lg shadow-indigo-950/30">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-400 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-500/20 text-xs">
                                                <i class="fa-solid fa-bolt"></i>
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-white">Xác minh 1 nhấp qua Email</span>
                                                    <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/30">Nhanh nhất</span>
                                                </div>
                                                <p class="text-[11px] text-slate-300/90 mt-1 leading-relaxed">
                                                    Bạn có thể mở hộp thư Email và bấm nút <strong class="text-cyan-200">"Đăng Nhập Nhanh Bằng 1 Chạm"</strong> để vào hệ thống ngay mà không cần nhập mã 6 số.
                                                </p>
                                            </div>
                                        </div>

                                        <!-- 6 ô nhập mã OTP độc lập -->
                                        <div class="pt-1">
                                            <label class="block text-xs font-medium text-slate-400 mb-2.5">
                                                Nhập mã xác thực (6 chữ số)
                                            </label>
                                            <div class="flex items-center justify-center gap-2 sm:gap-2.5" id="otp-digit-inputs">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="0" autocomplete="off">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="1" autocomplete="off">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="2" autocomplete="off">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="3" autocomplete="off">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="4" autocomplete="off">
                                                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit-box" data-index="5" autocomplete="off">
                                            </div>
                                            <p id="screen-otp-error" class="hidden text-xs text-rose-400 font-medium mt-2">
                                                <span id="screen-otp-error-text"></span>
                                            </p>
                                        </div>

                                        <!-- Nút xác nhận & Đăng nhập -->
                                        <div class="pt-1">
                                            <button type="button" 
                                                    id="btn-screen-verify-otp" 
                                                    onclick="handleVerifyLoginOtp()" 
                                                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950/40 transition-all duration-200 flex items-center justify-center gap-2">
                                                <i id="screen-otp-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                                                <span>Xác nhận &amp; Đăng nhập</span>
                                                <i class="fa-solid fa-arrow-right text-[10px] opacity-80"></i>
                                            </button>
                                        </div>

                                        <!-- Đếm ngược & Gửi lại mã OTP -->
                                        <div class="text-xs text-slate-400 flex items-center justify-center gap-1.5 pt-1">
                                            <span>Chưa nhận được mã?</span>
                                            <button type="button" 
                                                    id="btn-screen-resend-otp" 
                                                    onclick="handleSendLoginOtp(true)" 
                                                    class="text-emerald-400 hover:text-emerald-300 font-semibold transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:text-slate-500" 
                                                    disabled>
                                                <span>Gửi lại OTP</span> <span id="screen-otp-countdown" class="font-mono">(60s)</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bottom: Security Micro Footer -->
                                    <div class="pt-4 border-t border-slate-800/60 flex items-center justify-center gap-4 text-[11px] text-slate-500">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-shield-halved text-emerald-400/80 text-[10px]"></i>
                                            <span>AES-256 GCM</span>
                                        </span>
                                        <span class="text-slate-700">•</span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-stopwatch text-indigo-400/80 text-[10px]"></i>
                                            <span>Hạn 5 phút</span>
                                        </span>
                                        <span class="text-slate-700">•</span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check text-teal-400/80 text-[10px]"></i>
                                            <span>Phiên an toàn</span>
                                        </span>
                                    </div>

                                </div> <!-- Đóng #auth-screen-otp -->

                            </div> <!-- Đóng .auth-screen-container -->
                        </div> <!-- Đóng Cột Phải -->
            </div> <!-- Đóng flex items-stretch (2 cột) -->
        </div> <!-- Đóng #auth-card (Master Glass Card bao quanh cả 2 div) -->



    </div> <!-- Đóng max-w-5xl container -->
</main>

        <footer class="py-4 text-center text-[11px] text-slate-600 relative z-10 border-t border-slate-900/60">
            © 2026 SmartRoom &amp; Renty. Nền tảng quản lý lưu trú &amp; thuê phòng thông minh.
        </footer>
    @endif

    <script>
        // =========================================================
        // TOAST NOTIFICATIONS
        // =========================================================
        function showToast(message, type = 'info') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            let title = 'Thông báo';
            if (type === 'success') title = 'Thành công';
            else if (type === 'error') title = 'Lỗi';

            card.innerHTML = `
                <div class="flex-grow min-w-0">
                    <h4>${title}</h4>
                    <p class="mt-0.5">${message}</p>
                </div>
                <button type="button" onclick="this.closest('.toast-card').remove()" class="text-slate-500 hover:text-slate-300 text-xs shrink-0 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(card);
            requestAnimationFrame(() => card.classList.add('show'));
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 350);
            }, 4000);
        }

        // =========================================================
        // =========================================================
        // ROLE HELPER & FORM CLEANER
        // =========================================================
        let currentAuthRole = 'guest';

        function setLoginRole(role) {
            currentAuthRole = role;
            const authRoleInput = document.getElementById('auth-role-input');
            if (authRoleInput) authRoleInput.value = role;

            document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            ['signin-login-error', 'signin-password-error', 'login-error-message', 'otp-login-input-error', 'screen-otp-error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            document.querySelectorAll('.toast-card').forEach(t => t.remove());

            const screenOtp = document.getElementById('auth-screen-otp');
            if (screenOtp && screenOtp.classList.contains('screen-visible')) {
                exitOtpScreen();
            }
        }
        window.setLoginRole = setLoginRole;

        // =========================================================
        // LOGIN METHOD TOGGLE (PASSWORD vs OTP)
        // =========================================================
        function switchLoginMethod(method) {
            const btnPwd = document.getElementById('method-btn-password');
            const btnOtp = document.getElementById('method-btn-otp');
            const formPwd = document.getElementById('form-signin-password');
            const formOtp = document.getElementById('form-signin-otp');

            if (!btnPwd || !btnOtp || !formPwd || !formOtp) return;

            // Xóa sạch lỗi khi chuyển giữa 2 tab Mật khẩu và Mã OTP
            document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            ['signin-login-error', 'signin-password-error', 'login-error-message', 'otp-login-input-error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            document.querySelectorAll('.toast-card').forEach(t => t.remove());

            if (method === 'password') {
                btnPwd.classList.add('active');
                btnOtp.classList.remove('active');
                formPwd.classList.remove('hidden');
                formOtp.classList.add('hidden');
            } else {
                btnOtp.classList.add('active');
                btnPwd.classList.remove('active');
                formPwd.classList.add('hidden');
                formOtp.classList.remove('hidden');

                const signinLogin = document.getElementById('signin-login');
                const otpInput = document.getElementById('otp-login-input');
                if (signinLogin && otpInput && !otpInput.value && signinLogin.value) {
                    otpInput.value = signinLogin.value;
                }
            }
        }
        window.switchLoginMethod = switchLoginMethod;

        // =========================================================
        // SIGNIN / SIGNUP MODE SWITCHER
        // =========================================================
        function switchAuthMode(mode, updateHistory = true) {
            const paneSignin = document.getElementById('auth-pane-signin');
            const paneSignup = document.getElementById('auth-pane-signup');
            const footerSignup = document.getElementById('footer-switch-to-signup');
            const footerSignin = document.getElementById('footer-switch-to-signin');
            const authHeading = document.getElementById('auth-heading');
            const authSubheading = document.getElementById('auth-subheading');

            const landlordBox = document.getElementById('landlord-portal-box');

            if (mode === 'signup') {
                if (paneSignin) paneSignin.classList.add('hidden');
                if (paneSignup) paneSignup.classList.remove('hidden');
                if (footerSignup) footerSignup.classList.add('hidden');
                if (footerSignin) footerSignin.classList.remove('hidden');
                if (landlordBox) landlordBox.classList.add('hidden');

                if (authHeading) authHeading.textContent = 'Đăng ký tài khoản khách';
                if (authSubheading) authSubheading.textContent = 'Tạo tài khoản để tìm phòng và đặt lịch xem trực tuyến';

                if (updateHistory && window.history && window.history.replaceState) {
                    window.history.replaceState({}, document.title, "{{ route('user.createUser') }}");
                }
            } else {
                if (paneSignup) paneSignup.classList.add('hidden');
                if (paneSignin) paneSignin.classList.remove('hidden');
                if (footerSignin) footerSignin.classList.add('hidden');
                if (footerSignup) footerSignup.classList.remove('hidden');
                if (landlordBox) landlordBox.classList.remove('hidden');

                if (authHeading) authHeading.textContent = 'Đăng nhập tài khoản';
                if (authSubheading) authSubheading.textContent = 'Hệ sinh thái kết nối Khách thuê và Quản trị cơ sở lưu trú thông minh';

                if (updateHistory && window.history && window.history.replaceState) {
                    window.history.replaceState({}, document.title, "{{ route('login') }}");
                }
            }
        }
        window.switchAuthMode = switchAuthMode;

        // =========================================================
        // FULL SCREEN OTP CONTROLS & TRANSITION
        // =========================================================
        let otpCountdownTimer = null;

        function enterOtpScreen(target, countdownSeconds = 60) {
            const screenMain = document.getElementById('auth-screen-main');
            const screenOtp = document.getElementById('auth-screen-otp');
            const targetText = document.getElementById('screen-otp-target-text');
            const errEl = document.getElementById('screen-otp-error');

            if (!screenMain || !screenOtp) return;

            // Đảm bảo các ô số ở trạng thái ẩn ban đầu
            screenOtp.classList.remove('digits-revealed');

            // 1. Chạy animation trượt màn hình từ Screen 1 sang Screen 2
            screenMain.classList.remove('screen-visible');
            screenMain.classList.add('screen-exit-left');

            screenOtp.classList.remove('screen-enter-right');
            screenOtp.classList.add('screen-visible');

            // Cập nhật target hiển thị
            if (targetText) targetText.textContent = target;

            // Xóa trắng 6 ô số và reset lỗi
            const boxes = document.querySelectorAll('#otp-digit-inputs .otp-digit-box');
            boxes.forEach(b => {
                b.value = '';
                b.classList.remove('filled', 'has-error');
            });
            if (errEl) errEl.classList.add('hidden');

            // 2. CHỜ MÀN HÌNH TRƯỢT XONG HOÀN TOÀN (500ms) RỒI MỚI HIỆN CÁC Ô OTP TỪ TỪ
            setTimeout(() => {
                screenOtp.classList.add('digits-revealed');

                // Khi chuỗi 6 ô đã lần lượt xuất hiện hoàn tất (~750ms sau), tự động focus vào ô đầu tiên
                setTimeout(() => {
                    const firstBox = document.querySelector('#otp-digit-inputs .otp-digit-box[data-index="0"]');
                    if (firstBox) firstBox.focus();
                }, 750);
            }, 500);

            // Bắt đầu đếm ngược theo thời gian thực tế còn lại
            startOtpCountdown(countdownSeconds);
        }
        window.enterOtpScreen = enterOtpScreen;

        function exitOtpScreen() {
            const screenMain = document.getElementById('auth-screen-main');
            const screenOtp = document.getElementById('auth-screen-otp');

            if (!screenMain || !screenOtp) return;

            // Reset trạng thái hiển thị ô số và lỗi
            screenOtp.classList.remove('digits-revealed');
            const screenErr = document.getElementById('screen-otp-error');
            if (screenErr) screenErr.classList.add('hidden');
            const boxes = document.querySelectorAll('#otp-digit-inputs .otp-digit-box');
            boxes.forEach(b => {
                b.value = '';
                b.classList.remove('filled', 'has-error');
            });

            // Chuyển ngược về màn hình chính
            screenOtp.classList.remove('screen-visible');
            screenOtp.classList.add('screen-enter-right');

            screenMain.classList.remove('screen-exit-left');
            screenMain.classList.add('screen-visible');

            // Focus lại ô nhập SĐT/Email
            setTimeout(() => {
                const loginInput = document.getElementById('otp-login-input');
                if (loginInput) loginInput.focus();
            }, 380);
        }
        window.exitOtpScreen = exitOtpScreen;

        function handleSendLoginOtp(isResend = false) {
            const loginInput = document.getElementById('otp-login-input');
            const val = loginInput ? loginInput.value.trim() : '';
            const errEl = document.getElementById('otp-login-input-error');
            const errText = document.getElementById('otp-login-input-error-text');
            const btnSend = document.getElementById('btn-send-login-otp');
            const spinner = document.getElementById('otp-send-spinner');
            const btnText = document.getElementById('otp-send-btn-text');
            const arrowIcon = document.getElementById('otp-send-arrow');
            const btnScreenResend = document.getElementById('btn-screen-resend-otp');

            if (!val) {
                if (isResend) {
                    exitOtpScreen();
                }
                if (loginInput) loginInput.classList.add('has-error');
                if (errText) errText.textContent = 'Vui lòng nhập tên tài khoản, email hoặc số điện thoại';
                if (errEl) errEl.classList.remove('hidden');
                return;
            }

            if (loginInput) loginInput.classList.remove('has-error');
            if (errEl) errEl.classList.add('hidden');

            if (isResend) {
                if (btnScreenResend) {
                    btnScreenResend.disabled = true;
                }
            } else {
                if (btnSend) btnSend.disabled = true;
                if (spinner) spinner.classList.remove('hidden');
                if (arrowIcon) arrowIcon.classList.add('hidden');
                if (btnText) btnText.textContent = 'Đang gửi...';
            }

            fetch("{{ route('user.sendLoginOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ login: val, is_resend: isResend, auth_role: currentAuthRole })
            })
            .then(res => res.json())
            .then(data => {
                // Khôi phục nút gửi ở form chính
                if (spinner) spinner.classList.add('hidden');
                if (arrowIcon) arrowIcon.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Gửi mã OTP xác nhận';
                if (btnSend) btnSend.disabled = false;

                if (data.success) {
                    showToast(data.message || 'Mã xác nhận đã được gửi thành công!', 'success');
                    
                    // Tự động nhận diện vai trò và chuyển tab mượt mà nếu đứng nhầm cổng
                    if (data.detected_role && data.detected_role !== currentAuthRole) {
                        setLoginRole(data.detected_role);
                        setTimeout(() => {
                            enterOtpScreen(data.masked_target || val, data.retry_after || 60);
                        }, 500);
                    } else {
                        // KÍCH HOẠT CHUYỂN SANG MÀN HÌNH XÁC THỰC OTP CHUYÊN BIỆT
                        enterOtpScreen(data.masked_target || val, data.retry_after || 60);
                    }
                } else {
                    if (btnScreenResend) btnScreenResend.disabled = false;
                    let displayMsg = data.message || 'Không thể gửi mã OTP';
                    if (displayMsg.toLowerCase().includes('too many attempts')) {
                        displayMsg = 'Bạn đã gửi yêu cầu quá nhanh. Vui lòng đợi 1 phút trước khi thử lại.';
                    }
                    if (isResend) {
                        const screenErr = document.getElementById('screen-otp-error');
                        const screenErrText = document.getElementById('screen-otp-error-text');
                        if (screenErrText) screenErrText.textContent = displayMsg;
                        if (screenErr) screenErr.classList.remove('hidden');
                    } else {
                        if (errText) errText.textContent = displayMsg;
                        if (errEl) errEl.classList.remove('hidden');
                        if (loginInput) loginInput.classList.add('has-error');
                    }
                    showToast(displayMsg, 'error');
                }
            })
            .catch(() => {
                if (spinner) spinner.classList.add('hidden');
                if (arrowIcon) arrowIcon.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Gửi mã OTP xác nhận';
                if (btnSend) btnSend.disabled = false;
                if (btnScreenResend) btnScreenResend.disabled = false;
                showToast('Lỗi kết nối máy chủ, vui lòng thử lại', 'error');
            });
        }
        window.handleSendLoginOtp = handleSendLoginOtp;

        function startOtpCountdown(seconds) {
            const btnResend = document.getElementById('btn-screen-resend-otp');
            const countdownEl = document.getElementById('screen-otp-countdown');

            clearInterval(otpCountdownTimer);
            let remaining = seconds;

            if (btnResend) {
                btnResend.disabled = true;
            }
            if (countdownEl) {
                countdownEl.textContent = `(${remaining}s)`;
            }

            otpCountdownTimer = setInterval(() => {
                remaining--;
                if (countdownEl) {
                    countdownEl.textContent = `(${remaining}s)`;
                }

                if (remaining <= 0) {
                    clearInterval(otpCountdownTimer);
                    if (btnResend) {
                        btnResend.disabled = false;
                    }
                    if (countdownEl) {
                        countdownEl.textContent = '(Hết hạn)';
                    }
                }
            }, 1000);
        }

        function handleVerifyLoginOtp() {
            const loginInput = document.getElementById('otp-login-input');
            const loginVal = loginInput ? loginInput.value.trim() : '';

            // Gom 6 chữ số từ 6 box
            const boxes = document.querySelectorAll('#otp-digit-inputs .otp-digit-box');
            let codeVal = '';
            boxes.forEach(b => codeVal += (b.value || '').trim());

            const errEl = document.getElementById('screen-otp-error');
            const errText = document.getElementById('screen-otp-error-text');
            const btnVerify = document.getElementById('btn-screen-verify-otp');
            const spinner = document.getElementById('screen-otp-spinner');

            if (!codeVal || codeVal.length !== 6) {
                boxes.forEach(b => b.classList.add('has-error'));
                if (errText) errText.textContent = 'Vui lòng nhập đủ 6 chữ số mã OTP';
                if (errEl) errEl.classList.remove('hidden');
                
                // Focus ô đầu tiên còn trống
                for (let b of boxes) {
                    if (!b.value) { b.focus(); break; }
                }
                return;
            }

            boxes.forEach(b => b.classList.remove('has-error'));
            if (errEl) errEl.classList.add('hidden');

            if (btnVerify) btnVerify.disabled = true;
            if (spinner) spinner.classList.remove('hidden');

            fetch("{{ route('user.loginWithOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ login: loginVal, otp: codeVal, auth_role: currentAuthRole, remember: true })
            })
            .then(res => res.json())
            .then(data => {
                if (spinner) spinner.classList.add('hidden');

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || "{{ route('renty.user') }}";
                    }, 500);
                } else {
                    if (btnVerify) btnVerify.disabled = false;
                    if (errText) errText.textContent = data.message || 'Mã xác thực không chính xác';
                    if (errEl) errEl.classList.remove('hidden');
                    boxes.forEach(b => b.classList.add('has-error'));
                    showToast(data.message || 'Mã xác thực không chính xác', 'error');
                }
            })
            .catch(() => {
                if (spinner) spinner.classList.add('hidden');
                if (btnVerify) btnVerify.disabled = false;
                showToast('Lỗi máy chủ khi xác thực OTP, vui lòng thử lại', 'error');
            });
        }
        window.handleVerifyLoginOtp = handleVerifyLoginOtp;

        function setupOtpDigitBoxes() {
            const boxes = document.querySelectorAll('#otp-digit-inputs .otp-digit-box');
            if (!boxes.length) return;

            boxes.forEach((box, idx) => {
                // Khi nhập ký tự
                box.addEventListener('input', function(e) {
                    const val = this.value.replace(/\D/g, '');
                    this.value = val ? val[0] : '';

                    const errEl = document.getElementById('screen-otp-error');
                    if (errEl) errEl.classList.add('hidden');
                    boxes.forEach(b => b.classList.remove('has-error'));

                    if (this.value) {
                        this.classList.add('filled');
                        const next = boxes[idx + 1];
                        if (next) {
                            next.focus();
                        } else {
                            // Nếu đã là ô cuối cùng và đủ 6 số, tự động xác thực
                            let full = '';
                            boxes.forEach(b => full += b.value.trim());
                            if (full.length === 6) {
                                handleVerifyLoginOtp();
                            }
                        }
                    } else {
                        this.classList.remove('filled');
                    }
                });

                // Xử lý phím Backspace, Enter, Mũi tên
                box.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!this.value) {
                            const prev = boxes[idx - 1];
                            if (prev) {
                                prev.focus();
                                prev.value = '';
                                prev.classList.remove('filled');
                            }
                        } else {
                            this.value = '';
                            this.classList.remove('filled');
                        }
                        e.preventDefault();
                    } else if (e.key === 'ArrowLeft') {
                        const prev = boxes[idx - 1];
                        if (prev) prev.focus();
                    } else if (e.key === 'ArrowRight') {
                        const next = boxes[idx + 1];
                        if (next) next.focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        handleVerifyLoginOtp();
                    }
                });

                // Hỗ trợ Paste cả chuỗi OTP 6 số
                box.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const clipText = (e.clipboardData || window.clipboardData).getData('text') || '';
                    const digits = clipText.replace(/\D/g, '').slice(0, 6);
                    if (!digits) return;

                    boxes.forEach((b, i) => {
                        b.value = digits[i] || '';
                        if (digits[i]) b.classList.add('filled');
                        else b.classList.remove('filled');
                    });

                    if (digits.length === 6) {
                        boxes[5].focus();
                        handleVerifyLoginOtp();
                    } else {
                        const nextEmpty = boxes[digits.length];
                        if (nextEmpty) nextEmpty.focus();
                    }
                });
            });
        }

        // =========================================================
        // FORM VALIDATION & UTILITIES
        // =========================================================
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

        function clearSigninError(fieldId) {
            const input = document.getElementById(fieldId);
            const errEl = document.getElementById(fieldId + '-error');
            const serverErr = document.getElementById('login-error-message');
            if (input) input.classList.remove('has-error');
            if (errEl) errEl.classList.add('hidden');
            if (serverErr) serverErr.classList.add('hidden');
        }

        function validateSigninLoginBlur(input) {
            const val = input.value.trim();
            const errEl = document.getElementById('signin-login-error');
            const errText = document.getElementById('signin-login-error-text');
            if (!val) {
                input.classList.add('has-error');
                if (errText) errText.textContent = 'Vui lòng nhập tài khoản đăng nhập';
                if (errEl) errEl.classList.remove('hidden');
                return false;
            }
            clearSigninError('signin-login');
            return true;
        }

        function validateSigninPasswordBlur(input) {
            const val = input.value;
            const errEl = document.getElementById('signin-password-error');
            const errText = document.getElementById('signin-password-error-text');
            if (!val) {
                input.classList.add('has-error');
                if (errText) errText.textContent = 'Vui lòng nhập mật khẩu';
                if (errEl) errEl.classList.remove('hidden');
                return false;
            }
            if (val.length < 6) {
                input.classList.add('has-error');
                if (errText) errText.textContent = 'Mật khẩu phải có tối thiểu 6 ký tự.';
                if (errEl) errEl.classList.remove('hidden');
                return false;
            }
            clearSigninError('signin-password');
            return true;
        }

        function handleSigninSubmit(e) {
            e.preventDefault();
            const loginInput = document.getElementById('signin-login');
            const passwordInput = document.getElementById('signin-password');
            const rememberInput = document.getElementById('signin-remember');
            const loginVal = loginInput ? loginInput.value.trim() : '';
            const passwordVal = passwordInput ? passwordInput.value : '';
            const rememberVal = rememberInput ? rememberInput.checked : true;

            let hasError = false;

            if (!loginVal) {
                if (loginInput) {
                    validateSigninLoginBlur(loginInput);
                    if (!hasError) loginInput.focus();
                }
                hasError = true;
            }

            if (!passwordVal) {
                if (passwordInput) {
                    validateSigninPasswordBlur(passwordInput);
                    if (!hasError) passwordInput.focus();
                }
                hasError = true;
            } else if (passwordVal.length < 6) {
                if (passwordInput) {
                    validateSigninPasswordBlur(passwordInput);
                    if (!hasError) passwordInput.focus();
                }
                hasError = true;
            }

            if (hasError) {
                if (!loginVal && !passwordVal) {
                    showToast('Vui lòng nhập tài khoản đăng nhập và mật khẩu.', 'error');
                } else if (!loginVal) {
                    showToast('Vui lòng nhập tài khoản đăng nhập', 'error');
                } else if (!passwordVal) {
                    showToast('Vui lòng nhập mật khẩu', 'error');
                } else {
                    showToast('Mật khẩu phải có tối thiểu 6 ký tự.', 'error');
                }
                return false;
            }

            const btn = document.getElementById('btn-submit-signin');
            const spinner = document.getElementById('signin-spinner');
            const btnText = document.getElementById('btn-submit-signin-text');
            if (btn) btn.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            if (btnText) btnText.textContent = 'Đang xử lý...';

            fetch("{{ route('user.authUser') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    login: loginVal,
                    password: passwordVal,
                    remember: rememberVal,
                    auth_role: currentAuthRole
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Nếu phát hiện tài khoản thuộc vai trò khác với tab đang chọn -> TỰ ĐỘNG CHUYỂN TAB MƯỢT MÀ!
                    if (data.detected_role && data.detected_role !== currentAuthRole) {
                        setLoginRole(data.detected_role);
                        if (data.detected_role === 'landlord') {
                            showToast('Nhận diện tài khoản Đối tác / Quản trị viên. Đang chuyển bạn sang Cổng Chủ nhà trọ...', 'info');
                        } else {
                            showToast('Nhận diện tài khoản Khách thuê. Đang chuyển bạn sang Cổng Khách thuê...', 'info');
                        }
                        setTimeout(() => {
                            window.location.href = data.redirect_url || "{{ route('renty.user') }}";
                        }, 850);
                    } else {
                        showToast(data.message || 'Đăng nhập thành công! Đang chuyển hướng...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect_url || "{{ route('renty.user') }}";
                        }, 400);
                    }
                } else {
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.classList.add('hidden');
                    if (btnText) btnText.textContent = 'Đăng nhập';
                    
                    const pwdErr = document.getElementById('signin-password-error');
                    const pwdErrText = document.getElementById('signin-password-error-text');
                    if (pwdErrText) pwdErrText.textContent = data.message || 'Thông tin tài khoản hoặc mật khẩu không chính xác.';
                    if (pwdErr) pwdErr.classList.remove('hidden');
                    if (passwordInput) passwordInput.classList.add('has-error');
                    if (loginInput) loginInput.classList.add('has-error');
                    showToast(data.message || 'Thông tin tài khoản hoặc mật khẩu không chính xác.', 'error');
                }
            })
            .catch(() => {
                if (btn) btn.disabled = false;
                if (spinner) spinner.classList.add('hidden');
                if (btnText) btnText.textContent = 'Đăng nhập';
                showToast('Lỗi kết nối máy chủ, vui lòng thử lại.', 'error');
            });

            return false;
        }

        function evalPasswordStrength(pwd) {
            const box = document.getElementById('signup-strength-box');
            const status = document.getElementById('signup-pwd-status');
            if (!box || !status) return;

            box.className = 'strength-bar-track';

            if (!pwd || pwd.length === 0) {
                status.textContent = 'Tối thiểu 6 ký tự';
                status.className = 'text-[10px] text-slate-500';
                return;
            }

            let score = 0;
            if (pwd.length >= 6) score++;
            if (pwd.length >= 8 && /[0-9]/.test(pwd)) score++;
            if (pwd.length >= 8 && /[^A-Za-z0-9]/.test(pwd) && /[A-Z]/.test(pwd)) score++;

            if (score <= 1) {
                box.classList.add('strength-weak');
                status.textContent = 'Độ mạnh: Yếu';
                status.className = 'text-[10px] text-rose-400';
            } else if (score === 2) {
                box.classList.add('strength-medium');
                status.textContent = 'Độ mạnh: Vừa';
                status.className = 'text-[10px] text-amber-400';
            } else {
                box.classList.add('strength-strong');
                status.textContent = 'Độ mạnh: Tốt';
                status.className = 'text-[10px] text-emerald-400';
            }
        }

        function validateSignupUsernameBlur(input) {
            const val = input.value.trim();
            const feedback = document.getElementById('signup-username-feedback');
            const msg = document.getElementById('signup-username-msg');
            if (!feedback) return;

            if (!val) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Vui lòng nhập tên đăng nhập';
                input.classList.add('has-error');
                return;
            }

            if (!/^[a-zA-Z0-9_-]{3,50}$/.test(val)) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Tên tài khoản 3–50 ký tự, chỉ gồm chữ cái, số, gạch nối hoặc gạch dưới';
                input.classList.add('has-error');
                return;
            }

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
                    feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.add('has-error');
                } else {
                    feedback.className = 'mt-1 text-[11px] text-emerald-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.remove('has-error');
                }
            })
            .catch(() => {});
        }

        function validatePhoneInput(input) {
            input.value = input.value.replace(/\D/g, '').slice(0, 10);
            const val = input.value;
            const feedback = document.getElementById('signup-phone-feedback');
            const msg = document.getElementById('signup-phone-msg');
            if (!feedback) return;

            if (!val) {
                feedback.className = 'hidden';
                input.classList.remove('has-error');
                return;
            }

            feedback.classList.remove('hidden');

            if (!val.startsWith('0')) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Số điện thoại phải bắt đầu bằng chữ số 0';
                input.classList.add('has-error');
            } else if (val.length < 10) {
                feedback.className = 'mt-1 text-[11px] text-amber-400 block';
                if (msg) msg.textContent = `Số điện thoại gồm 10 số (${val.length}/10)`;
                input.classList.remove('has-error');
            } else if (val.length === 10) {
                feedback.className = 'mt-1 text-[11px] text-emerald-400 block';
                if (msg) msg.textContent = 'Số điện thoại hợp lệ';
                input.classList.remove('has-error');
            }
        }

        function validatePhoneBlur(input) {
            const val = input.value.trim();
            const feedback = document.getElementById('signup-phone-feedback');
            const msg = document.getElementById('signup-phone-msg');
            if (!feedback) return;

            if (!val) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Vui lòng nhập số điện thoại';
                input.classList.add('has-error');
                return;
            }

            if (!/^0[0-9]{9}$/.test(val)) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Số điện thoại phải gồm đúng 10 chữ số bắt đầu bằng số 0';
                input.classList.add('has-error');
                return;
            }

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
                    feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.add('has-error');
                } else {
                    feedback.className = 'mt-1 text-[11px] text-emerald-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.remove('has-error');
                }
            })
            .catch(() => {});
        }

        let guestEmailDebounceTimer = null;
        function isEmailValidFormat(email) {
            return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.trim());
        }

        function updateEmailOtpState() {
            const emailInput = document.getElementById('signup-email');
            const pillEmail = document.getElementById('signup-pill-email');
            const radioEmail = document.getElementById('signup-method-email');
            const radioPhone = document.getElementById('signup-method-phone');
            const hint = document.getElementById('email-method-hint');

            if (!emailInput || !pillEmail || !radioEmail) return;

            const val = emailInput.value.trim();
            const isValid = isEmailValidFormat(val);

            if (isValid) {
                radioEmail.disabled = false;
                pillEmail.classList.remove('opacity-40', 'cursor-not-allowed');
                if (hint) hint.classList.add('hidden');
            } else {
                const wasChecked = radioEmail.checked;
                radioEmail.disabled = true;
                pillEmail.classList.add('opacity-40', 'cursor-not-allowed');

                if (hint) hint.classList.remove('hidden');

                if (wasChecked && radioPhone) {
                    radioPhone.checked = true;
                    showToast('Đã chuyển sang nhận OTP qua SĐT vì email chưa hợp lệ', 'info');
                }
            }
        }

        function handleEmailInput(input) {
            clearTimeout(guestEmailDebounceTimer);
            guestEmailDebounceTimer = setTimeout(updateEmailOtpState, 300);
        }

        function handleEmailBlur(input) {
            clearTimeout(guestEmailDebounceTimer);
            updateEmailOtpState();
            const val = input.value.trim();
            const feedback = document.getElementById('signup-email-feedback');
            const msg = document.getElementById('signup-email-msg');
            if (!feedback) return;

            if (!val) {
                feedback.className = 'hidden';
                input.classList.remove('has-error');
                return;
            }

            if (!isEmailValidFormat(val)) {
                feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                if (msg) msg.textContent = 'Địa chỉ email không đúng định dạng';
                input.classList.add('has-error');
                return;
            }

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
                    feedback.className = 'mt-1 text-[11px] text-rose-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.add('has-error');
                } else {
                    feedback.className = 'mt-1 text-[11px] text-emerald-400 block';
                    if (msg) msg.textContent = data.message;
                    input.classList.remove('has-error');
                }
            })
            .catch(() => {});
        }

        function onSignupMethodChange(method) {
            const emailInput = document.getElementById('signup-email');
            if (method === 'email' && emailInput && !emailInput.value.trim()) {
                emailInput.focus();
                showToast('Vui lòng nhập email để nhận mã OTP', 'info');
            }
        }

        function handleSignupSubmit(e) {
            const usernameInput = document.getElementById('signup-username');
            const phoneInput = document.getElementById('signup-phone');
            const emailInput = document.getElementById('signup-email');
            const methodEl = document.querySelector('input[name="verification_method"]:checked');
            const method = methodEl ? methodEl.value : 'phone';

            const usernameVal = usernameInput ? usernameInput.value.trim() : '';
            if (!usernameVal || !/^[a-zA-Z0-9_-]{3,50}$/.test(usernameVal)) {
                e.preventDefault();
                if (usernameInput) {
                    validateSignupUsernameBlur(usernameInput);
                    usernameInput.focus();
                }
                showToast('Tên đăng nhập từ 3–50 ký tự (chữ, số, gạch nối hoặc gạch dưới)', 'error');
                return false;
            }

            const phoneVal = phoneInput ? phoneInput.value.trim() : '';
            if (!phoneVal || !/^0[0-9]{9}$/.test(phoneVal)) {
                e.preventDefault();
                if (phoneInput) {
                    validatePhoneBlur(phoneInput);
                    phoneInput.focus();
                }
                showToast('Số điện thoại là bắt buộc (10 số bắt đầu bằng số 0)', 'error');
                return false;
            }

            if (method === 'email') {
                const emailVal = emailInput ? emailInput.value.trim() : '';
                if (!emailVal || !isEmailValidFormat(emailVal)) {
                    e.preventDefault();
                    if (emailInput) {
                        emailInput.classList.add('has-error');
                        emailInput.focus();
                    }
                    showToast('Vui lòng nhập địa chỉ email hợp lệ để nhận OTP.', 'error');
                    return false;
                }
            }

            const terms = document.getElementById('signup-terms');
            if (terms && !terms.checked) {
                e.preventDefault();
                showToast('Vui lòng đồng ý với Điều khoản dịch vụ và Chính sách bảo mật.', 'error');
                return false;
            }

            return true;
        }

        // =========================================================
        // THEME MODE TOGGLE
        // =========================================================
        function toggleThemeMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark') || !html.classList.contains('theme-light');
            if (isDark) {
                html.classList.remove('dark');
                html.classList.add('theme-light');
                localStorage.setItem('smartroom_theme', 'light');
            } else {
                html.classList.remove('theme-light');
                html.classList.add('dark');
                localStorage.setItem('smartroom_theme', 'dark');
            }
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isLight = document.documentElement.classList.contains('theme-light');
            document.querySelectorAll('[data-theme-icon]').forEach(icon => {
                if (isLight) {
                    icon.className = 'fa-solid fa-sun text-amber-500 text-xs';
                } else {
                    icon.className = 'fa-solid fa-moon text-slate-400 text-xs';
                }
            });
        }

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) window.location.reload();
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('smartroom_theme') === 'light') {
                document.documentElement.classList.add('theme-light');
            }
            updateThemeIcons();

            @if(($page === 'create' && !Auth::check()) || $errors->has('phone') || $errors->has('email') || $errors->has('name') || $errors->has('password') || old('phone') || old('email'))
                switchAuthMode('signup', false);
            @endif

            updateEmailOtpState();

            // Keyboard & Auto-submit cho Form OTP
            const otpLoginInput = document.getElementById('otp-login-input');
            if (otpLoginInput) {
                otpLoginInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleSendLoginOtp();
                    }
                });
                otpLoginInput.addEventListener('input', function() {
                    const errEl = document.getElementById('otp-login-input-error');
                    if (errEl) errEl.classList.add('hidden');
                    this.classList.remove('has-error');
                });
            }

            // Khởi tạo 6 ô số OTP độc lập
            setupOtpDigitBoxes();

            @if(session('success'))
                showToast(@json(session('success')), "success");
            @endif
            @if(session('error'))
                showToast(@json(session('error')), "error");
            @endif
            @if($errors->any())
                showToast(@json($errors->first()), "error");
            @endif
        });
    </script>
</body>
</html>
