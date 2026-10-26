<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Báo cáo thống kê thiết bị, tồn kho và công nợ phòng trọ - SmartRoom.">
    <title>Báo Cáo - Thống Kê - SmartRoom</title>
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

        /* Light Mode styling */
        html.theme-light input[type="month"],
        body.theme-light input[type="month"],
        html.theme-light input[type="date"],
        body.theme-light input[type="date"] {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            color-scheme: light !important;
        }
        html.theme-light input[type="month"]::-webkit-calendar-picker-indicator,
        body.theme-light input[type="month"]::-webkit-calendar-picker-indicator {
            filter: none !important;
            opacity: 0.8;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <h1 class="text-lg font-bold text-slate-100">Báo Cáo - Thống Kê</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tổng hợp thiết bị, phân bổ và phòng còn nợ tiền điện nước, dịch vụ.</p>
            </div>
            <div class="flex items-center gap-3">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                    <input type="month" name="billing_month" value="{{ $billingMonth ?: now()->format('Y-m') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" title="Chọn tháng báo cáo">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    @if($billingMonth)
                        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold">Tất cả</a>
                    @endif
                </form>
            </div>
        </header>

        <main class="p-8 flex-grow overflow-y-auto space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Tổng thiết bị</div>
                    <div class="mt-2 text-3xl font-extrabold text-slate-100">{{ number_format($summary['equipment_total']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Số lượng đang quản lý</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Đã phân bổ</div>
                    <div class="mt-2 text-3xl font-extrabold text-amber-400">{{ number_format($summary['equipment_allocated']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Thiết bị đang ở phòng</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Còn tồn</div>
                    <div class="mt-2 text-3xl font-extrabold text-emerald-400">{{ number_format($summary['equipment_stock']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Có thể bàn giao tiếp</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Phòng còn nợ</div>
                    <div class="mt-2 text-3xl font-extrabold text-rose-400">{{ number_format($summary['unpaid_room_count']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Theo hóa đơn chưa thanh toán</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Tổng công nợ</div>
                    <div class="mt-2 text-2xl font-extrabold text-indigo-300">{{ number_format($summary['unpaid_total_amount']) }}đ</div>
                    <div class="mt-1 text-[11px] text-slate-500">Gồm phòng, điện, nước, dịch vụ</div>
                </div>
            </section>

            <!-- Financial Ledger & PnL Reports -->
            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Ledger Table & Stats -->
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-2 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-calculator text-emerald-400"></i> Sổ Quỹ Thu Chi & Báo Cáo Lợi Nhuận
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">Tổng quan về dòng tiền thực tế thu được từ tiền thuê phòng và các khoản chi phí vận hành.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reports.export_excel', ['billing_month' => $billingMonth]) }}" id="btnExportExcel" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-600/20 transition-all">
                                <i class="fa-solid fa-file-excel"></i> Xuất Excel
                            </a>
                        </div>
                    </div>

                    <!-- Mini cards for Financials -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Tổng Thu Thực Tế</span>
                            <strong class="text-emerald-400 text-xl font-bold mt-1 block">
                                +{{ number_format($financialSummary['total_income']) }}đ
                            </strong>
                        </div>
                        <div class="bg-rose-500/5 border border-rose-500/10 rounded-xl p-4">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Tổng Chi Thực Tế</span>
                            <strong class="text-rose-400 text-xl font-bold mt-1 block">
                                -{{ number_format($financialSummary['total_expense']) }}đ
                            </strong>
                        </div>
                        <div class="bg-indigo-500/5 border border-indigo-500/10 rounded-xl p-4">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Lợi Nhuận Thuần</span>
                            <strong class="text-indigo-300 text-xl font-bold mt-1 block">
                                {{ number_format($financialSummary['net_profit']) }}đ
                            </strong>
                        </div>
                    </div>

                    <!-- Transaction List -->
                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Ngày</th>
                                    <th class="px-4 py-3 font-bold">Danh mục</th>
                                    <th class="px-4 py-3 font-bold">Mô tả</th>
                                    <th class="px-4 py-3 font-bold text-right">Số tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($transactions as $tx)
                                    <tr class="hover:bg-slate-900/40 transition-all">
                                        <td class="px-4 py-3 text-xs text-slate-400">
                                            {{ $tx->transaction_date ? $tx->transaction_date->format('d/m/Y') : '' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300">
                                                {{ $tx->category }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-400 max-w-[180px] truncate">
                                            {{ $tx->description ?: 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount) }}đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">Chưa có giao dịch thu chi nào được ghi nhận.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Transaction Form (Bảng 134) -->
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-1 space-y-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-plus-circle text-indigo-400"></i> Ghi Nhận Thu Chi Sổ Quỹ
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Ghi nhận các chi phí phát sinh ngoài hóa đơn (tiền sửa đồ, mua sắm đồ mới, thu phí dịch vụ...).</p>
                    </div>

                    <form action="{{ route('admin.reports.transaction.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-[11px] text-slate-400 uppercase font-bold tracking-wider block mb-2">Loại giao dịch (Thu / Chi)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-emerald-500/50 cursor-pointer">
                                    <input type="radio" id="rdoType" name="type" value="income" checked class="text-emerald-500 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-emerald-400">Khoản thu (+)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-rose-500/50 cursor-pointer">
                                    <input type="radio" id="rdoTypeExpense" name="type" value="expense" class="text-rose-500 focus:ring-rose-500">
                                    <span class="text-xs font-semibold text-rose-400">Khoản chi (-)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="amount" class="text-[11px] text-slate-400 uppercase font-bold tracking-wider block mb-1">Số tiền (VNĐ) <span class="text-rose-400">*</span></label>
                            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Nhập số tiền giao dịch..." class="w-full bg-slate-950/80 border @error('amount') border-rose-500 ring-1 ring-rose-500 @else border-slate-800 @enderror rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            @error('amount')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="text-[11px] text-slate-400 uppercase font-bold tracking-wider block mb-1">Danh mục giao dịch <span class="text-rose-400">*</span></label>
                            <select id="category" name="category" class="w-full bg-slate-950/80 border @error('category') border-rose-500 ring-1 ring-rose-500 @else border-slate-800 @enderror rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="">-- Chọn danh mục --</option>
                                <option value="Tiền phòng" {{ old('category') === 'Tiền phòng' ? 'selected' : '' }}>Tiền phòng</option>
                                <option value="Tiền điện" {{ old('category') === 'Tiền điện' ? 'selected' : '' }}>Tiền điện</option>
                                <option value="Tiền nước" {{ old('category') === 'Tiền nước' ? 'selected' : '' }}>Tiền nước</option>
                                <option value="Sửa chữa" {{ old('category') === 'Sửa chữa' ? 'selected' : '' }}>Sửa chữa</option>
                                <option value="Vệ sinh" {{ old('category') === 'Vệ sinh' ? 'selected' : '' }}>Vệ sinh</option>
                                <option value="Mua sắm đồ" {{ old('category') === 'Mua sắm đồ' ? 'selected' : '' }}>Mua sắm đồ</option>
                                <option value="Khác" {{ old('category') === 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('category')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date" class="text-[11px] text-slate-400 uppercase font-bold tracking-wider block mb-1">Ngày ghi nhận <span class="text-rose-400">*</span></label>
                            <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full bg-slate-950/80 border @error('date') border-rose-500 ring-1 ring-rose-500 @else border-slate-800 @enderror rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            @error('date')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="note" class="text-[11px] text-slate-400 uppercase font-bold tracking-wider block mb-1">Ghi chú / Diễn giải</label>
                            <textarea id="note" name="note" rows="2" placeholder="Nhập ghi chú thêm nếu có..." class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit" id="btnSaveLedger" class="w-full py-3 rounded-xl bg-blue-700 hover:bg-blue-600 font-bold text-xs text-white shadow-lg shadow-blue-700/30 hover:shadow-blue-600/40 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu Giao Dịch
                        </button>
                    </form>
                </div>
            </section>

            <section class="panel glass-card rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> Tình Hình Thiết Bị
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Theo dõi tổng số lượng, đã phân bổ và số còn tồn của từng thiết bị.</p>
                    </div>
                    <a href="{{ route('admin.equipment.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Quản lý thiết bị
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                            <tr>
                                <th class="px-4 py-4 font-bold">Mã</th>
                                <th class="px-4 py-4 font-bold">Tên thiết bị</th>
                                <th class="px-4 py-4 font-bold">Tổng</th>
                                <th class="px-4 py-4 font-bold">Đã phân bổ</th>
                                <th class="px-4 py-4 font-bold">Còn tồn</th>
                                <th class="px-4 py-4 font-bold">Số phòng dùng</th>
                                <th class="px-4 py-4 font-bold">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                            @forelse($equipmentReports as $item)
                                <tr class="hover:bg-slate-900/40 transition-all">
                                    <td class="px-4 py-4 font-bold text-indigo-400">{{ $item['code'] }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-200">{{ $item['name'] }}</div>
                                        <div class="text-[11px] text-slate-500">Đơn vị: {{ $item['unit'] }}</div>
                                    </td>
                                    <td class="px-4 py-4 font-bold">{{ number_format($item['total_quantity']) }}</td>
                                    <td class="px-4 py-4 font-bold text-amber-400">{{ number_format($item['allocated_quantity']) }}</td>
                                    <td class="px-4 py-4 font-bold text-emerald-400">{{ number_format($item['stock_quantity']) }}</td>
                                    <td class="px-4 py-4">{{ number_format($item['using_rooms_count']) }}</td>
                                    <td class="px-4 py-4">
                                        @if($item['stock_quantity'] > 0)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Còn tồn</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Hết tồn</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">Chưa có thiết bị để thống kê.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-1">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-right-left text-emerald-400"></i> Phân Bổ Theo Phòng
                    </h2>
                    <div class="mt-5 space-y-3 max-h-[520px] overflow-y-auto pr-1">
                        @forelse($allocationReports as $allocation)
                            <div class="glass-card rounded-xl bg-slate-950/50 border border-slate-800 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-200">Phòng {{ $allocation->room->room_number ?? 'N/A' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $allocation->room->building->name ?? 'Chưa có tòa nhà' }}</div>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ number_format($allocation->quantity) }} {{ $allocation->equipment->unit ?? '' }}
                                    </span>
                                </div>
                                <div class="mt-3 text-xs font-bold text-indigo-300">{{ $allocation->equipment->name ?? 'Thiết bị' }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">Mã: {{ $allocation->equipment->code ?? 'N/A' }}</div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500">Chưa có thiết bị nào được phân bổ cho phòng.</div>
                        @endforelse
                    </div>
                </div>

                <div class="panel glass-card rounded-2xl p-6 xl:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-rose-400"></i> Phòng Còn Nợ Điện Nước Và Dịch Vụ
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">
                                @if($billingMonth)
                                    Đang xem công nợ tháng {{ $billingMonth }}.
                                @else
                                    Đang xem toàn bộ hóa đơn chưa thanh toán.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-4 font-bold">Tháng</th>
                                    <th class="px-4 py-4 font-bold">Phòng</th>
                                    <th class="px-4 py-4 font-bold">Người thuê</th>
                                    <th class="px-4 py-4 font-bold">Điện</th>
                                    <th class="px-4 py-4 font-bold">Nước</th>
                                    <th class="px-4 py-4 font-bold">Dịch vụ</th>
                                    <th class="px-4 py-4 font-bold">Tổng nợ</th>
                                    <th class="px-4 py-4 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($unpaidUtilities as $record)
                                    <tr class="hover:bg-slate-900/40 transition-all">
                                        <td class="px-4 py-4 font-bold text-indigo-300">{{ $record->billing_month }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">Phòng {{ $record->room->room_number ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $record->room->building->name ?? 'Chưa có tòa nhà' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-xs text-slate-400">
                                            {{ optional($record->room->residents->first())->name ?? 'Chưa gán cư dân' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">{{ number_format($record->electricity_amount) }}đ</div>
                                            <div class="text-[11px] text-slate-500">{{ number_format($record->electricity_usage) }} kWh</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">{{ number_format($record->water_amount) }}đ</div>
                                            <div class="text-[11px] text-slate-500">{{ number_format($record->water_usage) }} m3</div>
                                        </td>
                                        <td class="px-4 py-4 font-bold">{{ number_format($record->service_amount) }}đ</td>
                                        <td class="px-4 py-4 font-extrabold text-rose-400">{{ number_format($record->total_amount) }}đ</td>
                                        <td class="px-4 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                {{ $record->status === 'overdue' ? 'Quá hạn' : 'Đã gửi' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-500">Không có phòng còn nợ trong phạm vi đang xem.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
