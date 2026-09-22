<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chứng Từ & Hóa Đơn Điện Tử (Nghị Định 123) - SmartRoom Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

    <!-- TOP NAV -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('smartroom.admin') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-xs font-semibold">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại Dashboard
                </a>
                <div class="h-4 w-px bg-slate-800"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-600/20 text-red-400 flex items-center justify-center border border-red-500/20">
                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                    </div>
                    <span class="font-extrabold text-sm sm:text-base text-white">Quản Lý Chứng Từ & Hóa Đơn Điện Tử CQT</span>
                    <span class="hidden sm:inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        NĐ 123/2020/NĐ-CP & TT 78
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('configModal').classList.remove('hidden')"
                    class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-sliders"></i> Cấu Hình e-Invoice API
                </button>
                <a href="{{ route('einvoice.lookup') }}" target="_blank"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-all flex items-center gap-1.5 border border-slate-700">
                    <i class="fa-solid fa-magnifying-glass"></i> Cổng Tra Cứu
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
            {{ session('success') }}
        </div>
        @endif

        <!-- KPI SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Tổng Hóa Đơn Xuất</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-white mt-3">{{ number_format($stats['total_count']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Hóa đơn điện tử trong kỳ</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Đã Có Mã Cơ Quan Thuế</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-emerald-400 mt-3">{{ number_format($stats['cqt_accepted']) }}</div>
                <p class="text-[11px] text-emerald-500/80 mt-1">100% hợp lệ theo NĐ 123</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Doanh Thu Kê Khai</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-white mt-3">{{ number_format($stats['total_revenue']) }} đ</div>
                <p class="text-[11px] text-slate-500 mt-1">Tổng tiền thanh toán sau thuế</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Thuế GTGT Phát Sinh</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-rose-400 mt-3">{{ number_format($stats['total_tax']) }} đ</div>
                <p class="text-[11px] text-slate-500 mt-1">Nghĩa vụ thuế GTGT tính toán</p>
            </div>
        </div>

        <!-- INTEGRATION INFO BANNER -->
        @php
            $cfg = $tenant?->einvoice_config ?? [];
            $activeProvider = $cfg['provider'] ?? 'mock';
            $providerNames = [
                'misa' => 'MISA meInvoice Open API',
                'vnpt' => 'VNPT Invoice API (TT78)',
                'viettel' => 'Viettel S-Invoice Enterprise',
                'mock' => 'Sandbox Mock Driver (Mô phỏng NĐ 123)',
            ];
        @endphp
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-500/20 rounded-2xl p-4 sm:p-5 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/30">
                    <i class="fa-solid fa-network-wired text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        Nhà Cung Cấp Đang Kích Hoạt: 
                        <span class="text-indigo-400 uppercase tracking-wide">{{ $providerNames[$activeProvider] ?? $activeProvider }}</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        MST Bên bán: <strong class="text-slate-200 font-mono">{{ $cfg['tax_code'] ?? '0101234567-001' }}</strong> • 
                        Ký hiệu mẫu số: <strong class="text-slate-200 font-mono">{{ $cfg['template_symbol'] ?? '1C26TAA' }}</strong> • 
                        Thuế suất mặc định: <strong class="text-emerald-400">{{ $cfg['tax_rate'] ?? 8 }}%</strong>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ ($cfg['auto_issue_on_payment'] ?? true) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400' }}">
                    <i class="fa-solid fa-circle text-[8px] {{ ($cfg['auto_issue_on_payment'] ?? true) ? 'text-emerald-400 animate-pulse' : 'text-slate-500' }}"></i>
                    Tự động xuất khi thanh toán: {{ ($cfg['auto_issue_on_payment'] ?? true) ? 'BẬT' : 'TẮT' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ ($cfg['auto_send_email'] ?? true) ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-slate-800 text-slate-400' }}">
                    <i class="fa-solid fa-envelope"></i> Email: {{ ($cfg['auto_send_email'] ?? true) ? 'BẬT' : 'TẮT' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ ($cfg['auto_send_zalo'] ?? true) ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-slate-800 text-slate-400' }}">
                    <i class="fa-solid fa-comment-dots"></i> Zalo: {{ ($cfg['auto_send_zalo'] ?? true) ? 'BẬT' : 'TẮT' }}
                </span>
            </div>
        </div>

        <!-- FILTER & SEARCH -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6">
            <form action="{{ route('smartroom.admin.einvoices.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <div class="relative sm:col-span-2">
                    <i class="fa-solid fa-search absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm theo số HĐ, mã CQT, tên khách thuê, mã tra cứu..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <select name="cqt_status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Trạng thái CQT --</option>
                        <option value="CQT_ACCEPTED" {{ request('cqt_status') === 'CQT_ACCEPTED' ? 'selected' : '' }}>Đã cấp mã CQT hợp lệ</option>
                        <option value="CQT_PENDING" {{ request('cqt_status') === 'CQT_PENDING' ? 'selected' : '' }}>Đang chờ mã CQT</option>
                        <option value="CQT_REJECTED" {{ request('cqt_status') === 'CQT_REJECTED' ? 'selected' : '' }}>Bị CQT từ chối</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition-all">
                        <i class="fa-solid fa-filter mr-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['search', 'cqt_status', 'provider']))
                    <a href="{{ route('smartroom.admin.einvoices.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-xl text-xs transition-all flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- INVOICES TABLE -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-4">Số HĐ / Ký Hiệu</th>
                            <th class="py-3.5 px-4">Phòng / Khách Thuê</th>
                            <th class="py-3.5 px-4">Mã CQT (NĐ 123)</th>
                            <th class="py-3.5 px-4">Doanh Thu & Thuế</th>
                            <th class="py-3.5 px-4">Nhà Cung Cấp</th>
                            <th class="py-3.5 px-4">Trạng Thái CQT</th>
                            <th class="py-3.5 px-4 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4 font-mono">
                                <div class="font-bold text-white text-sm text-indigo-400">
                                    {{ $inv->invoice_number }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-sans">
                                    Ký hiệu: <span class="font-mono text-slate-300 font-semibold">{{ $inv->invoice_symbol }}</span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-sans">
                                    {{ $inv->issue_date->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">
                                    Phòng {{ $inv->room?->room_number ?? 'N/A' }}
                                </div>
                                <div class="text-[11px] text-slate-300">
                                    {{ $inv->buyer_name }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">
                                    {{ $inv->buyer_phone ?: 'Không có SĐT' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-mono">
                                @if($inv->tax_authority_code)
                                <div class="text-[11px] text-emerald-400 font-bold bg-emerald-950/40 px-2 py-1 rounded border border-emerald-500/20 inline-block max-w-[220px] truncate" title="{{ $inv->tax_authority_code }}">
                                    {{ $inv->tax_authority_code }}
                                </div>
                                @else
                                <span class="text-slate-500 italic">Đang đồng bộ</span>
                                @endif
                                <div class="text-[10px] text-slate-500 font-sans mt-0.5">
                                    Mã tra cứu: <span class="font-mono text-slate-400 font-semibold">{{ $inv->lookup_code }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">
                                    {{ $inv->formatted_total }}
                                </div>
                                <div class="text-[11px] text-rose-400 font-medium">
                                    VAT {{ $inv->tax_rate }}%: +{{ $inv->formatted_tax }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-800 border border-slate-700 text-slate-300">
                                    {{ $inv->provider }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $inv->cqtBadgeClass() }}">
                                    <i class="fa-solid fa-shield-check"></i> {{ $inv->cqtLabel() }}
                                </span>
                                <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-2">
                                    <span title="Đã gửi Email"><i class="fa-solid fa-envelope {{ $inv->sent_email_at ? 'text-emerald-400' : 'text-slate-600' }}"></i></span>
                                    <span title="Đã gửi Zalo"><i class="fa-solid fa-comment-dots {{ $inv->sent_zalo_at ? 'text-indigo-400' : 'text-slate-600' }}"></i></span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('smartroom.admin.einvoices.view', $inv->id) }}" target="_blank"
                                        class="p-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-400 border border-indigo-500/30 transition-colors" title="Xem Bản Thể Hiện Chuẩn NĐ 123">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('smartroom.admin.einvoices.pdf', $inv->id) }}"
                                        class="p-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-400 border border-rose-500/30 transition-colors" title="Tải PDF Bản Thể Hiện">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('smartroom.admin.einvoices.xml', $inv->id) }}"
                                        class="p-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/40 text-emerald-400 border border-emerald-500/30 transition-colors" title="Tải File XML Ký Số Chuẩn TCT">
                                        <i class="fa-solid fa-file-code"></i>
                                    </a>
                                    <button onclick="resendNotification({{ $inv->id }})"
                                        class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors" title="Gửi lại Email / Zalo">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 text-slate-700"></i>
                                <p>Chưa có hóa đơn điện tử nào được phát hành trong hệ thống.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $invoices->links() }}
            </div>
            @endif
        </div>

    </main>

    <!-- MODAL CẤU HÌNH NHÀ CUNG CẤP HĐĐT -->
    <div id="configModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <h3 class="font-bold text-white text-base">Cấu Hình Nhà Cung Cấp Hóa Đơn Điện Tử</h3>
                </div>
                <button onclick="document.getElementById('configModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('smartroom.admin.einvoices.config') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-300 mb-1.5">Nhà cung cấp giải pháp HĐĐT</label>
                    <select name="provider" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 font-semibold">
                        <option value="mock" {{ ($cfg['provider'] ?? '') === 'mock' ? 'selected' : '' }}>Môi trường Sandbox / Mock (Chuẩn NĐ 123/2020/NĐ-CP & TT 78)</option>
                        <option value="misa" {{ ($cfg['provider'] ?? '') === 'misa' ? 'selected' : '' }}>MISA meInvoice Open API</option>
                        <option value="vnpt" {{ ($cfg['provider'] ?? '') === 'vnpt' ? 'selected' : '' }}>VNPT Invoice (TT 78)</option>
                        <option value="viettel" {{ ($cfg['provider'] ?? '') === 'viettel' ? 'selected' : '' }}>Viettel S-Invoice</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Mã số thuế bên bán (MST)</label>
                        <input type="text" name="tax_code" value="{{ $cfg['tax_code'] ?? '0101234567-001' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Ký hiệu mẫu số & HĐ</label>
                        <input type="text" name="template_symbol" value="{{ $cfg['template_symbol'] ?? '1C26TAA' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Tên đơn vị bán / Hộ kinh doanh</label>
                    <input type="text" name="company_name" value="{{ $cfg['company_name'] ?? ($tenant->name ?? 'Cơ sở SmartRoom') }}" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Địa chỉ trụ sở kê khai thuế</label>
                    <input type="text" name="address" value="{{ $cfg['address'] ?? 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Cầu Giấy, Hà Nội' }}" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Thuế suất GTGT mặc định (%)</label>
                        <input type="number" step="0.5" name="tax_rate" value="{{ $cfg['tax_rate'] ?? 8 }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">API App ID / Username</label>
                        <input type="text" name="app_id" value="{{ $cfg['app_id'] ?? '' }}" placeholder="app-id / user"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">API Secret Key / Token kết nối</label>
                    <input type="password" name="secret_key" value="{{ $cfg['secret_key'] ?? '' }}" placeholder="sk_live_... hoặc token"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 font-mono focus:outline-none focus:border-indigo-500">
                </div>

                <!-- CHECKBOXES -->
                <div class="pt-2 border-t border-slate-800 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_issue_on_payment" value="1" {{ ($cfg['auto_issue_on_payment'] ?? true) ? 'checked' : '' }}
                            class="rounded border-slate-700 text-indigo-600 focus:ring-0">
                        <span class="text-slate-300">Tự động xuất hóa đơn có mã Cơ quan Thuế ngay khi thanh toán</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_send_email" value="1" {{ ($cfg['auto_send_email'] ?? true) ? 'checked' : '' }}
                            class="rounded border-slate-700 text-indigo-600 focus:ring-0">
                        <span class="text-slate-300">Tự động gửi bản thể hiện hóa đơn tới Email khách thuê</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_send_zalo" value="1" {{ ($cfg['auto_send_zalo'] ?? true) ? 'checked' : '' }}
                            class="rounded border-slate-700 text-indigo-600 focus:ring-0">
                        <span class="text-slate-300">Tự động gửi tin nhắn Zalo (ZNS) kèm mã tra cứu và link xem HĐĐT</span>
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('configModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition-all">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold transition-all shadow-lg shadow-indigo-600/20">
                        Lưu Cấu Hình
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function resendNotification(id) {
            if (!confirm('Bạn có chắc muốn gửi lại Email và Zalo thông báo hóa đơn này cho khách thuê?')) return;

            fetch(`/smartroom/admin/einvoices/${id}/notify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ send_email: true, send_zalo: true })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Đã gửi thông báo thành công!');
                location.reload();
            })
            .catch(err => {
                alert('Có lỗi xảy ra khi gửi thông báo: ' + err);
            });
        }
    </script>

</body>
</html>
