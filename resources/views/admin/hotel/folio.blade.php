<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Folio Khách Sạn - {{ $booking->booking_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 md:p-8">
    <div class="max-w-3xl mx-auto bg-slate-800 border border-slate-700 rounded-2xl p-6 md:p-8 shadow-2xl print-card">
        <!-- Top Action Bar -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-700 no-print">
            <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Sơ đồ phòng
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl shadow-lg transition inline-flex items-center gap-2">
                <i class="fa-solid fa-print"></i> In Hóa Đơn Folio
            </button>
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full text-xs font-semibold uppercase tracking-wider">
                    Bảng Kê Dịch Vụ Folio
                </span>
                <h1 class="text-2xl md:text-3xl font-bold mt-2 text-white">
                    {{ $booking->room->building->name ?? 'Khách Sạn Renty Star' }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $booking->room->building->address ?? 'Cơ sở lưu trú thông minh SmartRoom' }}
                </p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-sm text-slate-400">Mã đặt phòng</div>
                <div class="text-xl font-mono font-bold text-indigo-400">{{ $booking->booking_code }}</div>
                <div class="text-xs text-slate-400 mt-1">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Guest & Room Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-900/60 rounded-xl border border-slate-700/60 mb-6 text-sm">
            <div>
                <span class="text-xs text-slate-400 block">Khách lưu trú</span>
                <strong class="text-white">{{ $booking->guest_name }}</strong>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Số phòng</span>
                <strong class="text-indigo-400 font-mono text-base">P.{{ $booking->room->room_number }}</strong>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Thời gian nhận</span>
                <span class="text-slate-200">{{ \Carbon\Carbon::parse($booking->check_in_at)->format('d/m H:i') }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Thời gian trả</span>
                <span class="text-slate-200">{{ \Carbon\Carbon::parse($calc['check_out_at'])->format('d/m H:i') }}</span>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-700 text-slate-400 text-xs uppercase">
                        <th class="py-3 px-2">Khoản mục / Dịch vụ</th>
                        <th class="py-3 px-2 text-center">SL</th>
                        <th class="py-3 px-2 text-right">Đơn giá</th>
                        <th class="py-3 px-2 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <!-- Room Charge Breakdown -->
                    @foreach($calc['breakdown'] as $b)
                    <tr>
                        <td class="py-3 px-2 text-slate-200 font-medium">{{ $b['label'] }}</td>
                        <td class="py-3 px-2 text-center text-slate-400">{{ $b['quantity'] }}</td>
                        <td class="py-3 px-2 text-right text-slate-400">{{ number_format($b['unit_price']) }}đ</td>
                        <td class="py-3 px-2 text-right text-white font-medium">{{ number_format($b['amount']) }}đ</td>
                    </tr>
                    @endforeach

                    <!-- Minibar / Services Items -->
                    @foreach($booking->folioItems as $item)
                    <tr>
                        <td class="py-3 px-2 text-slate-200">
                            <span class="text-amber-400 mr-1"><i class="fa-solid fa-wine-glass"></i></span>
                            {{ $item->item_name }}
                        </td>
                        <td class="py-3 px-2 text-center text-slate-400">{{ $item->quantity }}</td>
                        <td class="py-3 px-2 text-right text-slate-400">{{ number_format($item->unit_price) }}đ</td>
                        <td class="py-3 px-2 text-right text-white font-medium">{{ number_format($item->subtotal) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculation Summary & VietQR -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-700 items-center">
            <!-- VietQR Payment Quick Box -->
            @php
                $tenant = $booking->tenant;
                $bankId = strtoupper((string) ($tenant?->bank_name ?: 'MB'));
                $accountNo = (string) ($tenant?->bank_account_no ?: '0988000001');
                $accountName = rawurlencode((string) ($tenant?->bank_account_name ?: 'RENTRY HOTEL'));
                $addInfo = rawurlencode("Thanh toan Folio {$booking->booking_code}");
                $qrAmount = (int) $calc['total_amount'];
                $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$qrAmount}&addInfo={$addInfo}&accountName={$accountName}";
            @endphp
            <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-700/60 flex items-center gap-4">
                <img src="{{ $qrUrl }}" alt="VietQR Thanh Toán" class="w-28 h-28 object-contain rounded-lg bg-white p-1 shadow">
                <div class="text-xs space-y-1">
                    <span class="font-bold text-amber-400 block text-sm">Quét mã VietQR</span>
                    <p class="text-slate-300">Ngân hàng: <span class="font-semibold text-white">{{ $bankId }}</span></p>
                    <p class="text-slate-300">STK: <span class="font-mono text-white">{{ $accountNo }}</span></p>
                    <p class="text-slate-400">Nội dung: <span class="text-indigo-400 font-mono">{{ $booking->booking_code }}</span></p>
                </div>
            </div>

            <!-- Grand Total Summary -->
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-slate-400">
                    <span>Tổng tiền phòng:</span>
                    <span class="text-slate-200">{{ number_format($calc['room_amount']) }}đ</span>
                </div>
                @if($calc['surcharge_amount'] > 0)
                <div class="flex justify-between text-amber-400">
                    <span>Phụ thu trễ hạn:</span>
                    <span>+{{ number_format($calc['surcharge_amount']) }}đ</span>
                </div>
                @endif
                @if($calc['service_amount'] > 0)
                <div class="flex justify-between text-slate-400">
                    <span>Minibar & Dịch vụ:</span>
                    <span class="text-slate-200">+{{ number_format($calc['service_amount']) }}đ</span>
                </div>
                @endif
                @if($calc['deposit_amount'] > 0)
                <div class="flex justify-between text-emerald-400">
                    <span>Đã đặt cọc:</span>
                    <span>-{{ number_format($calc['deposit_amount']) }}đ</span>
                </div>
                @endif
                <div class="flex justify-between items-center text-lg font-bold pt-2 border-t border-slate-700 text-white">
                    <span>CẦN THANH TOÁN:</span>
                    <span class="text-emerald-400 text-2xl font-mono">{{ number_format($calc['total_amount']) }}đ</span>
                </div>
                <div class="text-right text-xs text-emerald-400">
                    <i class="fa-solid fa-circle-check"></i> Trạng thái: Đã hoàn tất trả phòng
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-slate-400 pt-4 border-t border-slate-800">
            Cảm ơn Quý khách đã lựa chọn dịch vụ lưu trú của chúng tôi. Hẹn gặp lại Quý khách!
        </div>
    </div>
</body>
</html>
