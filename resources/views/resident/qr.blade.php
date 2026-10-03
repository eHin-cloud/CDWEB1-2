<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartRoom - QR Thanh Toán</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-[#080b11] text-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-slate-900/80 border border-slate-800 p-6 text-center">
        <a href="{{ route('smartroom.resident') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 hover:text-slate-200 mb-5">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>

        <h1 class="text-xl font-black">QR thanh toán</h1>
        <p class="text-xs text-slate-500 mt-1">Hóa đơn {{ $bill->billing_month }} - {{ $resident->name }}</p>

        <div class="mt-6 rounded-2xl bg-white p-4 shadow-lg">
            <img src="{{ $qrUrl ?? 'https://img.vietqr.io/image/VCB-1051572297-compact.png' }}" alt="VietQR" class="w-full aspect-square object-contain" onerror="this.src='https://img.vietqr.io/image/VCB-1051572297-compact.png';">
        </div>

        <div class="mt-5 rounded-xl bg-slate-950/60 border border-slate-800 p-4 text-left text-sm space-y-2">
            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Ngân hàng</span>
                <strong class="text-emerald-400 font-bold">Vietcombank (VCB)</strong>
            </div>
            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Số tài khoản</span>
                <strong class="text-slate-100 font-mono tracking-wider">1051572297</strong>
            </div>
            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Số tiền</span>
                <strong class="text-amber-300 font-bold">{{ number_format($bill->total_amount) }} VND</strong>
            </div>
            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Nội dung</span>
                <span class="text-slate-200 text-xs font-semibold">Thanh toan phong {{ $resident->room->room_number ?? '' }} thang {{ $bill->billing_month }}</span>
            </div>
            <div class="flex justify-between gap-3 pt-2 border-t border-slate-800">
                <span class="text-slate-400">Trạng thái</span>
                <strong>{{ $bill->status_label }}</strong>
            </div>
        </div>

        <a href="{{ $qrUrl ?? 'https://img.vietqr.io/image/VCB-1051572297-compact.png' }}" download="VietQR_VCB_1051572297.png" class="mt-5 inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/20">
            <i class="fa-solid fa-download"></i> Tải mã QR
        </a>
    </main>
</body>
</html>
