<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản lý Cơ sở Lưu trú / Tòa nhà - SmartRoom.">
    <title>Quản Lý Cơ Sở Lưu Trú - SmartRoom</title>
    
    <!-- Google Fonts -->
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
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    
    <style>
        .glass-card {
            background: rgba(13, 18, 31, 0.55);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 41, 59, 0.7);
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #080b11;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/10 blur-[120px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-sm">
                    <i class="fa-solid fa-city text-base"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-100">Quản Lý Cơ Sở Lưu Trú</h2>
                    <p class="text-xs text-slate-400">Danh mục tòa nhà & chuỗi bất động sản của bạn</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button p-2.5 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <a href="{{ route('admin.buildings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/25 transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Thêm Cơ Sở Mới</span>
                </a>
            </div>
        </header>

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto">

            <!-- Flash alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm font-semibold flex items-center gap-3 shadow-lg shadow-emerald-500/5">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm font-semibold flex items-center gap-3 shadow-lg shadow-rose-500/5">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- STATS CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-indigo-500/40 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tổng số cơ sở</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <i class="fa-solid fa-building text-base"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white">{{ $totalBuildings }}</div>
                    <p class="text-xs text-slate-400 mt-1">Cơ sở / Tòa nhà đang quản lý</p>
                </div>

                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/40 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tổng số phòng</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <i class="fa-solid fa-door-open text-base"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white">{{ $totalRooms }}</div>
                    <p class="text-xs text-slate-400 mt-1">Phòng trên toàn hệ thống cơ sở</p>
                </div>

                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-sky-500/40 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tỷ lệ lấp đầy</span>
                        <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                            <i class="fa-solid fa-chart-pie text-base"></i>
                        </div>
                    </div>
                    @php
                        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
                    @endphp
                    <div class="text-3xl font-extrabold text-white">{{ $occupancyRate }}%</div>
                    <p class="text-xs text-slate-400 mt-1">{{ $occupiedRooms }} / {{ $totalRooms }} phòng đang có khách thuê</p>
                </div>
            </div>

            <!-- FILTER & SEARCH BAR -->
            <div class="glass-card rounded-2xl p-5 mb-8">
                <form action="{{ route('admin.buildings.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm theo tên cơ sở, địa chỉ hoặc số hotline..." class="w-full pl-11 pr-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    </div>

                    <div class="flex items-center gap-3">
                        <select name="status" class="px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="maintenance" {{ $status === 'maintenance' ? 'selected' : '' }}>Đang bảo trì</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Tạm ngưng</option>
                        </select>

                        <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl border border-slate-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-filter text-xs"></i>
                            <span>Lọc</span>
                        </button>
                        @if($search || $status)
                            <a href="{{ route('admin.buildings.index') }}" class="px-3 py-2.5 text-slate-400 hover:text-slate-200 text-sm font-medium transition">
                                Đặt lại
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- BUILDINGS LIST GRID -->
            @if($buildings->isEmpty())
                <div class="glass-card rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1">Chưa tìm thấy cơ sở lưu trú nào</h3>
                    <p class="text-sm text-slate-400 max-w-md mx-auto mb-6">Bạn chưa có cơ sở lưu trú nào hoặc không có kết quả phù hợp với bộ lọc tìm kiếm.</p>
                    <a href="{{ route('admin.buildings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/25 transition">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Thêm Cơ Sở Đầu Tiên</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($buildings as $b)
                        <div class="glass-card rounded-2xl overflow-hidden flex flex-col hover:border-indigo-500/40 transition group">
                            <!-- Image banner -->
                            <div class="h-44 w-full bg-slate-900/80 relative overflow-hidden border-b border-slate-800/80">
                                @if($b->image)
                                    <img src="{{ str_starts_with($b->image, 'http') ? $b->image : asset('storage/' . $b->image) }}" alt="{{ $b->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-950/60 to-slate-900 flex items-center justify-center text-slate-700 group-hover:scale-105 transition duration-300">
                                        <i class="fa-solid fa-hotel text-5xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    @if($b->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 backdrop-blur-md">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Hoạt động
                                        </span>
                                    @elseif($b->status === 'maintenance')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 backdrop-blur-md">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Bảo trì
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-300 border border-slate-500/30 backdrop-blur-md">
                                            Tạm ngưng
                                        </span>
                                    @endif
                                </div>

                                <!-- Floors Pill -->
                                <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10 text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                                    <i class="fa-solid fa-stairs text-indigo-400 text-xs"></i>
                                    <span>{{ $b->total_floors ?? 1 }} Tầng</span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-white mb-2 line-clamp-1 group-hover:text-indigo-400 transition" title="{{ $b->name }}">
                                        {{ $b->name }}
                                    </h3>

                                    <div class="space-y-1.5 text-xs text-slate-400 mb-4">
                                        <div class="flex items-start gap-2">
                                            <i class="fa-solid fa-location-dot text-indigo-400 mt-0.5 shrink-0"></i>
                                            <span class="line-clamp-2">{{ $b->address }}</span>
                                        </div>
                                        @if($b->phone)
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-phone text-emerald-400 shrink-0"></i>
                                                <span>{{ $b->phone }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Quick amenities pills -->
                                    @if(!empty($b->amenities) && is_array($b->amenities))
                                        <div class="flex flex-wrap gap-1.5 mb-4">
                                            @foreach(array_slice($b->amenities, 0, 4) as $am)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-800/80 border border-slate-700/60 text-[11px] text-slate-300">
                                                    {{ $am }}
                                                </span>
                                            @endforeach
                                            @if(count($b->amenities) > 4)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-800/80 text-[11px] text-slate-400">
                                                    +{{ count($b->amenities) - 4 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <!-- Room counts breakdown -->
                                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center justify-between text-xs mb-4">
                                        <div>
                                            <span class="text-slate-400 block text-[11px]">Tổng phòng</span>
                                            <span class="font-bold text-white text-sm">{{ $b->rooms_count ?? 0 }}</span>
                                        </div>
                                        <div class="border-l border-slate-800 pl-3">
                                            <span class="text-slate-400 block text-[11px]">Đang thuê</span>
                                            <span class="font-bold text-emerald-400 text-sm">{{ $b->occupied_rooms_count ?? 0 }}</span>
                                        </div>
                                        <div class="border-l border-slate-800 pl-3">
                                            <span class="text-slate-400 block text-[11px]">Còn trống</span>
                                            <span class="font-bold text-sky-400 text-sm">{{ $b->empty_rooms_count ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 pt-2 border-t border-slate-800/80">
                                        <a href="{{ route('admin.rooms.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-400 text-xs font-semibold border border-indigo-500/20 transition flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-door-open text-xs"></i>
                                            <span>Xem phòng</span>
                                        </a>
                                        <a href="{{ route('admin.buildings.edit', $b->id) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition" title="Chỉnh sửa thông tin">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.buildings.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cơ sở này không? Lưu ý: Cơ sở chỉ có thể xóa khi không còn phòng trọ trực thuộc.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 text-xs font-semibold border border-rose-500/20 transition" title="Xóa cơ sở">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION -->
                <div class="mt-8">
                    {{ $buildings->links() }}
                </div>
            @endif

        </main>
    </div>
</body>
</html>
