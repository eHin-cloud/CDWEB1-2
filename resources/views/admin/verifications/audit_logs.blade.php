<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật Ký Kiểm Toán Hồ Sơ - Superadmin Console | SmartRoom & Renty</title>
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
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden font-sans">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- STANDARDIZED SUPERADMIN TOPBAR -->
        <header class="h-16 border-b border-slate-900 glass-header flex items-center justify-between px-6 lg:px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.superadmin.dashboard') }}" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition-all text-xs" title="Quay lại Superadmin Console">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[11px] font-extrabold tracking-wider uppercase">
                    <i class="fa-solid fa-shield-halved text-[10px]"></i> FEAT_10_AUDIT
                </span>
                <div>
                    <h1 class="text-base font-extrabold text-slate-100 tracking-tight flex items-center gap-2">
                        Nhật Ký Kiểm Toán Hồ Sơ & Mở Khóa Tài Liệu
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.superadmin.dashboard') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-all hidden sm:flex items-center gap-2">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>Superadmin Console</span>
                </a>
                <a href="{{ route('admin.analytics') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-all hidden md:flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-rose-400"></i>
                    <span>Thống Kê Hệ Thống</span>
                </a>
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl hidden lg:flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="p-6 lg:p-8 space-y-6 flex-grow">
            <!-- Header Title Card -->
            <div class="panel rounded-2xl p-6 border border-indigo-500/20 bg-gradient-to-r from-indigo-950/20 via-slate-900/60 to-purple-950/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i> Giám Sát Lịch Sử Truy Vết An Ninh
                    </h2>
                    <p class="text-xs text-slate-400">
                        Ghi nhận toàn bộ thao tác phê duyệt, từ chối và lịch sử mở khóa giải mã tài liệu nhạy cảm (JIT Access) của quản trị viên.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-2.5 text-xs font-bold text-slate-200 hover:text-white hover:border-indigo-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fa-solid fa-users-gear text-indigo-400"></i>
                        Quản lý người dùng
                    </a>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap items-center gap-2 nav-tabs-wrapper bg-slate-900/40 p-1.5 rounded-xl border border-slate-800/80 max-w-max">
                <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.verifications.index') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-file-signature text-emerald-400"></i> Duyệt Hồ Sơ KYC
                </a>
                <a href="{{ route('admin.kyc.pending') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.kyc.pending') ? 'bg-amber-500 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-hourglass-half text-amber-400"></i> Chờ Duyệt (Pending)
                </a>
                <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.audit-logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> Nhật Ký Kiểm Toán
                </a>
                <a href="{{ route('admin.analytics') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.analytics') ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/20' : 'nav-tab-inactive text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-chart-pie text-rose-300"></i> Thống Kê Hệ Thống
                </a>
            </div>

        <!-- Filter Panel -->
        <div class="glass-panel rounded-2xl p-6 mb-8 transition-all duration-300">
            <form action="{{ route('admin.audit-logs', [], false) }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="flex-1 w-full space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tìm kiếm nhanh</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo lý do, IP, loại tài liệu..." class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs font-medium text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="w-full md:w-64 space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quản trị viên</label>
                    <select name="admin_id" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 px-4 text-xs font-semibold text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                        <option value="">-- Tất cả Admin --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} ({{ $admin->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-none inline-flex justify-center items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-500 px-5 py-2.5 text-xs font-bold text-white transition-all">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    @if(request()->anyFilled(['search', 'admin_id']))
                        <a href="{{ route('admin.audit-logs') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-800 hover:border-slate-700 bg-slate-900/60 px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-white transition-all">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-slate-800/80 transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-800/80 bg-slate-950/40 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6">Quản trị viên</th>
                            <th class="py-4 px-6">Chủ trọ mục tiêu</th>
                            <th class="py-4 px-6">Thông tin truy cập</th>
                            <th class="py-4 px-6">Lý do giải mã</th>
                            <th class="py-4 px-6">IP & Thiết bị</th>
                            <th class="py-4 px-6">Thời gian</th>
                            <th class="py-4 px-6 text-center">Bảo mật Row</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-xs">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-900/25 transition-all">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($log->admin->name ?? 'AD', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-200">{{ $log->admin->name ?? 'Đã xóa Admin' }}</p>
                                            <p class="text-[10px] text-slate-500 font-medium">ID: {{ $log->admin_user_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="font-bold text-slate-300">{{ $log->targetLandlord->name ?? 'Chưa xác định' }}</p>
                                            <p class="text-[10px] text-slate-500">Chủ trọ</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase">
                                                {{ $log->access_type }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $log->document_type }}
                                            </span>
                                        </div>
                                        @if($log->presigned_url_expires_at)
                                            <p class="text-[10px] text-slate-500">
                                                Hạn link: {{ $log->presigned_url_expires_at->format('H:i:s d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <p class="text-slate-350 italic line-clamp-2" title="{{ $log->reason }}">
                                        "{{ $log->reason }}"
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        <p class="font-mono text-slate-300 text-[11px]">{{ $log->ip_address }}</p>
                                        <p class="text-[10px] text-slate-500 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-slate-400">
                                    {{ $log->created_at->format('H:i:s d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="Dòng log đã ký số băm row_hash: {{ $log->row_hash }}">
                                        <i class="fa-solid fa-lock text-[9px]"></i> ĐÃ KÝ
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-shield-halved text-2xl mb-3 block opacity-30"></i>
                                    Chưa có nhật ký truy cập tài liệu bảo mật nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="py-4 px-6 border-t border-slate-800 bg-slate-950/20">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
        </main>
    </div>
    {{-- <script src="{{ asset('js/admin-sidebar.js') }}"></script> --}}
</body>
</html>
