<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản lý danh sách phòng trọ - SmartRoom.">
    <title>Quản Lý Phòng Trọ - SmartRoom</title>
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
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    
    <!-- Custom CSS Styles -->
    <style>
        .glass-card {
            background: rgba(13, 18, 31, 0.45);
            backdrop-filter: blur(16px);
            border: 1px border-slate-800/80;
        }
        /* Custom scrollbar */
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
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @php
        $isLandlord = Auth::user()?->isLandlord();
    @endphp

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-100">Quản Lý Danh Sách Phòng Trọ</h2>
            </div>
            
            <div class="flex items-center gap-4">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-sm font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    <span>{{ date('d/m/Y') }}</span>
                </div>
            </div>
        </header>

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto">

            <!-- Thông báo thành công / lỗi -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Bảng danh sách phòng trọ -->
            <div class="glass-card rounded-2xl p-6 border border-slate-800/40 relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-200">Quản Lý Phòng</h3>
                        <p class="text-xs text-slate-500 mt-1">Danh sách phòng hiện có trong hệ thống của bạn.</p>
                    </div>
                    <!-- UI Element STT 4: btnAdd - Thêm phòng mới nền xanh Emerald lá cây -->
                    <a href="{{ route('admin.rooms.create') }}" 
                       id="btnAdd" 
                       name="btnAdd" 
                       class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-600/25 hover:-translate-y-0.5">
                        <i class="fa-solid fa-plus-circle text-sm"></i> + Thêm phòng mới
                    </a>
                </div>

                <!-- Cảnh báo lỗi khoảng giá lọc DoD 1 -->
                <div id="price-range-error-banner" class="{{ !empty($rangePriceError) ? '' : 'hidden' }} mb-4 p-3.5 rounded-xl bg-rose-950/60 border border-rose-500 text-rose-300 text-xs font-bold flex items-center gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                    <span id="price-range-error-text">Khoảng giá lọc không hợp lệ: Giá tối thiểu phải nhỏ hơn hoặc bằng giá tối đa</span>
                </div>

                <form id="filter-rooms-form" method="GET" action="{{ route('admin.rooms.index', [], false) }}" onsubmit="return validateFilterPriceRange(event)" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-8 gap-3 mb-6">
                    <!-- UI Element STT 1: txtSearch - Tìm kiếm phòng -->
                    <div class="xl:col-span-2">
                        <input type="search" 
                               id="txtSearch" 
                               name="txtSearch" 
                               value="{{ $filters['txtSearch'] ?? $filters['room_number'] }}" 
                               placeholder="Nhập tên phòng, số phòng..." 
                               class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                               title="Tìm kiếm phòng">
                    </div>

                    <!-- UI Element STT 2: cboBuilding - Chọn tòa nhà -->
                    <div>
                        <select id="cboBuilding" 
                                name="cboBuilding" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                title="Chọn tòa nhà">
                            <option value="">Tất cả tòa nhà</option>
                            @foreach($buildings ?? [] as $b)
                                <option value="{{ $b->id }}" @selected(($filters['cboBuilding'] ?? $filters['building_id']) == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="">Tất cả trạng thái</option>
                            <option value="empty" @selected($filters['status'] === 'empty')>Trống</option>
                            <option value="occupied" @selected($filters['status'] === 'occupied')>Đang thuê</option>
                            <option value="overdue" @selected($filters['status'] === 'overdue')>Nợ tiền</option>
                            <option value="maintenance" @selected($filters['status'] === 'maintenance')>Bảo trì</option>
                        </select>
                    </div>

                    <div>
                        <input type="number" name="floor" value="{{ $filters['floor'] }}" min="1" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500" placeholder="Tầng">
                    </div>

                    <div>
                        <input type="number" 
                               id="min_price" 
                               name="min_price" 
                               value="{{ $filters['min_price'] }}" 
                               min="0" 
                               placeholder="Giá từ (VNĐ)" 
                               class="w-full px-3 py-2 rounded-xl bg-slate-900 border {{ !empty($rangePriceError) ? 'border-rose-500 ring-2 ring-rose-500' : 'border-slate-800' }} text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <input type="number" 
                               id="max_price" 
                               name="max_price" 
                               value="{{ $filters['max_price'] }}" 
                               min="0" 
                               placeholder="Giá đến (VNĐ)" 
                               class="w-full px-3 py-2 rounded-xl bg-slate-900 border {{ !empty($rangePriceError) ? 'border-rose-500 ring-2 ring-rose-500' : 'border-slate-800' }} text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md">
                            <i class="fa-solid fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.rooms.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold transition-all" title="Đặt lại bộ lọc">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>

                <!-- UI Element STT 3: tblRooms - Danh sách phòng -->
                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table id="tblRooms" name="tblRooms" data-field-name="tblRooms" class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                            <tr>
                                <th class="px-6 py-4 font-bold">Hình Ảnh</th>
                                <th class="px-6 py-4 font-bold">Số Phòng</th>
                                <th class="px-6 py-4 font-bold">Hạng Phòng</th>
                                <th class="px-6 py-4 font-bold">Tòa Nhà</th>
                                <th class="px-6 py-4 font-bold">Tầng</th>
                                <th class="px-6 py-4 font-bold">Diện Tích</th>
                                <th class="px-6 py-4 font-bold">Giá Thuê & Cọc</th>
                                <th class="px-6 py-4 font-bold text-center">Trạng Thái</th>
                                <th class="px-6 py-4 font-bold text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                            @forelse($rooms as $room)
                            <tr class="hover:bg-slate-900/40 transition-all">
                                <td class="px-6 py-4">
                                    <div class="w-14 h-10 rounded-lg overflow-hidden border border-slate-800 bg-slate-900 flex items-center justify-center shadow-inner">
                                        <img src="{{ $room->thumbnail_url }}" alt="P. {{ $room->room_number }}" class="object-cover w-full h-full" 
                                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';">
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-200">P. {{ $room->room_number }}</td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-300">
                                    @php
                                        $type = $room->room_type;
                                        if ($type === 'normal') $type = 'standard';
                                    @endphp
                                    @if($type === 'vip')
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 font-bold uppercase text-[9px]">VIP</span>
                                    @elseif($type === 'deluxe')
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-bold uppercase text-[9px]">Deluxe</span>
                                    @elseif($type === 'studio')
                                        <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20 font-bold uppercase text-[9px]">Studio</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-500/10 text-slate-400 border border-slate-500/20 font-bold uppercase text-[9px]">Standard</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="font-semibold text-indigo-400">{{ $room->building->name ?? 'N/A' }}</div>
                                    @if(!empty($room->building?->address))
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($room->building->address) }}" 
                                           target="_blank" 
                                           title="Xem trên Google Maps: {{ $room->building->address }}" 
                                           class="inline-flex items-center gap-1 text-[11px] text-slate-400 hover:text-blue-400 transition mt-0.5 max-w-[220px] truncate">
                                            <i class="fa-solid fa-location-dot text-rose-400 text-[10px] shrink-0"></i>
                                            <span class="truncate">{{ $room->building->address }}</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">Tầng {{ $room->floor }}</td>
                                <td class="px-6 py-4 text-xs text-slate-400">{{ $room->area }} m²</td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="font-bold text-emerald-400">
                                        {{ number_format($room->price) }}đ
                                        <span class="text-[10px] text-slate-400 font-normal">
                                            @if(($room->rental_type ?? 'month') === 'day')
                                                / ngày
                                            @elseif(($room->rental_type ?? 'month') === 'hour')
                                                / giờ
                                            @else
                                                / tháng
                                            @endif
                                        </span>
                                    </div>
                                    @if($room->deposit > 0)
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Cọc: {{ number_format($room->deposit) }}đ
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($room->status === 'empty')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Trống</span>
                                    @elseif($room->status === 'occupied')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Đầy (Đang thuê)</span>
                                    @elseif($room->status === 'maintenance')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Đang sửa chữa</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Nợ tiền</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.rooms.edit', $room->id) }}" class="px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-xl text-xs font-bold border border-indigo-500/20 transition-all flex items-center gap-1">
                                            <i class="fa-regular fa-pen-to-square"></i> Sửa
                                        </a>

                                        @if($isLandlord)
                                            <!-- Chống xóa bằng GET, bắt buộc POST với CSRF và check xóa trùng -->
                                            <form action="{{ route('admin.rooms.destroy', $room->id, false) }}" method="POST" class="delete-form inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirmDelete(event, '{{ $room->room_number }}')" class="delete-btn px-3 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/25 transition-all">
                                                    <i class="fa-regular fa-trash-can"></i> Xóa
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-xs text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <i class="fa-solid fa-door-closed text-2xl text-slate-700"></i>
                                        <span>Chưa có phòng trọ nào được thêm. Hãy thêm phòng mới!</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="mt-6">
                    {{ $rooms->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- JS CHỐNG PHÁ HOẠI QUA DEVTOOLS & SPAM CLICK -->
    <script>
        // Kiểm tra logic khoảng giá lọc (DoD 1)
        function validateFilterPriceRange(e) {
            const minInput = document.getElementById('min_price');
            const maxInput = document.getElementById('max_price');
            const banner = document.getElementById('price-range-error-banner');
            const text = document.getElementById('price-range-error-text');

            const minVal = minInput && minInput.value !== '' ? parseFloat(minInput.value) : null;
            const maxVal = maxInput && maxInput.value !== '' ? parseFloat(maxInput.value) : null;

            // Kịch bản DoD 1: Giá tối thiểu lớn hơn giá tối đa
            if (minVal !== null && maxVal !== null && minVal > maxVal) {
                if (e) e.preventDefault();
                if (banner) {
                    banner.classList.remove('hidden');
                    if (text) text.textContent = 'Khoảng giá lọc không hợp lệ: Giá tối thiểu phải nhỏ hơn hoặc bằng giá tối đa';
                }
                if (minInput) minInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                if (maxInput) maxInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }

            if (minInput) minInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            if (maxInput) maxInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            if (banner) banner.classList.add('hidden');
            return true;
        }

        // 1. Chống spam click nút xóa
        function confirmDelete(e, roomNumber) {
            e.preventDefault();
            if (confirm(`Bạn có chắc chắn muốn xóa phòng ${roomNumber} không? Hành động này không thể hoàn tác!`)) {
                const form = e.target.closest('form');
                const btn = form.querySelector('.delete-btn');
                
                // Tránh nhấn đúp
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xóa...';
                form.submit();
            }
            return false;
        }

        // 2. Chặn các phím tắt mở DevTools
        document.addEventListener('keydown', function (e) {
            // Chặn F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            // Chặn Ctrl+Shift+I, J, C
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            // Chặn Ctrl+U (Xem source code)
            if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
        });

        // 3. Chặn chuột phải trên toàn trang
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            alert('Chuột phải đã bị vô hiệu hóa để bảo mật!');
            return false;
        });

        // 4. MutationObserver để chống xóa thuộc tính trái phép
        const forms = document.querySelectorAll('.delete-form');
        forms.forEach(form => {
            const observer = new MutationObserver((mutationsList) => {
                for (let mutation of mutationsList) {
                    if (mutation.type === 'attributes') {
                        alert('Phát hiện hành vi can thiệp DOM hệ thống!');
                        window.location.reload();
                    }
                }
            });
            observer.observe(form, { attributes: true, childList: true, subtree: true });
        });
    </script>
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
