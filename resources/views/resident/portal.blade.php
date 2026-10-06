<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartRoom - Trang Cư Dân</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        body { font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .panel { background: rgba(15, 23, 42, .72); border: 1px solid rgba(51, 65, 85, .8); }
    </style>
</head>
<body class="min-h-screen bg-[#080b11] text-slate-100">
    <div class="min-h-screen">
        <header class="sticky top-0 z-20 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                        <i class="fa-solid fa-hotel"></i>
                    </span>
                    <span class="font-black tracking-tight">SmartRoom Resident</span>
                </a>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                        <i class="fa-solid fa-moon" data-theme-icon></i>
                    </button>
                    <span class="hidden sm:inline text-xs text-slate-400">{{ Auth::user()->name ?? 'Resident' }}</span>
                    <a href="{{ route('signout') }}" class="px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">
            @if(session('success'))
                <div id="resident-toast-alert" class="panel rounded-2xl p-4 text-sm text-emerald-200 bg-emerald-950/60 border border-emerald-500/40 flex items-center justify-between shadow-xl shadow-emerald-950/30">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-check text-base"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">ERR_28_03</span>
                            <div class="font-bold text-white text-xs sm:text-sm mt-0.5">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('resident-toast-alert')?.remove()" class="text-slate-400 hover:text-white px-2 py-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="panel rounded-xl p-4 text-sm text-rose-300 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="panel rounded-xl p-4 text-sm text-rose-300">
                    <div class="font-bold mb-1">Dữ liệu chưa hợp lệ</div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(!$room)
                <section class="panel rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-user-lock text-4xl text-amber-300 mb-4"></i>
                    <h1 class="text-xl font-black">Tài khoản chưa được gán phòng</h1>
                    <p class="text-sm text-slate-400 mt-2">Vui lòng liên hệ ban quản lý để kích hoạt hồ sơ cư dân.</p>
                </section>
            @else
                <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                            <span>Cư dân</span>
                            <i class="fa-solid fa-user text-slate-400"></i>
                        </div>
                        <div class="mt-2 text-xl font-black truncate" title="{{ $resident->name }}">{{ $resident->name }}</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $resident->phone }}</div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                            <span>Phòng</span>
                            <i class="fa-solid fa-door-open text-indigo-400"></i>
                        </div>
                        <div class="mt-2 text-xl font-black text-indigo-300">P. {{ $room->room_number }}</div>
                        <div class="mt-1 text-xs text-slate-400 truncate" title="{{ $room->building->name ?? 'Chưa có tòa nhà' }}">{{ $room->building->name ?? 'Chưa có tòa nhà' }}</div>
                    </div>
                    <div class="panel rounded-2xl p-5 border-l-4 border-l-emerald-500">
                        <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                            <span>Chủ trọ / QL</span>
                            <i class="fa-solid fa-user-tie text-emerald-400"></i>
                        </div>
                        <div class="mt-2 text-xl font-black text-emerald-300 truncate" title="{{ $landlordName }}">{{ $landlordName }}</div>
                        <div class="mt-1 text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-[10px] text-emerald-400"></i>
                            <a href="tel:{{ $landlordPhone }}" class="hover:underline text-slate-200 font-semibold">{{ $landlordPhone }}</a>
                        </div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                            <span>Nợ cần thanh toán</span>
                            <i class="fa-solid fa-receipt text-amber-400"></i>
                        </div>
                        <div class="mt-2 text-xl font-black text-amber-300">{{ number_format($unpaidTotal) }} VND</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $bills->where('status', '!=', 'paid')->count() }} hóa đơn</div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                            <span>Sự cố đang mở</span>
                            <i class="fa-solid fa-screwdriver-wrench text-cyan-400"></i>
                        </div>
                        <div class="mt-2 text-xl font-black text-cyan-300">{{ ($maintenanceTickets ?? $tickets->where('category', '!=', 'housekeeping'))->where('status', '!=', 'resolved')->count() }}</div>
                        <div class="mt-1 text-xs text-slate-400">Bảo trì / sửa chữa</div>
                    </div>
                </section>

                <section class="panel rounded-2xl p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="switchResidentTab('bills')" class="resident-tab px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold">Hóa đơn</button>
                        <button type="button" onclick="switchResidentTab('contract')" class="resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800">Hợp đồng</button>
                        <button type="button" onclick="switchResidentTab('tickets')" class="resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800">
                            <i class="fa-solid fa-screwdriver-wrench mr-1 text-cyan-400"></i> Báo hỏng thiết bị
                        </button>
                        <button type="button" onclick="switchResidentTab('housekeeping')" class="resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800">
                            <i class="fa-solid fa-broom mr-1 text-emerald-400"></i> Dọn dẹp phòng
                        </button>
                    </div>
                </section>

                <section id="resident-tab-bills" class="resident-section panel rounded-2xl p-6">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-lg font-black">Hóa đơn của tôi</h2>
                            <p class="text-xs text-slate-500 mt-1">Xem chi tiết tiền phòng, điện, nước và mã QR thanh toán.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-950 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Tháng</th>
                                    <th class="px-4 py-3">Chi tiết</th>
                                    <th class="px-4 py-3">Tổng</th>
                                    <th class="px-4 py-3">Trạng thái</th>
                                    <th class="px-4 py-3 text-right">QR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @forelse($bills as $bill)
                                    <tr class="hover:bg-slate-900/30">
                                        <td class="px-4 py-4 font-bold text-indigo-300">{{ $bill->billing_month }}</td>
                                        <td class="px-4 py-4 text-xs text-slate-400">
                                            <div>Phòng: {{ number_format($bill->room_amount) }} VND</div>
                                            <div>Điện: {{ number_format($bill->electricity_amount) }} VND / {{ $bill->electricity_usage }} kWh</div>
                                            <div>Nước: {{ number_format($bill->water_amount) }} VND / {{ $bill->water_usage }} m3</div>
                                            <div>Dịch vụ: {{ number_format($bill->service_amount) }} VND</div>
                                        </td>
                                        <td class="px-4 py-4 font-black">{{ number_format($bill->total_amount) }} VND</td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $bill->status === 'paid' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-amber-500/10 text-amber-300 border-amber-500/20' }}">
                                                {{ $bill->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('smartroom.resident.bills.qr', $bill->id) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                                                <i class="fa-solid fa-qrcode"></i> QR
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-500">Chưa có hóa đơn.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="resident-tab-contract" class="resident-section panel rounded-2xl p-6 hidden">
                    <h2 class="text-lg font-black mb-5">Hợp đồng của tôi</h2>
                    @if($contract)
                        <div class="mb-5 p-4 rounded-xl bg-slate-950/60 border border-emerald-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                                    <i class="fa-solid fa-building-user"></i>
                                </div>
                                <div>
                                    <div class="text-[11px] uppercase text-emerald-400 font-bold tracking-wider">Bên cho thuê / Ban quản lý</div>
                                    <div class="text-base font-black text-white mt-0.5">{{ $landlordName }}</div>
                                    <div class="text-xs text-slate-400">{{ $tenant?->name ?? ($room->building->name ?? 'Cơ sở trọ SmartRoom') }}</div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-xs">
                                <div class="flex items-center gap-1.5 bg-slate-900/80 px-3 py-1.5 rounded-lg border border-slate-800">
                                    <i class="fa-solid fa-phone text-emerald-400"></i>
                                    <span class="text-slate-400">Hotline:</span>
                                    <a href="tel:{{ $landlordPhone }}" class="text-emerald-300 font-bold hover:underline">{{ $landlordPhone }}</a>
                                </div>
                                @if($tenant?->bank_name && $tenant?->bank_account_no)
                                    <div class="flex items-center gap-1.5 bg-slate-900/80 px-3 py-1.5 rounded-lg border border-slate-800">
                                        <i class="fa-solid fa-credit-card text-indigo-400"></i>
                                        <span class="text-slate-400">TK thuê:</span>
                                        <span class="text-slate-200 font-bold">{{ $tenant->bank_name }} - {{ $tenant->bank_account_no }} ({{ $tenant->bank_account_name ?? $landlordName }})</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Mã hợp đồng</div>
                                <div class="mt-2 font-black">{{ $contract->contract_code }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Thời hạn</div>
                                <div class="mt-2 font-black">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Đặt cọc</div>
                                <div class="mt-2 font-black">{{ number_format($contract->deposit) }} VND</div>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                            <div class="text-xs text-slate-500 font-bold uppercase mb-2">Điều khoản</div>
                            <div class="text-sm text-slate-300 whitespace-pre-line">{{ $contract->terms }}</div>
                        </div>

                        @if($contract->renewal_status === 'requested')
                            <div class="mt-4 p-4 rounded-xl border border-amber-500/20 bg-amber-500/10 text-amber-300 text-sm flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-hourglass-half animate-pulse text-amber-400"></i>
                                    <span>Đang chờ duyệt yêu cầu gia hạn thêm <strong>{{ $contract->renewal_months }} tháng</strong>.</span>
                                </div>
                                <span class="text-xs text-slate-400 italic">Đã gửi yêu cầu</span>
                            </div>
                        @elseif($contract->renewal_status === 'approved')
                            <div class="mt-4 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                <span>Yêu cầu gia hạn thêm <strong>{{ $contract->renewal_months }} tháng</strong> đã được chấp nhận!</span>
                            </div>
                        @elseif($contract->renewal_status === 'declined')
                            <div class="mt-4 p-4 rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-300 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-circle-xmark text-rose-400"></i>
                                <span>Yêu cầu gia hạn thêm <strong>{{ $contract->renewal_months }} tháng</strong> đã bị từ chối.</span>
                            </div>
                        @elseif($contract->renewal_status === 'renewed')
                            <div class="mt-4 p-4 rounded-xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-sync text-indigo-400"></i>
                                <span>Hợp đồng đã được gia hạn/tái ký thành công.</span>
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('smartroom.contract.sign_view', $contract->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem / ký hợp đồng
                            </a>

                            @if(!$contract->renewal_status || $contract->renewal_status === 'declined' || $contract->renewal_status === 'approved')
                                <button type="button" onclick="toggleRenewalModal(true)" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Yêu cầu gia hạn / Tái ký
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-sm text-slate-500">Chưa có hợp đồng.</div>
                    @endif
                </section>

                <section id="resident-tab-tickets" class="resident-section panel rounded-2xl p-6 hidden">
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <form method="POST" action="{{ route('smartroom.resident.tickets.store') }}" enctype="multipart/form-data" class="xl:col-span-1 rounded-xl bg-slate-950/40 border border-slate-800 p-4 space-y-3.5" id="resident-ticket-form" onsubmit="return handleTicketFormSubmit(event, this)">
                            @csrf
                            <div class="flex items-center justify-between pb-2 border-b border-slate-900">
                                <h2 class="text-base font-black text-slate-100 flex items-center gap-2">
                                    <i class="fa-solid fa-headset text-indigo-400"></i> Báo Hỏng Thiết Bị & Sửa Chữa
                                </h2>
                                <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">FEAT_28_TICKETS</span>
                            </div>

                            @if(isset($errors) && $errors->any())
                                <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs space-y-1">
                                    @foreach($errors->all() as $error)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-[11px]"></i>
                                            <span>{{ $error }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-broom text-emerald-400"></i>
                                    <span>Cần dọn dẹp vệ sinh phòng?</span>
                                </div>
                                <button type="button" onclick="switchResidentTab('housekeeping')" class="text-xs font-bold underline hover:text-white">
                                    Sang mục Dọn phòng
                                </button>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Loại sự cố hư hỏng <span class="text-rose-500">*</span></label>
                                <select name="category" id="ticket-category" onchange="handleCategoryChange(this.value); clearCategoryError();" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors">
                                    <option value="">-- Chọn phân loại sự cố hư hỏng --</option>
                                    <option value="electric">Điện (Cháy đèn, mất điện, ổ cắm)</option>
                                    <option value="water">Nước (Rò rỉ ống, vòi sen, tắc bồn cầu)</option>
                                    <option value="lock">Khóa cửa (Kẹt khóa, hỏng chìa, bản lề)</option>
                                    <option value="furniture">Nội thất & Trang thiết bị (Tủ, giường, bàn ghế)</option>
                                    <option value="maintenance">Bảo trì phòng định kỳ</option>
                                    <option value="other">Khác</option>
                                </select>
                                <p id="ticket-category-error" class="hidden text-xs text-rose-400 mt-1 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                                    <span id="ticket-category-error-text">Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).</span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tiêu đề sự cố <span class="text-rose-500">*</span></label>
                                <input name="title" id="ticket-title" maxlength="100" oninput="clearTitleError()" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 placeholder-slate-500 transition-colors" placeholder="VD: Máy lạnh chảy nước ở dàn lạnh">
                                <p id="ticket-title-error" class="hidden text-xs text-rose-400 mt-1 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                                    <span id="ticket-title-error-text">Vui lòng nhập tiêu đề sự cố cần sửa chữa</span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mức độ khẩn cấp <span class="text-rose-500">*</span></label>
                                <select name="urgency" id="ticket-urgency" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors">
                                    <option value="normal" selected>Bình thường</option>
                                    <option value="urgent">Gấp / Cần gấp</option>
                                    <option value="emergency">Khẩn cấp</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Vị trí cụ thể trong phòng</label>
                                <input name="specific_location" id="ticket-specific-location" maxlength="150" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 placeholder-slate-500 transition-colors" placeholder="VD: Nhà vệ sinh, Góc bếp, Cạnh ban công...">
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mô tả chi tiết sự cố <span class="text-rose-500">*</span></label>
                                    <span id="ticket-desc-count" class="text-[10px] text-slate-500">0/1000</span>
                                </div>
                                <textarea name="description" id="ticket-description" maxlength="1000" rows="4" oninput="handleDescriptionInput(this)" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 placeholder-slate-500 transition-colors resize-none" placeholder="Mô tả thời điểm xảy ra, mức độ rò rỉ nước..."></textarea>
                                <p id="ticket-desc-error" class="hidden text-xs text-rose-400 mt-1 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                                    <span id="ticket-desc-error-text">Vui lòng mô tả chi tiết sự cố hư hỏng gặp phải.</span>
                                </p>
                            </div>

                            <button type="button" onclick="analyzeTicketWithAi(this)" class="w-full px-4 py-2 rounded-xl bg-emerald-600/90 hover:bg-emerald-500 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-md shadow-emerald-600/10">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> AI phân tích sự cố & Gợi ý
                            </button>
                            <div id="ticket-ai-result" class="hidden rounded-xl bg-slate-900/90 border border-emerald-500/20 p-3 text-xs text-slate-300 shadow-inner"></div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ảnh chụp sự cố hiện trường (Tối đa 5MB)</label>
                                <input name="image" id="ticket-image-input" type="file" accept="image/jpeg,image/png,image/webp" onchange="validateTicketImageSize(this)" class="w-full text-xs text-slate-400 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-xs file:font-bold hover:file:bg-indigo-500 file:transition-colors file:cursor-pointer">
                                <p id="ticket-image-error" class="hidden text-xs text-rose-400 mt-1 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                                    <span>Kích thước ảnh chụp sự cố quá lớn. Vui lòng chọn ảnh dung lượng dưới 5MB.</span>
                                </p>
                            </div>

                            <button type="submit" id="btnSubmitTicket" name="btnSubmitTicket" class="submit-btn w-full px-4 py-3 rounded-xl bg-blue-800 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-blue-900/30 transition-all active:scale-[0.98]">
                                <i class="fa-solid fa-paper-plane"></i> Gửi phiếu báo hỏng
                            </button>
                        </form>

                        <div class="xl:col-span-2 overflow-x-auto rounded-xl border border-slate-900">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-950 text-slate-500 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3">Ngày</th>
                                        <th class="px-4 py-3">Phòng / Vị trí</th>
                                        <th class="px-4 py-3">Nội dung</th>
                                        <th class="px-4 py-3">Mức độ</th>
                                        <th class="px-4 py-3">Trạng thái</th>
                                        <th class="px-4 py-3">Phụ trách</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900">
                                    @forelse($maintenanceTickets ?? $tickets->where('category', '!=', 'housekeeping') as $ticket)
                                        <tr class="hover:bg-slate-900/30">
                                            <td class="px-4 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="font-bold text-indigo-400">P.{{ $ticket->room->room_number ?? 'N/A' }}</div>
                                                @if($ticket->specific_location)
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-[11px] text-slate-300 font-medium">
                                                        <i class="fa-solid fa-location-dot text-rose-400 text-[10px]"></i>{{ $ticket->specific_location }}
                                                    </span>
                                                @else
                                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-800/40 text-[11px] text-slate-500">Toàn phòng</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="font-bold text-slate-200">{{ $ticket->title }}</div>
                                                <div class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $ticket->description }}</div>
                                                @if($ticket->image_path)
                                                    <a href="{{ $ticket->image_path }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 mt-1 inline-flex items-center gap-1 font-medium">
                                                        <i class="fa-regular fa-image"></i> Xem ảnh
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $urgencyVal = $ticket->urgency ?? 'normal';
                                                    $urgencyMeta = match($urgencyVal) {
                                                        'urgent' => ['label' => 'Cần gấp', 'class' => 'bg-amber-500/10 text-amber-300 border-amber-500/20'],
                                                        'emergency' => ['label' => 'Khẩn cấp', 'class' => 'bg-rose-500/10 text-rose-300 border-rose-500/20 animate-pulse'],
                                                        default => ['label' => 'Bình thường', 'class' => 'bg-slate-800 text-slate-400 border-slate-700'],
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $urgencyMeta['class'] }}">
                                                    {{ $urgencyMeta['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $statusVal = $ticket->status ?? 'pending';
                                                    $statusMeta = match($statusVal) {
                                                        'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-sky-500/10 text-sky-300 border-sky-500/20'],
                                                        'resolved' => ['label' => 'Đã hoàn thành', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'],
                                                        default => ['label' => 'Chờ tiếp nhận', 'class' => 'bg-amber-500/10 text-amber-300 border-amber-500/20'],
                                                    };
                                                @endphp
                                                <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $statusMeta['class'] }}">
                                                    {{ $statusMeta['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $ticket->assigned_to ?? 'Chưa phân công' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-xs text-slate-500">Chưa có sự cố báo hỏng thiết bị nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- SECTION DỌN DẸP BUỒNG PHÒNG RIÊNG BIỆT (HOUSEKEEPING) -->
                <section id="resident-tab-housekeeping" class="resident-section panel rounded-2xl p-6 hidden">
                    <!-- Trạng thái buồng phòng hiện tại của cư dân -->
                    <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-[#0b101c] border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl shrink-0">
                                <i class="fa-solid fa-broom"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-white flex items-center gap-2">
                                    Dịch Vụ Dọn Dẹp Phòng (Housekeeping)
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Phòng P.{{ $room->room_number }} - Tầng {{ $room->floor }} ({{ $room->building->name ?? 'Cơ sở lưu trú' }})
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400">Trạng thái phòng:</span>
                            @if($room->cleaning_status === 'cleaning')
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30 inline-flex items-center gap-1.5 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-orange-400"></span> Nhân viên đang dọn dẹp
                                </span>
                            @elseif($room->cleaning_status === 'dirty' || (isset($housekeepingTickets) && $housekeepingTickets->whereIn('status', ['pending', 'processing'])->isNotEmpty()))
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Đã gửi yêu cầu (Chờ nhân viên dọn)
                                </span>
                            @else
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Phòng sạch sẽ
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <!-- Form Gửi Yêu Cầu Dọn Phòng -->
                        <form method="POST" action="{{ route('smartroom.resident.housekeeping.store') }}" class="xl:col-span-1 rounded-xl bg-slate-950/40 border border-slate-800 p-4 space-y-4" id="resident-housekeeping-form" onsubmit="return handleHousekeepingSubmit(event, this)">
                            @csrf
                            <div class="flex items-center justify-between pb-2 border-b border-slate-900">
                                <h4 class="text-sm font-black text-slate-100 flex items-center gap-2">
                                    <i class="fa-solid fa-sparkles text-emerald-400"></i> Đặt Yêu Cầu Dọn Dẹp
                                </h4>
                                <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Buồng phòng</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Thời gian mong muốn dọn phòng</label>
                                <select name="requested_time" id="hk-requested-time" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
                                    <option value="Hôm nay (Càng sớm càng tốt)">Hôm nay (Càng sớm càng tốt)</option>
                                    <option value="Sáng nay (8h00 - 11h30)">Sáng nay (8h00 - 11h30)</option>
                                    <option value="Chiều nay (14h00 - 17h30)">Chiều nay (14h00 - 17h30)</option>
                                    <option value="Sáng mai (8h00 - 11h30)">Sáng mai (8h00 - 11h30)</option>
                                    <option value="Chiều mai (14h00 - 17h30)">Chiều mai (14h00 - 17h30)</option>
                                    <option value="Vào cuối tuần">Vào cuối tuần</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mức độ ưu tiên</label>
                                <select name="urgency" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors">
                                    <option value="normal" selected>Bình thường</option>
                                    <option value="urgent">Gấp / Cần dọn sớm</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Chi tiết các hạng mục cần dọn <span class="text-rose-500">*</span></label>
                                    <span id="hk-note-count" class="text-[10px] text-slate-500">0/1000</span>
                                </div>
                                <textarea name="note" id="hk-note" maxlength="1000" rows="4" oninput="document.getElementById('hk-note-count').textContent = this.value.length + '/1000'" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition-colors resize-none" placeholder="VD: Nhờ dọn rác, lau sàn nhà, thay vỏ ga gối và cọ rửa nhà vệ sinh..."></textarea>
                                <p id="hk-note-error" class="hidden text-xs text-rose-400 mt-1 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                                    <span>Vui lòng nhập chi tiết yêu cầu dọn phòng (tối thiểu 5 ký tự).</span>
                                </p>
                            </div>

                            <!-- Gợi ý nhanh -->
                            <div class="space-y-1.5">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Gợi ý yêu cầu nhanh:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" onclick="appendHkNote('Dọn rác & lau sạch sàn nhà.')" class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[11px] text-slate-300 transition">Lau sàn + đổ rác</button>
                                    <button type="button" onclick="appendHkNote('Thay ga trải giường và vỏ gối mới.')" class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[11px] text-slate-300 transition">Thay ga gối</button>
                                    <button type="button" onclick="appendHkNote('Cọ rửa và khử mùi nhà vệ sinh.')" class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[11px] text-slate-300 transition">Cọ toilet</button>
                                    <button type="button" onclick="appendHkNote('Tổng vệ sinh toàn bộ phòng.')" class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[11px] text-slate-300 transition">Tổng vệ sinh</button>
                                </div>
                            </div>

                            <button type="submit" class="submit-btn w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Dọn Phòng
                            </button>
                        </form>

                        <!-- Danh Sách Lịch Sử Yêu Cầu Dọn Phòng -->
                        <div class="xl:col-span-2 overflow-x-auto rounded-xl border border-slate-900">
                            <div class="p-3 bg-slate-950/70 border-b border-slate-900 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Lịch Sử Yêu Cầu Dọn Buồng Phòng</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-900 text-slate-400 border border-slate-800">
                                    {{ ($housekeepingTickets ?? collect())->count() }} lượt yêu cầu
                                </span>
                            </div>
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-950 text-slate-500 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3">Thời gian gửi</th>
                                        <th class="px-4 py-3">Nội dung yêu cầu</th>
                                        <th class="px-4 py-3">Mức độ</th>
                                        <th class="px-4 py-3">Trạng thái xử lý</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900">
                                    @forelse($housekeepingTickets ?? $tickets->where('category', 'housekeeping') as $hk)
                                        <tr class="hover:bg-slate-900/30">
                                            <td class="px-4 py-4 text-xs text-slate-500 whitespace-nowrap">
                                                {{ $hk->created_at ? $hk->created_at->format('d/m/Y H:i') : '' }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-xs text-slate-200 whitespace-pre-line leading-relaxed font-medium">{{ $hk->description }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if(($hk->urgency ?? 'normal') === 'urgent')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-amber-500/10 text-amber-300 border-amber-500/20">Cần gấp</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-slate-800 text-slate-400 border-slate-700">Bình thường</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $hkStatus = $hk->status ?? 'pending';
                                                    $hkMeta = match($hkStatus) {
                                                        'processing' => ['label' => 'Đang dọn dẹp', 'class' => 'bg-orange-500/10 text-orange-300 border-orange-500/20 animate-pulse'],
                                                        'resolved' => ['label' => 'Đã dọn xong (Sạch)', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'],
                                                        default => ['label' => 'Chờ tiếp nhận', 'class' => 'bg-amber-500/10 text-amber-300 border-amber-500/20'],
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $hkMeta['class'] }}">
                                                    {{ $hkMeta['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">
                                                Chưa có yêu cầu dọn phòng nào được gửi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

            <!-- RENEWAL MODAL -->
            @if($contract)
            <div id="renewal-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
                <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 p-6 rounded-3xl shadow-2xl relative mx-4 animate-fade-in">
                    <button onclick="toggleRenewalModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <h2 class="text-lg font-bold mb-4 text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i> Yêu Cầu Gia Hạn Hợp Đồng
                    </h2>
                    <form action="{{ route('smartroom.resident.contract.request_renewal', $contract->id) }}" method="POST" class="space-y-4" onsubmit="return disableSubmit(this)">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số tháng muốn gia hạn</label>
                            <select name="renewal_months" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="3">3 tháng</option>
                                <option value="6" selected>6 tháng</option>
                                <option value="12">12 tháng (1 năm)</option>
                                <option value="24">24 tháng (2 năm)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ghi chú gửi chủ nhà</label>
                            <textarea name="renewal_note" rows="3" placeholder="Ví dụ: Tôi muốn gia hạn thêm 6 tháng kể từ ngày hết hạn hợp đồng cũ..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" onclick="toggleRenewalModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                                Hủy
                            </button>
                            <button type="submit" class="submit-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/20 transition-all">
                                Gửi Yêu Cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </main>
    </div>

    <script>
        function toggleRenewalModal(show) {
            const modal = document.getElementById('renewal-modal');
            if (!modal) return;
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function switchResidentTab(tab) {
            document.querySelectorAll('.resident-section').forEach(section => section.classList.add('hidden'));
            document.getElementById(`resident-tab-${tab}`)?.classList.remove('hidden');
            document.querySelectorAll('.resident-tab').forEach(button => {
                button.className = 'resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800';
            });
            event.currentTarget.className = 'resident-tab px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold';
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        function handleCategoryChange(category) {
            const titleInput = document.getElementById('ticket-title');
            const descInput = document.getElementById('ticket-description');
            const locationInput = document.getElementById('ticket-specific-location');

            if (category === 'housekeeping') {
                if (!titleInput.value || titleInput.value.includes('Vòi nước') || titleInput.value.includes('Sự cố')) {
                    titleInput.value = 'Yêu cầu dọn vệ sinh buồng phòng';
                }
                descInput.placeholder = 'Vui lòng ghi rõ thời gian mong muốn dọn phòng (VD: Sáng thứ 7 lúc 9h30), yêu cầu đặc biệt (lau sàn, thay ga, cọ rửa toilet, đổ rác)...';
                if (locationInput && !locationInput.value) {
                    locationInput.placeholder = 'VD: Toàn bộ phòng hoặc Nhà vệ sinh';
                }
            } else {
                if (titleInput.value === 'Yêu cầu dọn vệ sinh buồng phòng') {
                    titleInput.value = '';
                }
                descInput.placeholder = 'Mô tả hiện trạng sự cố chi tiết (tiếng kêu, rò rỉ, mức độ ảnh hưởng sinh hoạt)...';
                if (locationInput) {
                    locationInput.placeholder = 'VD: Nhà vệ sinh, Bếp, Ban công, Cửa chính...';
                }
            }
        }

        function clearCategoryError() {
            const cat = document.getElementById('ticket-category');
            const errorEl = document.getElementById('ticket-category-error');
            if (cat) {
                cat.classList.remove('border-rose-500');
                cat.classList.add('border-slate-800');
            }
            if (errorEl) errorEl.classList.add('hidden');
        }

        function clearTitleError() {
            const title = document.getElementById('ticket-title');
            const errorEl = document.getElementById('ticket-title-error');
            if (title) {
                title.classList.remove('border-rose-500');
                title.classList.add('border-slate-800');
            }
            if (errorEl) errorEl.classList.add('hidden');
        }

        function handleDescriptionInput(textarea) {
            const countEl = document.getElementById('ticket-desc-count');
            const errorEl = document.getElementById('ticket-desc-error');
            if (countEl) {
                countEl.textContent = `${textarea.value.length}/1000`;
            }
            if (textarea.value.trim().length >= 10) {
                textarea.classList.remove('border-rose-500');
                textarea.classList.add('border-slate-800');
                if (errorEl) errorEl.classList.add('hidden');
            }
        }

        function validateTicketImageSize(input) {
            const errorEl = document.getElementById('ticket-image-error');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxBytes = 5 * 1024 * 1024; // 5MB theo spec
                if (file.size > maxBytes) {
                    input.value = '';
                    if (errorEl) errorEl.classList.remove('hidden');
                    return false;
                }
            }
            if (errorEl) errorEl.classList.add('hidden');
            return true;
        }

        function handleTicketFormSubmit(event, form) {
            const title = document.getElementById('ticket-title');
            const titleError = document.getElementById('ticket-title-error');
            const titleErrorText = document.getElementById('ticket-title-error-text');

            const category = document.getElementById('ticket-category');
            const categoryError = document.getElementById('ticket-category-error');
            const categoryErrorText = document.getElementById('ticket-category-error-text');

            const desc = document.getElementById('ticket-description');
            const descError = document.getElementById('ticket-desc-error');
            const descErrorText = document.getElementById('ticket-desc-error-text');

            const imageInput = document.getElementById('ticket-image-input');

            // 1. Kiểm tra Tiêu đề sự cố
            const titleVal = title ? title.value.trim() : '';
            if (!titleVal) {
                if (event) event.preventDefault();
                if (title) {
                    title.classList.remove('border-slate-800');
                    title.classList.add('border-rose-500');
                    title.focus();
                }
                if (titleError) {
                    if (titleErrorText) titleErrorText.textContent = 'Vui lòng nhập tiêu đề sự cố cần sửa chữa';
                    titleError.classList.remove('hidden');
                }
                return false;
            }
            if (titleVal.length < 5 || titleVal.length > 100) {
                if (event) event.preventDefault();
                if (title) {
                    title.classList.remove('border-slate-800');
                    title.classList.add('border-rose-500');
                    title.focus();
                }
                if (titleError) {
                    if (titleErrorText) titleErrorText.textContent = 'Tiêu đề sự cố phải có từ 5 đến 100 ký tự';
                    titleError.classList.remove('hidden');
                }
                return false;
            }

            // 2. Kiểm tra Phân loại sự cố (ERR_28_02)
            if (!category || !category.value) {
                if (event) event.preventDefault();
                if (category) {
                    category.classList.remove('border-slate-800');
                    category.classList.add('border-rose-500');
                    category.focus();
                }
                if (categoryError) {
                    if (categoryErrorText) categoryErrorText.textContent = 'Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).';
                    categoryError.classList.remove('hidden');
                }
                return false;
            }

            // 3. Kiểm tra Mô tả sự cố (ERR_28_01 & Min 10 chars)
            const descVal = desc ? desc.value.trim() : '';
            if (!descVal) {
                if (event) event.preventDefault();
                if (desc) {
                    desc.classList.remove('border-slate-800');
                    desc.classList.add('border-rose-500');
                    desc.focus();
                }
                if (descError) {
                    if (descErrorText) descErrorText.textContent = 'Vui lòng mô tả chi tiết sự cố hư hỏng gặp phải.';
                    descError.classList.remove('hidden');
                }
                return false;
            }
            if (descVal.length < 10) {
                if (event) event.preventDefault();
                if (desc) {
                    desc.classList.remove('border-slate-800');
                    desc.classList.add('border-rose-500');
                    desc.focus();
                }
                if (descError) {
                    if (descErrorText) descErrorText.textContent = 'Mô tả sự cố phải có ít nhất 10 ký tự';
                    descError.classList.remove('hidden');
                }
                return false;
            }

            // 4. Kiểm tra ảnh đính kèm (<= 5MB)
            if (imageInput && !validateTicketImageSize(imageInput)) {
                if (event) event.preventDefault();
                return false;
            }

            return disableSubmit(form);
        }

        function disableSubmit(form) {
            const btn = form.querySelector('.submit-btn');
            if (!btn) return true;
            if (btn.disabled) return false;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang gửi...';
            return true;
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function analyzeTicketWithAi(btn) {
            const titleInput = document.getElementById('ticket-title');
            const descriptionInput = document.getElementById('ticket-description');
            const categoryInput = document.getElementById('ticket-category');
            const result = document.getElementById('ticket-ai-result');
            const description = descriptionInput.value.trim();

            if (description.length < 5) {
                result.classList.remove('hidden');
                result.textContent = 'Vui lòng nhập mô tả sự cố rõ hơn trước khi dùng AI.';
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang phân tích...';
            result.classList.remove('hidden');
            result.textContent = 'AI đang phân loại sự cố...';

            fetch("{{ route('smartroom.resident.tickets.analyze') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    title: titleInput.value,
                    description
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = original;

                if (!data.success) {
                    result.textContent = 'Không thể phân tích sự cố bằng AI.';
                    return;
                }

                const analysis = data.analysis;
                titleInput.value = analysis.title || titleInput.value;
                categoryInput.value = analysis.category || categoryInput.value;
                descriptionInput.value = analysis.normalized_description || descriptionInput.value;
                result.innerHTML = `Mức ưu tiên: <strong>${escapeHtml(analysis.priority)}</strong><br>Gợi ý xử lý: ${escapeHtml(analysis.suggestion || 'Chưa có gợi ý.')}`;
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = original;
                result.textContent = 'Không thể kết nối AI.';
            });
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function appendHkNote(text) {
            const textarea = document.getElementById('hk-note');
            if (!textarea) return;
            if (textarea.value.trim().length > 0) {
                textarea.value += ' ' + text;
            } else {
                textarea.value = text;
            }
            const countEl = document.getElementById('hk-note-count');
            if (countEl) countEl.textContent = textarea.value.length + '/1000';
            document.getElementById('hk-note-error')?.classList.add('hidden');
            textarea.classList.remove('border-rose-500');
            textarea.classList.add('border-slate-800');
        }

        function handleHousekeepingSubmit(event, form) {
            const note = document.getElementById('hk-note');
            const noteError = document.getElementById('hk-note-error');
            if (!note || note.value.trim().length < 5) {
                if (event) event.preventDefault();
                if (note) {
                    note.classList.remove('border-slate-800');
                    note.classList.add('border-rose-500');
                    note.focus();
                }
                if (noteError) noteError.classList.remove('hidden');
                return false;
            }
            return disableSubmit(form);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tab = new URLSearchParams(window.location.search).get('tab');
            if (tab && document.getElementById(`resident-tab-${tab}`)) {
                const buttons = Array.from(document.querySelectorAll('.resident-tab'));
                const index = ['bills', 'contract', 'tickets', 'housekeeping'].indexOf(tab);
                if (buttons[index]) buttons[index].click();
            }
        });
    </script>
</body>
</html>
