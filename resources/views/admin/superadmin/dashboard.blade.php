<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Console - Quản Trị Nền Tảng Toàn Hệ Thống | SmartRoom & Renty</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .panel { background: rgba(13, 18, 31, 0.75); border: 1px solid rgba(30, 41, 59, 0.85); }
        .glass-header { background: rgba(8, 11, 17, 0.85); backdrop-filter: blur(12px); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
        .input-error { border-color: #ef4444 !important; box-shadow: 0 0 0 1px #ef4444 !important; }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-amber-500 selection:text-slate-900 overflow-hidden font-sans">

    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP HEADER -->
        <header class="h-16 border-b border-slate-900 glass-header flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px] font-extrabold tracking-wider uppercase">
                    <i class="fa-solid fa-crown text-[10px]"></i> FEAT_20_SUPERADMIN
                </span>
                <div>
                    <h1 class="text-base font-extrabold text-slate-100 tracking-tight flex items-center gap-2">
                        Bảng Điều Khiển Quản Trị Nền Tảng (Superadmin Console)
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.audit-logs') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                    <span>Nhật Ký Kiểm Toán Toàn Sàn</span>
                </a>
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="p-6 lg:p-8 space-y-6">

            <!-- TOAST THÔNG BÁO THÀNH CÔNG HOẶC LỖI TOÀN CỤC -->
            @if(session('success'))
                <div id="toast-success" class="flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold shadow-lg shadow-emerald-950/40">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-xs font-extrabold text-emerald-300 block uppercase tracking-wider">{{ session('toast_code') ?? 'ERR_20_04' }}</span>
                        {{ session('success') }}
                    </div>
                    <button type="button" onclick="document.getElementById('toast-success').remove()" class="text-emerald-400 hover:text-emerald-200 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="toast-error" class="flex items-center gap-3 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm font-semibold shadow-lg shadow-rose-950/40">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                    </div>
                    <div class="flex-1">
                        @if(session('error_code'))
                            <span class="text-xs font-extrabold text-rose-300 block uppercase tracking-wider">{{ session('error_code') }}</span>
                        @endif
                        {{ session('error') }}
                    </div>
                    <button type="button" onclick="document.getElementById('toast-error').remove()" class="text-rose-400 hover:text-rose-200 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <!-- 1. METRIC CARDS (kpi_cards) - 4 THẺ THỐNG KÊ ĐẦU TRANG -->
            <section id="kpi_cards" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                
                <!-- CARD 1: TỔNG NGƯỜI DÙNG TOÀN SÀN -->
                <div class="panel rounded-2xl p-5 relative overflow-hidden group hover:border-slate-700 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng Người Dùng Sàn</span>
                            <h3 class="text-2xl font-black text-slate-100 mt-1 tracking-tight">{{ number_format($kpi['total_users']) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 text-xl group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Chủ trọ: <strong class="text-slate-200">{{ $kpi['landlord_count'] }}</strong></span>
                        <span>Cư dân: <strong class="text-slate-200">{{ $kpi['resident_count'] }}</strong></span>
                        <span>Admin: <strong class="text-amber-400">{{ $kpi['admin_count'] }}</strong></span>
                    </div>
                </div>

                <!-- CARD 2: TỔNG DOANH THU SÀN -->
                <div class="panel rounded-2xl p-5 relative overflow-hidden group hover:border-slate-700 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Doanh Thu Toàn Hệ Thống</span>
                            <h3 class="text-2xl font-black text-emerald-400 mt-1 tracking-tight">{{ number_format($kpi['total_revenue']) }} <span class="text-xs font-bold text-emerald-500">đ</span></h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Hoa hồng sàn: <strong class="text-emerald-400">{{ $systemConfigs['commission_rate']->value ?? 10 }}%</strong></span>
                        <span>Doanh thu phí: <strong class="text-slate-200">{{ number_format($kpi['total_revenue'] * (($systemConfigs['commission_rate']->value ?? 10) / 100)) }} đ</strong></span>
                    </div>
                </div>

                <!-- CARD 3: SỐ TIN ĐĂNG / PHÒNG HOẠT ĐỘNG -->
                <div class="panel rounded-2xl p-5 relative overflow-hidden group hover:border-slate-700 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Số Tin Đăng & Phòng</span>
                            <h3 class="text-2xl font-black text-slate-100 mt-1 tracking-tight">{{ number_format($kpi['total_rooms']) }} <span class="text-xs font-semibold text-slate-400">phòng</span></h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-600/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Đang thuê: <strong class="text-emerald-400">{{ $kpi['active_rooms'] }}</strong></span>
                        <span>Trống: <strong class="text-amber-400">{{ $kpi['total_rooms'] - $kpi['active_rooms'] }}</strong></span>
                        <span>Tỷ lệ lấp đầy: <strong class="text-slate-200">{{ $kpi['total_rooms'] > 0 ? round(($kpi['active_rooms'] / $kpi['total_rooms']) * 100, 1) : 0 }}%</strong></span>
                    </div>
                </div>

                <!-- CARD 4: CƠ SỞ LƯU TRÚ & TOÀ NHÀ -->
                <div class="panel rounded-2xl p-5 relative overflow-hidden group hover:border-slate-700 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cơ Sở Lưu Trú (Tenants)</span>
                            <h3 class="text-2xl font-black text-slate-100 mt-1 tracking-tight">{{ number_format($kpi['total_tenants']) }} <span class="text-xs font-semibold text-slate-400">cơ sở</span></h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-purple-600/20 to-violet-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-city"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Multi-tenancy: <strong class="text-emerald-400">Đã kích hoạt</strong></span>
                        <span>Cách ly dữ liệu: <strong class="text-slate-200">100%</strong></span>
                    </div>
                </div>

            </section>

            <!-- 2. KHU VỰC QUẢN LÝ TÀI KHOẢN & BỘ LỌC TÌM KIẾM (User Table & Filter) -->
            <section class="panel rounded-2xl p-6 space-y-6">
                
                <!-- BỘ LỌC TÌM KIẾM (Input Specification Contract) -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-800/80 pb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-address-book text-amber-400"></i>
                            Quản Trị Người Dùng & Phân Quyền Vai Trò Toàn Sàn
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tìm kiếm tài khoản, cấp quyền Admin, bổ nhiệm quản lý hoặc kiểm soát trạng thái.</p>
                    </div>

                    <!-- FORM BỘ LỌC -->
                    <form method="GET" action="{{ route('admin.superadmin.dashboard') }}" class="flex flex-wrap items-center gap-3">
                        
                        <!-- 1. search_user: Tìm kiếm Email, Họ tên hoặc Số điện thoại -->
                        <div class="relative min-w-[240px]">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                            <input type="text" 
                                   name="search_user" 
                                   id="search_user"
                                   value="{{ request('search_user') }}" 
                                   placeholder="Họ tên, email hoặc SĐT..." 
                                   class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                        </div>

                        <!-- 2. role_filter: Lọc theo vai trò (Tất cả, Chủ trọ, Khách thuê, Quản trị viên) -->
                        <div>
                            <select name="role_filter" id="role_filter" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition-all">
                                <option value="all" {{ request('role_filter') == 'all' ? 'selected' : '' }}>-- Tất cả vai trò --</option>
                                <option value="landlord" {{ request('role_filter') == 'landlord' ? 'selected' : '' }}>Chủ trọ</option>
                                <option value="tenant" {{ request('role_filter') == 'tenant' ? 'selected' : '' }}>Khách thuê / Cư dân</option>
                                <option value="admin" {{ request('role_filter') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                                <option value="manager" {{ request('role_filter') == 'manager' ? 'selected' : '' }}>Nhân viên quản lý</option>
                                <option value="receptionist" {{ request('role_filter') == 'receptionist' ? 'selected' : '' }}>Lễ tân</option>
                                <option value="housekeeper" {{ request('role_filter') == 'housekeeper' ? 'selected' : '' }}>Buồng phòng</option>
                            </select>
                        </div>

                        <!-- 3. status_filter: Trạng thái tài khoản (Đang hoạt động, Đã khóa, Chờ duyệt) -->
                        <div>
                            <select name="status_filter" id="status_filter" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition-all">
                                <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>-- Tất cả trạng thái --</option>
                                <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="locked" {{ request('status_filter') == 'locked' ? 'selected' : '' }}>Đã khóa</option>
                                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            </select>
                        </div>

                        <!-- Nút Lọc & Nút Làm Mới -->
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-md shadow-amber-500/20 flex items-center gap-1.5">
                            <i class="fa-solid fa-filter"></i> Lọc
                        </button>
                        @if(request()->hasAny(['search_user', 'role_filter', 'status_filter']))
                            <a href="{{ route('admin.superadmin.dashboard') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- BẢNG DANH SÁCH TÀI KHOẢN (tblUsers) -->
                <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                    <table id="tblUsers" class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5">Họ Tên & Tài Khoản</th>
                                <th class="px-4 py-3.5">Email & SĐT</th>
                                <th class="px-4 py-3.5">Vai Trò (Role)</th>
                                <th class="px-4 py-3.5">Cơ Sở Lưu Trú Gán</th>
                                <th class="px-4 py-3.5">Tình Trạng</th>
                                <th class="px-4 py-3.5 text-right">Hành Động Quản Trị</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-900/40 transition-colors {{ $user->id === auth()->id() ? 'bg-amber-500/5' : '' }}">
                                    <!-- Cột 1: Họ tên -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-bold text-slate-200 text-xs shrink-0">
                                                {{ mb_strtoupper(mb_substr($user->name ?: 'U', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-100 flex items-center gap-1.5">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">Bạn (Superadmin)</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">@ {{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột 2: Email & SĐT -->
                                    <td class="px-4 py-3">
                                        <div class="text-slate-300">{{ $user->email ?: '—' }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $user->phone ?: 'Chưa cập nhật' }}</div>
                                    </td>

                                    <!-- Cột 3: Vai trò -->
                                    <td class="px-4 py-3">
                                        @php
                                            $slug = $user->roleSlug();
                                            $badgeClass = match($slug) {
                                                'superadmin' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                                'admin' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                                                'landlord', 'unverified_landlord' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
                                                'manager' => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                                                'receptionist' => 'bg-teal-500/15 text-teal-300 border-teal-500/30',
                                                'housekeeper' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                                default => 'bg-slate-700/30 text-slate-300 border-slate-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeClass }}">
                                            @if($slug === 'superadmin')
                                                <i class="fa-solid fa-crown text-[10px]"></i>
                                            @elseif($slug === 'admin')
                                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                                            @endif
                                            {{ $user->roleName() }}
                                        </span>
                                    </td>

                                    <!-- Cột 4: Cơ sở lưu trú -->
                                    <td class="px-4 py-3">
                                        @if($user->tenant)
                                            <span class="text-slate-300 font-semibold flex items-center gap-1.5">
                                                <i class="fa-solid fa-hotel text-slate-500 text-[11px]"></i>
                                                {{ $user->tenant->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-600 italic">Toàn hệ thống</span>
                                        @endif
                                    </td>

                                    <!-- Cột 5: Tình trạng -->
                                    <td class="px-4 py-3">
                                        @if($user->status === 'locked')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã khóa
                                            </span>
                                        @elseif($user->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ duyệt
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Cột 6: Hành động Quản trị (menuActions) -->
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            
                                            <!-- Nút 1: Phân Quyền (Mở Modal RBAC) -->
                                            <button type="button" 
                                                    onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->roleSlug() }}', {{ $user->tenant_id ?: 'null' }})"
                                                    title="Phân quyền tài khoản"
                                                    class="px-2.5 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/20 transition-all font-semibold text-xs flex items-center gap-1">
                                                <i class="fa-solid fa-user-gear"></i>
                                                <span>Phân Quyền</span>
                                            </button>

                                            <!-- Nút 2: Đổi Mật Khẩu -->
                                            <button type="button" 
                                                    onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    title="Đổi mật khẩu người dùng"
                                                    class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-all text-xs">
                                                <i class="fa-solid fa-key"></i>
                                            </button>

                                            <!-- Nút 3: Khóa / Mở Khóa Tài Khoản -->
                                            @if($user->status === 'locked')
                                                <form method="POST" action="{{ route('admin.superadmin.users.status', $user->id) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" 
                                                            title="Mở khóa tài khoản"
                                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition-all font-semibold text-xs flex items-center gap-1">
                                                        <i class="fa-solid fa-lock-open"></i>
                                                        <span>Mở Khóa</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" 
                                                        onclick="handleLockUser({{ $user->id }}, {{ auth()->id() }})"
                                                        title="Khóa tài khoản"
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all font-semibold text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-lock"></i>
                                                    <span>Khóa</span>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        <i class="fa-solid fa-user-slash text-2xl mb-2 block"></i>
                                        Không tìm thấy tài khoản nào phù hợp với bộ lọc hiện tại.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PHÂN TRANG -->
                <div class="pt-2">
                    {{ $users->links() }}
                </div>

            </section>

            <!-- 3. GIAO DIỆN CẤU HÌNH THAM SỐ TOÀN SÀN & PHÂN QUYỀN RBAC (Hình 20.2) -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- CỘT TRÁI: FORM CẤU HÌNH THAM SỐ TOÀN SÀN (/admin/system/config) -->
                <div class="lg:col-span-7 panel rounded-2xl p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-sliders text-amber-400"></i>
                                Thiết Lập Tham Số Toàn Sàn & Chính Sách Phí
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">POST /admin/system/config — Điều chỉnh hạn ngạch, tỷ lệ hoa hồng và quy chế sàn.</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            auth, role:superadmin
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.superadmin.config.update') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- 1. Tỷ lệ hoa hồng (%) -->
                            <div>
                                <label for="commission_rate" class="block text-xs font-bold text-slate-300 mb-1">
                                    Tỷ Lệ Hoa Hồng Sàn (%)
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           step="0.1" 
                                           min="0" 
                                           max="100" 
                                           name="commission_rate" 
                                           id="commission_rate"
                                           value="{{ $systemConfigs['commission_rate']->value ?? 10 }}" 
                                           class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">%</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Chiết khấu trên tổng giao dịch hóa đơn.</span>
                            </div>

                            <!-- 2. Hạn ngạch phòng miễn phí -->
                            <div>
                                <label for="free_listing_quota" class="block text-xs font-bold text-slate-300 mb-1">
                                    Hạn Ngạch Tin Đăng Miễn Phí
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           min="0" 
                                           name="free_listing_quota" 
                                           id="free_listing_quota"
                                           value="{{ $systemConfigs['free_listing_quota']->value ?? 5 }}" 
                                           class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">Phòng</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Số phòng tối đa cho chủ trọ mới chưa nâng cấp.</span>
                            </div>

                            <!-- 3. Phí sàn cố định -->
                            <div>
                                <label for="platform_fee_fixed" class="block text-xs font-bold text-slate-300 mb-1">
                                    Phí Cố Định Mỗi Hợp Đồng (VNĐ)
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           min="0" 
                                           step="1000"
                                           name="platform_fee_fixed" 
                                           id="platform_fee_fixed"
                                           value="{{ $systemConfigs['platform_fee_fixed']->value ?? 50000 }}" 
                                           class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">VNĐ</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Phí quản trị và chứng thực pháp lý online.</span>
                            </div>

                            <!-- 4. Duyệt KYC Chủ trọ -->
                            <div>
                                <label for="auto_approve_landlord" class="block text-xs font-bold text-slate-300 mb-1">
                                    Quy Trình Duyệt Xác Minh KYC
                                </label>
                                <select name="auto_approve_landlord" id="auto_approve_landlord" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50">
                                    <option value="0" {{ ($systemConfigs['auto_approve_landlord']->value ?? 0) == 0 ? 'selected' : '' }}>Thủ công (Superadmin duyệt hồ sơ)</option>
                                    <option value="1" {{ ($systemConfigs['auto_approve_landlord']->value ?? 0) == 1 ? 'selected' : '' }}>Tự động (AI OCR kiểm tra hợp lệ)</option>
                                </select>
                                <span class="text-[11px] text-slate-500 mt-1 block">Chính sách cấp huy hiệu Tích Xanh KYC.</span>
                            </div>

                            <!-- 5. Chế độ bảo trì hệ thống -->
                            <div>
                                <label for="maintenance_mode" class="block text-xs font-bold text-slate-300 mb-1">
                                    Chế Độ Bảo Trì Sàn
                                </label>
                                <select name="maintenance_mode" id="maintenance_mode" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50">
                                    <option value="0" {{ ($systemConfigs['maintenance_mode']->value ?? 0) == 0 ? 'selected' : '' }}>Hoạt động bình thường</option>
                                    <option value="1" {{ ($systemConfigs['maintenance_mode']->value ?? 0) == 1 ? 'selected' : '' }}>Bảo trì (Chỉ Superadmin truy cập)</option>
                                </select>
                                <span class="text-[11px] text-slate-500 mt-1 block">Tạm ngừng nhận booking/tin đăng mới khi nâng cấp.</span>
                            </div>

                            <!-- 6. Hotline hỗ trợ toàn sàn -->
                            <div>
                                <label for="system_hotline" class="block text-xs font-bold text-slate-300 mb-1">
                                    Hotline Khẩn Cấp Hệ Thống
                                </label>
                                <input type="text" 
                                       name="system_hotline" 
                                       id="system_hotline"
                                       value="{{ $systemConfigs['system_hotline']->value ?? '1900 8888' }}" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                <span class="text-[11px] text-slate-500 mt-1 block">Hiển thị tại tổng đài hỗ trợ 24/7.</span>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Lưu Thiết Lập Tham Số Toàn Sàn</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- CỘT PHẢI: BẢNG TRA CỨU PHÂN QUYỀN RBAC (RBAC Matrix) -->
                <div class="lg:col-span-5 panel rounded-2xl p-6 space-y-4">
                    <div class="border-b border-slate-800/80 pb-3">
                        <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                            Ma Trận Danh Mục Phân Quyền (RBAC)
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Đặc quyền và phạm vi thao tác của từng vai trò trên nền tảng.</p>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></span>
                            <div>
                                <strong class="text-amber-300">Superadmin:</strong> Quản trị viên tối cao, toàn quyền nền tảng, quản lý cấu hình sàn, nâng/hạ quyền admin, kiểm toán bất biến.
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-purple-400 mt-1.5 shrink-0"></span>
                            <div>
                                <strong class="text-purple-300">Admin:</strong> Kiểm duyệt tin đăng, duyệt KYC hồ sơ chủ trọ, xem báo cáo toàn hệ thống.
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 shrink-0"></span>
                            <div>
                                <strong class="text-blue-300">Chủ trọ (Landlord):</strong> Quản lý cơ sở lưu trú, tòa nhà, sơ đồ phòng, cư dân, hợp đồng và dòng tiền thu chi.
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-teal-400 mt-1.5 shrink-0"></span>
                            <div>
                                <strong class="text-teal-300">Nhân viên / Quản lý:</strong> Bắt buộc gán cơ sở lưu trú cụ thể, thực hiện lễ tân, dọn buồng phòng, hỗ trợ sự cố cư dân.
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5 shrink-0"></span>
                            <div>
                                <strong class="text-emerald-300">Khách thuê / Cư dân:</strong> Tìm kiếm phòng, ký hợp đồng điện tử, thanh toán VietQR và gửi báo hỏng online.
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <!-- 4. NHẬT KÝ KIỂM TOÁN AN NINH TRUY VẾT BẤT BIẾN GẦN ĐÂY (log_viewer) -->
            <section id="log_viewer" class="panel rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-fingerprint text-indigo-400"></i>
                            Nhật Ký Kiểm Toán An Ninh Mới Nhất (Audit Log Chain)
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Truy vết bất biến với chuỗi băm bảo mật SHA-256 (Prev Hash &rarr; Row Hash).</p>
                    </div>
                    <a href="{{ route('admin.audit-logs') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1 transition-colors">
                        <span>Xem tất cả nhật ký</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Thời Gian</th>
                                <th class="px-4 py-3">Người Thao Tác (Actor)</th>
                                <th class="px-4 py-3">Hành Động (Action)</th>
                                <th class="px-4 py-3">Đối Tượng (Resource)</th>
                                <th class="px-4 py-3">Địa Chỉ IP</th>
                                <th class="px-4 py-3">Chuỗi Băm Xác Thực (Row Hash)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse($recentAuditLogs as $log)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-4 py-2.5 text-slate-400 font-mono text-[11px]">
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="font-bold text-slate-200">
                                            {{ $log->actor?->name ?: ($log->actor?->username ?: 'Hệ thống') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-300">
                                        {{ $log->resource_type }} #{{ $log->resource_id }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-400 font-mono text-[11px]">
                                        {{ $log->ip_address ?: '127.0.0.1' }}
                                    </td>
                                    <td class="px-4 py-2.5 font-mono text-[11px] text-emerald-400 truncate max-w-[200px]" title="{{ $log->row_hash }}">
                                        {{ $log->row_hash ? substr($log->row_hash, 0, 16) . '...' : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                        Chưa có nhật ký kiểm toán an ninh nào được ghi nhận.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: PHÂN QUYỀN VAI TRÒ RBAC (ERR_20_01, ERR_20_02, ERR_20_04)        -->
    <!-- ========================================================================= -->
    <div id="modal-role" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
        <div class="panel rounded-2xl w-full max-w-lg p-6 space-y-5 border border-slate-700 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-amber-400"></i>
                    Phân Quyền Vai Trò Tài Khoản
                </h3>
                <button type="button" onclick="closeRoleModal()" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-role-update" method="POST" action="" class="space-y-4">
                @csrf
                <div class="text-xs text-slate-300">
                    Đang thiết lập vai trò cho tài khoản: <strong id="modal-role-username" class="text-amber-400"></strong>
                </div>

                <!-- Dropdown Chọn Vai Trò (Kèm xử lý viền đỏ ERR_20_01) -->
                <div>
                    <label for="select-role-slug" class="block text-xs font-bold text-slate-300 mb-1">
                        Vai Trò Phân Quyền <span class="text-rose-500">*</span>
                    </label>
                    <select name="role_slug" 
                            id="select-role-slug" 
                            onchange="onRoleChange(this.value)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-100 focus:outline-none focus:border-amber-500 transition-all">
                        <option value="">-- Vui lòng chọn vai trò --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->slug }}">{{ $r->name }} ({{ $r->slug }})</option>
                        @endforeach
                    </select>
                    <!-- Thông báo lỗi ERR_20_01 -->
                    <p id="error-role-slug" class="text-[11px] text-rose-400 mt-1.5 hidden flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>ERR_20_01: Vui lòng chọn vai trò phân quyền hợp lệ cho tài khoản.</span>
                    </p>
                </div>

                <!-- Dropdown Chọn Cơ Sở Lưu Trú (Kèm xử lý viền đỏ ERR_20_02 khi là staff/manager) -->
                <div id="container-tenant-select" class="hidden">
                    <label for="select-tenant-id" class="block text-xs font-bold text-slate-300 mb-1">
                        Cơ Sở Lưu Trú Gán <span class="text-rose-500">*</span>
                    </label>
                    <select name="tenant_id" 
                            id="select-tenant-id" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-100 focus:outline-none focus:border-amber-500 transition-all">
                        <option value="">-- Chọn cơ sở lưu trú (Bắt buộc cho NV/Quản lý) --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->address }})</option>
                        @endforeach
                    </select>
                    <!-- Thông báo lỗi ERR_20_02 -->
                    <p id="error-tenant-id" class="text-[11px] text-rose-400 mt-1.5 hidden flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>ERR_20_02: Nhân viên hoặc Quản lý bắt buộc phải được gán vào một cơ sở lưu trú cụ thể.</span>
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Hủy Bỏ
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-md shadow-amber-500/20">
                        Cập Nhật Quyền Hạn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: CẢNH BÁO ĐỎ CHẶN TỰ KHÓA CHÍNH MÌNH (ERR_20_03)                   -->
    <!-- ========================================================================= -->
    <div id="modal-alert-self-lock" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm hidden">
        <div class="rounded-2xl w-full max-w-md p-6 bg-[#160a0a] border-2 border-rose-500 shadow-2xl shadow-rose-950/60 relative animate-in fade-in zoom-in-95 duration-200">
            <div class="text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center mx-auto text-2xl animate-bounce">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded bg-rose-500/20 text-rose-300 text-[11px] font-extrabold uppercase tracking-wider border border-rose-500/30">
                        Mã Lỗi: ERR_20_03
                    </span>
                    <h3 class="text-base font-extrabold text-white mt-1">Hành Động Bị Từ Chối!</h3>
                </div>
                <p class="text-xs text-rose-200 leading-relaxed font-semibold">
                    Không thể tự khóa tài khoản quản trị viên đang thực hiện phiên làm việc!
                </p>
            </div>
            <div class="mt-6 flex justify-center">
                <button type="button" onclick="closeSelfLockAlert()" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition-all">
                    Đã Hiểu & Đóng Cảnh Báo
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: ĐỔI MẬT KHẨU TÀI KHOẢN                                           -->
    <!-- ========================================================================= -->
    <div id="modal-password" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
        <div class="panel rounded-2xl w-full max-w-md p-6 space-y-5 border border-slate-700 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-key text-amber-400"></i>
                    Đặt Lại Mật Khẩu
                </h3>
                <button type="button" onclick="closePasswordModal()" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-password-reset" method="POST" action="" class="space-y-4">
                @csrf
                <div class="text-xs text-slate-300">
                    Đặt mật khẩu mới cho: <strong id="modal-password-username" class="text-amber-400"></strong>
                </div>
                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-300 mb-1">Mật khẩu mới (Tối thiểu 6 ký tự)</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-100 focus:outline-none focus:border-amber-500">
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="closePasswordModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Hủy</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-md shadow-amber-500/20">Lưu Mật Khẩu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT XỬ LÝ CLIENT VALIDATION & HIỆU ỨNG GIAO DIỆN -->
    <script>
        // 1. Xử lý mở/đóng Modal phân quyền và Client validation
        function openRoleModal(userId, userName, currentRole, currentTenantId) {
            const modal = document.getElementById('modal-role');
            const form = document.getElementById('form-role-update');
            const userLabel = document.getElementById('modal-role-username');
            const roleSelect = document.getElementById('select-role-slug');
            const tenantSelect = document.getElementById('select-tenant-id');

            // Reset error states
            clearRoleErrors();

            form.action = `/admin/users/${userId}/role`;
            userLabel.textContent = userName;
            roleSelect.value = currentRole || '';
            tenantSelect.value = currentTenantId || '';

            onRoleChange(currentRole);
            modal.classList.remove('hidden');
        }

        function closeRoleModal() {
            document.getElementById('modal-role').classList.add('hidden');
            clearRoleErrors();
        }

        function onRoleChange(role) {
            const tenantContainer = document.getElementById('container-tenant-select');
            const staffRoles = ['manager', 'receptionist', 'housekeeper'];
            if (staffRoles.includes(role)) {
                tenantContainer.classList.remove('hidden');
            } else {
                tenantContainer.classList.add('hidden');
            }
        }

        function clearRoleErrors() {
            const roleSelect = document.getElementById('select-role-slug');
            const tenantSelect = document.getElementById('select-tenant-id');
            const roleError = document.getElementById('error-role-slug');
            const tenantError = document.getElementById('error-tenant-id');

            roleSelect.classList.remove('input-error');
            tenantSelect.classList.remove('input-error');
            roleError.classList.add('hidden');
            tenantError.classList.add('hidden');
        }

        // Bắt sự kiện submit form phân quyền để kiểm tra Client Validation đúng đặc tả:
        document.getElementById('form-role-update').addEventListener('submit', function(e) {
            clearRoleErrors();
            let hasError = false;
            const roleSlug = document.getElementById('select-role-slug').value.trim();
            const tenantId = document.getElementById('select-tenant-id').value.trim();
            const staffRoles = ['manager', 'receptionist', 'housekeeper'];

            // Kiểm tra ERR_20_01: Chưa chọn vai trò
            if (!roleSlug) {
                e.preventDefault();
                document.getElementById('select-role-slug').classList.add('input-error');
                document.getElementById('error-role-slug').classList.remove('hidden');
                hasError = true;
            }

            // Kiểm tra ERR_20_02: Nhân viên hoặc Quản lý thiếu cơ sở lưu trú
            if (staffRoles.includes(roleSlug) && !tenantId) {
                e.preventDefault();
                document.getElementById('select-tenant-id').classList.add('input-error');
                document.getElementById('error-tenant-id').classList.remove('hidden');
                hasError = true;
            }
        });

        // 2. Xử lý khóa tài khoản & Modal cảnh báo đỏ tự khóa chính mình (ERR_20_03)
        function handleLockUser(targetUserId, currentAuthId) {
            if (targetUserId === currentAuthId) {
                // Tự khóa chính mình -> Bật modal cảnh báo đỏ từ chối hành động ERR_20_03
                document.getElementById('modal-alert-self-lock').classList.remove('hidden');
                return;
            }

            if (confirm('Bạn có chắc chắn muốn khóa tài khoản này không? Người dùng sẽ không thể đăng nhập vào hệ thống.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${targetUserId}/status`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'locked';
                form.appendChild(statusInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeSelfLockAlert() {
            document.getElementById('modal-alert-self-lock').classList.add('hidden');
        }

        // 3. Xử lý đổi mật khẩu
        function openPasswordModal(userId, userName) {
            const modal = document.getElementById('modal-password');
            const form = document.getElementById('form-password-reset');
            form.action = `/admin/users/${userId}/password`;
            document.getElementById('modal-password-username').textContent = userName;
            modal.classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('modal-password').classList.add('hidden');
        }
    </script>
</body>
</html>
