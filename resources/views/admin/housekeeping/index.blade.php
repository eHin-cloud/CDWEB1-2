<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Buồng Phòng - SmartRoom Housekeeping</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen pb-12 font-sans antialiased">
    <!-- Mobile App-like Header -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-30 shadow-lg px-4 py-3">
        <div class="max-w-xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-lg border border-orange-500/30">
                    <i class="fa-solid fa-broom"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">Nhiệm Vụ Buồng Phòng</h1>
                    <p class="text-xs text-slate-400">{{ Auth::user()->name }} ({{ Auth::user()->roleName() }})</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('signout') }}" class="text-xs px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700 transition">
                    <i class="fa-solid fa-right-from-bracket mr-1"></i> Thoát
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-xl mx-auto px-4 mt-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 rounded-xl text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-slate-900/90 border border-orange-500/30 p-4 rounded-2xl">
                <div class="text-xs text-orange-400 font-medium">CẦN DỌN DẸP</div>
                <div class="text-3xl font-black text-white mt-1">{{ $rooms->count() }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Phòng khách vừa trả</div>
            </div>
            <div class="bg-slate-900/90 border border-emerald-500/30 p-4 rounded-2xl">
                <div class="text-xs text-emerald-400 font-medium">PHÒNG ĐÃ SẠCH</div>
                <div class="text-3xl font-black text-white mt-1">{{ $cleanRoomsCount }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Sẵn sàng đón khách</div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Danh Sách Phòng Chờ Vệ Sinh</h2>
            <button onclick="window.location.reload()" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                <i class="fa-solid fa-rotate-right"></i> Làm mới
            </button>
        </div>

        @if($rooms->isEmpty())
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-8 text-center mt-4">
                <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h3 class="text-base font-bold text-white">Tất Cả Phòng Đều Đã Sạch!</h3>
                <p class="text-xs text-slate-400 mt-1">Không có phòng nào cần dọn dẹp tại thời điểm này.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($rooms as $room)
                <div class="bg-slate-900 border {{ $room->cleaning_status === 'cleaning' ? 'border-orange-500/40 bg-orange-500/5' : 'border-slate-800' }} rounded-2xl p-4 shadow-sm transition hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-black font-mono text-white tracking-wide">P.{{ $room->room_number }}</span>
                                <span class="text-xs px-2 py-0.5 bg-slate-800 text-slate-300 rounded-md border border-slate-700">
                                    Tầng {{ $room->floor }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                <i class="fa-solid fa-building text-[10px] mr-1"></i> {{ $room->building->name ?? 'Tòa nhà' }}
                            </p>
                        </div>
                        <div>
                            @if($room->cleaning_status === 'cleaning')
                                <span class="px-2.5 py-1 bg-orange-500/20 text-orange-300 border border-orange-500/30 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-orange-400"></span> Đang dọn dẹp
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span> Phòng bẩn (Chờ dọn)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-800/80">
                        @if($room->cleaning_status !== 'cleaning')
                        <form action="{{ route('admin.housekeeping.update', $room->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="cleaning_status" value="cleaning">
                            <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-person-digging"></i> Bắt đầu dọn
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.housekeeping.update', $room->id) }}" method="POST" class="{{ $room->cleaning_status === 'cleaning' ? 'col-span-2' : '' }}">
                            @csrf
                            <input type="hidden" name="cleaning_status" value="clean">
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-check-double"></i> Đã dọn xong (Sạch)
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
