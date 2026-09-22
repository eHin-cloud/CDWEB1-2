<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Tra Cứu Hóa Đơn Điện Tử - SmartRoom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- HEADER -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fa-solid fa-file-invoice text-white text-lg"></i>
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-indigo-300 bg-clip-text text-transparent">SmartRoom e-Invoice</span>
                    <span class="text-[10px] block font-medium text-emerald-400">Cổng Tra Cứu Hóa Đơn Chuẩn NĐ 123/2020/NĐ-CP</span>
                </div>
            </a>
            <div class="flex items-center gap-3 text-xs">
                <a href="/login" class="px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                    <i class="fa-solid fa-user mr-1.5"></i>Đăng nhập
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN SECTION -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-12">

        <!-- TITLE HERO -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold mb-4">
                <i class="fa-solid fa-shield-check"></i> Tra cứu trực tiếp từ Cơ quan Thuế & Nhà cung cấp
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">
                Tra Cứu Hóa Đơn Điện Tử
            </h1>
            <p class="text-slate-400 text-sm max-w-xl mx-auto">
                Nhập <strong class="text-slate-200">Mã tra cứu hóa đơn</strong> hoặc <strong class="text-slate-200">Mã CQT</strong> được gửi qua Email / Zalo để kiểm tra và tải bản thể hiện hợp pháp.
            </p>
        </div>

        <!-- SEARCH FORM -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-2xl backdrop-blur-xl mb-8">
            <form action="{{ route('einvoice.lookup') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                    <input type="text" name="code" value="{{ $code }}" required
                        placeholder="Nhập mã tra cứu (VD: SRM-XXXXXXXX) hoặc Mã CQT..."
                        class="w-full pl-11 pr-4 py-3 bg-slate-950 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-mono">
                </div>
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2 shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i> Tra Cứu Ngay
                </button>
            </form>
        </div>

        <!-- RESULT SECTION -->
        @if($searched)
            @if($invoice)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl -z-10"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-800 gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $invoice->statusBadgeClass() }}">
                                {{ $invoice->statusLabel() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $invoice->cqtBadgeClass() }}">
                                <i class="fa-solid fa-check-circle mr-1"></i>{{ $invoice->cqtLabel() }}
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-white">
                            Hóa Đơn Số: <span class="text-indigo-400 font-mono">{{ $invoice->invoice_number }}</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Ký hiệu mẫu số: <strong class="text-slate-300 font-mono">{{ $invoice->invoice_symbol }}</strong> • Ngày lập: {{ $invoice->issue_date->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('einvoice.public.view', $invoice->lookup_code) }}" target="_blank"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-eye"></i> Xem Bản Thể Hiện
                        </a>
                        <a href="{{ route('einvoice.public.pdf', $invoice->lookup_code) }}"
                            class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pdf text-rose-400"></i> PDF
                        </a>
                        <a href="{{ route('einvoice.public.xml', $invoice->lookup_code) }}"
                            class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-file-code text-emerald-400"></i> XML Ký Số
                        </a>
                    </div>
                </div>

                <!-- DETAILS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6 text-sm">
                    <!-- Bên bán -->
                    <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-rose-400 mb-3 flex items-center gap-1.5">
                            <i class="fa-solid fa-store"></i> Đơn vị bán hàng
                        </h3>
                        <p class="font-bold text-white">{{ $invoice->seller_name }}</p>
                        <p class="text-xs text-slate-400 mt-1">MST: <strong class="text-slate-200 font-mono">{{ $invoice->seller_tax_code }}</strong></p>
                        <p class="text-xs text-slate-400 mt-1">Địa chỉ: {{ $invoice->seller_address }}</p>
                        <p class="text-xs text-slate-400 mt-1">SĐT: {{ $invoice->seller_phone ?: '19008888' }}</p>
                    </div>

                    <!-- Bên mua -->
                    <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-3 flex items-center gap-1.5">
                            <i class="fa-solid fa-user"></i> Người mua hàng
                        </h3>
                        <p class="font-bold text-white">{{ $invoice->buyer_name }}</p>
                        @if($invoice->buyer_legal_name)
                        <p class="text-xs text-slate-400 mt-1">Tên đơn vị: {{ $invoice->buyer_legal_name }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-1">CCCD/Định danh: <strong class="text-slate-200 font-mono">{{ $invoice->buyer_id_card ?: 'N/A' }}</strong></p>
                        <p class="text-xs text-slate-400 mt-1">Email: {{ $invoice->buyer_email ?: 'N/A' }}</p>
                        <p class="text-xs text-slate-400 mt-1">SĐT: {{ $invoice->buyer_phone ?: 'N/A' }}</p>
                    </div>
                </div>

                <!-- CQT TAX AUTHORITY BADGE -->
                <div class="bg-emerald-950/30 border border-emerald-500/30 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block mb-1">
                            <i class="fa-solid fa-stamp mr-1"></i> Mã Cơ Quan Thuế Cấp (NĐ 123/2020/NĐ-CP):
                        </span>
                        <span class="font-mono text-emerald-300 font-extrabold text-sm sm:text-base tracking-wider break-all">
                            {{ $invoice->tax_authority_code ?: 'Đang chờ CQT đồng bộ' }}
                        </span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs text-slate-400 block">Tổng tiền thanh toán</span>
                        <span class="text-xl font-extrabold text-white text-emerald-400">{{ $invoice->formatted_total }}</span>
                    </div>
                </div>
            </div>
            @else
            <!-- NOT FOUND -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-10 text-center shadow-xl">
                <div class="w-16 h-16 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Không Tìm Thấy Hóa Đơn</h3>
                <p class="text-slate-400 text-sm max-w-md mx-auto">
                    Mã tra cứu "<strong class="text-slate-200 font-mono">{{ $code }}</strong>" không tồn tại trên hệ thống. Vui lòng kiểm tra lại chính xác mã số được cấp trong Email hoặc Zalo.
                </p>
            </div>
            @endif
        @endif

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-900 bg-slate-950/80 py-6 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4">
            <p>Hệ Thống Quản Lý Cơ Sở Lưu Trú & Hóa Đơn Điện Tử SmartRoom • Tuân thủ Nghị định 123/2020/NĐ-CP & Thông tư 78/2021/TT-BTC</p>
            <p class="mt-1">Tích hợp VNPT Invoice • Viettel S-Invoice • MISA meInvoice</p>
        </div>
    </footer>

</body>
</html>
