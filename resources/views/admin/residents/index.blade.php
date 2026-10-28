<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Cư Dân & Khách Thuê - SmartRoom</title>
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
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 1px #ef4444 !important;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col min-h-screen relative z-10 transition-[margin-left] duration-200">
        <!-- Header -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-30">
            <div>
                <h1 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-400"></i>
                    Quản Lý Cư Dân & Khách Thuê
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">FEAT_26_RESIDENTS • Phụ trách: Huỳnh Văn Vĩnh Em</p>
            </div>
            <div class="flex items-center gap-3">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button p-2 text-slate-400 hover:text-white" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6 md:p-8 space-y-8 flex-1">
            <!-- Toast Thông Báo (Alerts) -->
            <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <span class="text-xs font-mono bg-emerald-500/20 px-2 py-0.5 rounded">{{ session('code', 'ERR_26_05') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center justify-between animate-fade-in">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <span class="text-xs font-mono bg-rose-500/20 px-2 py-0.5 rounded">{{ session('error_code', 'ERROR') }}</span>
                </div>
            @endif

            <!-- 1. Thống Kê Nhân Khẩu & Tạm Trú -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.15)] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Tổng cư dân</p>
                            <h3 class="text-2xl font-extrabold text-white mt-1">{{ $residentStats['total'] }}</h3>
                            <span class="text-[11px] text-slate-500">Người đang lưu trú</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.15)] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Đã khai báo tạm trú</p>
                            <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">{{ $residentStats['registered'] }}</h3>
                            <span class="text-[11px] text-emerald-500/80">Hồ sơ đầy đủ</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <i class="fa-solid fa-clipboard-check text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.15)] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Tạm vắng</p>
                            <h3 class="text-2xl font-extrabold text-amber-400 mt-1">{{ $residentStats['absent'] }}</h3>
                            <span class="text-[11px] text-amber-500/80">Khai báo vắng mặt</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                            <i class="fa-solid fa-user-clock text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(239,68,68,0.15)] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Chưa khai báo</p>
                            <h3 class="text-2xl font-extrabold text-rose-400 mt-1">{{ $residentStats['unregistered'] }}</h3>
                            <span class="text-[11px] text-rose-500/80">Cần đăng ký gấp</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400">
                            <i class="fa-solid fa-user-xmark text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Danh Sách Cư Dân & Bộ Lọc -->
            <div class="panel rounded-3xl p-6 relative">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <span>Danh sách cư dân</span>
                            <span class="text-xs font-normal text-slate-400">({{ $residents->total() }} bản ghi)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Quản lý danh sách cư dân, khách lưu trú và thân nhân theo từng phòng</p>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="flex flex-wrap items-center gap-3">
                        <form method="GET" action="{{ route('admin.residents.index', [], false) }}" class="flex flex-wrap items-center gap-2">
                            <!-- Input Tìm kiếm -->
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                <input type="search" name="q" value="{{ $searchQuery }}" 
                                    class="pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none w-48 sm:w-60 transition-all" 
                                    placeholder="Tìm tên, SĐT, CCCD, phòng...">
                            </div>

                            <!-- Filter Phòng -->
                            <select name="room_id" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-300 focus:border-indigo-500 focus:outline-none">
                                <option value="">Tất cả phòng</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" {{ $filterRoomId == $r->id ? 'selected' : '' }}>
                                        P. {{ $r->room_number }} ({{ $r->residents_count }} người)
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Tạm trú -->
                            <select name="temporary_status" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-300 focus:border-indigo-500 focus:outline-none">
                                <option value="">Tất cả tạm trú</option>
                                <option value="registered" {{ $filterStatus === 'registered' ? 'selected' : '' }}>Đã đăng ký</option>
                                <option value="absent" {{ $filterStatus === 'absent' ? 'selected' : '' }}>Tạm vắng</option>
                                <option value="none" {{ $filterStatus === 'none' ? 'selected' : '' }}>Chưa đăng ký</option>
                            </select>

                            @if($searchQuery !== '' || !empty($filterRoomId) || !empty($filterStatus))
                                <a href="{{ route('admin.residents.index') }}" class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white rounded-xl text-xs transition-all" title="Xóa bộ lọc">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                        </form>

                        <!-- Nút Thêm Cư Dân Mới -->
                        <button type="button" onclick="openAddResidentModal()" id="btnOpenAddResident" 
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-600/20 active:scale-95">
                            <i class="fa-solid fa-user-plus"></i>
                            + Thêm cư dân mới
                        </button>
                    </div>
                </div>

                <!-- Bảng Cư Dân (Resident Table: tblResidents) -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                    <table id="tblResidents" class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-400 uppercase bg-slate-900/80 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 font-bold">Họ tên</th>
                                <th class="px-4 py-3.5 font-bold">Phòng</th>
                                <th class="px-4 py-3.5 font-bold">SĐT</th>
                                <th class="px-4 py-3.5 font-bold">CCCD</th>
                                <th class="px-4 py-3.5 font-bold">Tạm trú</th>
                                <th class="px-4 py-3.5 font-bold text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                            @forelse($residents as $resident)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-100">{{ $resident->name }}</div>
                                        @if($resident->email)
                                            <div class="text-[11px] text-slate-500">{{ $resident->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($resident->room)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                <i class="fa-solid fa-door-open text-[10px]"></i>
                                                P. {{ $resident->room->room_number }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-500">Chưa xếp phòng</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-mono text-slate-300">
                                        {{ $resident->phone }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-mono text-slate-300">
                                        {{ $resident->cccd ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($resident->temporary_residence_status === 'registered')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Đã khai báo tạm trú
                                            </span>
                                        @elseif($resident->temporary_residence_status === 'absent')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Tạm vắng
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                Chưa khai báo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Xem chi tiết -->
                                            <button type="button" 
                                                onclick="viewResidentDetail({{ json_encode($resident) }})"
                                                class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-emerald-400 border border-slate-800 transition-all text-xs" 
                                                title="Xem chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>

                                            <!-- Xuất CT01 -->
                                            <a href="{{ route('smartroom.admin.resident.export_ct01', $resident->id) }}" 
                                                target="_blank"
                                                class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-cyan-400 border border-slate-800 transition-all text-xs" 
                                                title="Xuất phiếu CT01 Tạm trú">
                                                <i class="fa-solid fa-file-lines"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-600">
                                                <i class="fa-solid fa-user-slash text-xl"></i>
                                            </div>
                                            <p class="text-sm">Không tìm thấy cư dân nào phù hợp.</p>
                                            <button type="button" onclick="openAddResidentModal()" class="px-3.5 py-1.5 text-xs font-semibold bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 rounded-xl hover:bg-indigo-600/30 transition-all">
                                                + Thêm cư dân mới
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($residents->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $residents->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- 3. Modal Thêm Cư Dân Mới (Wireframe Hình 26.1) -->
    <div id="modalAddResident" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="w-full max-w-xl bg-[#0a0f1d] border border-slate-800 p-6 sm:p-8 rounded-3xl shadow-2xl relative animate-fade-in max-h-[90vh] overflow-y-auto">
            <!-- Nút đóng -->
            <button type="button" onclick="closeAddResidentModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Tiêu đề Modal -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-user-plus text-base"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-100">Thêm Cư Dân Mới Vào Phòng Trọ</h2>
                    <p class="text-xs text-slate-500">Nhập đầy đủ thông tin nhân khẩu để đăng ký tạm trú và bàn giao phòng</p>
                </div>
            </div>

            <!-- Form Thêm Cư Dân -->
            <form id="formAddResident" action="{{ route('admin.residents.store', [], false) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Họ và tên cư dân * -->
                <div>
                    <label for="full_name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Họ và tên cư dân <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" 
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" 
                        placeholder="Trần Văn C">
                    <p id="err_full_name" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span class="err-text"></span>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Số điện thoại * -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Số điện thoại <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" maxlength="10" 
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all font-mono" 
                            placeholder="09xxxxxxxx">
                        <p id="err_phone" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                            <span class="err-text"></span>
                        </p>
                    </div>

                    <!-- Số CCCD 12 số * -->
                    <div>
                        <label for="id_card" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Số CCCD 12 số <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="id_card" name="id_card" maxlength="12" 
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all font-mono" 
                            placeholder="07920100xxxx">
                        <p id="err_id_card" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                            <span class="err-text"></span>
                        </p>
                    </div>
                </div>

                <!-- Phòng lưu trú * -->
                <div>
                    <label for="room_id" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Phòng lưu trú bàn giao <span class="text-rose-500">*</span>
                    </label>
                    <select id="room_id" name="room_id" 
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <option value="">-- Chọn phòng bàn giao cho cư dân --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">
                                P. {{ $room->room_number }} ({{ $room->status_label }} - {{ $room->residents_count }} người)
                            </option>
                        @endforeach
                    </select>
                    <p id="err_room_id" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span class="err-text"></span>
                    </p>
                </div>

                <!-- Đã khai báo tạm trú (Checkbox) -->
                <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center justify-between">
                    <div>
                        <label for="temporary_residence" class="text-xs font-bold text-slate-300 block cursor-pointer">
                            Đã khai báo tạm trú
                        </label>
                        <p class="text-[11px] text-slate-500">Đánh dấu nếu đã nộp hồ sơ tạm trú với Công an khu vực</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="temporary_residence" name="temporary_residence" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- Nút thao tác -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/80">
                    <button type="button" onclick="closeAddResidentModal()" 
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-transparent hover:bg-slate-900 transition-all">
                        Hủy bỏ
                    </button>
                    <!-- Nút Lưu cư dân: ID btnSaveResident, Nền xanh đậm -->
                    <button type="submit" id="btnSaveResident" 
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-700 hover:bg-blue-600 active:scale-95 shadow-lg shadow-blue-700/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Lưu cư dân</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. Modal Xem Chi Tiết Cư Dân -->
    <div id="modalViewResident" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 p-6 rounded-3xl shadow-2xl relative animate-fade-in">
            <button type="button" onclick="closeViewResidentModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 class="text-base font-bold text-slate-100 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-indigo-400"></i>
                Hồ Sơ Chi Tiết Cư Dân
            </h3>
            <div id="viewResidentContent" class="space-y-3 text-xs text-slate-300">
                <!-- Sẽ nạp qua JS -->
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeViewResidentModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal Controls
        function openAddResidentModal() {
            clearErrors();
            document.getElementById('formAddResident').reset();
            document.getElementById('modalAddResident').classList.remove('hidden');
        }

        function closeAddResidentModal() {
            document.getElementById('modalAddResident').classList.add('hidden');
            clearErrors();
        }

        function closeViewResidentModal() {
            document.getElementById('modalViewResident').classList.add('hidden');
        }

        function viewResidentDetail(res) {
            const content = document.getElementById('viewResidentContent');
            content.innerHTML = `
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div><span class="text-slate-500">Họ và tên:</span> <p class="font-bold text-slate-100 text-sm mt-0.5">${res.name || '—'}</p></div>
                    <div><span class="text-slate-500">Phòng:</span> <p class="font-bold text-indigo-400 text-sm mt-0.5">P. ${res.room ? res.room.room_number : 'N/A'}</p></div>
                    <div><span class="text-slate-500">Số điện thoại:</span> <p class="font-mono text-slate-200 mt-0.5">${res.phone || '—'}</p></div>
                    <div><span class="text-slate-500">Số CCCD:</span> <p class="font-mono text-slate-200 mt-0.5">${res.cccd || '—'}</p></div>
                    <div><span class="text-slate-500">Trạng thái tạm trú:</span> <p class="font-semibold text-emerald-400 mt-0.5">${res.temporary_residence_status === 'registered' ? 'Đã đăng ký' : (res.temporary_residence_status === 'absent' ? 'Tạm vắng' : 'Chưa đăng ký')}</p></div>
                    <div><span class="text-slate-500">Ngày vào ở:</span> <p class="text-slate-200 mt-0.5">${res.start_date || '—'}</p></div>
                </div>
            `;
            document.getElementById('modalViewResident').classList.remove('hidden');
        }

        // Error message helpers
        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errEl = document.getElementById('err_' + fieldId);
            if (input) {
                input.classList.add('is-invalid');
                input.classList.add('border-red-500');
            }
            if (errEl) {
                const textSpan = errEl.querySelector('.err-text');
                if (textSpan) textSpan.innerText = message;
                errEl.classList.remove('hidden');
            }
        }

        function clearErrors() {
            ['full_name', 'phone', 'id_card', 'room_id'].forEach(id => {
                const input = document.getElementById(id);
                const errEl = document.getElementById('err_' + id);
                if (input) {
                    input.classList.remove('is-invalid');
                    input.classList.remove('border-red-500');
                }
                if (errEl) {
                    errEl.classList.add('hidden');
                }
            });
        }

        function showToast(message, isSuccess = true, code = '') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `p-4 rounded-2xl border text-sm font-semibold flex items-center justify-between gap-4 shadow-2xl transition-all duration-300 pointer-events-auto ${
                isSuccess 
                ? 'bg-emerald-950/95 border-emerald-500 text-emerald-300 shadow-emerald-900/30' 
                : 'bg-rose-950/95 border-rose-500 text-rose-300 shadow-rose-900/30'
            }`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fa-solid ${isSuccess ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400'} text-lg"></i>
                    <span>${message}</span>
                </div>
                ${code ? `<span class="text-[10px] font-mono px-2 py-0.5 rounded ${isSuccess ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'}">${code}</span>` : ''}
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Form Submit Handler with Validation matching specification
        document.getElementById('formAddResident').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const fullName = document.getElementById('full_name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const idCard = document.getElementById('id_card').value.trim();
            const roomId = document.getElementById('room_id').value;
            const tempRes = document.getElementById('temporary_residence').checked;

            let hasError = false;

            // 1. Kiểm tra Họ và tên (ERR_26_01)
            if (!fullName) {
                showError('full_name', 'Họ và tên cư dân là bắt buộc.');
                hasError = true;
            } else if (fullName.length < 2 || fullName.length > 100) {
                showError('full_name', 'Họ và tên cư dân phải từ 2 đến 100 ký tự.');
                hasError = true;
            }

            // 2. Kiểm tra Số CCCD (ERR_26_02, Regex ^[0-9]{12}$)
            if (!idCard) {
                showError('id_card', 'Số Căn cước công dân là bắt buộc để đăng ký cư trú.');
                hasError = true;
            } else if (!/^[0-9]{12}$/.test(idCard)) {
                showError('id_card', 'Số CCCD phải gồm đúng 12 chữ số');
                hasError = true;
            }

            // 3. Kiểm tra Phòng lưu trú (ERR_26_04)
            if (!roomId) {
                showError('room_id', 'Vui lòng chọn phòng lưu trú bàn giao cho cư dân ở.');
                hasError = true;
            }

            // 4. Kiểm tra SĐT (10 số)
            if (!phone) {
                showError('phone', 'Số điện thoại là bắt buộc.');
                hasError = true;
            } else if (!/^[0-9]{10}$/.test(phone)) {
                showError('phone', 'Số điện thoại phải gồm đúng 10 chữ số.');
                hasError = true;
            }

            if (hasError) return;

            // Submit qua Fetch API
            const btn = document.getElementById('btnSaveResident');
            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang lưu...</span>`;

            try {
                const response = await fetch("{{ route('admin.residents.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        full_name: fullName,
                        phone: phone,
                        id_card: idCard,
                        room_id: roomId,
                        temporary_residence: tempRes ? 1 : 0
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    // Xử lý lỗi trả về từ máy chủ
                    if (data.errors) {
                        if (data.errors.full_name) showError('full_name', data.errors.full_name[0]);
                        if (data.errors.id_card) showError('id_card', data.errors.id_card[0]);
                        if (data.errors.room_id) showError('room_id', data.errors.room_id[0]);
                        if (data.errors.phone) showError('phone', data.errors.phone[0]);
                    }
                    showToast(data.message || 'Không thể lưu hồ sơ cư dân.', false, data.code || 'ERR');
                } else {
                    // Thành công: ERR_26_05
                    closeAddResidentModal();
                    showToast(data.message || 'Đã lưu hồ sơ cư dân thành công! Phòng đã tự động chuyển sang trạng thái Đã thuê.', true, data.code || 'ERR_26_05');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } catch (err) {
                showToast('Đã xảy ra lỗi kết nối mạng. Vui lòng thử lại!', false, 'NETWORK_ERROR');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
        });
    </script>
</body>
</html>
