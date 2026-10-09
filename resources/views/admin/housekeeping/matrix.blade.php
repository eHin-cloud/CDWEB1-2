<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sơ Đồ Ma Trận Buồng Phòng & Lễ Tân - SmartRoom (FEAT_18)</title>
    @include('admin.partials.theme-head-script')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- FontAwesome & Sidebar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    
    <script>
        try {
            if (localStorage.getItem('smartroom.sidebar.collapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch(e) {}
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .shake-error {
            animation: shake 0.4s ease-in-out;
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.4) !important;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #080b11; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #334155; }
        
        .glass-panel {
            background: rgba(13, 18, 31, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 41, 59, 0.8);
        }

        #admin-sidebar {
            width: 16rem !important;
            transition: width 0.2s ease-in-out;
        }

        #admin-shell {
            margin-left: 16rem !important;
            min-width: 0;
            transition: margin-left 0.2s ease-in-out;
        }

        html.sidebar-collapsed #admin-sidebar,
        body.sidebar-collapsed #admin-sidebar {
            width: 5rem !important;
        }

        html.sidebar-collapsed #admin-shell,
        body.sidebar-collapsed #admin-shell {
            margin-left: 5rem !important;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-teal-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[450px] h-[450px] rounded-full bg-teal-600/10 blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[450px] h-[450px] rounded-full bg-indigo-600/10 blur-[130px] pointer-events-none"></div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-md w-full pointer-events-none px-4"></div>

    <!-- SIDEBAR QUẢN TRỊ VIÊN CHUẨN CỦA HỆ THỐNG -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER CÓ THANH SIDEBAR BÊN TRÁI -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-hidden relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP NAVBAR ĐIỀU HÀNH -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-6 sm:px-8 sticky top-0 z-20 shrink-0">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 shadow-sm shrink-0">
                    <i class="fa-solid fa-broom-ball text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-white tracking-tight truncate">
                            {{ $isHousekeeper ? 'SƠ ĐỒ BUỒNG PHÒNG' : 'SƠ ĐỒ BUỒNG PHÒNG & LỄ TÂN' }}
                        </h2>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/30 shrink-0 hidden sm:inline-block">
                            FEAT_18_HOUSEKEEPING_FRONTDESK
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 truncate">
                        {{ Auth::user()->tenant->name ?? 'Cơ sở lưu trú' }} • {{ Auth::user()->name }} ({{ Auth::user()->roleName() }})
                    </p>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-3 shrink-0">
                @unless($isHousekeeper)
                <button type="button" onclick="openAssignModal(null)" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/25 transition">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span class="hidden sm:inline">Phân Công Dọn Buồng</span>
                    <span class="sm:hidden">Phân công</span>
                </button>
                @endunless
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button p-2.5 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                @if(Auth::user()?->canAccessLandlordDashboard())
                    <a href="{{ route('smartroom.admin') }}" class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition hidden sm:inline-flex items-center gap-1.5" title="Về tổng quan quản trị">
                        <i class="fa-solid fa-arrow-left"></i> Về Quản Trị
                    </a>
                @else
                    <a href="{{ route('renty.user') }}" class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition hidden sm:inline-flex items-center gap-1.5" title="Về trang chủ Renty">
                        <i class="fa-solid fa-house text-xs"></i> Trang Chủ
                    </a>
                @endif
            </div>
        </header>

        <!-- MAIN CONTENT PANEL (Cuộn độc lập) -->
        <main class="p-6 sm:p-8 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-6">

            <!-- Thống Kê Tổng Quan Trạng Thái FSM (KPI Summary Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
                <!-- Tổng số phòng -->
                <div class="bg-[#0f1423] border border-slate-800/90 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tất Cả Phòng</div>
                    <div class="text-2xl sm:text-3xl font-black text-white mt-1.5">{{ $stats['total'] }}</div>
                    <div class="text-[10px] text-slate-500 mt-1">Tổng phòng trong cơ sở</div>
                </div>

                <!-- Cần dọn (Dirty - Đỏ #EF4444) -->
                <div class="bg-[#181115] border border-red-500/30 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-red-400 uppercase tracking-wider">Cần dọn (Dirty)</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-500/50"></span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-red-400 mt-1.5" id="stat-dirty">{{ $stats['dirty'] }}</div>
                    <div class="text-[10px] text-red-400/70 mt-1">Khách vừa trả / cần vệ sinh</div>
                </div>

                <!-- Đang dọn (Cleaning - Vàng cam #F59E0B) -->
                <div class="bg-[#18150f] border border-amber-500/30 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Đang dọn</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm shadow-amber-500/50 animate-pulse"></span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-amber-400 mt-1.5" id="stat-cleaning">{{ $stats['cleaning'] }}</div>
                    <div class="text-[10px] text-amber-400/70 mt-1">Nhân viên đang làm vệ sinh</div>
                </div>

                <!-- Đã dọn xong (Clean - Xanh lục #10B981) -->
                <div class="bg-[#0f1814] border border-emerald-500/30 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Đã dọn (Clean)</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1.5" id="stat-clean">{{ $stats['clean'] }}</div>
                    <div class="text-[10px] text-emerald-400/70 mt-1">Chờ Lễ tân nghiệm thu</div>
                </div>

                <!-- Đã nghiệm thu (Inspected - Xanh ngọc #0D9488) -->
                <div class="bg-[#0e1718] border border-teal-500/30 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-teal-400 uppercase tracking-wider">Nghiệm thu đạt</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-400 shadow-sm shadow-teal-500/50"></span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-teal-300 mt-1.5" id="stat-inspected">{{ $stats['inspected'] }}</div>
                    <div class="text-[10px] text-teal-400/70 mt-1">Sẵn sàng bàn giao khách</div>
                </div>

                <!-- Tạm dừng phục vụ (Out of service - Xám #64748B) -->
                <div class="bg-[#11141c] border border-slate-700/50 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tạm dừng</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-300 mt-1.5" id="stat-out-of-service">{{ $stats['out_of_service'] }}</div>
                    <div class="text-[10px] text-slate-500 mt-1">Đang bảo trì thiết bị</div>
                </div>
            </div>

            <!-- Thanh Công Cụ Bộ Lọc (UI Element 1: btnStatusFilter) & Tìm Kiếm -->
            <div class="bg-[#0f1423] border border-slate-800/80 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                
                <!-- STT 1: btnStatusFilter - Lọc trạng thái phòng -->
                <div class="flex flex-wrap items-center gap-1.5" id="status-filter-group">
                    <span class="text-xs font-bold text-slate-400 mr-2 flex items-center gap-1">
                        <i class="fa-solid fa-filter text-[11px]"></i> Lọc:
                    </span>
                    <button type="button" name="btnStatusFilter" data-status="all" onclick="filterByStatus('all')" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border {{ $statusFilter === 'all' ? 'bg-indigo-600 text-white border-indigo-500 shadow-md shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-300 border-slate-700 hover:bg-slate-700 hover:text-white' }}">
                        Tất cả ({{ $stats['total'] }})
                    </button>
                    <button type="button" name="btnStatusFilter" data-status="dirty" onclick="filterByStatus('dirty')" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border {{ $statusFilter === 'dirty' ? 'bg-red-600 text-white border-red-500 shadow-md shadow-red-600/30' : 'bg-red-500/10 text-red-400 border-red-500/20 hover:bg-red-500/20' }}">
                        <i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i> Cần dọn ({{ $stats['dirty'] }})
                    </button>
                    <button type="button" name="btnStatusFilter" data-status="cleaning" onclick="filterByStatus('cleaning')" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border {{ $statusFilter === 'cleaning' ? 'bg-amber-600 text-white border-amber-500 shadow-md shadow-amber-600/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/20 hover:bg-amber-500/20' }}">
                        <i class="fa-solid fa-spray-can-sparkles text-[10px] mr-1"></i> Đang dọn ({{ $stats['cleaning'] }})
                    </button>
                    <button type="button" name="btnStatusFilter" data-status="clean" onclick="filterByStatus('clean')" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border {{ $statusFilter === 'clean' ? 'bg-emerald-600 text-white border-emerald-500 shadow-md shadow-emerald-600/30' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' }}">
                        <i class="fa-solid fa-check text-[10px] mr-1"></i> Đã xong ({{ $stats['clean'] }})
                    </button>
                    <button type="button" name="btnStatusFilter" data-status="inspected" onclick="filterByStatus('inspected')" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border {{ $statusFilter === 'inspected' ? 'bg-teal-600 text-white border-teal-500 shadow-md shadow-teal-600/30' : 'bg-teal-500/10 text-teal-400 border-teal-500/20 hover:bg-teal-500/20' }}">
                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Nghiệm thu ({{ $stats['inspected'] }})
                    </button>
                    @if($isHousekeeper)
                    <button type="button" name="btnStatusFilter" data-status="my_assigned" onclick="filterMyAssigned()" class="status-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-indigo-500/10 text-indigo-400 border-indigo-500/30 hover:bg-indigo-500/20">
                        <i class="fa-solid fa-user-check text-[10px] mr-1"></i> Phân công cho tôi
                    </button>
                    @endif
                </div>

                <!-- Tìm kiếm mã phòng & lọc tòa nhà -->
                <div class="flex items-center gap-2">
                    @if($buildings->count() > 1)
                    <select id="filter-building" onchange="filterBuilding(this.value)" class="bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 px-3 py-2 outline-none focus:border-indigo-500">
                        <option value="">Tất cả tòa nhà</option>
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}" {{ $currentBuildingId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <div class="relative flex-1 sm:w-56">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-500"></i>
                        <input type="text" id="search-room-input" onkeyup="searchRooms(this.value)" placeholder="Tìm mã phòng..." class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 pl-8 pr-3 py-2 outline-none focus:border-teal-500 placeholder-slate-500">
                    </div>

                    <button type="button" onclick="window.location.reload()" title="Tải lại ma trận" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                        <i class="fa-solid fa-rotate-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- STT 2: Room Matrix Grid (gridHousekeeping) - Sơ đồ buồng phòng dạng lưới -->
            <div id="gridHousekeeping" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                @forelse($rooms as $room)
                    @php
                        $hStatus = $room->housekeeping_status ?: 'dirty';
                        $priority = $room->priority ?: 'normal';
                        $staff = $room->assignedStaff;
                        $inspector = $room->inspector;
                        $activeBooking = $room->activeHotelBooking;
                    @endphp
                    <div id="room-card-{{ $room->id }}" 
                         data-room-id="{{ $room->id }}"
                         data-room-number="{{ $room->room_number }}"
                         data-housekeeping-status="{{ $hStatus }}"
                         data-status="{{ $room->status }}"
                         data-version="{{ (int) $room->version }}"
                         data-priority="{{ $priority }}"
                         data-building-id="{{ $room->building_id }}"
                         data-assigned-staff-id="{{ $room->assigned_staff_id }}"
                         class="room-card relative bg-[#0f1423] rounded-2xl border transition-all duration-200 p-4 shadow-md flex flex-col justify-between
                            {{ $hStatus === 'dirty' ? 'border-red-500/40 bg-red-950/10 hover:border-red-500' : '' }}
                            {{ $hStatus === 'cleaning' ? 'border-amber-500/40 bg-amber-950/10 hover:border-amber-500' : '' }}
                            {{ $hStatus === 'clean' ? 'border-emerald-500/40 bg-emerald-950/10 hover:border-emerald-500' : '' }}
                            {{ $hStatus === 'inspected' ? 'border-teal-500/40 bg-teal-950/10 hover:border-teal-500' : '' }}
                            {{ $hStatus === 'out_of_service' ? 'border-slate-700 bg-slate-900/30' : '' }}">
                        
                        <!-- Dải thông tin đầu thẻ phòng -->
                        <div>
                            <!-- Header thẻ phòng: Bên trái số phòng, bên phải huy hiệu FSM (Cố định không bao giờ bị lệch) -->
                            <div class="flex items-center justify-between gap-2.5 pb-2 border-b border-slate-800/80">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xl font-black font-mono text-white tracking-wide">P.{{ $room->room_number }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700 shrink-0">
                                        Tầng {{ $room->floor }}
                                    </span>
                                    @if($priority === 'urgent')
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-red-500 text-white shrink-0 animate-pulse" title="Khẩn cấp đón khách mới">
                                            KHẨN CẤP
                                        </span>
                                    @elseif($priority === 'high')
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-orange-500/20 text-orange-300 border border-orange-500/40 shrink-0">
                                            Ưu tiên cao
                                        </span>
                                    @endif
                                </div>

                                <!-- Huy hiệu FSM Badge cố định bên phải -->
                                <div id="badge-wrapper-{{ $room->id }}" class="shrink-0">
                                    @if($hStatus === 'dirty')
                                        <span class="px-2.5 py-1 bg-red-500/20 text-red-300 border border-red-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Cần dọn
                                        </span>
                                    @elseif($hStatus === 'cleaning')
                                        <span class="px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 animate-pulse whitespace-nowrap">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> Đang dọn
                                        </span>
                                    @elseif($hStatus === 'clean')
                                        <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Đã sạch
                                        </span>
                                    @elseif($hStatus === 'inspected')
                                        <span class="px-2.5 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="w-2 h-2 rounded-full bg-teal-400"></span> Nghiệm thu đạt
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-slate-800 text-slate-400 border border-slate-700 rounded-full text-[11px] font-semibold inline-flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="w-2 h-2 rounded-full bg-slate-500"></span> Tạm dừng
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Tên tòa nhà & loại phòng -->
                            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5 truncate">
                                <i class="fa-solid fa-hotel text-[10px] text-slate-500"></i>
                                <span class="truncate">{{ $room->building->name ?? 'Tòa nhà' }}</span>
                                <span class="text-slate-600">•</span>
                                <span class="capitalize text-slate-400">{{ $room->room_type }}</span>
                            </p>

                            <!-- Thông tin nhân sự phụ trách dọn & Trạng thái khách -->
                            <div class="mt-2.5 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="text-slate-400 flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-gear text-[11px] text-slate-500"></i> Buồng phòng:
                                    </span>
                                    <span class="font-semibold text-slate-200 truncate max-w-[150px]" id="staff-name-{{ $room->id }}">
                                        {{ $staff ? $staff->name : 'Chưa phân công' }}
                                    </span>
                                </div>

                                @if($hStatus === 'inspected' && $inspector)
                                <div class="flex items-center justify-between text-teal-300 text-[11px]">
                                    <span class="text-teal-400/80 flex items-center gap-1">
                                        <i class="fa-solid fa-stamp text-[10px]"></i> Nghiệm thu bởi:
                                    </span>
                                    <span class="font-bold truncate max-w-[140px]">{{ $inspector->name }}</span>
                                </div>
                                @endif

                                @if($room->status === 'occupied' && $activeBooking)
                                <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px] flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 font-semibold truncate">
                                        <i class="fa-solid fa-person-shelter text-rose-400"></i> {{ $activeBooking->guest_name }}
                                    </span>
                                    <span class="text-[10px] text-rose-400/80 shrink-0">Đang ở</span>
                                </div>
                                @elseif($room->status === 'occupied')
                                <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px]">
                                    <i class="fa-solid fa-person-shelter mr-1"></i> Có khách đang ở
                                </div>
                                @endif

                                @if($room->inspection_notes)
                                <p class="text-[11px] text-slate-400 italic line-clamp-1 mt-1 bg-slate-900/60 px-2 py-1 rounded-lg border border-slate-800">
                                    <i class="fa-solid fa-comment-dots mr-1 text-slate-500"></i> {{ $room->inspection_notes }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Khu vực các nút bấm thao tác (Action Buttons FSM) -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 space-y-2" id="card-actions-{{ $room->id }}">
                            
                            <!-- 1. Cần dọn (Dirty): Bắt đầu dọn (btnStartClean) -->
                            @if($hStatus === 'dirty')
                                @if($isHousekeeper)
                                    <button type="button" 
                                            name="btnStartClean" 
                                            onclick="updateRoomStatus({{ $room->id }}, 'cleaning')" 
                                            class="w-full py-2.5 px-3 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1.5"
                                            title="Bấm chọn phòng này để bắt đầu dọn">
                                        <i class="fa-solid fa-broom text-xs"></i>
                                        <span>Chọn phòng này để dọn</span>
                                    </button>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" 
                                                name="btnStartClean" 
                                                onclick="updateRoomStatus({{ $room->id }}, 'cleaning')" 
                                                class="py-2 px-2.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1.5"
                                                title="Bắt đầu ca dọn phòng">
                                            <i class="fa-solid fa-broom text-[11px]"></i>
                                            <span>Bắt đầu dọn</span>
                                        </button>
                                        <button type="button" 
                                                onclick="openAssignModal({{ $room->id }})" 
                                                class="py-2 px-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-user-clock text-[11px]"></i>
                                            <span>Phân công</span>
                                        </button>
                                    </div>
                                @endif

                            <!-- 2. Đang dọn (Cleaning): Báo dọn xong (Clean) -->
                            @elseif($hStatus === 'cleaning')
                                <div class="grid grid-cols-1 gap-2">
                                    <button type="button" 
                                            onclick="updateRoomStatus({{ $room->id }}, 'clean')" 
                                            class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-check-double text-xs"></i>
                                        <span>Xác nhận đã dọn xong (Clean)</span>
                                    </button>
                                </div>

                            <!-- 3. Đã dọn xong (Clean): Nghiệm thu phòng đạt chuẩn (btnInspectPass) -->
                            @elseif($hStatus === 'clean')
                                <div class="grid grid-cols-1 gap-2">
                                    <button type="button" 
                                            name="btnInspectPass" 
                                            onclick="openInspectModal({{ $room->id }})" 
                                            class="py-2.5 px-3 bg-[#0D9488] hover:bg-teal-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-700/25 transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-sm"></i>
                                        <span>Nghiệm thu đạt chuẩn</span>
                                    </button>
                                </div>

                            <!-- 4. Đã nghiệm thu (Inspected): Sẵn sàng Check-in đón khách -->
                            @elseif($hStatus === 'inspected')
                                @if($isHousekeeper)
                                    <div class="py-2 px-3 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs font-bold flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-teal-400"></i>
                                        <span>Phòng sạch đã nghiệm thu đạt chuẩn</span>
                                    </div>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" 
                                                onclick="openCheckInModal({{ $room->id }})" 
                                                class="py-2 px-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-key text-[11px]"></i>
                                            <span>Check-in</span>
                                        </button>
                                        <button type="button" 
                                                onclick="openAssignModal({{ $room->id }})" 
                                                class="py-2 px-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-rotate text-[11px]"></i>
                                            <span>Dọn lại</span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                @unless($isHousekeeper)
                                <button type="button" 
                                        onclick="updateRoomStatus({{ $room->id }}, 'clean')" 
                                        class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                                    Mở lại dịch vụ
                                </button>
                                @endunless
                            @endif

                            <!-- Check-out & Đối soát minibar (chỉ Lễ tân/Quản lý) -->
                            @if($room->status === 'occupied' && !$isHousekeeper)
                            <button type="button" 
                                    onclick="openCheckOutModal({{ $room->id }})" 
                                    class="w-full py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 text-xs font-bold rounded-xl border border-rose-500/30 transition flex items-center justify-center gap-1.5 mt-1">
                                <i class="fa-solid fa-right-from-bracket text-[11px]"></i>
                                <span>Check-out & Đối soát Minibar</span>
                            </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-[#0f1423] border border-slate-800 rounded-3xl p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-800/80 mx-auto flex items-center justify-center mb-4">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-white">Không tìm thấy phòng nào phù hợp</h3>
                        <p class="text-xs text-slate-400 mt-1">Vui lòng thử bỏ lọc hoặc thay đổi tiêu chí tìm kiếm.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- ==================== MODAL 1: PHÂN CÔNG BUỒNG PHÒNG ==================== -->
    <div id="modal-assign" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#0f1423] border border-slate-700/80 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in duration-150">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Phân Công Nhân Viên Buồng Phòng</h3>
                        <p class="text-xs text-slate-400" id="assign-modal-subtitle">Giao nhiệm vụ dọn dẹp theo ca</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-assign')" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-assign" onsubmit="handleAssignSubmit(event)" class="space-y-4">
                <!-- Chọn phòng -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Phòng cần phân công <span class="text-red-400">*</span>
                    </label>
                    <select id="assign-room-id" name="room_id" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                        <option value="">-- Chọn phòng cần dọn --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">Phòng {{ $r->room_number }} (Tầng {{ $r->floor }} - {{ $r->building->name ?? '' }}) - {{ $r->housekeeping_status }}</option>
                        @endforeach
                    </select>
                    <p id="err-room-id" class="text-red-400 text-[11px] mt-1 hidden"></p>
                </div>

                <!-- STT 3: Select cboStaff - Nhân viên dọn phòng * -->
                <div>
                    <label for="cboStaff" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Nhân viên dọn phòng <span class="text-red-400">*</span>
                    </label>
                    <select id="cboStaff" name="assigned_staff_id" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                        <option value="">Chọn nhân viên phụ trách...</option>
                        @foreach($housekeepers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->phone ?? $staff->roleName() }})</option>
                        @endforeach
                    </select>
                    <p id="err-staff-id" class="text-red-400 text-[11px] mt-1 hidden"></p>
                </div>

                <!-- STT 4: Select cboPriority - Mức độ ưu tiên -->
                <div>
                    <label for="cboPriority" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Mức độ ưu tiên
                    </label>
                    <select id="cboPriority" name="priority" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                        <option value="normal">Bình thường (Normal)</option>
                        <option value="urgent">Khẩn cấp đón khách (Urgent)</option>
                        <option value="high">Ưu tiên cao (High)</option>
                        <option value="low">Ưu tiên thấp (Low)</option>
                    </select>
                </div>

                <!-- Ghi chú kiểm phòng / ca dọn -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ghi chú yêu cầu dọn
                    </label>
                    <textarea id="assign-notes" name="notes" rows="2" maxlength="255" placeholder="Ghi chú thiết bị, yêu cầu ga gối hoặc bổ sung minibar..." class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500 placeholder-slate-500"></textarea>
                    <div class="text-[10px] text-slate-500 text-right mt-0.5">Tối đa 255 ký tự</div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('modal-assign')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Hủy Bỏ
                    </button>
                    <button type="submit" id="btn-submit-assign" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Xác Nhận Phân Công
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 2: NGHIỆM THU PHÒNG ĐẠT CHUẨN ==================== -->
    <div id="modal-inspect" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#0f1423] border border-teal-500/40 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5 animate-in fade-in duration-150">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center">
                        <i class="fa-solid fa-stamp text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Nghiệm Thu Buồng Phòng</h3>
                        <p class="text-xs text-teal-400" id="inspect-modal-room-label">Phòng P.---</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-inspect')" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-inspect" onsubmit="handleInspectSubmit(event)" class="space-y-4">
                <input type="hidden" id="inspect-room-id" name="room_id">

                <div class="p-3 bg-teal-950/20 border border-teal-500/30 rounded-xl text-xs text-teal-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-teal-400 text-sm mt-0.5"></i>
                    <div>
                        <p class="font-bold">Quy trình kiểm tra chất lượng (FSM):</p>
                        <p class="text-[11px] text-teal-200/80 mt-0.5">
                            Xác nhận phòng đã được nhân viên buồng phòng dọn sạch, chăn ga thơm tho, đồ đạc minibar đầy đủ và sẵn sàng bàn giao chìa khóa cho khách.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ghi chú nghiệm thu (tùy chọn)
                    </label>
                    <textarea id="inspect-notes" name="inspection_notes" rows="2" maxlength="255" placeholder="Ghi nhận tình trạng trang thiết bị đạt chuẩn..." class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-teal-500 placeholder-slate-500">Phòng đạt chuẩn vệ sinh 5 sao, sẵn sàng đón khách</textarea>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('modal-inspect')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Đóng
                    </button>
                    <!-- STT 6: Button btnInspectPass - Nghiệm thu phòng đạt chuẩn (Nền xanh ngọc #0D9488) -->
                    <button type="submit" id="btnInspectPass" class="px-5 py-2.5 rounded-xl bg-[#0D9488] hover:bg-teal-600 text-white text-xs font-bold shadow-lg shadow-teal-700/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> Nghiệm Thu Phòng Đạt Chuẩn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 3: CHECK-IN ĐÓN KHÁCH ==================== -->
    <div id="modal-checkin" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#0f1423] border border-slate-700/80 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in duration-150">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Check-in Khách Lưu Trú</h3>
                        <p class="text-xs text-indigo-400" id="checkin-modal-room-label">Phòng P.---</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-checkin')" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-checkin" onsubmit="handleCheckInSubmit(event)" class="space-y-4">
                <input type="hidden" id="checkin-room-id" name="room_id">

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Họ và tên khách lưu trú <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="checkin-guest-name" name="guest_name" required placeholder="Nguyễn Văn A" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                            Số điện thoại
                        </label>
                        <input type="text" id="checkin-guest-phone" name="guest_phone" placeholder="0987xxxxxx" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                            CCCD / Hộ chiếu
                        </label>
                        <input type="text" id="checkin-guest-cccd" name="guest_cccd" placeholder="0012xxxxxx" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                            Hình thức thuê <span class="text-red-400">*</span>
                        </label>
                        <select id="checkin-rental-type" name="rental_type" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                            <option value="day">Theo ngày (Day)</option>
                            <option value="hour">Theo giờ (Hour)</option>
                            <option value="month">Theo tháng (Month)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                            Tiền đặt cọc (VNĐ)
                        </label>
                        <input type="number" id="checkin-deposit" name="deposit_amount" min="0" step="50000" placeholder="0" class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ghi chú
                    </label>
                    <input type="text" id="checkin-note" name="note" placeholder="Yêu cầu thêm giường phụ, xuất hóa đơn..." class="w-full bg-[#080b11] text-slate-200 text-xs rounded-xl border border-slate-700 p-3 outline-none focus:border-indigo-500">
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('modal-checkin')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Hủy Bỏ
                    </button>
                    <button type="submit" id="btn-submit-checkin" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-key"></i> Xác Nhận Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 4: CẢNH BÁO ĐỎ CHẶN CHECK-IN (ERR_18_02) ==================== -->
    <div id="modal-guard-dirty" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="bg-[#1a0f12] border-2 border-red-500 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 duration-150">
            <div class="w-16 h-16 rounded-full bg-red-500/20 border-2 border-red-500/40 mx-auto flex items-center justify-center text-red-500 text-2xl shadow-lg shadow-red-500/30">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="text-center space-y-2">
                <span class="px-2.5 py-0.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 text-[10px] font-black uppercase tracking-wider">
                    FSM GUARD CONDITION (ERR_18_02)
                </span>
                <h3 class="text-lg font-black text-white">Chặn Check-In Tuyệt Đối!</h3>
                <p id="guard-dirty-msg" class="text-xs text-red-300 leading-relaxed font-semibold px-2">
                    Phòng đang ở trạng thái Cần dọn (Dirty). Không thể thực hiện Check-in đón khách!
                </p>
            </div>

            <div class="bg-red-950/40 border border-red-500/20 rounded-2xl p-3.5 text-xs text-red-200/90 space-y-1">
                <div class="flex items-center gap-2 font-bold text-red-300">
                    <i class="fa-solid fa-shield-halved"></i> Quy tắc bảo đảm an toàn dịch vụ:
                </div>
                <p class="text-[11px] text-red-300/80 leading-relaxed">
                    Theo quy trình máy trạng thái buồng phòng, khách chỉ được phép nhận chìa khóa khi phòng đã qua bước <b>Sạch sẽ (Clean)</b> và được Lễ tân <b>Nghiệm thu đạt chuẩn (Inspected)</b>.
                </p>
            </div>

            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-guard-dirty')" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                    Đã Hiểu
                </button>
                <button type="button" id="guard-action-assign-btn" class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-xs font-bold shadow-lg shadow-red-600/30 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-broom"></i> Điều Buồng Phòng
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 5: CHECK-OUT TRẢ PHÒNG & ĐỐI SOÁT MINIBAR ==================== -->
    <div id="modal-checkout" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#0f1423] border border-slate-700/80 rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-5 animate-in fade-in duration-150 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center">
                        <i class="fa-solid fa-receipt text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Check-Out & Đối Soát Minibar</h3>
                        <p class="text-xs text-rose-400" id="checkout-modal-room-label">Phòng P.---</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-checkout')" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-checkout" onsubmit="handleCheckOutSubmit(event)" class="space-y-4">
                <input type="hidden" id="checkout-room-id" name="room_id">

                <!-- Bảng Đối Soát Vật Tư Tiêu Hao & Dịch Vụ Phát Sinh (Mua hộ, đồ ăn ngoài...) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-cart-flatbed-suitcase text-amber-400"></i> Bảng kiểm kê Minibar &amp; Mặt hàng phát sinh
                        </label>
                        <button type="button" 
                                onclick="addCustomServiceRow()" 
                                class="px-2.5 py-1 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 text-amber-300 text-[11px] font-bold transition flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-plus text-[10px]"></i> Thêm mặt hàng / mua hộ
                        </button>
                    </div>

                    <div class="border border-slate-800 rounded-xl overflow-hidden bg-[#080b11]">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                                <tr>
                                    <th class="p-2.5">Mặt hàng / Dịch vụ</th>
                                    <th class="p-2.5 text-right w-28">Đơn giá</th>
                                    <th class="p-2.5 text-center w-24">Số lượng</th>
                                    <th class="p-2.5 text-right w-28">Thành tiền</th>
                                    <th class="p-2.5 text-center w-10"></th>
                                </tr>
                            </thead>
                            <!-- 1. Danh sách Minibar catalog mặc định -->
                            <tbody class="divide-y divide-slate-800/60" id="minibar-table-body">
                                @foreach($minibarCatalogs as $index => $item)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="p-2.5 font-medium text-slate-200">
                                        {{ $item['item_name'] }}
                                        <input type="hidden" name="minibar_items[{{ $index }}][item_name]" value="{{ $item['item_name'] }}">
                                        <input type="hidden" name="minibar_items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}">
                                        <input type="hidden" name="minibar_items[{{ $index }}][item_type]" value="minibar">
                                    </td>
                                    <td class="p-2.5 text-right font-mono text-slate-400">
                                        {{ number_format($item['unit_price']) }}đ
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <input type="number" 
                                               min="0" 
                                               value="0" 
                                               data-unit-price="{{ $item['unit_price'] }}"
                                               data-index="{{ $index }}"
                                               name="minibar_items[{{ $index }}][quantity]" 
                                               oninput="calcMinibarRow(this)"
                                               class="minibar-qty-input w-20 bg-[#0f1423] text-center text-white font-bold rounded-lg border border-slate-700 py-1 px-2 text-xs focus:border-indigo-500 outline-none">
                                    </td>
                                    <td class="p-2.5 text-right font-mono font-bold text-amber-400" id="subtotal-item-{{ $index }}">
                                        0đ
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-500 font-bold" title="Minibar có sẵn trong phòng">MB</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <!-- 2. Danh sách mặt hàng / dịch vụ phát sinh mua hộ thêm -->
                            <tbody class="divide-y divide-slate-800/60 border-t border-slate-800" id="custom-services-table-body">
                                <!-- Dòng động được chèn vào đây bởi addCustomServiceRow() -->
                            </tbody>

                            <tfoot class="bg-slate-900/80 border-t border-slate-800 font-bold">
                                <tr>
                                    <td colspan="3" class="p-2.5 text-right text-slate-400">Tổng phụ thu Minibar &amp; Dịch vụ thêm:</td>
                                    <td class="p-2.5 text-right text-amber-400 font-mono text-sm" id="minibar-grand-total">0đ</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-1.5">
                        <p id="err-minibar-qty" class="text-red-400 text-[11px] hidden"></p>
                        <button type="button" 
                                onclick="addCustomServiceRow()" 
                                class="text-xs text-amber-400 hover:text-amber-300 font-semibold inline-flex items-center gap-1 transition">
                            <i class="fa-solid fa-circle-plus text-[11px]"></i> Thêm mặt hàng khách mua hộ / phát sinh khác
                        </button>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Phương thức thanh toán Folio
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-indigo-500/40 bg-indigo-500/10 cursor-pointer">
                            <input type="radio" name="payment_method" value="vietqr" checked class="text-indigo-600">
                            <span class="text-xs font-bold text-white"><i class="fa-solid fa-qrcode mr-1 text-indigo-400"></i> VietQR Napas247</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-700 bg-slate-800/40 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="text-indigo-600">
                            <span class="text-xs font-semibold text-slate-300"><i class="fa-solid fa-money-bill-wave mr-1 text-emerald-400"></i> Tiền mặt</span>
                        </label>
                    </div>
                </div>

                <!-- Cảnh báo tự động chuyển sang Dirty -->
                <div class="p-3 bg-red-950/20 border border-red-500/30 rounded-xl text-xs text-red-300 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-rotate-right text-red-400"></i>
                    <span>Sau khi trả phòng, hệ thống sẽ <b>tự động chuyển phòng sang Cần dọn (Dirty)</b> để điều phối dọn dẹp.</span>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('modal-checkout')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Hủy Bỏ
                    </button>
                    <button type="submit" id="btn-submit-checkout" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Trả Phòng & Chuyển Sang Dirty
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts điều khiển tương tác -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const isHousekeeperUser = {{ $isHousekeeper ? 'true' : 'false' }};
        const currentAuthUserId = {{ Auth::id() ?? 0 }};
        const currentTenantId = {{ (int) ($scopedTenantId ?? Auth::user()->tenant_id ?? 0) }};

        let currentFilterStatus = '{{ $statusFilter ?? 'all' }}';
        let lastHousekeepingSyncTimestamp = Math.floor(Date.now() / 1000);
        let lastHandledEventKey = '';

        // Hiển thị Toast thông báo
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            
            const isSuccess = type === 'success';
            toast.className = `p-4 rounded-2xl border text-xs sm:text-sm flex items-center justify-between shadow-2xl pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0 ${
                isSuccess 
                    ? 'bg-[#0b1712] border-emerald-500/50 text-emerald-300 shadow-emerald-500/10' 
                    : 'bg-[#1a0f12] border-red-500/50 text-red-300 shadow-red-500/10'
            }`;
            
            toast.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid ${isSuccess ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-red-400'} text-base"></i>
                    <span class="font-semibold">${message}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white ml-3">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            }
        }

        // ==================== BỘ LỌC MA TRẬN ====================
        function filterByStatus(status) {
            currentFilterStatus = status;
            document.querySelectorAll('.status-filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'bg-red-600', 'bg-amber-600', 'bg-emerald-600', 'bg-teal-600', 'text-white');
                if (btn.getAttribute('data-status') === status) {
                    btn.classList.add('bg-indigo-600', 'text-white');
                }
            });

            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });
        }

        function filterMyAssigned() {
            currentFilterStatus = 'my_assigned';
            document.querySelectorAll('.status-filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'bg-red-600', 'bg-amber-600', 'bg-emerald-600', 'bg-teal-600', 'text-white');
                if (btn.getAttribute('data-status') === 'my_assigned') {
                    btn.classList.add('bg-indigo-600', 'text-white');
                }
            });

            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });
        }

        function applyCurrentFilterToCard(card) {
            if (!card) return;
            if (currentFilterStatus === 'all') {
                card.style.display = 'flex';
            } else if (currentFilterStatus === 'my_assigned') {
                const assignedId = parseInt(card.getAttribute('data-assigned-staff-id') || '0');
                card.style.display = (assignedId === currentAuthUserId) ? 'flex' : 'none';
            } else {
                const roomStatus = card.getAttribute('data-housekeeping-status');
                card.style.display = (roomStatus === currentFilterStatus) ? 'flex' : 'none';
            }
        }

        function filterBuilding(bId) {
            document.querySelectorAll('.room-card').forEach(card => {
                const cardBId = card.getAttribute('data-building-id');
                if (!bId || cardBId === bId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function searchRooms(keyword) {
            const val = keyword.trim().toLowerCase();
            document.querySelectorAll('.room-card').forEach(card => {
                const num = (card.getAttribute('data-room-number') || '').toLowerCase();
                if (!val || num.includes(val)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // ==================== CẬP NHẬT SỐ LIỆU KPI ĐẾM BỘ LỌC ====================
        function updateHousekeepingFilterCounts(stats = null) {
            let dirty = 0, cleaning = 0, clean = 0, inspected = 0, outOfService = 0, total = 0;
            
            if (stats) {
                dirty = stats.dirty ?? 0;
                cleaning = stats.cleaning ?? 0;
                clean = stats.clean ?? 0;
                inspected = stats.inspected ?? 0;
                outOfService = stats.out_of_service ?? 0;
                total = stats.total ?? 0;
            } else {
                const cards = document.querySelectorAll('.room-card');
                total = cards.length;
                cards.forEach(c => {
                    const s = c.getAttribute('data-housekeeping-status');
                    if (s === 'dirty') dirty++;
                    else if (s === 'cleaning') cleaning++;
                    else if (s === 'clean') clean++;
                    else if (s === 'inspected') inspected++;
                    else if (s === 'out_of_service') outOfService++;
                });
            }

            const elDirty = document.getElementById('stat-dirty');
            if (elDirty) elDirty.textContent = dirty;
            const elCleaning = document.getElementById('stat-cleaning');
            if (elCleaning) elCleaning.textContent = cleaning;
            const elClean = document.getElementById('stat-clean');
            if (elClean) elClean.textContent = clean;
            const elInspected = document.getElementById('stat-inspected');
            if (elInspected) elInspected.textContent = inspected;
            const elOut = document.getElementById('stat-out-of-service');
            if (elOut) elOut.textContent = outOfService;

            document.querySelectorAll('.status-filter-btn').forEach(btn => {
                const status = btn.getAttribute('data-status');
                if (status === 'all') {
                    btn.innerHTML = `Tất cả (${total})`;
                } else if (status === 'dirty') {
                    btn.innerHTML = `<i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i> Cần dọn (${dirty})`;
                } else if (status === 'cleaning') {
                    btn.innerHTML = `<i class="fa-solid fa-spray-can-sparkles text-[10px] mr-1"></i> Đang dọn (${cleaning})`;
                } else if (status === 'clean') {
                    btn.innerHTML = `<i class="fa-solid fa-check text-[10px] mr-1"></i> Đã xong (${clean})`;
                } else if (status === 'inspected') {
                    btn.innerHTML = `<i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Nghiệm thu (${inspected})`;
                }
            });
        }

        // ==================== CẬP NHẬT REALTIME THẺ PHÒNG KHÔNG RELOAD TRANG ====================
        function applyHousekeepingCardUpdate(roomData, stats = null) {
            if (!roomData || !roomData.id) return;
            const card = document.getElementById('room-card-' + roomData.id);
            if (!card) return;

            const hStatus = roomData.housekeeping_status || 'dirty';
            const version = roomData.version || 1;
            const assignedStaffId = roomData.assigned_staff_id || 0;
            const assignedStaffName = roomData.assigned_staff_name || 'Chưa phân công';

            // 1. Cập nhật data attributes
            card.setAttribute('data-housekeeping-status', hStatus);
            if (roomData.status) card.setAttribute('data-status', roomData.status);
            card.setAttribute('data-version', version);
            if (roomData.priority) card.setAttribute('data-priority', roomData.priority);
            card.setAttribute('data-assigned-staff-id', assignedStaffId);

            // 2. Cập nhật class Border & Background theo FSM
            card.classList.remove(
                'border-red-500/40', 'bg-red-950/10', 'hover:border-red-500',
                'border-amber-500/40', 'bg-amber-950/10', 'hover:border-amber-500',
                'border-emerald-500/40', 'bg-emerald-950/10', 'hover:border-emerald-500',
                'border-teal-500/40', 'bg-teal-950/10', 'hover:border-teal-500',
                'border-slate-700', 'bg-slate-900/30'
            );

            if (hStatus === 'dirty') {
                card.classList.add('border-red-500/40', 'bg-red-950/10', 'hover:border-red-500');
            } else if (hStatus === 'cleaning') {
                card.classList.add('border-amber-500/40', 'bg-amber-950/10', 'hover:border-amber-500');
            } else if (hStatus === 'clean') {
                card.classList.add('border-emerald-500/40', 'bg-emerald-950/10', 'hover:border-emerald-500');
            } else if (hStatus === 'inspected') {
                card.classList.add('border-teal-500/40', 'bg-teal-950/10', 'hover:border-teal-500');
            } else {
                card.classList.add('border-slate-700', 'bg-slate-900/30');
            }

            // 3. Cập nhật Badge FSM
            const badgeWrapper = document.getElementById('badge-wrapper-' + roomData.id);
            if (badgeWrapper) {
                if (hStatus === 'dirty') {
                    badgeWrapper.innerHTML = `
                        <span class="px-2.5 py-1 bg-red-500/20 text-red-300 border border-red-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Cần dọn
                        </span>`;
                } else if (hStatus === 'cleaning') {
                    badgeWrapper.innerHTML = `
                        <span class="px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 animate-pulse whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> Đang dọn
                        </span>`;
                } else if (hStatus === 'clean') {
                    badgeWrapper.innerHTML = `
                        <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Đã sạch
                        </span>`;
                } else if (hStatus === 'inspected') {
                    badgeWrapper.innerHTML = `
                        <span class="px-2.5 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/40 rounded-full text-[11px] font-extrabold inline-flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-teal-400"></span> Nghiệm thu đạt
                        </span>`;
                } else {
                    badgeWrapper.innerHTML = `
                        <span class="px-2.5 py-1 bg-slate-800 text-slate-400 border border-slate-700 rounded-full text-[11px] font-semibold inline-flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span> Tạm dừng
                        </span>`;
                }
            }

            // 4. Cập nhật tên nhân viên phụ trách
            const staffNameEl = document.getElementById('staff-name-' + roomData.id);
            if (staffNameEl) {
                staffNameEl.textContent = assignedStaffName;
            }

            // 5. Cập nhật các nút bấm thao tác (Action Buttons) theo vai trò
            const actionsWrapper = document.getElementById('card-actions-' + roomData.id);
            if (actionsWrapper) {
                if (isHousekeeperUser) {
                    // Quyền buồng phòng:
                    if (hStatus === 'dirty') {
                        actionsWrapper.innerHTML = `
                            <button type="button" 
                                    name="btnStartClean" 
                                    onclick="updateRoomStatus(${roomData.id}, 'cleaning')" 
                                    class="w-full py-2.5 px-3 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1.5"
                                    title="Bấm chọn phòng này để bắt đầu dọn">
                                <i class="fa-solid fa-broom text-xs"></i>
                                <span>Chọn phòng này để dọn</span>
                            </button>`;
                    } else if (hStatus === 'cleaning') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" 
                                        onclick="updateRoomStatus(${roomData.id}, 'clean')" 
                                        class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check-double text-xs"></i>
                                    <span>Xác nhận đã dọn xong (Clean)</span>
                                </button>
                            </div>`;
                    } else if (hStatus === 'clean') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" 
                                        name="btnInspectPass" 
                                        onclick="openInspectModal(${roomData.id})" 
                                        class="py-2.5 px-3 bg-[#0D9488] hover:bg-teal-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-700/25 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-sm"></i>
                                    <span>Nghiệm thu đạt chuẩn</span>
                                </button>
                            </div>`;
                    } else if (hStatus === 'inspected') {
                        actionsWrapper.innerHTML = `
                            <div class="py-2 px-3 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs font-bold flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-teal-400"></i>
                                <span>Phòng sạch đã nghiệm thu đạt chuẩn</span>
                            </div>`;
                    } else {
                        actionsWrapper.innerHTML = `<div class="py-2 text-center text-xs text-slate-500 italic">Đang tạm dừng</div>`;
                    }
                } else {
                    // Quyền Lễ tân / Quản lý:
                    if (hStatus === 'dirty') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" 
                                        name="btnStartClean" 
                                        onclick="updateRoomStatus(${roomData.id}, 'cleaning')" 
                                        class="py-2 px-2.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1.5"
                                        title="Bắt đầu ca dọn phòng">
                                    <i class="fa-solid fa-broom text-[11px]"></i>
                                    <span>Bắt đầu dọn</span>
                                </button>
                                <button type="button" 
                                        onclick="openAssignModal(${roomData.id})" 
                                        class="py-2 px-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-user-clock text-[11px]"></i>
                                    <span>Phân công</span>
                                </button>
                            </div>`;
                    } else if (hStatus === 'cleaning') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" 
                                        onclick="updateRoomStatus(${roomData.id}, 'clean')" 
                                        class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check-double text-xs"></i>
                                    <span>Báo dọn xong (Clean)</span>
                                </button>
                            </div>`;
                    } else if (hStatus === 'clean') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" 
                                        name="btnInspectPass" 
                                        onclick="openInspectModal(${roomData.id})" 
                                        class="py-2.5 px-3 bg-[#0D9488] hover:bg-teal-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-700/25 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-sm"></i>
                                    <span>Nghiệm thu đạt chuẩn</span>
                                </button>
                            </div>`;
                    } else if (hStatus === 'inspected') {
                        actionsWrapper.innerHTML = `
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" 
                                        onclick="openCheckInModal(${roomData.id})" 
                                        class="py-2 px-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-key text-[11px]"></i>
                                    <span>Check-in</span>
                                </button>
                                <button type="button" 
                                        onclick="updateRoomStatus(${roomData.id}, 'dirty')" 
                                        class="py-2 px-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition flex items-center justify-center gap-1"
                                        title="Chuyển phòng về trạng thái cần dọn dẹp lại">
                                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                    <span>Dọn lại</span>
                                </button>
                            </div>`;
                    } else {
                        actionsWrapper.innerHTML = `<div class="py-2 text-center text-xs text-slate-500 italic">Đang tạm dừng</div>`;
                    }
                }
            }

            // 6. Hiệu ứng viền phát sáng nhẹ để người dùng nhận biết ngay
            card.classList.add('ring-2', 'ring-teal-400');
            setTimeout(() => card.classList.remove('ring-2', 'ring-teal-400'), 1500);

            // 7. Đồng bộ hiển thị theo bộ lọc hiện tại và đếm lại số liệu KPI
            applyCurrentFilterToCard(card);
            updateHousekeepingFilterCounts(stats);
        }

        // ==================== PHÂN CÔNG BUỒNG PHÒNG (ASSIGN) ====================
        function openAssignModal(roomId) {
            const roomSelect = document.getElementById('assign-room-id');
            const staffSelect = document.getElementById('cboStaff');
            const errRoom = document.getElementById('err-room-id');
            const errStaff = document.getElementById('err-staff-id');

            // Reset errors
            roomSelect.classList.remove('border-red-500');
            staffSelect.classList.remove('border-red-500');
            errRoom.classList.add('hidden');
            errStaff.classList.add('hidden');

            if (roomId) {
                roomSelect.value = roomId;
            } else {
                roomSelect.value = "";
            }

            openModal('modal-assign');
        }

        async function handleAssignSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const roomId = form.room_id.value;
            const staffId = form.assigned_staff_id.value;
            const priority = form.priority.value;
            const notes = form.notes.value;

            const roomSelect = document.getElementById('assign-room-id');
            const staffSelect = document.getElementById('cboStaff');
            const errRoom = document.getElementById('err-room-id');
            const errStaff = document.getElementById('err-staff-id');

            roomSelect.classList.remove('border-red-500');
            staffSelect.classList.remove('border-red-500');
            errRoom.classList.add('hidden');
            errStaff.classList.add('hidden');

            // Validation UI: ERR_18_01 Chưa chọn phòng
            if (!roomId) {
                errRoom.textContent = "Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.";
                errRoom.classList.remove('hidden');
                roomSelect.classList.add('border-red-500');

                document.querySelectorAll('.room-card').forEach(card => {
                    card.classList.add('shake-error');
                    setTimeout(() => card.classList.remove('shake-error'), 500);
                });
                return;
            }

            // Validation UI: ERR_18_03 Chưa chọn nhân viên buồng phòng
            if (!staffId) {
                errStaff.textContent = "Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.";
                errStaff.classList.remove('hidden');
                staffSelect.classList.add('border-red-500');
                staffSelect.focus();
                return;
            }

            // Lấy version hiện tại từ DOM để Optimistic Locking
            const targetCard = document.getElementById('room-card-' + roomId);
            const currentVersion = targetCard ? parseInt(targetCard.getAttribute('data-version') || '1') : null;

            try {
                const res = await fetch("{{ route('smartroom.admin.housekeeping.assign') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        room_id: parseInt(roomId),
                        assigned_staff_id: parseInt(staffId),
                        priority: priority,
                        inspection_notes: notes,
                        version: currentVersion
                    })
                });

                const data = await res.json();

                // Optimistic Locking: Xung đột phiên bản dữ liệu (HTTP 409)
                if (res.status === 409 || data.code === 'ERR_OPTIMISTIC_LOCK') {
                    showToast(data.message, 'error');
                    if (data.room) applyHousekeepingCardUpdate(data.room);
                    closeModal('modal-assign');
                    return;
                }

                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-assign');
                    // CẬP NHẬT TỨC THÌ GIAO DIỆN KHÔNG CẦN F5 HOẶC RELOAD LẠI TRANG
                    if (data.room) {
                        applyHousekeepingCardUpdate(data.room);
                    }
                } else {
                    if (data.code === 'ERR_18_03') {
                        errStaff.textContent = data.message;
                        errStaff.classList.remove('hidden');
                        staffSelect.classList.add('border-red-500');
                    } else if (data.code === 'ERR_18_01') {
                        errRoom.textContent = data.message;
                        errRoom.classList.remove('hidden');
                        roomSelect.classList.add('border-red-500');
                    } else {
                        showToast(data.message || 'Lỗi phân công.', 'error');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast("Lỗi kết nối tới máy chủ.", "error");
            }
        }

        // ==================== CẬP NHẬT TIẾN ĐỘ DỌN PHÒNG (STATUS) ====================
        async function updateRoomStatus(roomId, newStatus) {
            const targetCard = document.getElementById('room-card-' + roomId);
            const currentVersion = targetCard ? parseInt(targetCard.getAttribute('data-version') || '1') : null;

            try {
                const res = await fetch("{{ route('smartroom.admin.housekeeping.status') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        housekeeping_status: newStatus,
                        version: currentVersion
                    })
                });

                const data = await res.json();

                // Optimistic Locking: Xung đột phiên bản dữ liệu (HTTP 409)
                if (res.status === 409 || data.code === 'ERR_OPTIMISTIC_LOCK') {
                    showToast(data.message, 'error');
                    if (data.room) applyHousekeepingCardUpdate(data.room);
                    return;
                }

                if (data.success) {
                    showToast(data.message, 'success');
                    // CẬP NHẬT TỨC THÌ GIAO DIỆN KHÔNG CẦN RELOAD TRANG
                    if (data.room) {
                        applyHousekeepingCardUpdate(data.room);
                    }
                } else {
                    // ERR_18_04: Toast cảnh báo màu đỏ góc màn hình
                    showToast(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast("Lỗi kết nối máy chủ.", "error");
            }
        }

        // ==================== NGHIỆM THU BUỒNG PHÒNG (INSPECT) ====================
        function openInspectModal(roomId) {
            const card = document.getElementById(`room-card-${roomId}`);
            const roomNum = card?.getAttribute('data-room-number') || roomId;

            document.getElementById('inspect-room-id').value = roomId;
            document.getElementById('inspect-modal-room-label').textContent = `Phòng P.${roomNum} - Sẵn sàng nghiệm thu`;
            openModal('modal-inspect');
        }

        async function handleInspectSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const roomId = form.room_id.value;
            const notes = form.inspection_notes.value;

            const targetCard = document.getElementById('room-card-' + roomId);
            const currentVersion = targetCard ? parseInt(targetCard.getAttribute('data-version') || '1') : null;

            try {
                const res = await fetch("{{ route('smartroom.admin.housekeeping.inspect') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        room_id: parseInt(roomId),
                        inspection_notes: notes,
                        version: currentVersion
                    })
                });

                const data = await res.json();

                // Optimistic Locking: Xung đột phiên bản dữ liệu (HTTP 409)
                if (res.status === 409 || data.code === 'ERR_OPTIMISTIC_LOCK') {
                    showToast(data.message, 'error');
                    if (data.room) applyHousekeepingCardUpdate(data.room);
                    closeModal('modal-inspect');
                    return;
                }

                if (data.success) {
                    // ERR_18_06: Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.
                    showToast(data.message, 'success');
                    closeModal('modal-inspect');
                    // CẬP NHẬT TỨC THÌ GIAO DIỆN KHÔNG CẦN RELOAD TRANG
                    if (data.room) {
                        applyHousekeepingCardUpdate(data.room);
                    }
                } else {
                    // ERR_18_04: Chuyển đổi trạng thái FSM không hợp lệ
                    showToast(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast("Lỗi kết nối máy chủ.", "error");
            }
        }

        // ==================== CHECK-IN ĐÓN KHÁCH (GUARD CHECK DIRTY) ====================
        function openCheckInModal(roomId) {
            const card = document.getElementById(`room-card-${roomId}`);
            const roomNum = card?.getAttribute('data-room-number') || roomId;
            const hStatus = card?.getAttribute('data-housekeeping-status') || 'dirty';

            // Guard Check Client-side (ERR_18_02)
            if (hStatus === 'dirty') {
                document.getElementById('guard-dirty-msg').textContent = 
                    `Phòng ${roomNum} đang ở trạng thái Cần dọn (Dirty). Không thể thực hiện Check-in đón khách!`;
                
                const assignBtn = document.getElementById('guard-action-assign-btn');
                assignBtn.onclick = () => {
                    closeModal('modal-guard-dirty');
                    openAssignModal(roomId);
                };

                openModal('modal-guard-dirty');
                return;
            }

            document.getElementById('checkin-room-id').value = roomId;
            document.getElementById('checkin-modal-room-label').textContent = `Phòng P.${roomNum}`;
            openModal('modal-checkin');
        }

        async function handleCheckInSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const roomId = form.room_id.value;

            const payload = {
                room_id: parseInt(roomId),
                guest_name: form.guest_name.value,
                guest_phone: form.guest_phone.value,
                guest_cccd: form.guest_cccd.value,
                rental_type: form.rental_type.value,
                deposit_amount: parseFloat(form.deposit_amount.value) || 0,
                note: form.note.value
            };

            try {
                const res = await fetch("{{ route('smartroom.admin.frontdesk.checkin') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-checkin');
                    if (data.room) {
                        applyHousekeepingCardUpdate(data.room);
                    }
                } else {
                    if (data.code === 'ERR_18_02') {
                        closeModal('modal-checkin');
                        document.getElementById('guard-dirty-msg').textContent = data.message;
                        document.getElementById('guard-action-assign-btn').onclick = () => {
                            closeModal('modal-guard-dirty');
                            openAssignModal(roomId);
                        };
                        openModal('modal-guard-dirty');
                    } else {
                        showToast(data.message || 'Lỗi Check-in.', 'error');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast("Lỗi kết nối máy chủ.", "error");
            }
        }

        // ==================== CHECK-OUT & ĐỐI SOÁT MINIBAR & DỊCH VỤ PHÁT SINH ====================
        function openCheckOutModal(roomId) {
            const card = document.getElementById(`room-card-${roomId}`);
            const roomNum = card?.getAttribute('data-room-number') || roomId;

            document.getElementById('checkout-room-id').value = roomId;
            document.getElementById('checkout-modal-room-label').textContent = `Phòng P.${roomNum} - Đối soát trả phòng`;

            // Reset inputs Minibar catalog
            document.querySelectorAll('.minibar-qty-input').forEach(input => {
                input.value = 0;
                input.classList.remove('border-red-500');
            });
            document.querySelectorAll('[id^="subtotal-item-"]').forEach(el => el.textContent = '0đ');
            
            // Dọn sạch danh sách mặt hàng phát sinh cũ
            const customTbody = document.getElementById('custom-services-table-body');
            if (customTbody) customTbody.innerHTML = '';

            document.getElementById('minibar-grand-total').textContent = '0đ';
            document.getElementById('err-minibar-qty').classList.add('hidden');

            openModal('modal-checkout');
        }

        // Tính lại tổng tiền chung (Minibar catalog + Mặt hàng/dịch vụ mua hộ phát sinh)
        function recalculateGrandTotal() {
            let grandTotal = 0;

            // 1. Minibar catalog mặc định
            document.querySelectorAll('.minibar-qty-input').forEach(inp => {
                const q = parseInt(inp.value) || 0;
                const p = parseFloat(inp.getAttribute('data-unit-price') || 0);
                if (q > 0) grandTotal += (q * p);
            });

            // 2. Custom services / mặt hàng phát sinh mua hộ
            document.querySelectorAll('.custom-service-row').forEach(row => {
                const qtyInp = row.querySelector('.custom-item-qty');
                const priceInp = row.querySelector('.custom-item-price');
                const q = parseInt(qtyInp?.value) || 0;
                const p = parseFloat(priceInp?.value) || 0;
                if (q > 0 && p > 0) {
                    grandTotal += (q * p);
                }
            });

            const grandTotalEl = document.getElementById('minibar-grand-total');
            if (grandTotalEl) {
                grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(grandTotal) + 'đ';
            }
        }

        function calcMinibarRow(input) {
            const errEl = document.getElementById('err-minibar-qty');
            const qty = parseFloat(input.value);
            const unitPrice = parseFloat(input.getAttribute('data-unit-price') || 0);
            const index = input.getAttribute('data-index');

            // Validate ERR_18_05
            if (isNaN(qty) || qty < 0 || !Number.isInteger(qty)) {
                input.classList.add('border-red-500');
                errEl.textContent = "Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0.";
                errEl.classList.remove('hidden');
            } else {
                input.classList.remove('border-red-500');
                errEl.classList.add('hidden');
            }

            const rowSubtotal = Math.max(0, (Number.isInteger(qty) && qty >= 0 ? qty : 0) * unitPrice);
            document.getElementById(`subtotal-item-${index}`).textContent = new Intl.NumberFormat('vi-VN').format(rowSubtotal) + 'đ';

            recalculateGrandTotal();
        }

        // Thêm dòng mặt hàng / dịch vụ phát sinh mua hộ
        function addCustomServiceRow(defaultName = '', defaultPrice = '', defaultQty = 1) {
            const tbody = document.getElementById('custom-services-table-body');
            if (!tbody) return;

            const tr = document.createElement('tr');
            tr.className = "custom-service-row bg-amber-950/15 hover:bg-amber-950/25 border-b border-slate-800 transition-colors";
            tr.innerHTML = `
                <td class="p-2">
                    <input type="text" 
                           value="${defaultName}"
                           placeholder="VD: Mua hộ 1 thùng nước, Giặt ủi..." 
                           class="custom-item-name w-full bg-[#080b11] text-amber-200 text-xs rounded-lg border border-amber-500/40 focus:border-amber-400 px-2.5 py-1.5 outline-none placeholder-slate-500 font-semibold"
                           required>
                </td>
                <td class="p-2 text-right">
                    <input type="number" 
                           min="0" 
                           step="1000" 
                           value="${defaultPrice}"
                           placeholder="Đơn giá (đ)" 
                           oninput="calcCustomServiceRow(this)"
                           class="custom-item-price w-24 bg-[#080b11] text-right font-mono text-white text-xs rounded-lg border border-slate-700 focus:border-amber-400 px-2 py-1.5 outline-none font-bold"
                           required>
                </td>
                <td class="p-2 text-center">
                    <input type="number" 
                           min="1" 
                           value="${defaultQty}" 
                           oninput="calcCustomServiceRow(this)"
                           class="custom-item-qty w-16 bg-[#080b11] text-center font-bold text-white text-xs rounded-lg border border-slate-700 focus:border-amber-400 py-1.5 px-1 outline-none"
                           required>
                </td>
                <td class="p-2 text-right font-mono font-bold text-amber-400 custom-item-subtotal">
                    0đ
                </td>
                <td class="p-2 text-center">
                    <button type="button" 
                            onclick="removeCustomServiceRow(this)" 
                            class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/20 w-7 h-7 rounded-lg transition inline-flex items-center justify-center" 
                            title="Xóa dòng mặt hàng này">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            const nameInput = tr.querySelector('.custom-item-name');
            if (nameInput && !defaultName) {
                nameInput.focus();
            }

            if (defaultPrice) {
                calcCustomServiceRow(nameInput);
            }
        }

        // Tính thành tiền cho dòng mặt hàng phát sinh
        function calcCustomServiceRow(input) {
            const row = input.closest('.custom-service-row');
            if (!row) return;

            const qtyInp = row.querySelector('.custom-item-qty');
            const priceInp = row.querySelector('.custom-item-price');
            const subtotalEl = row.querySelector('.custom-item-subtotal');

            const qty = parseInt(qtyInp?.value) || 0;
            const price = parseFloat(priceInp?.value) || 0;

            const subtotal = Math.max(0, qty * price);
            if (subtotalEl) {
                subtotalEl.textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
            }

            recalculateGrandTotal();
        }

        // Xóa dòng mặt hàng phát sinh
        function removeCustomServiceRow(btn) {
            const row = btn.closest('.custom-service-row');
            if (row) {
                row.remove();
                recalculateGrandTotal();
            }
        }

        async function handleCheckOutSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const roomId = form.room_id.value;
            const paymentMethod = form.payment_method.value;

            // Kiểm tra validate ERR_18_05
            let hasError = false;
            let errorMsg = "Số lượng vật tư tiêu hao minibar hoặc dịch vụ thêm phải là số nguyên dương lớn hơn hoặc bằng 0.";
            const items = [];

            // 1. Thu thập Minibar catalog mặc định
            document.querySelectorAll('.minibar-qty-input').forEach(input => {
                const val = input.value;
                const qty = parseInt(val);
                const index = input.getAttribute('data-index');
                const name = form[`minibar_items[${index}][item_name]`].value;
                const unitPrice = parseFloat(form[`minibar_items[${index}][unit_price]`].value);

                if (isNaN(qty) || qty < 0 || String(qty) !== String(val).trim()) {
                    input.classList.add('border-red-500');
                    hasError = true;
                } else if (qty > 0) {
                    items.push({
                        item_name: name,
                        quantity: qty,
                        unit_price: unitPrice,
                        item_type: 'minibar'
                    });
                }
            });

            // 2. Thu thập các mặt hàng / dịch vụ phát sinh thêm
            document.querySelectorAll('.custom-service-row').forEach(row => {
                const nameInp = row.querySelector('.custom-item-name');
                const priceInp = row.querySelector('.custom-item-price');
                const qtyInp = row.querySelector('.custom-item-qty');

                const name = (nameInp?.value || '').trim();
                const price = parseFloat(priceInp?.value);
                const qty = parseInt(qtyInp?.value);

                // Nếu dòng hoàn toàn trống -> bỏ qua
                if (!name && (isNaN(price) || price === 0)) {
                    return;
                }

                if (!name) {
                    nameInp.classList.add('border-red-500');
                    hasError = true;
                    errorMsg = "Vui lòng nhập tên mặt hàng hoặc dịch vụ mua hộ phát sinh.";
                } else if (isNaN(price) || price < 0) {
                    priceInp.classList.add('border-red-500');
                    hasError = true;
                    errorMsg = "Đơn giá của mặt hàng phát sinh phải là số lớn hơn hoặc bằng 0.";
                } else if (isNaN(qty) || qty <= 0) {
                    qtyInp.classList.add('border-red-500');
                    hasError = true;
                    errorMsg = "Số lượng mặt hàng phát sinh phải là số nguyên lớn hơn 0.";
                } else {
                    nameInp.classList.remove('border-red-500');
                    priceInp.classList.remove('border-red-500');
                    qtyInp.classList.remove('border-red-500');

                    items.push({
                        item_name: name,
                        quantity: qty,
                        unit_price: price,
                        item_type: 'service'
                    });
                }
            });

            if (hasError) {
                const errEl = document.getElementById('err-minibar-qty');
                errEl.textContent = errorMsg;
                errEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch("{{ route('smartroom.admin.frontdesk.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        room_id: parseInt(roomId),
                        minibar_items: items,
                        payment_method: paymentMethod
                    })
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-checkout');
                    if (data.room) {
                        applyHousekeepingCardUpdate(data.room);
                    }
                } else {
                    if (data.code === 'ERR_18_05') {
                        const errEl = document.getElementById('err-minibar-qty');
                        errEl.textContent = data.message;
                        errEl.classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Lỗi Check-out.', 'error');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast("Lỗi kết nối máy chủ.", "error");
            }
        }

        // ==================== REALTIME WEBSOCKET REVERB + SMART POLLING ====================
        function handleIncomingHousekeepingUpdate(eventData) {
            if (!eventData) return;
            const room = eventData.roomData || eventData.room || eventData;
            if (!room || !room.id) return;

            const eventKey = `${room.id}_${room.housekeeping_status || room.status}_${room.version || ''}_${room.assigned_staff_id || ''}`;
            if (eventKey === lastHandledEventKey) return;
            lastHandledEventKey = eventKey;
            lastHousekeepingSyncTimestamp = Math.floor(Date.now() / 1000);

            applyHousekeepingCardUpdate(room, eventData.stats || null);
        }

        function initHousekeepingRealtime() {
            // 1. WebSocket Reverb qua Laravel Echo
            if (window.Echo) {
                try {
                    if (currentTenantId) {
                        window.Echo.channel(`tenant.${currentTenantId}.room-matrix`)
                            .listen('.room.status.updated', handleIncomingHousekeepingUpdate);
                    }
                    window.Echo.channel('room-matrix')
                        .listen('.room.status.updated', handleIncomingHousekeepingUpdate);
                } catch (err) {
                    console.warn('Housekeeping Echo subscription notice:', err);
                }
            }

            // 2. Smart Polling Fallback (chạy mỗi 2.5s) đảm bảo tự động cập nhật ngay cả khi Reverb chưa bật local
            let isPolling = false;
            setInterval(async () => {
                if (document.hidden || isPolling) return;
                isPolling = true;
                try {
                    const res = await fetch("{{ route('smartroom.admin.housekeeping.poll') }}?since=" + lastHousekeepingSyncTimestamp, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.has_update && data.room) {
                            lastHousekeepingSyncTimestamp = data.room.updated_at || Math.floor(Date.now() / 1000);
                            handleIncomingHousekeepingUpdate(data);
                        }
                    }
                } catch (e) {
                    // Im lặng bỏ qua lỗi mạng
                } finally {
                    isPolling = false;
                }
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initHousekeepingRealtime, 250);
        });
    </script>
</body>
</html>
