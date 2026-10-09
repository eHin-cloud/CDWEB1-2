<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hóa Đơn & Lịch Sử Thanh Toán - Cư Dân SmartRoom</title>
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
        <!-- Top Navigation -->
        <header class="sticky top-0 z-20 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('smartroom.resident.portal') }}" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-300 hover:text-white transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="font-black text-white text-base tracking-tight">Sổ Hóa Đơn & VietQR</div>
                        <div class="text-[11px] text-slate-400">Cổng Dịch Vụ Cư Dân (FEAT_27_PORTAL)</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-indigo-400 font-bold bg-indigo-500/10 border border-indigo-500/20 px-3 py-1 rounded-xl">
                        P.{{ $room->room_number }}
                    </span>
                    <a href="{{ route('signout') }}" class="px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">
            <!-- 1. STATUS BANNER (Spec Section 6 STT 1) -->
            <section class="panel rounded-2xl p-5 border-l-4 border-l-indigo-500 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl shadow-black/40">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl shrink-0">
                        <i class="fa-solid fa-building-user"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-indigo-400">Thông tin phòng thuê (room_status)</div>
                        <h1 class="text-lg sm:text-xl font-extrabold text-white mt-0.5">
                            Phòng {{ $room->room_number }} - Tòa nhà {{ $room->building->name ?? 'SmartRoom House' }}
                        </h1>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $room->building->address ?? 'Hà Nội' }} • Cư dân: <span class="font-bold text-slate-200">{{ $resident->name }}</span> ({{ $resident->phone }})
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('smartroom.resident.portal') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                    <a href="{{ route('smartroom.resident.contract.pdf') }}" class="px-4 py-2 rounded-xl bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-300 border border-indigo-500/20 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf text-rose-400"></i> Tải hợp đồng PDF
                    </a>
                </div>
            </section>

            <!-- 2. BILL SUMMARY CARDS (Spec Section 6 STT 2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="panel rounded-2xl p-5">
                    <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                        <span>Hóa đơn kỳ này</span>
                        <i class="fa-solid fa-receipt text-indigo-400"></i>
                    </div>
                    @if($latestBill)
                        <div class="mt-2 text-2xl font-black text-indigo-300">
                            {{ number_format($latestBill->total_amount) }} VND
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Tháng: {{ $latestBill->billing_month }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $latestBill->status === 'paid' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-rose-500/10 text-rose-300 border-rose-500/20' }}">
                                {{ $latestBill->status_label }}
                            </span>
                        </div>
                    @else
                        <div class="mt-2 text-xl font-bold text-slate-500">Chưa có phát sinh</div>
                    @endif
                </div>

                <div class="panel rounded-2xl p-5">
                    <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                        <span>Tổng nợ cần thanh toán</span>
                        <i class="fa-solid fa-money-bill-wave text-amber-400"></i>
                    </div>
                    <div class="mt-2 text-2xl font-black text-amber-300">
                        {{ number_format($unpaidTotal) }} VND
                    </div>
                    <div class="mt-1 text-xs text-slate-400">
                        {{ $bills->where('status', '!=', 'paid')->count() }} hóa đơn chưa thanh toán
                    </div>
                </div>

                <div class="panel rounded-2xl p-5">
                    <div class="text-xs font-bold uppercase text-slate-500 flex items-center justify-between">
                        <span>Thông tin thụ hưởng</span>
                        <i class="fa-solid fa-qrcode text-emerald-400"></i>
                    </div>
                    <div class="mt-2 text-sm font-bold text-slate-200">
                        {{ $tenant->bank_name ?? 'Vietcombank' }}
                    </div>
                    <div class="mt-1 font-mono text-xs text-emerald-400 font-bold tracking-wider">
                        STK: {{ $tenant->bank_account_no ?? '1051572297' }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5 truncate">
                        Chủ TK: {{ $tenant->bank_account_name ?? $landlordName }}
                    </div>
                </div>
            </div>

            <!-- 3. INVOICES TABLE -->
            <section class="panel rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-black text-white">Lịch sử hóa đơn tiền phòng</h2>
                        <p class="text-xs text-slate-400 mt-1">Danh sách chi tiết tiền phòng, chỉ số điện nước và mã QR thanh toán Napas 247.</p>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-xl">
                        {{ $bills->count() }} kỳ hóa đơn
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-900">
                            <tr>
                                <th class="px-5 py-3.5">Kỳ / Tháng</th>
                                <th class="px-5 py-3.5">Tiền phòng</th>
                                <th class="px-5 py-3.5">Điện (kWh)</th>
                                <th class="px-5 py-3.5">Nước (m³)</th>
                                <th class="px-5 py-3.5">Dịch vụ</th>
                                <th class="px-5 py-3.5">Tổng cộng</th>
                                <th class="px-5 py-3.5">Trạng thái</th>
                                <th class="px-5 py-3.5 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900">
                            @forelse($bills as $bill)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="px-5 py-4 font-mono font-bold text-indigo-400 whitespace-nowrap">
                                        {{ $bill->billing_month }}
                                    </td>
                                    <td class="px-5 py-4 font-semibold whitespace-nowrap">
                                        {{ number_format($bill->room_amount) }} đ
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                        <div class="font-bold text-amber-300">{{ number_format($bill->electricity_amount) }} đ</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $bill->electricity_usage }} kWh ({{ $bill->old_electricity }} &rarr; {{ $bill->new_electricity }})</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                        <div class="font-bold text-cyan-300">{{ number_format($bill->water_amount) }} đ</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $bill->water_usage }} m³ ({{ $bill->old_water }} &rarr; {{ $bill->new_water }})</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap">
                                        {{ number_format($bill->service_amount) }} đ
                                    </td>
                                    <td class="px-5 py-4 font-black text-sm text-white whitespace-nowrap">
                                        {{ number_format($bill->total_amount) }} đ
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $bill->status === 'paid' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-rose-500/10 text-rose-300 border-rose-500/20' }}">
                                            {{ $bill->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <button type="button" onclick="openVietQrModal({{ $bill->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-md shadow-indigo-600/20">
                                            <i class="fa-solid fa-qrcode"></i> Quét VietQR
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center text-xs text-slate-500">
                                        <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-700 block"></i>
                                        Chưa có bản ghi hóa đơn nào cho phòng này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL VIETQR CHUẨN NAPAS 247 (ERR_27_03) -->
    <div id="vietqr-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-opacity">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 p-6 rounded-3xl shadow-2xl relative animate-fade-in" onclick="event.stopPropagation()">
            <button type="button" onclick="closeVietQrModal()" class="absolute top-5 right-5 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="text-center mb-4">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                    ERR_27_03 • Napas 247
                </span>
                <h3 class="text-base font-extrabold text-white mt-1.5">Mã VietQR Thanh Toán Tiền Nhà</h3>
                <p id="qr-modal-desc" class="text-xs text-slate-400 mt-0.5">Hiển thị mã VietQR thanh toán tiền nhà chuẩn Napas 247.</p>
            </div>

            <div class="bg-white p-3 rounded-2xl shadow-inner flex items-center justify-center">
                <img id="qr-modal-image" src="" alt="VietQR Napas 247" class="w-60 h-60 object-contain mx-auto" onerror="this.src='https://img.vietqr.io/image/VCB-1051572297-compact.png';">
            </div>

            <div class="mt-4 p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-400">Ngân hàng:</span>
                    <strong id="qr-modal-bank" class="text-emerald-400">Vietcombank</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Số tài khoản:</span>
                    <strong id="qr-modal-account" class="font-mono text-slate-200">1051572297</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Số tiền:</span>
                    <strong id="qr-modal-amount" class="text-amber-300 font-bold text-sm">0 đ</strong>
                </div>
                <div class="flex justify-between items-start gap-2 pt-1 border-t border-slate-900">
                    <span class="text-slate-400 shrink-0">Nội dung CK:</span>
                    <span id="qr-modal-content" class="text-slate-200 font-mono text-[11px] text-right break-all">Thanh toan phong</span>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <a id="qr-modal-download-btn" href="#" download="VietQR_ThanhToan.png" class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold text-center transition shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-download"></i> Tải ảnh QR
                </a>
                <button type="button" onclick="closeVietQrModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
        function openVietQrModal(billId) {
            fetch(`/smartroom/resident/bills/${billId}/qr-data`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('qr-modal-image').src = data.qr_url;
                        document.getElementById('qr-modal-bank').textContent = data.bank_name;
                        document.getElementById('qr-modal-account').textContent = data.bank_account_no;
                        document.getElementById('qr-modal-amount').textContent = new Intl.NumberFormat('vi-VN').format(data.amount) + ' VND';
                        document.getElementById('qr-modal-content').textContent = data.transfer_content;
                        document.getElementById('qr-modal-download-btn').href = data.qr_url;

                        const modal = document.getElementById('vietqr-modal');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } else {
                        alert(data.message || 'Không thể lấy thông tin VietQR.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi khi tải thông tin mã VietQR.');
                });
        }

        function closeVietQrModal() {
            const modal = document.getElementById('vietqr-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
