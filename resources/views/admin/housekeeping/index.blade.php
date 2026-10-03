<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nhiệm Vụ Buồng Phòng - SmartRoom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen pb-12 font-sans antialiased">
    <!-- Header chuẩn xác theo wireframe -->
    <header class="bg-[#0b0e14] border-b border-zinc-800/80 sticky top-0 z-30 px-4 py-3.5 shadow-md">
        <div class="max-w-2xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-800/90 border border-zinc-700/80 flex items-center justify-center text-white text-base shadow-sm">
                    <i class="fa-solid fa-paintbrush text-zinc-200"></i>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-black text-white leading-tight">Nhiệm Vụ Buồng Phòng</h1>
                    <p class="text-xs text-zinc-400">
                        {{ Auth::user()->name }} ({{ Auth::user()->building->name ?? (Auth::user()->tenant->name ?? 'SmartRoom Cầu Giấy') }}) ({{ Auth::user()->roleName() }})
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('signout') }}" class="px-3.5 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold border border-zinc-700/90 inline-flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Thoát
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 mt-6">
        <!-- Toast thông báo -->
        <div id="housekeeping-toast" class="{{ session('success') ? '' : 'hidden' }} mb-5 p-4 bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 rounded-2xl text-xs sm:text-sm flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                <span id="housekeeping-toast-msg">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('housekeeping-toast').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Quick Summary Cards chuẩn ảnh -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
            <div class="bg-[#0f1218] border border-zinc-800/90 p-4 sm:p-5 rounded-2xl shadow-sm">
                <div class="text-[11px] sm:text-xs text-zinc-300 font-bold uppercase tracking-wider">CẦN DỌN DẸP</div>
                <div class="text-3xl sm:text-4xl font-black text-white mt-1 tracking-tight" id="stat-dirty-rooms">{{ $rooms->count() }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Phòng khách vừa trả</div>
            </div>
            <div class="bg-[#0f1218] border border-zinc-800/90 p-4 sm:p-5 rounded-2xl shadow-sm">
                <div class="text-[11px] sm:text-xs text-zinc-300 font-bold uppercase tracking-wider">PHÒNG ĐÃ SẠCH</div>
                <div class="text-3xl sm:text-4xl font-black text-white mt-1 tracking-tight" id="stat-clean-rooms">{{ $cleanRoomsCount }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Sẵn sàng đón khách</div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider">DANH SÁCH PHÒNG CHỜ VỆ SINH</h2>
            <button onclick="window.location.reload()" class="text-xs text-zinc-300 hover:text-white inline-flex items-center gap-1.5 transition">
                <i class="fa-solid fa-rotate-right"></i> Làm mới
            </button>
        </div>

        @if($rooms->isEmpty())
            <div class="bg-[#0d1016] border border-zinc-800/90 rounded-2xl py-14 px-6 text-center mt-2 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-zinc-800/60 mx-auto mb-4 flex items-center justify-center">
                    <span class="w-3 h-3 rounded-full bg-zinc-700"></span>
                </div>
                <h3 class="text-base sm:text-lg font-black text-white">Tất Cả Phòng Đều Đã Sạch!</h3>
                <p class="text-xs text-zinc-400 mt-1.5">Không có phòng nào cần dọn dẹp tại thời điểm này.</p>
            </div>
        @else
            <div class="space-y-3" id="housekeeping-room-list">
                @foreach($rooms as $room)
                <div id="room-card-{{ $room->id }}" class="room-card bg-[#0f1218] border {{ $room->cleaning_status === 'cleaning' ? 'border-orange-500/40 bg-orange-500/5' : 'border-zinc-800' }} rounded-2xl p-4 shadow-sm transition hover:border-zinc-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-black font-mono text-white tracking-wide">P.{{ $room->room_number }}</span>
                                <span class="text-xs px-2 py-0.5 bg-zinc-800 text-zinc-300 rounded-md border border-zinc-700">
                                    Tầng {{ $room->floor }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-400 mt-1">
                                <i class="fa-solid fa-building text-[10px] mr-1"></i> {{ $room->building->name ?? 'Tòa nhà' }}
                            </p>
                        </div>
                        <div id="status-badge-{{ $room->id }}">
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
                    <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-zinc-800/80" id="actions-{{ $room->id }}">
                        @if($room->cleaning_status !== 'cleaning')
                        <button type="button" onclick="updateCleaning({{ $room->id }}, 'cleaning')" class="w-full py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-bold rounded-xl border border-zinc-700 transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-person-digging"></i> Bắt đầu dọn
                        </button>
                        @endif

                        <button type="button" onclick="updateCleaning({{ $room->id }}, 'clean')" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5 {{ $room->cleaning_status === 'cleaning' ? 'col-span-2' : '' }}">
                            <i class="fa-solid fa-check-double"></i> Đã dọn xong (Sạch)
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>

    <script>
        function showHousekeepingToast(msg) {
            const toast = document.getElementById('housekeeping-toast');
            const msgEl = document.getElementById('housekeeping-toast-msg');
            if (toast && msgEl) {
                msgEl.textContent = msg;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 5000);
            }
        }

        function updateCleaning(roomId, status) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = `{{ url('/smartroom/housekeeping/update') }}/${roomId}`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ cleaning_status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showHousekeepingToast(data.message);

                    const card = document.getElementById(`room-card-${roomId}`);
                    const badge = document.getElementById(`status-badge-${roomId}`);
                    const actions = document.getElementById(`actions-${roomId}`);

                    if (status === 'clean') {
                        // Hiệu ứng đổi màu thẻ phòng sang Xanh theo ERR_28_04
                        if (card) {
                            card.classList.remove('border-zinc-800', 'border-orange-500/40', 'bg-orange-500/5', 'bg-[#0f1218]');
                            card.classList.add('border-emerald-500/40', 'bg-emerald-500/10');
                        }
                        if (badge) {
                            badge.innerHTML = `<span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-semibold inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Đã sạch (Clean)</span>`;
                        }
                        if (actions) {
                            actions.innerHTML = `<div class="col-span-2 text-center text-xs font-bold text-emerald-400 py-1"><i class="fa-solid fa-circle-check mr-1"></i> Phòng đã sạch, sẵn sàng đón khách</div>`;
                        }

                        // Cập nhật bộ đếm
                        const dirtyEl = document.getElementById('stat-dirty-rooms');
                        const cleanEl = document.getElementById('stat-clean-rooms');
                        if (dirtyEl) {
                            const cur = parseInt(dirtyEl.textContent) || 0;
                            dirtyEl.textContent = Math.max(0, cur - 1);
                        }
                        if (cleanEl) {
                            const cur = parseInt(cleanEl.textContent) || 0;
                            cleanEl.textContent = cur + 1;
                        }
                    } else if (status === 'cleaning') {
                        if (card) {
                            card.classList.add('border-orange-500/40', 'bg-orange-500/5');
                        }
                        if (badge) {
                            badge.innerHTML = `<span class="px-2.5 py-1 bg-orange-500/20 text-orange-300 border border-orange-500/30 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 animate-pulse"><span class="w-2 h-2 rounded-full bg-orange-400"></span> Đang dọn dẹp</span>`;
                        }
                        if (actions) {
                            actions.innerHTML = `
                                <button type="button" onclick="updateCleaning(${roomId}, 'clean')" class="col-span-2 w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check-double"></i> Đã dọn xong (Sạch)
                                </button>
                            `;
                        }
                    }
                } else {
                    alert(data.message || 'Không thể cập nhật trạng thái phòng.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Đã xảy ra lỗi kết nối tới máy chủ.');
            });
        }
    </script>
</body>
</html>
