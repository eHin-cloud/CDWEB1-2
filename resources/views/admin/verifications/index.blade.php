<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Duyệt Hồ Sơ KYC & Cấp Tích Xanh - Superadmin Console | SmartRoom & Renty</title>
    @include('admin.partials.theme-head-script')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/npm/@laragear/webpass@2/dist/webpass.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .panel, .glass-panel {
            background: rgba(13, 18, 31, 0.75);
            border: 1px solid rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .glass-panel:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }
        .glass-header { 
            background: rgba(8, 11, 17, 0.85); 
            backdrop-filter: blur(12px); 
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }

        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 420px;
            width: 100%;
        }
        .toast-card {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .toast-card.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-success { border-left: 4px solid #10b981; }
        .toast-error { border-left: 4px solid #f43f5e; }
        .toast-info { border-left: 4px solid #3b82f6; }

        /* Light Mode High Contrast Enhancements (Đồng bộ chuẩn Superadmin) */
        html.theme-light body, body.theme-light { 
            background: #f3f7fb !important; 
            color: #0f172a !important; 
        }
        html.theme-light .panel, body.theme-light .panel,
        html.theme-light .glass-panel, body.theme-light .glass-panel { 
            background: #ffffff !important; 
            border-color: #e2e8f0 !important; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important; 
        }
        html.theme-light .glass-header, body.theme-light .glass-header { 
            background: rgba(255, 255, 255, 0.95) !important; 
            border-color: #e2e8f0 !important; 
        }
        html.theme-light table thead, body.theme-light table thead,
        html.theme-light table thead tr, body.theme-light table thead tr { 
            background-color: #f8fafc !important; 
            border-bottom: 1px solid #e2e8f0 !important; 
        }
        html.theme-light table thead th, body.theme-light table thead th { 
            color: #334155 !important; 
            font-weight: 700 !important; 
        }
        html.theme-light table tbody tr, body.theme-light table tbody tr {
            border-color: #f1f5f9 !important;
        }
        html.theme-light table tbody tr:hover, body.theme-light table tbody tr:hover {
            background-color: #f8fafc !important;
        }
        html.theme-light .text-white, body.theme-light .text-white { 
            color: #0f172a !important; 
        }
        html.theme-light .text-slate-100, body.theme-light .text-slate-100 { 
            color: #0f172a !important; 
        }
        html.theme-light .text-slate-200, body.theme-light .text-slate-200 { 
            color: #1e293b !important; 
        }
        html.theme-light .text-slate-300, body.theme-light .text-slate-300 { 
            color: #334155 !important; 
        }
        html.theme-light .text-slate-400, body.theme-light .text-slate-400 { 
            color: #64748b !important; 
        }
        html.theme-light .border-slate-800, body.theme-light .border-slate-800,
        html.theme-light .border-slate-850, body.theme-light .border-slate-850,
        html.theme-light .border-slate-900, body.theme-light .border-slate-900 {
            border-color: #e2e8f0 !important;
        }
        html.theme-light .bg-slate-950, body.theme-light .bg-slate-950,
        html.theme-light .bg-slate-950\/60, body.theme-light .bg-slate-950\/60,
        html.theme-light .bg-slate-950\/70, body.theme-light .bg-slate-950\/70,
        html.theme-light .bg-slate-950\/80, body.theme-light .bg-slate-950\/80,
        html.theme-light .bg-slate-900, body.theme-light .bg-slate-900,
        html.theme-light .bg-slate-900\/40, body.theme-light .bg-slate-900\/40,
        html.theme-light .bg-slate-900\/60, body.theme-light .bg-slate-900\/60,
        html.theme-light .bg-slate-900\/80, body.theme-light .bg-slate-900\/80 {
            background-color: #ffffff !important;
        }
        html.theme-light input, body.theme-light input,
        html.theme-light select, body.theme-light select,
        html.theme-light textarea, body.theme-light textarea {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light input::placeholder, body.theme-light input::placeholder,
        html.theme-light textarea::placeholder, body.theme-light textarea::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light #modalRejectKYC > div {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
        }
        html.theme-light .toast-card {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.1) !important;
        }
        html.theme-light .toast-card p {
            color: #1e293b !important;
        }
        html.theme-light .nav-tabs-wrapper {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        html.theme-light .nav-tab-inactive {
            color: #475569 !important;
        }
        html.theme-light .nav-tab-inactive:hover {
            color: #0f172a !important;
            background-color: #e2e8f0 !important;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-emerald-500 selection:text-white overflow-hidden font-sans">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- STANDARDIZED SUPERADMIN TOPBAR -->
        <header class="h-16 border-b border-slate-900 glass-header flex items-center justify-between px-6 lg:px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.superadmin.dashboard') }}" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition-all text-xs" title="Quay lại Superadmin Console">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-extrabold tracking-wider uppercase">
                    <i class="fa-solid fa-id-card-clip text-[10px]"></i> FEAT_10_ADMIN_KYC
                </span>
                <div>
                    <h1 class="text-base font-extrabold text-slate-100 tracking-tight flex items-center gap-2">
                        Kiểm Duyệt Hồ Sơ KYC & Cấp Tích Xanh
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button id="btnRegisterPasskey" onclick="registerBiometricDevice()" class="inline-flex items-center gap-2 rounded-xl border border-sky-800 bg-sky-950/40 backdrop-blur px-3.5 py-1.5 text-xs font-bold text-sky-300 hover:text-white hover:border-sky-500 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i id="passkeyIcon" class="fa-solid fa-fingerprint text-sky-400"></i>
                    <span id="passkeyBtnText">Đăng ký Passkey</span>
                </button>

                <a href="{{ route('admin.audit-logs') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-all hidden sm:flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                    <span>Nhật Ký Kiểm Toán</span>
                </a>
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl hidden lg:flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </header>

        <div id="toast-container"></div>

        <!-- MAIN CONTENT AREA -->
        <main class="p-6 lg:p-8 space-y-6 flex-grow">
            <!-- Header Title Card -->
            <div class="panel rounded-2xl p-6 border border-emerald-500/20 bg-gradient-to-r from-emerald-950/20 via-slate-900/60 to-purple-950/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        <i class="fa-solid fa-id-card-clip text-emerald-400"></i> Thẩm Định Hồ Sơ & Cấp Huy Hiệu Tích Xanh
                    </h2>
                    <p class="text-xs text-slate-400">
                        Đối soát CCCD 2 mặt, giấy tờ sở hữu hợp pháp và cấp huy hiệu Tích Xanh uy tín cho cơ sở lưu trú trên sàn Renty.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-2.5 text-xs font-bold text-slate-200 hover:text-white hover:border-indigo-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fa-solid fa-users-gear text-indigo-400"></i>
                        Quản lý người dùng
                    </a>
                </div>
            </div>

            <!-- Navigation Tabs & View Toggles -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2 nav-tabs-wrapper bg-slate-900/40 p-1.5 rounded-xl border border-slate-800/80 max-w-max">
                    <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.verifications.index') && !request()->routeIs('admin.kyc.pending') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-file-signature text-emerald-400"></i> Tất Cả Hồ Sơ
                    </a>
                    <a href="{{ route('admin.kyc.pending') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.kyc.pending') ? 'bg-amber-500 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-hourglass-half text-amber-400"></i> Chờ Duyệt (Pending)
                    </a>
                    <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.audit-logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i> Nhật Ký Kiểm Toán
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.analytics') ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-chart-pie text-rose-300"></i> Thống Kê
                    </a>
                </div>

                <!-- Chế độ hiển thị Table / Cards -->
                <div class="flex items-center gap-2 nav-tabs-wrapper bg-slate-900/60 p-1.5 rounded-xl border border-slate-800 max-w-max">
                    <button type="button" onclick="setViewMode('table')" id="btnViewTable" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm">
                        <i class="fa-solid fa-table-list"></i> Bảng Danh Sách
                    </button>
                    <button type="button" onclick="setViewMode('cards')" id="btnViewCards" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800">
                        <i class="fa-solid fa-grip"></i> Thẻ Chi Tiết
                    </button>
                </div>
            </div>

            <!-- Quick Stats Dashboard -->
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 text-base">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Tất Cả Hồ Sơ</p>
                        <p class="text-xl font-bold text-slate-100 mt-0.5">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 text-base">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đang Chờ Duyệt</p>
                        <p class="text-xl font-bold text-amber-400 mt-0.5">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 text-base">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đã Phê Duyệt</p>
                        <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ $stats['approved'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20 text-base">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đã Từ Chối</p>
                        <p class="text-xl font-bold text-rose-400 mt-0.5">{{ $stats['rejected'] ?? 0 }}</p>
                    </div>
                </div>
            </section>

            <!-- Filters Section -->
            <section class="glass-panel rounded-2xl p-5 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="verification-search" oninput="filterRequests()" placeholder="Tìm theo tên nhà trọ, chủ trọ, số điện thoại..." class="w-full rounded-xl border border-slate-800 bg-slate-950/60 pl-10 pr-4 py-2.5 text-xs text-slate-200 outline-none focus:border-emerald-500/80 transition-all font-semibold placeholder-slate-500">
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select id="status-filter" onchange="filterRequests()" class="w-full md:w-40 rounded-xl border border-slate-800 bg-slate-950/60 px-3.5 py-2.5 text-xs text-slate-300 outline-none focus:border-emerald-500/80 transition-all font-semibold font-sans">
                        <option value="all">Trạng thái (Tất cả)</option>
                        <option value="pending" {{ request()->routeIs('admin.kyc.pending') ? 'selected' : '' }}>Đang chờ duyệt</option>
                        <option value="approved">Đã phê duyệt</option>
                        <option value="rejected">Đã từ chối</option>
                        <option value="superseded">Đã bị thay thế</option>
                    </select>

                    <select id="type-filter" onchange="filterRequests()" class="w-full md:w-40 rounded-xl border border-slate-800 bg-slate-950/60 px-3.5 py-2.5 text-xs text-slate-300 outline-none focus:border-emerald-500/80 transition-all font-semibold font-sans">
                        <option value="all">Loại xác minh (Tất cả)</option>
                        <option value="kyc">KYC nhận tiền</option>
                        <option value="premium">Tích xanh Premium</option>
                    </select>
                </div>
            </section>

            <!-- 1. BẢNG DANH SÁCH tblKYC (Theo UI Elements Description của Spec) -->
            <section id="table-view-wrapper" class="glass-panel rounded-3xl overflow-hidden mb-8 border border-slate-800 shadow-2xl">
                <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-400"></i> Danh Sách Hồ Sơ Thẩm Định KYC (tblKYC)
                    </h2>
                    <span class="text-xs text-slate-400">Tổng cộng: <strong class="text-emerald-400">{{ count($requests) }}</strong> hồ sơ</span>
                </div>

                <div class="overflow-x-auto">
                    <table id="tblKYC" class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800/80 bg-slate-950/70 text-slate-400 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-4 px-6">Chủ trọ</th>
                                <th class="py-4 px-4">SĐT</th>
                                <th class="py-4 px-4">Số CCCD</th>
                                <th class="py-4 px-4">Ảnh đính kèm</th>
                                <th class="py-4 px-4">Ngày nộp</th>
                                <th class="py-4 px-4">Trạng thái</th>
                                <th class="py-4 px-6 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850/60" id="tblKYC-tbody">
                            @forelse($requests as $request)
                                @php
                                    $senderName = $request->user?->name ?? 'Không rõ';
                                    $phone = $request->user?->phone ?? 'Chưa cập nhật';
                                    $maskedCccd = $request->masked_cccd_number ?? ($request->cccd_number ? substr($request->cccd_number, 0, 3) . '******' . substr($request->cccd_number, -3) : 'Chưa cập nhật');
                                    $statusName = match ($request->status) {
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        'superseded' => 'Thay thế',
                                        default => 'Chờ duyệt',
                                    };
                                    $statusBadge = match ($request->status) {
                                        'approved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                        'rejected' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                        'superseded' => 'bg-slate-800 text-slate-400 border border-slate-700',
                                        default => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-900/40 transition-colors user-request-row"
                                    data-tenant="{{ strtolower($request->tenant?->name ?? '') }}"
                                    data-user="{{ strtolower($senderName) }}"
                                    data-phone="{{ strtolower($phone) }}"
                                    data-status="{{ $request->status }}"
                                    data-type="{{ $request->type }}">
                                    <!-- Chủ trọ -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 text-slate-200 font-bold flex items-center justify-center uppercase select-none text-xs">
                                                {{ mb_substr($senderName, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-white text-sm">{{ $senderName }}</p>
                                                <p class="text-slate-400 text-xs mt-0.5">{{ $request->tenant?->name ?? 'Cơ sở chưa đặt tên' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- SĐT -->
                                    <td class="py-4 px-4 font-semibold text-slate-300">
                                        {{ $phone }}
                                    </td>

                                    <!-- Số CCCD -->
                                    <td class="py-4 px-4 font-mono font-semibold text-slate-300">
                                        <span class="px-2 py-1 rounded bg-slate-900/80 border border-slate-800">
                                            {{ $maskedCccd }}
                                        </span>
                                    </td>

                                    <!-- Ảnh đính kèm (CCCD trước/sau) -->
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @foreach($request->documents as $doc)
                                                @php
                                                    $isFront = str_contains(strtolower($doc->document_type), 'front') || str_contains(strtolower($doc->document_type), 'truoc');
                                                    $isBack = str_contains(strtolower($doc->document_type), 'back') || str_contains(strtolower($doc->document_type), 'sau');
                                                    $badgeText = $isFront ? 'CCCD Trước' : ($isBack ? 'CCCD Sau' : str_replace('_', ' ', strtoupper($doc->document_type)));
                                                @endphp

                                                @if($request->allowsDefaultAdminDocumentReview())
                                                    <a href="{{ route('admin.verification-documents.show', $doc) }}" 
                                                       target="_blank" 
                                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-sky-300 hover:text-white hover:border-sky-500/50 hover:bg-sky-950/30 transition-all"
                                                       title="Xem tài liệu bảo mật">
                                                        <i class="fa-solid fa-image text-sky-400"></i>
                                                        <span>{{ $badgeText }}</span>
                                                        <i class="fa-solid fa-up-right-from-square text-[9px] text-slate-500"></i>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-850 bg-slate-950/40 text-[11px] font-semibold text-slate-400">
                                                        <i class="fa-solid fa-lock text-amber-400"></i> {{ $badgeText }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Ngày nộp -->
                                    <td class="py-4 px-4 text-slate-400 whitespace-nowrap">
                                        {{ $request->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    <!-- Trạng thái -->
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider {{ $statusBadge }}">
                                            {{ $statusName }}
                                        </span>
                                    </td>

                                    <!-- Hành động (btnApprove & btnReject) -->
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        @if($request->status === 'pending')
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Button btnApprove: Phê duyệt & Cấp tích xanh (Nền Emerald) -->
                                                <form method="POST" action="{{ route('admin.kyc.approve', $request->id) }}" onsubmit="return confirm('Xác nhận phê duyệt hồ sơ định danh KYC và cấp huy hiệu Tích Xanh cho cơ sở này?');" class="inline-block">
                                                    @csrf
                                                    <button type="submit" 
                                                            name="btnApprove" 
                                                            id="btnApprove_{{ $request->id }}" 
                                                            class="btnApprove inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition-all">
                                                        <i class="fa-solid fa-certificate"></i> Phê duyệt & Cấp tích xanh
                                                    </button>
                                                </form>

                                                <!-- Button btnReject: Từ chối hồ sơ (Nền đỏ Rose) -->
                                                <button type="button" 
                                                        name="btnReject" 
                                                        id="btnReject_{{ $request->id }}" 
                                                        onclick="openRejectModal({{ $request->id }}, '{{ addslashes($senderName) }}')" 
                                                        class="btnReject inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                                                    <i class="fa-solid fa-ban"></i> Từ chối hồ sơ
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-500 italic">Đã xử lý ({{ $statusName }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                                <i class="fa-solid fa-inbox text-lg"></i>
                                            </div>
                                            <p class="font-bold text-slate-400">Không có hồ sơ nào trong danh mục</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 2. DANH SÁCH DẠNG THẺ (Cards View) -->
            <section class="space-y-5 hidden" id="cards-view-wrapper">
                @forelse($requests as $request)
                    @php
                        $typeLabel = $request->type === 'premium' ? 'Tích xanh' : 'KYC nhận tiền';
                        $statusClass = match ($request->status) {
                            'approved' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-350',
                            'rejected' => 'border-rose-500/25 bg-rose-500/10 text-rose-350',
                            'superseded' => 'border-slate-800 bg-slate-900/60 text-slate-400',
                            default => 'border-amber-500/25 bg-amber-500/10 text-amber-350',
                        };
                        $statusName = match ($request->status) {
                            'approved' => 'Đã duyệt',
                            'rejected' => 'Từ chối',
                            'superseded' => 'Thay thế',
                            default => 'Chờ duyệt',
                        };
                        $senderName = $request->user?->name ?? 'Không rõ';
                        $initial = mb_substr($senderName, 0, 1);
                    @endphp

                    <article class="glass-panel rounded-3xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-950/10 user-request-card"
                             data-tenant="{{ strtolower($request->tenant?->name ?? '') }}"
                             data-user="{{ strtolower($senderName) }}"
                             data-phone="{{ strtolower($request->user?->phone ?? '') }}"
                             data-status="{{ $request->status }}"
                             data-type="{{ $request->type }}">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                            <!-- Left Details Content -->
                            <div class="flex-grow space-y-4 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg border px-2.5 py-1 text-[10px] font-extrabold tracking-wider {{ $statusClass }}">
                                        {{ strtoupper($statusName) }}
                                    </span>
                                    <span class="rounded-lg border border-sky-500/20 bg-sky-500/10 px-2.5 py-1 text-[10px] font-extrabold tracking-wider text-sky-300">
                                        {{ $typeLabel }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium ml-1">
                                        #{{ $request->id }} • {{ $request->created_at?->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div>
                                    <h2 class="text-xl font-bold text-white tracking-tight leading-snug">
                                        {{ $request->tenant?->name ?? 'Không gán cơ sở kinh doanh' }}
                                    </h2>
                                    
                                    <div class="flex items-center gap-3 mt-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 text-xs font-bold text-slate-300 flex items-center justify-center uppercase select-none">
                                            {{ $initial }}
                                        </div>
                                        <div class="text-xs">
                                            <p class="font-semibold text-slate-200">{{ $senderName }}</p>
                                            <p class="text-slate-400 mt-0.5">{{ $request->user?->phone ?? 'N/A' }} • {{ $request->user?->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($request->reject_reason)
                                    <div class="rounded-2xl border border-rose-500/10 bg-rose-500/5 p-4 text-xs leading-relaxed text-rose-200">
                                        <p class="font-semibold text-rose-450 flex items-center gap-1.5 mb-1">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Lý do từ chối:
                                        </p>
                                        <p class="text-slate-300 italic">"{{ $request->reject_reason }}"</p>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                        <i class="fa-solid fa-paperclip"></i> Tài liệu đính kèm đối soát
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($request->documents as $document)
                                            @if($request->allowsDefaultAdminDocumentReview())
                                                <a href="{{ route('admin.verification-documents.show', $document) }}" target="_blank" class="flex items-center justify-between rounded-xl border border-slate-850 bg-slate-950/65 px-4 py-3 text-xs font-bold text-slate-200 hover:border-sky-500/40 hover:bg-slate-900/40 transition-all group shadow-inner">
                                                    <span class="flex items-center gap-2 truncate pr-2">
                                                        <i class="fa-regular fa-id-badge text-sky-400 group-hover:scale-110 transition-transform text-sm"></i>
                                                        <span class="truncate">{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</span>
                                                    </span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 group-hover:text-sky-400"></i>
                                                </a>
                                            @else
                                                <div class="rounded-xl border border-slate-850 bg-slate-950/40 p-3.5 shadow-inner">
                                                    <div class="flex items-center gap-2 font-bold text-slate-300 text-xs">
                                                        <i class="fa-solid fa-lock text-amber-500/80"></i>
                                                        {{ str_replace('_', ' ', strtoupper($document->document_type)) }}
                                                    </div>
                                                    
                                                    <form method="POST" action="{{ route('admin.verification-documents.unlock', $document) }}" onsubmit="handleJitUnlockSubmit(event, this)" class="mt-2.5 space-y-2">
                                                        @csrf
                                                        <textarea name="reason" required minlength="12" rows="2" placeholder="Lý do mở khóa dữ liệu bảo mật JIT..." class="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-[11px] text-slate-200 outline-none focus:border-amber-500/80 transition-all placeholder-slate-600 font-sans"></textarea>
                                                        <button class="w-full py-1.5 rounded-lg border border-amber-500/30 bg-amber-500/10 text-[10px] font-extrabold text-amber-300 hover:bg-amber-500/20 active:scale-[0.98] transition-all">
                                                            Unlock & Request Access
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Right Actions Panel -->
                            @if($request->status === 'pending')
                                <div class="w-full lg:w-80 flex-shrink-0 bg-slate-950/50 rounded-2xl p-4 border border-slate-850 space-y-3 shadow-md self-stretch flex flex-col justify-center">
                                    <h3 class="text-xs font-bold text-slate-400 pb-2 border-b border-slate-850 flex items-center gap-1.5 mb-1">
                                        <i class="fa-solid fa-sliders text-emerald-400"></i> Thao Tác Quyết Định
                                    </h3>

                                    <form method="POST" action="{{ route('admin.kyc.approve', $request->id) }}">
                                        @csrf
                                        <button class="btnApprove w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all duration-200 active:scale-[0.97]">
                                            <i class="fa-solid fa-square-check mr-1.5 text-base align-middle"></i> Phê Duyệt & Cấp Tích Xanh
                                        </button>
                                    </form>

                                    <button type="button" onclick="openRejectModal({{ $request->id }}, '{{ addslashes($senderName) }}')" class="btnReject w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg shadow-rose-500/20 hover:shadow-rose-500/30 transition-all active:scale-[0.97]">
                                        <i class="fa-solid fa-rectangle-xmark mr-1.5 text-base align-middle"></i> Từ Chối Hồ Sơ
                                    </button>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-slate-850 bg-slate-900/20 backdrop-blur-md p-12 text-center text-slate-400 flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center text-xl mb-4">
                            <i class="fa-solid fa-folder-closed"></i>
                        </div>
                        <p class="font-bold text-sm text-slate-300">Chưa Có Hồ Sơ Xác Minh Nào</p>
                        <p class="text-xs text-slate-500 mt-1">Hệ thống hiện tại chưa ghi nhận yêu cầu phê duyệt nào từ chủ trọ.</p>
                    </div>
                @endforelse
            </section>

            <!-- Pagination -->
            @if($requests->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $requests->links() }}
                </div>
            @endif
        </main>
    </div>

    <!-- ================= MODAL TỪ CHỐI DUYỆT HỒ SƠ KYC (Popup Form) ================= -->
    <div id="modalRejectKYC" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md hidden transition-opacity duration-300">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <h3 class="text-base font-bold text-white">Từ Chối Duyệt Hồ Sơ KYC</h3>
                </div>
                <button type="button" onclick="closeRejectModal()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formRejectKYC" method="POST" action="" onsubmit="return validateRejectForm(event)" class="mt-5 space-y-4">
                @csrf
                <div>
                    <p class="text-xs text-slate-300 mb-2">
                        Chủ trọ: <span id="rejectTargetName" class="font-bold text-emerald-400"></span>
                    </p>
                    <label for="reject_reason" class="block text-xs font-bold text-slate-200 mb-1.5">
                        Lý do từ chối *
                    </label>
                    <!-- Modal Input reject_reason: đúng tên và placeholder theo bảng UI spec -->
                    <textarea name="reject_reason" 
                              id="reject_reason" 
                              rows="4" 
                              placeholder="Nhập lý do ảnh mờ, sai số CCCD..." 
                              class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-slate-100 outline-none focus:border-rose-500 transition-all font-sans placeholder-slate-600 resize-none"></textarea>
                    
                    <!-- Vùng bôi đỏ viền & thông báo lỗi cụ thể theo spec ERR_10_01 và ERR_10_02 -->
                    <p id="reject_reason_error" class="hidden text-xs text-rose-400 mt-2 font-medium flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span id="reject_error_text"></span>
                    </p>
                </div>

                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 text-[11px] text-slate-400">
                    <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i>
                    Lý do từ chối sẽ được gửi trực tiếp qua thông báo và email cho chủ trọ để họ chỉnh sửa, bổ sung tài liệu.
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-800/60 text-xs font-bold text-slate-300 hover:bg-slate-700 transition-all">
                        Hủy Bỏ
                    </button>
                    <button type="submit" id="btnConfirmReject" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg shadow-rose-600/20 active:scale-95 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-ban"></i> Xác Nhận Từ Chối
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ================= Quản lý Toast Notification =================
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            let icon = 'fa-circle-info';
            if (type === 'success') icon = 'fa-circle-check';
            if (type === 'error') icon = 'fa-circle-exclamation';
            
            card.innerHTML = `
                <i class="fa-solid ${icon} mt-0.5 text-lg ${type === 'success' ? 'text-emerald-400' : (type === 'error' ? 'text-rose-400' : 'text-blue-400')}"></i>
                <div class="flex-grow">
                    <p class="text-xs font-semibold text-slate-200 leading-relaxed">${message}</p>
                </div>
            `;
            container.appendChild(card);
            setTimeout(() => card.classList.add('show'), 10);
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 400);
            }, 5000);
        }

        // ================= Chuyển Đổi Chế Độ View (Table / Cards) =================
        function setViewMode(mode) {
            const tableView = document.getElementById('table-view-wrapper');
            const cardsView = document.getElementById('cards-view-wrapper');
            const btnTable = document.getElementById('btnViewTable');
            const btnCards = document.getElementById('btnViewCards');

            if (mode === 'table') {
                tableView.classList.remove('hidden');
                cardsView.classList.add('hidden');
                btnTable.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm";
                btnCards.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-400 hover:text-white hover:bg-slate-800";
            } else {
                tableView.classList.add('hidden');
                cardsView.classList.remove('hidden');
                btnCards.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all bg-emerald-600 text-white shadow-sm";
                btnTable.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all text-slate-400 hover:text-white hover:bg-slate-800";
            }
        }

        // ================= Bộ Lọc Danh Sách (Filter & Search) =================
        function filterRequests() {
            const searchQuery = document.getElementById('verification-search').value.toLowerCase().trim();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            
            // Lọc trên Table
            const rows = document.querySelectorAll('.user-request-row');
            rows.forEach(row => {
                const tenant = row.getAttribute('data-tenant') || '';
                const user = row.getAttribute('data-user') || '';
                const phone = row.getAttribute('data-phone') || '';
                const status = row.getAttribute('data-status') || '';
                const type = row.getAttribute('data-type') || '';

                const matchesSearch = tenant.includes(searchQuery) || user.includes(searchQuery) || phone.includes(searchQuery);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;
                const matchesType = typeFilter === 'all' || type === typeFilter;

                row.style.display = (matchesSearch && matchesStatus && matchesType) ? '' : 'none';
            });

            // Lọc trên Cards
            const cards = document.querySelectorAll('.user-request-card');
            cards.forEach(card => {
                const tenant = card.getAttribute('data-tenant') || '';
                const user = card.getAttribute('data-user') || '';
                const phone = card.getAttribute('data-phone') || '';
                const status = card.getAttribute('data-status') || '';
                const type = card.getAttribute('data-type') || '';

                const matchesSearch = tenant.includes(searchQuery) || user.includes(searchQuery) || phone.includes(searchQuery);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;
                const matchesType = typeFilter === 'all' || type === typeFilter;

                card.style.display = (matchesSearch && matchesStatus && matchesType) ? '' : 'none';
            });
        }

        // ================= Modal Từ Chối & Validation (ERR_10_01, ERR_10_02) =================
        function openRejectModal(id, landlordName) {
            const modal = document.getElementById('modalRejectKYC');
            const form = document.getElementById('formRejectKYC');
            const targetNameSpan = document.getElementById('rejectTargetName');
            const textarea = document.getElementById('reject_reason');
            const errorBlock = document.getElementById('reject_reason_error');

            form.action = `/admin/kyc/${id}/reject`;
            targetNameSpan.innerText = landlordName || 'Chủ trọ';
            textarea.value = '';
            
            // Xóa trạng thái lỗi cũ
            textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
            errorBlock.classList.add('hidden');

            modal.classList.remove('hidden');
            setTimeout(() => textarea.focus(), 100);
        }

        function closeRejectModal() {
            document.getElementById('modalRejectKYC').classList.add('hidden');
        }

        function validateRejectForm(event) {
            const textarea = document.getElementById('reject_reason');
            const errorBlock = document.getElementById('reject_reason_error');
            const errorText = document.getElementById('reject_error_text');
            const val = textarea.value.trim();

            // Kịch bản ERR_10_01: Để trống lý do từ chối
            if (!val) {
                event.preventDefault();
                textarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                errorText.innerText = 'Vui lòng nhập lý do từ chối để chủ cơ sở nắm rõ thông tin bổ sung.';
                errorBlock.classList.remove('hidden');
                textarea.focus();
                return false;
            }

            // Kịch bản ERR_10_02: Lý do từ chối quá ngắn (< 10 ký tự)
            if (val.length < 10) {
                event.preventDefault();
                textarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                errorText.innerText = 'Nội dung lý do từ chối phải có độ dài tối thiểu 10 ký tự.';
                errorBlock.classList.remove('hidden');
                textarea.focus();
                return false;
            }

            // Hợp lệ: Xóa viền đỏ và cho submit
            textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
            errorBlock.classList.add('hidden');
            return true;
        }

        // Tự động xóa lỗi khi người dùng gõ
        document.getElementById('reject_reason').addEventListener('input', function() {
            const val = this.value.trim();
            const errorBlock = document.getElementById('reject_reason_error');
            if (val.length >= 10) {
                this.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
                errorBlock.classList.add('hidden');
            }
        });

        // Flash message từ session
        @if(session('success'))
            showToast(@json(session('success')), "success");
        @endif
        @if(session('error'))
            showToast(@json(session('error')), "error");
        @endif
        @if($errors->any())
            showToast(@json($errors->first()), "error");
        @endif

        // ================= WebAuthn FIDO2 Biometric Security =================
        window.hasPasskey = false;

        function ensureSecureWebAuthnDomain() {
            if (window.location.hostname === '127.0.0.1') {
                const newUrl = window.location.href.replace('127.0.0.1', 'localhost');
                if (confirm('Chuẩn bảo mật WebAuthn (Passkey) bắt buộc truy cập qua "localhost" hoặc HTTPS (không hỗ trợ địa chỉ IP 127.0.0.1 theo tiêu chuẩn W3C WebAuthn).\n\nBạn có muốn chuyển sang ' + newUrl + ' ngay bây giờ?')) {
                    window.location.href = newUrl;
                }
                return false;
            }
            return true;
        }

        function base64UrlToUint8Array(base64Url) {
            if (!base64Url) return new Uint8Array(0);
            const padding = '='.repeat((4 - base64Url.length % 4) % 4);
            const base64 = (base64Url + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        function hexToUint8Array(hex) {
            const cleanHex = hex.replace(/[^0-9a-fA-F]/g, '');
            const match = cleanHex.match(/.{1,2}/g) || [];
            return new Uint8Array(match.map(byte => parseInt(byte, 16)));
        }

        function parseUserIdToUint8Array(userId) {
            if (userId instanceof Uint8Array) return userId;
            if (typeof userId === 'string') {
                if (/^[0-9a-fA-F]{32}$/.test(userId)) {
                    return hexToUint8Array(userId);
                }
                try {
                    return base64UrlToUint8Array(userId);
                } catch (e) {
                    return new TextEncoder().encode(userId);
                }
            }
            return new TextEncoder().encode(String(userId || ''));
        }

        function bufferToBase64Url(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            const len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        function updatePasskeyButtonState() {
            const btn = document.getElementById('btnRegisterPasskey');
            const icon = document.getElementById('passkeyIcon');
            const text = document.getElementById('passkeyBtnText');
            if (window.hasPasskey) {
                btn.className = "inline-flex items-center gap-2 rounded-xl border border-emerald-800 bg-emerald-950/40 backdrop-blur px-4 py-2.5 text-xs font-bold text-emerald-300 hover:text-white hover:border-emerald-500 transition-all hover:scale-[1.02] active:scale-[0.98]";
                icon.className = "fa-solid fa-shield-halved text-emerald-400";
                text.innerText = "Passkey: Đã bảo vệ";
            } else {
                btn.className = "inline-flex items-center gap-2 rounded-xl border border-sky-800 bg-sky-950/40 backdrop-blur px-4 py-2.5 text-xs font-bold text-sky-300 hover:text-white hover:border-sky-500 transition-all hover:scale-[1.02] active:scale-[0.98]";
                icon.className = "fa-solid fa-fingerprint text-sky-400";
                text.innerText = "Đăng ký Passkey";
            }
        }

        async function checkPasskeyStatus() {
            try {
                const res = await fetch('{{ route("admin.verifications.has-passkey") }}');
                const data = await res.json();
                window.hasPasskey = data.has_passkey;
                updatePasskeyButtonState();
            } catch (e) {
                console.error("Lỗi check passkey status:", e);
            }
        }

        async function registerBiometricDevice() {
            if (!ensureSecureWebAuthnDomain()) return;
            if (typeof PublicKeyCredential === "undefined") {
                showToast("Trình duyệt hoặc thiết bị không hỗ trợ WebAuthn/Passkeys.", "error");
                return;
            }

            try {
                showToast("Đang kết nối máy chủ để khởi tạo Passkey...", "info");

                const optionsResponse = await fetch('/webauthn/register/options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                });

                if (!optionsResponse.ok) {
                    const errRes = await optionsResponse.json().catch(() => ({}));
                    throw new Error(errRes.message || "Không thể lấy cấu hình đăng ký từ máy chủ.");
                }

                const options = await optionsResponse.json();
                options.challenge = base64UrlToUint8Array(options.challenge);
                options.user.id = parseUserIdToUint8Array(options.user.id);

                if (options.excludeCredentials && Array.isArray(options.excludeCredentials)) {
                    options.excludeCredentials = options.excludeCredentials.map(cred => ({
                        ...cred,
                        id: base64UrlToUint8Array(cred.id)
                    }));
                }

                showToast("Vui lòng hoàn tất quét vân tay/khuôn mặt trên thiết bị của bạn...", "info");
                const credential = await navigator.credentials.create({
                    publicKey: options
                });

                const attestationResponse = {
                    id: credential.id,
                    rawId: bufferToBase64Url(credential.rawId),
                    type: credential.type,
                    response: {
                        clientDataJSON: bufferToBase64Url(credential.response.clientDataJSON),
                        attestationObject: bufferToBase64Url(credential.response.attestationObject)
                    }
                };

                const saveResponse = await fetch('/webauthn/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(attestationResponse)
                });

                if (saveResponse.ok) {
                    showToast("Đăng ký thiết bị bảo mật (Passkey) thành công!", "success");
                    window.hasPasskey = true;
                    updatePasskeyButtonState();
                } else {
                    const errData = await saveResponse.json().catch(() => ({}));
                    throw new Error(errData.message || "Không thể lưu thiết bị bảo mật.");
                }

            } catch (error) {
                console.error("Lỗi đăng ký Passkey:", error);
                showToast(error.message || "Đăng ký Passkey thất bại hoặc bị hủy bỏ.", "error");
            }
        }

        async function handleJitUnlockSubmit(event, form) {
            event.preventDefault();
            if (!ensureSecureWebAuthnDomain()) return;

            if (!window.hasPasskey) {
                showToast("Bảo mật bắt buộc: Vui lòng click nút 'Đăng ký Passkey' ở trên đầu trang trước khi mở khóa tài liệu nhạy cảm.", "error");
                return;
            }
            
            const reasonTextarea = form.querySelector('textarea[name="reason"]');
            const reason = reasonTextarea.value.trim();
            if (reason.length < 12) {
                showToast("Lý do mở khóa phải chứa ít nhất 12 ký tự.", "error");
                return;
            }
            
            try {
                showToast("Đang chuẩn bị xác thực sinh trắc học...", "info");
                
                const optionsResponse = await fetch('/webauthn/login/options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: '{{ Auth::user()->email }}'
                    })
                });
                
                if (!optionsResponse.ok) {
                    throw new Error("Không thể khởi tạo cấu hình xác thực sinh trắc học.");
                }
                
                const options = await optionsResponse.json();
                options.challenge = base64UrlToUint8Array(options.challenge);
                if (options.allowCredentials) {
                    options.allowCredentials = options.allowCredentials.map(cred => ({
                        ...cred,
                        id: base64UrlToUint8Array(cred.id)
                    }));
                }
                
                showToast("Vui lòng quét vân tay hoặc khuôn mặt để mở khóa...", "info");
                const assertion = await navigator.credentials.get({
                    publicKey: options
                });
                
                const payload = {
                    id: assertion.id,
                    rawId: bufferToBase64Url(assertion.rawId),
                    type: assertion.type,
                    response: {
                        clientDataJSON: bufferToBase64Url(assertion.response.clientDataJSON),
                        authenticatorData: bufferToBase64Url(assertion.response.authenticatorData),
                        signature: bufferToBase64Url(assertion.response.signature),
                        userHandle: assertion.response.userHandle ? bufferToBase64Url(assertion.response.userHandle) : null
                    },
                    reason: reason
                };
                
                showToast("Đang gửi yêu cầu giải mã lên máy chủ...", "info");
                const unlockResponse = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await unlockResponse.json();
                
                if (unlockResponse.ok && result.success) {
                    showToast("Xác thực thành công! Đang mở tài liệu...", "success");
                    window.open(result.url, '_blank');
                    reasonTextarea.value = '';
                } else {
                    throw new Error(result.message || "Xác thực Passkey không thành công.");
                }
                
            } catch (error) {
                console.error(error);
                showToast(error.message || "Xác thực sinh trắc học thất bại hoặc bị hủy.", "error");
            }
        }

        document.addEventListener('DOMContentLoaded', checkPasskeyStatus);
    </script>
</body>
</html>
