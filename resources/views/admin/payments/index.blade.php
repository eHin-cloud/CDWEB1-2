<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thanh Toán & Đối Soát Công Nợ - SmartRoom (FEAT_29_PAYMENTS)</title>
    @include('admin.partials.theme-head-script')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

    <style>
        .panel {
            background: rgba(13, 18, 31, 0.72);
            border: 1px solid rgba(30, 41, 59, 0.86);
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
        .input-error {
            border-color: #f43f5e !important;
            box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.35) !important;
            background-color: rgba(225, 29, 72, 0.08) !important;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        <!-- HEADER -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">FEAT_29_PAYMENTS</span>
                    <h1 class="text-lg font-bold text-slate-100">Quản Lý Thanh Toán & Đối Soát Công Nợ</h1>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Bảng kê quản lý thanh toán, công nợ và đối soát tiền phòng chuẩn VietQR Napas.</p>
            </div>
            <div class="flex items-center gap-3">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <a id="btnExportCsv" href="{{ route('admin.payments.export', request()->query()) }}" onclick="handleExportCsv(event, this.href)" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-900/20 transition-all">
                    <i class="fa-solid fa-file-csv text-sm"></i> Xuất CSV Đối Soát
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="p-8 flex-grow overflow-y-auto space-y-6">
            <!-- Toast notification container -->
            <div id="toastContainer" class="fixed top-20 right-8 z-50 flex flex-col gap-2 pointer-events-none"></div>

            @if(session('success'))
                <div id="serverSuccessAlert" class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 text-sm font-semibold flex items-center justify-between shadow-lg shadow-emerald-950/40">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 p-4 text-sm font-semibold flex items-center gap-3 shadow-lg shadow-rose-950/40">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- STATS SECTION: HÌNH 29.1 THỐNG KÊ TOÀN BỘ LỊCH SỬ NỘP TIỀN PHÒNG & TỶ LỆ THU NỢ -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-emerald-500 relative overflow-hidden">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Tỷ Lệ Thu Nợ Tháng</span>
                        <i class="fa-solid fa-chart-pie text-emerald-400"></i>
                    </div>
                    <div class="mt-2 text-3xl font-black text-emerald-300">{{ $summary['collection_rate'] }}%</div>
                    <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $summary['collection_rate']) }}%"></div>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                        <span>Đã thu: {{ number_format($summary['paid_total']) }}đ</span>
                    </div>
                </div>

                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-indigo-500">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Đã Thu Tiền Phòng</span>
                        <i class="fa-solid fa-vault text-indigo-400"></i>
                    </div>
                    <div class="mt-2 text-2xl font-black text-indigo-300">{{ number_format($summary['paid_total']) }}đ</div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">{{ number_format($summary['paid_count']) }} hóa đơn đã gạch nợ thành công</p>
                </div>

                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-amber-500">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Công Nợ Còn Lại</span>
                        <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i>
                    </div>
                    <div class="mt-2 text-2xl font-black text-amber-300">{{ number_format($summary['unpaid_total']) }}đ</div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">{{ number_format($summary['sent_count'] + $summary['overdue_count']) }} phòng đang nợ tiền</p>
                </div>

                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-cyan-500">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Đối Soát VietQR Tự Động</span>
                        <i class="fa-solid fa-qrcode text-cyan-400"></i>
                    </div>
                    <div class="mt-2 text-3xl font-black text-cyan-300">{{ number_format($summary['vietqr_count']) }}</div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Giao dịch quét mã Napas thành công</p>
                </div>

                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-rose-500">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Hóa Đơn Quá Hạn</span>
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                    </div>
                    <div class="mt-2 text-3xl font-black text-rose-400">{{ number_format($summary['overdue_count']) }}</div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Cần đôn đốc nhắc nhở thanh toán</p>
                </div>

                <div class="panel glass-card rounded-2xl p-5 border-l-4 border-l-slate-600">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Chưa Gửi Cư Dân</span>
                        <i class="fa-solid fa-file-invoice text-slate-400"></i>
                    </div>
                    <div class="mt-2 text-3xl font-black text-slate-300">{{ number_format($summary['draft_count']) }}</div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Phòng chưa phát hành hóa đơn</p>
                </div>
            </section>

            <!-- BỘ LỌC TÌM KIẾM & KỲ ĐỐI SOÁT -->
            <section class="panel glass-card rounded-2xl p-5 shadow-xl">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Kỳ tháng thu *</label>
                        <input type="month" name="billing_month" value="{{ $filters['billing_month'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Từ tháng</label>
                        <input type="month" name="from_month" value="{{ $filters['from_month'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Đến tháng</label>
                        <input type="month" name="to_month" value="{{ $filters['to_month'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($statusLabels as $value => $meta)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Hình thức thanh toán</label>
                        <select name="payment_method" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="">Mọi hình thức</option>
                            <option value="bank_transfer" @selected($filters['payment_method'] === 'bank_transfer')>Chuyển khoản VietQR</option>
                            <option value="cash" @selected($filters['payment_method'] === 'cash')>Tiền mặt</option>
                            <option value="gateway" @selected($filters['payment_method'] === 'gateway')>Cổng thanh toán</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Chọn phòng</label>
                        <select name="room_id" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="">Tất cả phòng</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected($filters['room_id'] === $room->id)>Phòng {{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="xl:col-span-2">
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Tìm kiếm thông minh</label>
                        <div class="flex gap-2">
                            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Phòng, tên khách, SĐT, CCCD..." class="min-w-0 flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-900/30">
                                <i class="fa-solid fa-filter"></i> Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- BẢNG KÊ QUẢN LÝ THANH TOÁN (STT 1: tblPayments) -->
            <section class="panel glass-card rounded-2xl p-6 shadow-2xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-800/80">
                    <div>
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-400"></i>
                            <span>Bảng Kê Quản Lý Thanh Toán & Đối Soát Công Nợ</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Danh sách chi tiết hóa đơn, xác nhận gạch nợ tiền phòng và tạo mã VietQR chuẩn Napas.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold bg-slate-900/80 px-3 py-1.5 rounded-xl border border-slate-800">
                        <i class="fa-solid fa-list-check text-indigo-400"></i>
                        <span>Tổng cộng: <strong class="text-slate-100">{{ $paymentRows->count() }}</strong> bản ghi</span>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table id="tblPayments" class="w-full text-left text-sm text-slate-300">
                        <thead class="text-[11px] text-slate-400 uppercase bg-slate-900/90 border-b border-slate-900 tracking-wider">
                            <tr>
                                <th class="px-4 py-3.5 font-bold">Phòng</th>
                                <th class="px-4 py-3.5 font-bold">Khách thuê</th>
                                <th class="px-4 py-3.5 font-bold">Chi phí phát sinh</th>
                                <th class="px-4 py-3.5 font-bold">Số tiền & Công nợ</th>
                                <th class="px-4 py-3.5 font-bold">Hình thức</th>
                                <th class="px-4 py-3.5 font-bold">Thời gian</th>
                                <th class="px-4 py-3.5 font-bold text-center">Trạng thái</th>
                                <th class="px-4 py-3.5 font-bold text-center min-w-[200px]">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900/80 bg-slate-950/30">
                            @forelse($paymentRows as $row)
                                <tr id="row-invoice-{{ $row->id ?: ('draft-' . $row->room_id) }}" 
                                    class="hover:bg-slate-900/50 transition-colors {{ $row->status === 'paid' ? 'bg-emerald-950/10' : '' }}"
                                    data-invoice-id="{{ $row->id }}"
                                    data-room-number="{{ $row->room_number }}"
                                    data-resident-name="{{ $row->resident_name }}"
                                    data-billing-month="{{ $row->billing_month }}"
                                    data-total-amount="{{ $row->total_amount }}"
                                    data-remaining-debt="{{ $row->remaining_debt }}"
                                    data-vietqr-url="{{ $row->vietqr_url }}">
                                    <!-- CỘT 1: PHÒNG -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-xs">
                                                P.{{ $row->room_number }}
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-100 text-sm">Phòng {{ $row->room_number }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $row->building_name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- CỘT 2: KHÁCH THUÊ -->
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-200 text-xs flex items-center gap-1.5">
                                            <i class="fa-solid fa-user text-[11px] text-slate-500"></i>
                                            <span>{{ $row->resident_name }}</span>
                                        </div>
                                        <div class="text-[10px] text-indigo-400 font-semibold mt-0.5">Kỳ: {{ $row->billing_month }}</div>
                                    </td>

                                    <!-- CỘT 3: CHI PHÍ PHÁT SINH -->
                                    <td class="px-4 py-4 text-xs text-slate-400">
                                        <div class="space-y-0.5 text-[11px]">
                                            <div class="flex justify-between gap-4"><span>Phòng:</span> <strong class="text-slate-300">{{ number_format($row->room_amount) }}đ</strong></div>
                                            <div class="flex justify-between gap-4"><span>Điện ({{ $row->electricity_usage }} kWh):</span> <span class="text-slate-300">{{ number_format($row->electricity_amount) }}đ</span></div>
                                            <div class="flex justify-between gap-4"><span>Nước ({{ $row->water_usage }} m³):</span> <span class="text-slate-300">{{ number_format($row->water_amount) }}đ</span></div>
                                            <div class="flex justify-between gap-4"><span>Dịch vụ:</span> <span class="text-slate-300">{{ number_format($row->service_amount) }}đ</span></div>
                                        </div>
                                    </td>

                                    <!-- CỘT 4: SỐ TIỀN & CÔNG NỢ -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-extrabold text-slate-100">{{ number_format($row->total_amount) }}đ</div>
                                        <div class="text-[11px] mt-1 cell-debt-status font-bold {{ $row->remaining_debt > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                                            @if($row->remaining_debt > 0)
                                                <span>Còn nợ: {{ number_format($row->remaining_debt) }}đ</span>
                                            @else
                                                <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[10px]"></i> Hết nợ (0đ)</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- CỘT 5: HÌNH THỨC -->
                                    <td class="px-4 py-4 text-xs cell-payment-method">
                                        @if($row->payment_method)
                                            <div class="font-bold text-slate-200 flex items-center gap-1.5">
                                                @if(in_array($row->payment_method, ['bank_transfer', 'vietqr']))
                                                    <i class="fa-solid fa-qrcode text-cyan-400 text-xs"></i>
                                                @else
                                                    <i class="fa-solid fa-money-bill-wave text-emerald-400 text-xs"></i>
                                                @endif
                                                <span>{{ $paymentMethods[$row->payment_method] ?? $row->payment_method }}</span>
                                            </div>
                                            @if($row->transaction_ref)
                                                <div class="text-[10px] text-slate-500 font-mono mt-0.5 truncate max-w-[120px]" title="{{ $row->transaction_ref }}">Ref: {{ $row->transaction_ref }}</div>
                                            @endif
                                        @else
                                            <span class="text-slate-500 italic text-[11px]">—</span>
                                        @endif
                                    </td>

                                    <!-- CỘT 6: THỜI GIAN -->
                                    <td class="px-4 py-4 text-xs cell-payment-date">
                                        @if($row->payment_date)
                                            <div class="font-bold text-emerald-300">{{ $row->payment_date->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $row->payment_date->format('H:i') }}</div>
                                        @else
                                            <span class="text-slate-500 text-[11px]">Chưa thu</span>
                                        @endif
                                    </td>

                                    <!-- CỘT 7: TRẠNG THÁI -->
                                    <td class="px-4 py-4 text-center cell-status">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-black border {{ $row->status_class }}">
                                            {{ $row->status_label }}
                                        </span>
                                    </td>

                                    <!-- CỘT 8: THAO TÁC -->
                                    <td class="px-4 py-4 text-center cell-actions">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($row->id)
                                                <!-- Nút Xem VietQR Modal (Hình 29.2) -->
                                                <button type="button" 
                                                        onclick="openVietQRModal('{{ $row->room_number }}', '{{ $row->resident_name }}', '{{ $row->billing_month }}', {{ $row->remaining_debt ?: $row->total_amount }}, '{{ $row->vietqr_url }}')" 
                                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-cyan-500/20 text-xs font-bold transition-all flex items-center gap-1"
                                                        title="Phóng to mã VietQR chuyển khoản">
                                                    <i class="fa-solid fa-qrcode text-xs"></i> <span>Mã QR</span>
                                                </button>

                                                @if($row->status !== 'paid')
                                                    <!-- Nút Gạch nợ mở Modal Form -->
                                                    <button type="button" 
                                                            onclick="openConfirmPayModal({{ $row->id }}, '{{ $row->room_number }}', '{{ $row->resident_name }}', '{{ $row->billing_month }}', {{ $row->remaining_debt }})"
                                                            class="btn-trigger-pay px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-emerald-900/30">
                                                        <i class="fa-solid fa-check-double text-xs"></i> <span>Gạch nợ</span>
                                                    </button>
                                                @else
                                                    <!-- Đã thanh toán -->
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-xl">
                                                        <i class="fa-solid fa-circle-check text-xs"></i> Hoàn tất
                                                    </span>
                                                @endif
                                            @else
                                                <a href="{{ route('smartroom.admin', ['tab' => 'utility-section']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold transition-all">
                                                    <i class="fa-solid fa-paper-plane text-[10px]"></i> Phát hành
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <i class="fa-solid fa-receipt text-3xl mb-3 text-slate-700 block"></i>
                                        Không tìm thấy giao dịch nào phù hợp với bộ lọc hiện tại.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- LỊCH SỬ THANH TOÁN & DOANH THU THEO KỲ -->
            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- BÁO CÁO DOANH THU KỲ -->
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-1 shadow-xl">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-emerald-400"></i> Doanh Thu Thu Được
                    </h2>
                    <div class="mt-5 space-y-3 max-h-[460px] overflow-y-auto pr-1">
                        @forelse($revenueReports as $report)
                            <div class="rounded-xl bg-slate-950/60 border border-slate-800 p-4 hover:border-slate-700 transition-colors">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-100">{{ $report['label'] }}</div>
                                        <div class="text-[11px] text-slate-500">{{ number_format($report['paid_count']) }} hóa đơn đã thanh toán</div>
                                    </div>
                                    <div class="text-sm font-black text-emerald-300">{{ number_format($report['total_revenue']) }}đ</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-6 text-center">Chưa có dữ liệu doanh thu trong phạm vi này.</div>
                        @endforelse
                    </div>
                </div>

                <!-- LỊCH SỬ NỘP TIỀN THEO PHÒNG -->
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-2 shadow-xl">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-amber-400"></i> Lịch Sử Nộp Tiền Từng Phòng
                    </h2>
                    <div class="mt-5 space-y-4 max-h-[460px] overflow-y-auto pr-1">
                        @forelse($histories as $roomHistory)
                            <div class="rounded-xl bg-slate-950/60 border border-slate-800 p-4">
                                <div class="flex items-center justify-between gap-3 mb-3 pb-2 border-b border-slate-900">
                                    <div class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded bg-indigo-500/20 text-indigo-300 text-xs flex items-center justify-center font-bold">P</span>
                                        <span>Phòng {{ $roomHistory->first()->room_number }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">{{ $roomHistory->first()->building_name }}</div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs text-slate-400">
                                        <thead class="text-[10px] uppercase text-slate-500 border-b border-slate-900">
                                            <tr>
                                                <th class="py-2 pr-3">Tháng</th>
                                                <th class="py-2 pr-3">Tổng tiền</th>
                                                <th class="py-2 pr-3">Trạng thái</th>
                                                <th class="py-2 pr-3">Ngày nộp</th>
                                                <th class="py-2 pr-3">Hình thức</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-900">
                                            @foreach($roomHistory as $item)
                                                <tr>
                                                    <td class="py-2.5 pr-3 font-bold text-indigo-300">{{ $item->billing_month }}</td>
                                                    <td class="py-2.5 pr-3 font-extrabold text-slate-200">{{ number_format($item->total_amount) }}đ</td>
                                                    <td class="py-2.5 pr-3">
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $item->status_class }}">{{ $item->status_label }}</span>
                                                    </td>
                                                    <td class="py-2.5 pr-3">{{ $item->payment_date ? $item->payment_date->format('d/m/Y') : '-' }}</td>
                                                    <td class="py-2.5 pr-3">{{ $paymentMethods[$item->payment_method] ?? ($item->payment_method ?: '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-6 text-center">Chưa có lịch sử thanh toán theo phòng.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: HỘP THOẠI XÁC NHẬN GẠCH NỢ THANH TOÁN (STT 2, 3, 4)               -->
    <!-- ========================================================================= -->
    <div id="modalConfirmPay" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
        <div class="w-full max-w-lg bg-[#0d121f] border border-slate-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 mb-5 border-b border-slate-800">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">FEAT_29_PAYMENTS</span>
                    <h3 class="text-base font-bold text-slate-100 mt-1 flex items-center gap-2">
                        <i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i>
                        <span>Xác Nhận Gạch Nợ Hóa Đơn Tiền Phòng</span>
                    </h3>
                    <p id="modalPaySubtitle" class="text-xs text-slate-400 mt-0.5">Phòng: — | Khách thuê: — | Tháng: —</p>
                </div>
                <button type="button" onclick="closeConfirmPayModal()" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Form Gạch Nợ -->
            <form id="formConfirmPayment" method="POST" action="{{ route('admin.payments.confirm') }}" onsubmit="handleSubmitConfirmPay(event)" class="space-y-4">
                @csrf
                <input type="hidden" name="invoice_id" id="modal_invoice_id" value="">

                <!-- Thông tin nợ còn lại -->
                <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-semibold">Số tiền nợ còn lại:</span>
                    <span id="modalRemainingDebtText" class="font-black text-amber-400 text-sm">0 đ</span>
                </div>

                <!-- STT 2: Input payment_amount -->
                <div>
                    <label for="payment_amount" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Số tiền thu * <span class="text-rose-500 font-normal">(bắt buộc)</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="payment_amount" 
                               id="payment_amount" 
                               placeholder="VD: 3.500.000"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 text-sm font-bold focus:outline-none focus:border-emerald-500 transition-colors pr-10"
                               autocomplete="off">
                        <span class="absolute right-3.5 top-3.5 text-xs font-bold text-slate-500">VNĐ</span>
                    </div>
                    <!-- Error message container cho payment_amount -->
                    <p id="error_payment_amount" class="hidden text-xs text-rose-400 font-semibold mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                        <span class="msg-content"></span>
                    </p>
                </div>

                <!-- STT 3: Select payment_method -->
                <div>
                    <label for="payment_method" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Phương thức * <span class="text-rose-500 font-normal">(bắt buộc)</span>
                    </label>
                    <select name="payment_method" 
                            id="payment_method" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-emerald-500 transition-colors">
                        <option value="">-- Vui lòng chọn hình thức nhận tiền --</option>
                        <option value="Chuyển khoản VietQR">Chuyển khoản VietQR</option>
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Cổng thanh toán">Cổng thanh toán</option>
                    </select>
                    <!-- Error message container cho payment_method (ERR_29_01) -->
                    <p id="error_payment_method" class="hidden text-xs text-rose-400 font-semibold mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                        <span class="msg-content"></span>
                    </p>
                </div>

                <!-- STT Input: transaction_ref (Mã tham chiếu ngân hàng) -->
                <div>
                    <label for="transaction_ref" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Mã tham chiếu ngân hàng <span class="text-slate-500 font-normal">(không bắt buộc)</span>
                    </label>
                    <input type="text" 
                           name="transaction_ref" 
                           id="transaction_ref" 
                           placeholder="VD: FT241012345678"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-emerald-500 transition-colors font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Mã giao dịch trên sao kê Internet Banking dùng để đối soát công nợ.</p>
                </div>

                <!-- STT 4: Button btnConfirmPay (Nền xanh lá cây) -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeConfirmPayModal()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs font-bold transition-colors">
                        Hủy Bỏ
                    </button>
                    <button type="submit" 
                            id="btnConfirmPay" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-900/30 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Xác nhận gạch nợ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: HỘP THOẠI QUÉT MÃ VIETQR TỰ ĐỘNG CHUẨN NAPAS (HÌNH 29.2)         -->
    <!-- ========================================================================= -->
    <div id="modalVietQR" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
        <div class="w-full max-w-md bg-[#0d121f] border border-slate-800 rounded-3xl p-6 shadow-2xl relative text-center">
            <button type="button" onclick="closeVietQRModal()" class="absolute top-4 right-4 w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="mb-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">VietQR Napas 247</span>
                <h3 class="text-base font-bold text-slate-100 mt-1">Quét Mã VietQR Thanh Toán Tiền Phòng</h3>
                <p id="qrModalSubtitle" class="text-xs text-slate-400 mt-0.5">Phòng: — | Khách: —</p>
            </div>

            <!-- Khung chứa ảnh QR phóng to -->
            <div class="p-3 bg-white rounded-2xl mx-auto w-fit shadow-xl border-4 border-cyan-500/30">
                <img id="qrModalImg" src="" alt="Mã VietQR" class="w-64 h-64 object-contain mx-auto">
            </div>

            <!-- Chi tiết tài khoản nhận tiền -->
            <div class="mt-4 p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl text-left text-xs space-y-1.5">
                <div class="flex justify-between items-center text-slate-400">
                    <span>Ngân hàng thụ hưởng:</span>
                    <strong class="text-slate-200">MB (Quân Đội)</strong>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Số tài khoản:</span>
                    <div class="flex items-center gap-1.5">
                        <strong id="qrAccountNo" class="text-slate-100 font-mono">999988880001</strong>
                        <button type="button" onclick="copyToClipboard('999988880001', 'Đã sao chép số tài khoản!')" class="text-cyan-400 hover:text-cyan-300" title="Sao chép">
                            <i class="fa-regular fa-copy text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Chủ tài khoản:</span>
                    <strong class="text-slate-200">TRAN VAN HOANG</strong>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Số tiền chính xác:</span>
                    <strong id="qrModalAmount" class="text-emerald-400 font-extrabold text-sm">0 đ</strong>
                </div>
                <div class="flex justify-between items-center text-slate-400 pt-1 border-t border-slate-800">
                    <span>Nội dung chuyển khoản:</span>
                    <div class="flex items-center gap-1.5">
                        <strong id="qrModalContent" class="text-indigo-300 font-mono text-[11px]">—</strong>
                        <button type="button" onclick="copyToClipboard(document.getElementById('qrModalContent').innerText, 'Đã sao chép nội dung!')" class="text-cyan-400 hover:text-cyan-300" title="Sao chép">
                            <i class="fa-regular fa-copy text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="button" onclick="closeVietQRModal()" class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-bold text-slate-300">
                    Đóng
                </button>
                <a id="qrDownloadBtn" href="" target="_blank" class="flex-1 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-xs font-bold text-white flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-download"></i> Tải Mã QR
                </a>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT XỬ LÝ NGHIỆP VỤ & KIỂM THỬ GIAO DIỆN -->
    <script>
        let currentModalDebt = 0;

        // Hiển thị thông báo Toast
        function showToast(message, type = 'success', code = '') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            const isSuccess = type === 'success';
            toast.className = `pointer-events-auto p-4 rounded-2xl border shadow-2xl flex items-center gap-3 text-xs font-bold transition-all duration-300 transform translate-y-2 opacity-0 ${
                isSuccess 
                    ? 'bg-[#0a151b] border-emerald-500/40 text-emerald-300 shadow-emerald-950/50' 
                    : 'bg-[#1b0a0e] border-rose-500/40 text-rose-300 shadow-rose-950/50'
            }`;

            const icon = isSuccess ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400';
            const codeBadge = code ? `<span class="px-1.5 py-0.5 rounded text-[9px] uppercase font-black bg-white/10 mr-1">${code}</span>` : '';

            toast.innerHTML = `
                <i class="fa-solid ${icon} text-base shrink-0"></i>
                <div class="flex-1">${codeBadge}${message}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white ml-2">
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
            }, 4000);
        }

        // Định dạng tiền tệ
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        // Sao chép clipboard
        function copyToClipboard(text, successMsg) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                showToast(successMsg, 'success');
            }).catch(() => {
                showToast('Không thể sao chép!', 'error');
            });
        }

        // Mở Modal Xác Nhận Gạch Nợ
        function openConfirmPayModal(invoiceId, roomNumber, residentName, billingMonth, remainingDebt) {
            currentModalDebt = parseInt(remainingDebt) || 0;

            document.getElementById('modal_invoice_id').value = invoiceId;
            document.getElementById('modalPaySubtitle').innerText = `Phòng: ${roomNumber} | Khách: ${residentName} | Tháng: ${billingMonth}`;
            document.getElementById('modalRemainingDebtText').innerText = formatVND(currentModalDebt);

            const amountInput = document.getElementById('payment_amount');
            amountInput.value = currentModalDebt > 0 ? new Intl.NumberFormat('vi-VN').format(currentModalDebt) : '';
            amountInput.classList.remove('input-error');
            document.getElementById('error_payment_amount').classList.add('hidden');

            const methodSelect = document.getElementById('payment_method');
            methodSelect.value = '';
            methodSelect.classList.remove('input-error');
            document.getElementById('error_payment_method').classList.add('hidden');

            document.getElementById('transaction_ref').value = '';

            document.getElementById('modalConfirmPay').classList.remove('hidden');
        }

        function closeConfirmPayModal() {
            document.getElementById('modalConfirmPay').classList.add('hidden');
        }

        // Mở Modal Quét VietQR Napas (Hình 29.2)
        function openVietQRModal(roomNumber, residentName, billingMonth, amount, qrUrl) {
            document.getElementById('qrModalSubtitle').innerText = `Phòng: ${roomNumber} | Khách: ${residentName} | Kỳ: ${billingMonth}`;
            document.getElementById('qrModalImg').src = qrUrl;
            document.getElementById('qrDownloadBtn').href = qrUrl;
            document.getElementById('qrModalAmount').innerText = formatVND(amount);

            const cleanMonth = (billingMonth || '').replace('-', '');
            document.getElementById('qrModalContent').innerText = `Thanh toan P${roomNumber} T${cleanMonth}`;

            document.getElementById('modalVietQR').classList.remove('hidden');
        }

        function closeVietQRModal() {
            document.getElementById('modalVietQR').classList.add('hidden');
        }

        // Xử lý nút Xuất CSV Đối Soát (ERR_29_03)
        function handleExportCsv(e, url) {
            // Hiển thị thông báo theo kịch bản ERR_29_03
            showToast('Tải xuống tệp danh sách đối soát thanh toán định dạng .csv thành công.', 'success', 'ERR_29_03');
        }

        // Realtime input amount formatter
        document.getElementById('payment_amount').addEventListener('input', function(e) {
            let val = this.value.replace(/[^\d]/g, '');
            if (val) {
                this.value = new Intl.NumberFormat('vi-VN').format(val);
                this.classList.remove('input-error');
                document.getElementById('error_payment_amount').classList.add('hidden');
            }
        });

        document.getElementById('payment_method').addEventListener('change', function(e) {
            if (this.value) {
                this.classList.remove('input-error');
                document.getElementById('error_payment_method').classList.add('hidden');
            }
        });

        // Xử lý gửi Form Xác nhận Gạch Nợ & Kiểm thử lỗi Validation
        async function handleSubmitConfirmPay(e) {
            e.preventDefault();

            const form = e.target;
            const invoiceId = document.getElementById('modal_invoice_id').value;
            const rawAmount = document.getElementById('payment_amount').value.trim();
            const rawMethod = document.getElementById('payment_method').value.trim();
            const transactionRef = document.getElementById('transaction_ref').value.trim();

            const amountInput = document.getElementById('payment_amount');
            const errorAmount = document.getElementById('error_payment_amount');
            const methodSelect = document.getElementById('payment_method');
            const errorMethod = document.getElementById('error_payment_method');

            // Reset lỗi giao diện
            amountInput.classList.remove('input-error');
            errorAmount.classList.add('hidden');
            methodSelect.classList.remove('input-error');
            errorMethod.classList.add('hidden');

            let hasError = false;

            // KỊCH BẢN KIỂM THỬ 1: Để trống số tiền và bấm Xác nhận
            if (!rawAmount) {
                amountInput.classList.add('input-error');
                errorAmount.querySelector('.msg-content').innerText = 'Vui lòng nhập số tiền thanh toán';
                errorAmount.classList.remove('hidden');
                hasError = true;
            }

            // KỊCH BẢN KIỂM THỬ: Bấm cập nhật nhưng chưa chọn phương thức (Mã lỗi ERR_29_01)
            if (!rawMethod) {
                methodSelect.classList.add('input-error');
                errorMethod.querySelector('.msg-content').innerText = 'Vui lòng chọn hình thức nhận tiền (Tiền mặt hoặc Chuyển khoản).';
                errorMethod.classList.remove('hidden');
                hasError = true;
            }

            if (hasError) return;

            const cleanAmount = parseInt(rawAmount.replace(/[^\d]/g, ''), 10);

            // Kiểm tra số tiền hợp lệ regex ^[1-9][0-9]*$
            if (isNaN(cleanAmount) || cleanAmount <= 0) {
                amountInput.classList.add('input-error');
                errorAmount.querySelector('.msg-content').innerText = 'Số tiền thanh toán không hợp lệ (phải lớn hơn 0)';
                errorAmount.classList.remove('hidden');
                return;
            }

            // KỊCH BẢN KIỂM THỬ 2: Nhập số tiền vượt quá số tiền nợ còn lại
            if (cleanAmount > currentModalDebt) {
                amountInput.classList.add('input-error');
                errorAmount.querySelector('.msg-content').innerText = `Số tiền thanh toán không được vượt quá số tiền nợ còn lại (${formatVND(currentModalDebt)})`;
                errorAmount.classList.remove('hidden');
                return;
            }

            // HỢP LỆ: Gửi Request lên Server
            const btnConfirm = document.getElementById('btnConfirmPay');
            const oldBtnText = btnConfirm.innerHTML;
            btnConfirm.disabled = true;
            btnConfirm.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang gạch nợ...</span>`;

            try {
                const response = await fetch("{{ route('admin.payments.confirm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        invoice_id: invoiceId,
                        payment_amount: cleanAmount,
                        payment_method: rawMethod,
                        transaction_ref: transactionRef
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // KỊCH BẢN 3 & ERR_29_02: Cập nhật trạng thái thành công
                    closeConfirmPayModal();
                    showToast(data.message || 'Đã ghi nhận thanh toán tiền phòng thành công!', 'success', data.code || 'ERR_29_02');

                    // Cập nhật DOM dòng phòng sang Đã thanh toán, tự động trừ nợ
                    const targetRow = document.getElementById(`row-invoice-${invoiceId}`);
                    if (targetRow) {
                        targetRow.classList.add('bg-emerald-950/20');

                        // Cột nợ
                        const debtCell = targetRow.querySelector('.cell-debt-status');
                        if (debtCell) {
                            debtCell.className = 'text-[11px] mt-1 cell-debt-status font-bold text-emerald-400';
                            debtCell.innerHTML = `<span class="flex items-center gap-1"><i class="fa-solid fa-check text-[10px]"></i> Hết nợ (0đ)</span>`;
                        }

                        // Cột hình thức
                        const methodCell = targetRow.querySelector('.cell-payment-method');
                        if (methodCell) {
                            methodCell.innerHTML = `
                                <div class="font-bold text-slate-200 flex items-center gap-1.5">
                                    <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                                    <span>${rawMethod}</span>
                                </div>
                                ${transactionRef ? `<div class="text-[10px] text-slate-500 font-mono mt-0.5">Ref: ${transactionRef}</div>` : ''}
                            `;
                        }

                        // Cột thời gian
                        const dateCell = targetRow.querySelector('.cell-payment-date');
                        if (dateCell) {
                            const now = new Date();
                            const dateStr = `${String(now.getDate()).padStart(2, '0')}/${String(now.getMonth() + 1).padStart(2, '0')}/${now.getFullYear()}`;
                            dateCell.innerHTML = `<div class="font-bold text-emerald-300">${dateStr}</div><div class="text-[10px] text-slate-500">Vừa xong</div>`;
                        }

                        // Cột trạng thái
                        const statusCell = targetRow.querySelector('.cell-status');
                        if (statusCell) {
                            statusCell.innerHTML = `<span class="px-2.5 py-1 rounded-full text-[11px] font-black border bg-emerald-500/10 text-emerald-300 border-emerald-500/20">Đã thanh toán</span>`;
                        }

                        // Cột thao tác
                        const actionCell = targetRow.querySelector('.cell-actions');
                        if (actionCell) {
                            actionCell.innerHTML = `
                                <div class="flex items-center justify-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-xl">
                                        <i class="fa-solid fa-circle-check text-xs"></i> Hoàn tất
                                    </span>
                                </div>
                            `;
                        }
                    }

                    // Tải lại trang sau 1.2s để đồng bộ lại các thẻ thống kê tổng quan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);

                } else {
                    // Server validation error
                    if (data.field === 'payment_method' || data.code === 'ERR_29_01') {
                        methodSelect.classList.add('input-error');
                        errorMethod.querySelector('.msg-content').innerText = data.message || 'Vui lòng chọn hình thức nhận tiền (Tiền mặt hoặc Chuyển khoản).';
                        errorMethod.classList.remove('hidden');
                    } else if (data.field === 'payment_amount') {
                        amountInput.classList.add('input-error');
                        errorAmount.querySelector('.msg-content').innerText = data.message;
                        errorAmount.classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra khi xác nhận gạch nợ.', 'error');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ, vui lòng thử lại!', 'error');
            } finally {
                btnConfirm.disabled = false;
                btnConfirm.innerHTML = oldBtnText;
            }
        }

        // Đóng modal khi nhấn Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmPayModal();
                closeVietQRModal();
            }
        });
    </script>
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
