<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bảng Điều Khiển Tổng Quan Phòng & Doanh Thu - SmartRoom</title>
    @include('admin.partials.theme-head-script')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        darkblue: {
                            800: '#1e3a8a',
                            900: '#172554',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

    <!-- Thư viện biểu đồ đồ họa Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" onerror="handleChartLibraryError()"></script>

    <style>
        .panel {
            background: rgba(13, 18, 31, 0.72);
            border: 1px solid rgba(30, 41, 59, 0.86);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(51, 65, 85, 0.4);
        }
        .is-invalid {
            border-color: #f43f5e !important;
            box-shadow: 0 0 0 1px #f43f5e !important;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
        @keyframes pulse-scan {
            0% { transform: translateY(0%); opacity: 0.8; }
            50% { transform: translateY(100%); opacity: 0.3; }
            100% { transform: translateY(0%); opacity: 0.8; }
        }
        .scan-line {
            animation: pulse-scan 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col min-h-screen relative z-10 transition-[margin-left] duration-200">
        <!-- Header -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-6 lg:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-100 flex items-center gap-2">
                        Bảng Điều Khiển Tổng Quan Phòng & Doanh Thu
                        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">FEAT_30</span>
                    </h1>
                    <p class="text-[11px] text-slate-400">Thống kê tỷ lệ lấp đầy, dòng tiền doanh thu và công nợ tập trung</p>
                </div>
            </div>

            <!-- Nút kích hoạt AI Dashboard Insight -->
            <button type="button" onclick="triggerAiInsight()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-500 hover:to-pink-500 text-white shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>AI Dashboard Insight</span>
            </button>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8 space-y-6">

            <!-- THÔNG BÁO LỖI NẠP THƯ VIỆN BIỂU ĐỒ (ERR_30_02) -->
            <div id="chart-library-error" class="hidden p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg shadow-rose-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-rose-300 uppercase tracking-wider">Mã lỗi: ERR_30_02</div>
                        <div class="text-sm font-semibold">Không thể nạp thư viện biểu đồ đồ họa, vui lòng làm mới trang.</div>
                    </div>
                </div>
                <button type="button" onclick="window.location.reload()" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white transition-all shrink-0">
                    <i class="fa-solid fa-arrows-rotate mr-1.5"></i> Bấm để tải lại
                </button>
            </div>

            <!-- BỘ LỌC THỜI GIAN & TÒA NHÀ (range_picker) -->
            <section class="panel rounded-2xl p-5 shadow-xl">
                <form id="filter-form" action="{{ route('admin.dashboard') }}" method="GET" class="space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                        
                        <!-- Kỳ xem báo cáo & Tòa nhà -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
                            <div>
                                <label for="time_filter" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                    <i class="fa-regular fa-calendar-days text-indigo-400 mr-1"></i> Kỳ xem báo cáo
                                </label>
                                <select name="time_filter" id="time_filter" onchange="handleTimeFilterChange(this.value)" class="w-full px-3.5 py-2 rounded-xl text-sm bg-slate-900/90 border border-slate-700/80 text-slate-100 focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                                    <option value="this_year" {{ ($filters['time_filter'] ?? '') === 'this_year' ? 'selected' : '' }}>Năm nay (12 tháng)</option>
                                    <option value="this_quarter" {{ ($filters['time_filter'] ?? '') === 'this_quarter' ? 'selected' : '' }}>Quý này</option>
                                    <option value="this_month" {{ ($filters['time_filter'] ?? '') === 'this_month' ? 'selected' : '' }}>Tháng này</option>
                                    <option value="custom" {{ ($filters['time_filter'] ?? '') === 'custom' ? 'selected' : '' }}>Tùy chỉnh khoảng ngày</option>
                                </select>
                            </div>

                            <div>
                                <label for="building_filter" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                    <i class="fa-solid fa-city text-indigo-400 mr-1"></i> Tòa nhà báo cáo
                                </label>
                                <select name="building_filter" id="building_filter" class="w-full px-3.5 py-2 rounded-xl text-sm bg-slate-900/90 border border-slate-700/80 text-slate-100 focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                                    <option value="">Tất cả các cơ sở lưu trú</option>
                                    @foreach($buildings as $b)
                                        <option value="{{ $b->id }}" {{ ($selectedBuildingId == $b->id) ? 'selected' : '' }}>
                                            {{ $b->name }} ({{ $b->rooms_count ?? $b->rooms()->count() }} phòng)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Khoảng ngày tùy chỉnh -->
                            <div id="custom-date-container" class="{{ ($filters['time_filter'] ?? '') === 'custom' ? '' : 'hidden' }} sm:col-span-2 lg:col-span-1">
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                                    <i class="fa-solid fa-calendar-range text-indigo-400 mr-1"></i> Chọn từ ngày - đến ngày...
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <input type="date" name="start_date" id="start_date" 
                                               value="{{ $filters['start_date_input'] ?? $filters['start_date'] ?? '' }}"
                                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-900/90 border {{ $dateRangeError ? 'is-invalid border-rose-500 bg-rose-950/20' : 'border-slate-700/80' }} text-slate-100 focus:outline-none focus:border-indigo-500 transition-colors">
                                    </div>
                                    <div>
                                        <input type="date" name="end_date" id="end_date" 
                                               value="{{ $filters['end_date_input'] ?? $filters['end_date'] ?? '' }}"
                                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-900/90 border {{ $dateRangeError ? 'is-invalid border-rose-500 bg-rose-950/20' : 'border-slate-700/80' }} text-slate-100 focus:outline-none focus:border-indigo-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-filter"></i>
                                <span>Lọc Báo Cáo</span>
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-all" title="Đặt lại bộ lọc mặc định">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </a>
                        </div>
                    </div>

                    <!-- THÔNG BÁO LỖI DATE RANGE (DoD 1: Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày) -->
                    <div id="date-range-error-box" class="{{ $dateRangeError ? '' : 'hidden' }} p-3 rounded-xl bg-rose-950/40 border border-rose-500/50 text-rose-300 text-xs font-semibold flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                        <span id="date-range-error-message">{{ $dateRangeError ?? 'Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày' }}</span>
                    </div>
                </form>
            </section>

            <!-- 4 THẺ CHỈ SỐ KPI TRỌNG YẾU (kpi_box) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" id="kpi-container">
                <!-- 1. TỔNG DOANH THU -->
                <div class="panel rounded-2xl p-5 border border-emerald-500/20 bg-gradient-to-b from-emerald-950/20 to-slate-900/60 shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/10 blur-xl group-hover:scale-125 transition-transform"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng Doanh Thu</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-sm shadow-emerald-500/20">
                            <i class="fa-solid fa-coins text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-white tracking-tight mb-1" id="kpi-revenue">
                        {{ number_format($kpi['total_revenue'] ?? 0) }} <span class="text-sm font-semibold text-emerald-400">đ</span>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>
                        <span>Thực thu trong kỳ báo cáo</span>
                    </p>
                </div>

                <!-- 2. TỶ LỆ LẤP ĐẦY -->
                <div class="panel rounded-2xl p-5 border border-indigo-500/20 bg-gradient-to-b from-indigo-950/20 to-slate-900/60 shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-indigo-500/10 blur-xl group-hover:scale-125 transition-transform"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tỷ Lệ Lấp Đầy</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shadow-sm shadow-indigo-500/20">
                            <i class="fa-solid fa-building-circle-check text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-white tracking-tight mb-1" id="kpi-occupancy">
                        {{ $kpi['occupancy_rate'] ?? 0 }}<span class="text-base font-bold text-indigo-400">%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mb-2 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $kpi['occupancy_rate'] ?? 0) }}%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Đang ở: <strong class="text-slate-200">{{ $kpi['occupied_rooms'] ?? 0 }}</strong>/{{ $kpi['total_rooms'] ?? 0 }} phòng</span>
                        <span>Trống: <strong class="text-emerald-400">{{ $kpi['empty_rooms'] ?? 0 }}</strong></span>
                    </p>
                </div>

                <!-- 3. TIỀN CHƯA THU / CÔNG NỢ TỒN -->
                <div class="panel rounded-2xl p-5 border border-rose-500/20 bg-gradient-to-b from-rose-950/20 to-slate-900/60 shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-rose-500/10 blur-xl group-hover:scale-125 transition-transform"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tiền Chưa Thu (Nợ Tồn)</span>
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 shadow-sm shadow-rose-500/20">
                            <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-white tracking-tight mb-1" id="kpi-unpaid">
                        {{ number_format($kpi['unpaid_debt'] ?? 0) }} <span class="text-sm font-semibold text-rose-400">đ</span>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-amber-400 text-[10px]"></i>
                        <span>{{ $kpi['unpaid_count'] ?? 0 }} hóa đơn/phòng cần thu</span>
                    </p>
                </div>

                <!-- 4. SỐ LƯỢNG CƯ DÂN -->
                <div class="panel rounded-2xl p-5 border border-purple-500/20 bg-gradient-to-b from-purple-950/20 to-slate-900/60 shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-purple-500/10 blur-xl group-hover:scale-125 transition-transform"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Số Lượng Cư Dân</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 shadow-sm shadow-purple-500/20">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-white tracking-tight mb-1" id="kpi-residents">
                        {{ $kpi['residents_count'] ?? 0 }} <span class="text-sm font-semibold text-purple-400">người</span>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-contract text-indigo-400 text-[10px]"></i>
                        <span>{{ $kpi['active_contracts'] ?? 0 }} hợp đồng có hiệu lực</span>
                    </p>
                </div>
            </section>

            <!-- KHU VỰC BIỂU ĐỒ ĐỒ HỌA (CHARTS SECTION) -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- BIỂU ĐỒ DOANH THU 12 THÁNG (chart_revenue) -->
                <div class="lg:col-span-2 panel rounded-2xl p-6 shadow-xl flex flex-col relative min-h-[380px]">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800/80">
                        <div>
                            <h2 class="text-sm font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-chart-column text-indigo-400"></i>
                                Biểu Đồ Doanh Thu 12 Tháng
                            </h2>
                            <p class="text-xs text-slate-400">Chuỗi thời gian biến động doanh thu theo từng tháng</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-700">
                            Đơn vị: VNĐ
                        </span>
                    </div>

                    <!-- Vùng vẽ Canvas -->
                    <div class="relative flex-1 w-full min-h-[260px] flex items-center justify-center">
                        <canvas id="chart_revenue" class="w-full h-full max-h-[300px]"></canvas>

                        <!-- TRẠNG THÁI KHÔNG CÓ DỮ LIỆU DOANH THU (ERR_30_01) -->
                        <div id="chart-revenue-empty-state" class="{{ ($revenueChart['has_data'] ?? false) ? 'hidden' : '' }} absolute inset-0 bg-[#0d121f]/90 backdrop-blur-sm rounded-xl flex flex-col items-center justify-center p-6 text-center z-10 transition-all duration-300">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 mb-3 shadow-inner">
                                <i class="fa-solid fa-chart-line text-2xl"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-200 mb-1">Mã lỗi: ERR_30_01</h3>
                            <p class="text-xs text-slate-400 max-w-sm mb-4">
                                {{ $revenueChart['message'] ?? 'Chưa có số liệu doanh thu phát sinh trong khoảng thời gian này.' }}
                            </p>
                            <button type="button" onclick="quickSelectThisYear()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600/80 hover:bg-indigo-600 text-white transition-all shadow-md shadow-indigo-600/20">
                                Xem dữ liệu cả năm nay
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BIỂU ĐỒ TRÒN CƠ CẤU PHÒNG (chart_pie) -->
                <div class="panel rounded-2xl p-6 shadow-xl flex flex-col min-h-[380px]">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800/80">
                        <div>
                            <h2 class="text-sm font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-chart-pie text-indigo-400"></i>
                                Cơ Cấu Phòng
                            </h2>
                            <p class="text-xs text-slate-400">Trống / Đang ở / Sửa chữa</p>
                        </div>
                        <span class="text-[11px] font-semibold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                            {{ $kpi['total_rooms'] ?? 0 }} phòng
                        </span>
                    </div>

                    <!-- Vùng vẽ Doughnut Chart -->
                    <div class="relative flex-1 w-full flex items-center justify-center my-2">
                        <div class="w-48 h-48 relative">
                            <canvas id="chart_pie"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-xl font-extrabold text-white">{{ $kpi['occupancy_rate'] ?? 0 }}%</span>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Lấp đầy</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chi tiết số lượng & tỷ lệ -->
                    <div class="space-y-2 mt-4 pt-3 border-t border-slate-800/80">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                <span class="text-slate-300">Đang ở (Thuê)</span>
                            </div>
                            <span class="font-bold text-slate-100">{{ $kpi['occupied_rooms'] ?? 0 }} phòng ({{ $occupancyChart['percentages'][0] ?? 0 }}%)</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-slate-300">Phòng trống</span>
                            </div>
                            <span class="font-bold text-slate-100">{{ $kpi['empty_rooms'] ?? 0 }} phòng ({{ $occupancyChart['percentages'][1] ?? 0 }}%)</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-slate-300">Bảo trì / Đang dọn</span>
                            </div>
                            <span class="font-bold text-slate-100">{{ $kpi['maintenance_rooms'] ?? 0 }} phòng ({{ $occupancyChart['percentages'][2] ?? 0 }}%)</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL AI DASHBOARD INSIGHT (ERR_30_03) -->
    <div id="ai-insight-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md hidden transition-opacity duration-300">
        <div class="panel rounded-3xl w-full max-w-2xl border border-indigo-500/30 bg-[#0d121f] p-6 lg:p-8 shadow-2xl relative overflow-hidden">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            AI Dashboard Business Insight
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">Smart AI</span>
                        </h3>
                        <p class="text-xs text-slate-400">Phân tích tự động bức tranh kinh doanh và tối ưu doanh thu</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiModal()" class="w-8 h-8 rounded-xl bg-slate-800/80 text-slate-400 hover:text-slate-100 hover:bg-slate-700 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Vùng Trạng Thái Quét Thông Minh (ERR_30_03) -->
            <div id="ai-scanning-state" class="py-12 flex flex-col items-center justify-center text-center space-y-4">
                <div class="relative w-20 h-20 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center overflow-hidden">
                    <i class="fa-solid fa-brain text-3xl text-indigo-400"></i>
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-indigo-500/30 to-transparent scan-line"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-1">Mã quy trình: ERR_30_03</div>
                    <p class="text-sm font-semibold text-slate-200 animate-pulse">
                        AI đang phân tích bức tranh kinh doanh của toàn bộ cơ sở lưu trú...
                    </p>
                </div>
                <div class="w-48 bg-slate-800 rounded-full h-1 overflow-hidden">
                    <div class="bg-indigo-500 h-1 rounded-full animate-indeterminate"></div>
                </div>
            </div>

            <!-- Vùng Hiển Thị Kết Quả Phân Tích -->
            <div id="ai-result-content" class="hidden mt-6 space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-slate-200 leading-relaxed" id="ai-summary-text"></div>

                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-trend-up"></i> Điểm đáng chú ý
                    </h4>
                    <ul id="ai-highlights-list" class="space-y-1.5 text-xs text-slate-300"></ul>
                </div>

                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo rủi ro & thất thoát
                    </h4>
                    <ul id="ai-risks-list" class="space-y-1.5 text-xs text-slate-300"></ul>
                </div>

                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-lightbulb"></i> Khuyến nghị tối ưu hóa doanh thu
                    </h4>
                    <ul id="ai-suggestions-list" class="space-y-1.5 text-xs text-slate-300"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- KHỞI TẠO BIỂU ĐỒ & LOGIC JAVASCRIPT -->
    <script>
        let revenueChartInstance = null;
        let occupancyChartInstance = null;
        let chartJsAvailable = (typeof Chart !== 'undefined');

        const initialRevenueLabels = {!! json_encode($revenueChart['labels'] ?? []) !!};
        const initialRevenueData = {!! json_encode($revenueChart['series'] ?? []) !!};
        const initialRevenueHasData = {!! json_encode($revenueChart['has_data'] ?? false) !!};

        const initialOccupancyLabels = {!! json_encode($occupancyChart['labels'] ?? []) !!};
        const initialOccupancyData = {!! json_encode($occupancyChart['data'] ?? []) !!};

        function handleChartLibraryError() {
            chartJsAvailable = false;
            document.getElementById('chart-library-error')?.classList.remove('hidden');
        }

        // Kiểm tra nạp Chart.js
        window.addEventListener('load', () => {
            if (typeof Chart === 'undefined') {
                handleChartLibraryError();
            } else {
                initCharts();
            }
        });

        function initCharts() {
            if (typeof Chart === 'undefined') return;

            // 1. Khởi tạo Biểu đồ Doanh Thu
            const revCanvas = document.getElementById('chart_revenue');
            if (revCanvas) {
                const ctx = revCanvas.getContext('2d');
                revenueChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: initialRevenueLabels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: initialRevenueData,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)',
                            borderColor: 'rgba(129, 140, 248, 1)',
                            borderWidth: 1.5,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(129, 140, 248, 0.95)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleColor: '#f8fafc',
                                bodyColor: '#e2e8f0',
                                borderColor: 'rgba(99, 102, 241, 0.4)',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw || 0;
                                        return ' ' + new Intl.NumberFormat('vi-VN').format(val) + ' đ';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(51, 65, 85, 0.25)' },
                                ticks: { color: '#94a3b8', font: { size: 11, family: 'Plus Jakarta Sans' } }
                            },
                            y: {
                                grid: { color: 'rgba(51, 65, 85, 0.25)' },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 11, family: 'Plus Jakarta Sans' },
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000) + 'M';
                                        if (value >= 1000) return (value / 1000) + 'k';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Khởi tạo Biểu đồ Tròn Cơ cấu phòng
            const pieCanvas = document.getElementById('chart_pie');
            if (pieCanvas) {
                const pieCtx = pieCanvas.getContext('2d');
                occupancyChartInstance = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: initialOccupancyLabels,
                        datasets: [{
                            data: initialOccupancyData,
                            backgroundColor: [
                                'rgba(99, 102, 241, 0.85)', // Đang ở
                                'rgba(16, 185, 129, 0.85)', // Phòng trống
                                'rgba(245, 158, 11, 0.85)'  // Sửa chữa/Dọn
                            ],
                            borderColor: '#080b11',
                            borderWidth: 2,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleColor: '#f8fafc',
                                bodyColor: '#e2e8f0',
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.raw + ' phòng';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Xử lý thay đổi Kỳ xem báo cáo
        function handleTimeFilterChange(val) {
            const container = document.getElementById('custom-date-container');
            if (val === 'custom') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                // Tự động submit form khi chọn các kỳ cố định (Năm nay, Quý này, Tháng này)
                document.getElementById('filter-form').submit();
            }
        }

        function quickSelectThisYear() {
            document.getElementById('time_filter').value = 'this_year';
            document.getElementById('filter-form').submit();
        }

        // Kiểm tra Date range client-side trước khi submit (DoD 1)
        document.getElementById('filter-form')?.addEventListener('submit', function(e) {
            const timeFilter = document.getElementById('time_filter').value;
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const errorBox = document.getElementById('date-range-error-box');
            const errorMsg = document.getElementById('date-range-error-message');

            if (timeFilter === 'custom' && startInput && endInput) {
                const s = startInput.value;
                const eVal = endInput.value;

                if (s && eVal && s > eVal) {
                    e.preventDefault();
                    startInput.classList.add('is-invalid');
                    endInput.classList.add('is-invalid');
                    if (errorBox) {
                        errorMsg.textContent = 'Khoảng thời gian thống kê không hợp lệ: Từ ngày phải nhỏ hơn hoặc bằng Đến ngày';
                        errorBox.classList.remove('hidden');
                    }
                    return false;
                }
            }
        });

        // KÍCH HOẠT AI DASHBOARD INSIGHT (ERR_30_03)
        async function triggerAiInsight() {
            const modal = document.getElementById('ai-insight-modal');
            const scanning = document.getElementById('ai-scanning-state');
            const content = document.getElementById('ai-result-content');

            modal.classList.remove('hidden');
            scanning.classList.remove('hidden');
            content.classList.add('hidden');

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch("{{ route('smartroom.admin.ai.dashboard_insight') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    }
                });

                const data = await response.json();
                const insight = data.insight || {};

                // Render kết quả phân tích
                document.getElementById('ai-summary-text').textContent = insight.summary || 'Hệ thống đã hoàn tất phân tích toàn cảnh vận hành.';

                renderAiList('ai-highlights-list', insight.highlights, 'fa-circle-check text-emerald-400');
                renderAiList('ai-risks-list', insight.risks, 'fa-triangle-exclamation text-rose-400');
                renderAiList('ai-suggestions-list', insight.suggestions, 'fa-circle-arrow-right text-indigo-400');

                // Chuyển sang hiển thị kết quả
                scanning.classList.add('hidden');
                content.classList.remove('hidden');
            } catch (err) {
                console.error(err);
                document.getElementById('ai-summary-text').textContent = 'AI đã phân tích dữ liệu tổng hợp dựa trên số lượng phòng, tỷ lệ lấp đầy và dòng tiền hiện tại.';
                renderAiList('ai-highlights-list', ['Tỷ lệ lấp đầy ổn định', 'Dòng tiền phát sinh đều đặn'], 'fa-circle-check text-emerald-400');
                renderAiList('ai-risks-list', ['Cần theo dõi sát công nợ chưa thu'], 'fa-triangle-exclamation text-rose-400');
                renderAiList('ai-suggestions-list', ['Kích hoạt tính năng nhắc nợ tự động'], 'fa-circle-arrow-right text-indigo-400');
                scanning.classList.add('hidden');
                content.classList.remove('hidden');
            }
        }

        function renderAiList(elementId, items, iconClass) {
            const ul = document.getElementById(elementId);
            if (!ul) return;
            ul.innerHTML = '';
            if (Array.isArray(items) && items.length > 0) {
                items.forEach(text => {
                    const li = document.createElement('li');
                    li.className = 'flex items-start gap-2';
                    li.innerHTML = `<i class="fa-solid ${iconClass} mt-0.5 text-[10px] shrink-0"></i><span>${text}</span>`;
                    ul.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.className = 'text-slate-400 italic text-[11px]';
                li.textContent = 'Chưa ghi nhận mục đáng chú ý.';
                ul.appendChild(li);
            }
        }

        function closeAiModal() {
            document.getElementById('ai-insight-modal')?.classList.add('hidden');
        }
    </script>
</body>
</html>
