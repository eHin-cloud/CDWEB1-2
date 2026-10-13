<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\AdminActivityLogger;

class PaymentController extends Controller
{
    private const SERVICE_FEE = 150000;

    private const PAYMENT_METHODS = [
        'bank_transfer' => 'Chuyển khoản VietQR',
        'cash' => 'Tiền mặt',
        'vietqr' => 'Chuyển khoản VietQR',
        'gateway' => 'Cổng thanh toán',
        'other' => 'Khác',
    ];

    public function index(Request $request)
    {
        $tenantId = $this->currentTenantId();
        $filters = $this->filters($request);

        $records = $this->filteredRecordQuery($tenantId, $filters)
            ->orderByDesc('billing_month')
            ->orderBy('room_id')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record));

        if ($filters['status'] === 'draft') {
            $records = collect();
        }

        $draftRooms = $this->draftRooms($tenantId, $filters);
        $paymentRows = $records->concat($draftRooms)
            ->sortBy([
                ['billing_month', 'desc'],
                ['room_number', 'asc'],
            ])
            ->values();

        $paidTotal = (int) $records->where('status', 'paid')->sum('total_amount');
        $unpaidTotal = (int) $records->whereIn('status', ['sent', 'overdue'])->sum('total_amount');
        $totalBilled = $paidTotal + $unpaidTotal;
        $collectionRate = $totalBilled > 0 ? round(($paidTotal / $totalBilled) * 100, 1) : 0;
        $vietqrCount = $records->whereIn('payment_method', ['bank_transfer', 'vietqr'])->where('status', 'paid')->count();

        $summary = [
            'draft_count' => $paymentRows->where('status', 'draft')->count(),
            'sent_count' => $paymentRows->where('status', 'sent')->count(),
            'paid_count' => $paymentRows->where('status', 'paid')->count(),
            'overdue_count' => $paymentRows->where('status', 'overdue')->count(),
            'paid_total' => $paidTotal,
            'unpaid_total' => $unpaidTotal,
            'collection_rate' => $collectionRate,
            'vietqr_count' => $vietqrCount,
        ];

        $rooms = Room::where('tenant_id', $tenantId)
            ->orderBy('room_number')
            ->get(['id', 'room_number']);

        $histories = UtilityRecord::with(['room.building'])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->orderBy('room_id')
            ->orderByDesc('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record))
            ->groupBy('room_id');

        $revenueReports = $this->revenueReports($tenantId, $filters);

        return view('admin.payments.index', [
            'filters' => $filters,
            'paymentRows' => $paymentRows,
            'summary' => $summary,
            'rooms' => $rooms,
            'histories' => $histories,
            'revenueReports' => $revenueReports,
            'paymentMethods' => self::PAYMENT_METHODS,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    /**
     * FEAT_29_PAYMENTS: Xác nhận gạch nợ thanh toán hóa đơn tiền mặt hoặc chuyển khoản VietQR
     */
    public function confirmPayment(Request $request)
    {
        $invoiceId = $request->input('invoice_id') ?? $request->input('payment_id');
        $rawAmount = $request->input('payment_amount');
        $rawMethod = $request->input('payment_method');
        $transactionRef = trim((string) $request->input('transaction_ref', ''));

        // 1. Kiểm tra để trống số tiền
        if ($rawAmount === null || trim((string) $rawAmount) === '') {
            $errorMsg = 'Vui lòng nhập số tiền thanh toán';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'field' => 'payment_amount',
                    'message' => $errorMsg,
                ], 422);
            }
            return back()->withInput()->withErrors(['payment_amount' => $errorMsg]);
        }

        // 2. Bấm cập nhật nhưng chưa chọn phương thức (Mã lỗi ERR_29_01)
        if ($rawMethod === null || trim((string) $rawMethod) === '') {
            $errorMsg = 'Vui lòng chọn hình thức nhận tiền (Tiền mặt hoặc Chuyển khoản).';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ERR_29_01',
                    'field' => 'payment_method',
                    'message' => $errorMsg,
                ], 422);
            }
            return back()->withInput()->withErrors(['payment_method' => $errorMsg]);
        }

        // Chuẩn hóa amount: loại bỏ ký tự không phải số (phân cách hàng nghìn)
        $cleanedAmount = preg_replace('/[^\d]/', '', (string) $rawAmount);

        // Regex quy định: Amount: ^[1-9][0-9]*$
        if (!preg_match('/^[1-9][0-9]*$/', (string) $cleanedAmount)) {
            $errorMsg = 'Số tiền thanh toán không hợp lệ (phải là số nguyên dương lớn hơn 0).';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'field' => 'payment_amount',
                    'message' => $errorMsg,
                ], 422);
            }
            return back()->withInput()->withErrors(['payment_amount' => $errorMsg]);
        }

        $paymentAmount = (int) $cleanedAmount;

        // 3. Kiểm tra invoice_id
        if (!$invoiceId) {
            $errorMsg = 'Mã hóa đơn không hợp lệ.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->withInput()->withErrors(['invoice_id' => $errorMsg]);
        }

        $tenantId = $this->currentTenantId();
        $record = UtilityRecord::with(['room.building'])
            ->whereHas('room', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('id', $invoiceId)
            ->first();

        if (!$record) {
            $errorMsg = 'Không tìm thấy hóa đơn cần thanh toán trong phạm vi cơ sở của bạn.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 404);
            }
            return back()->withInput()->with('error', $errorMsg);
        }

        $decorated = $this->decorateRecord($record);
        $remainingDebt = $decorated->status === 'paid' ? 0 : (int) $decorated->total_amount;

        // 4. Số tiền thanh toán không được vượt quá số tiền nợ còn lại của hóa đơn
        if ($paymentAmount > $remainingDebt) {
            $formattedDebt = number_format($remainingDebt, 0, ',', '.') . ' đ';
            $errorMsg = "Số tiền thanh toán không được vượt quá số tiền nợ còn lại ({$formattedDebt})";
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'field' => 'payment_amount',
                    'message' => $errorMsg,
                ], 422);
            }
            return back()->withInput()->withErrors(['payment_amount' => $errorMsg]);
        }

        // Chuẩn hóa phương thức thanh toán
        $normalizedMethod = match ($rawMethod) {
            'cash', 'Tiền mặt' => 'cash',
            'bank_transfer', 'vietqr', 'Chuyển khoản VietQR', 'Chuyển khoản' => 'bank_transfer',
            'gateway', 'Cổng thanh toán' => 'gateway',
            default => 'cash',
        };

        $methodDisplayLabel = match ($normalizedMethod) {
            'bank_transfer', 'vietqr' => 'Chuyển khoản VietQR',
            'gateway' => 'Cổng thanh toán',
            default => 'Tiền mặt',
        };

        $before = $record->only(['status', 'payment_date', 'payment_method', 'transaction_ref']);

        // Cập nhật hóa đơn sang 'Đã thanh toán'
        $record->update([
            'status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $normalizedMethod,
            'transaction_ref' => $transactionRef ?: null,
        ]);

        // Cập nhật bảng bills đồng bộ
        Bill::where('tenant_id', $tenantId)
            ->where('room_id', $record->room_id)
            ->where('billing_month', $record->billing_month)
            ->update([
                'status' => 'paid',
                'payment_date' => now(),
                'transaction_ref' => $transactionRef ?: null,
            ]);

        // Tự động sinh phiếu thu trong Sổ quỹ (Transaction)
        $roomNumber = $record->room->room_number ?? ('#' . $record->room_id);
        Transaction::create([
            'tenant_id' => $tenantId,
            'type' => 'income',
            'amount' => $paymentAmount,
            'category' => 'rental',
            'description' => "Thu tiền phòng {$roomNumber} tháng {$record->billing_month} ({$methodDisplayLabel}) - Gạch nợ FEAT_29" . ($transactionRef ? " - Ref: {$transactionRef}" : ''),
            'transaction_date' => now()->toDateString(),
        ]);

        // Đồng bộ trạng thái phòng
        $this->syncRoomPaymentStatus($record->room_id);

        // Ghi nhật ký quản trị
        AdminActivityLogger::log(
            'payment',
            'payments',
            "Xác nhận gạch nợ phòng {$roomNumber} tháng {$record->billing_month} số tiền " . number_format($paymentAmount) . "đ ({$methodDisplayLabel})",
            $record,
            ['room_number' => $roomNumber, 'billing_month' => $record->billing_month, 'amount' => $paymentAmount],
            $before,
            $record->fresh()->only(['status', 'payment_date', 'payment_method', 'transaction_ref'])
        );

        // Mã phản hồi thành công ERR_29_02
        $successMsg = 'Đã ghi nhận thanh toán tiền phòng thành công!';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'code' => 'ERR_29_02',
                'message' => $successMsg,
                'invoice_id' => $record->id,
                'status' => 'paid',
                'status_label' => 'Đã thanh toán',
                'paid_amount' => $paymentAmount,
                'remaining_debt' => 0,
            ]);
        }

        return back()->with('success', $successMsg)->with('success_code', 'ERR_29_02');
    }

    public function update(Request $request, UtilityRecord $payment)
    {
        $this->authorizeTenantRecord($payment);

        $validated = $request->validate([
            'status' => 'required|in:sent,paid,overdue',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'payment_method' => 'nullable|required_if:status,paid|in:cash,bank_transfer,vietqr,gateway,other',
        ]);

        if (
            $validated['status'] === 'paid'
            && in_array($validated['payment_method'], ['bank_transfer', 'vietqr'], true)
            && !$this->tenantCanReceiveOnlinePayments($this->currentTenantId())
        ) {
            return back()->with('error', 'Cần hoàn tất KYC trước khi ghi nhận thanh toán chuyển khoản/VietQR.');
        }

        $before = $payment->only(['status', 'payment_date', 'payment_method']);

        $payment->update([
            'status' => $validated['status'],
            'payment_date' => $validated['status'] === 'paid'
                ? Carbon::parse($validated['payment_date'] ?? now())->startOfDay()
                : null,
            'payment_method' => $validated['status'] === 'paid'
                ? $validated['payment_method']
                : null,
        ]);

        if ($validated['status'] === 'paid') {
            Bill::where('tenant_id', $this->currentTenantId())
                ->where('room_id', $payment->room_id)
                ->where('billing_month', $payment->billing_month)
                ->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);

            $decorated = $this->decorateRecord($payment);
            $roomNumber = $payment->room->room_number ?? ('#' . $payment->room_id);
            Transaction::create([
                'tenant_id' => $this->currentTenantId(),
                'type' => 'income',
                'amount' => $decorated->total_amount,
                'category' => 'rental',
                'description' => "Thu tiền phòng {$roomNumber} tháng {$payment->billing_month} ({$validated['payment_method']})",
                'transaction_date' => now()->toDateString(),
            ]);
        }

        $this->syncRoomPaymentStatus($payment->room_id);

        $payment->loadMissing('room');
        AdminActivityLogger::log(
            'payment',
            'payments',
            'Cập nhật thanh toán phòng ' . ($payment->room->room_number ?? $payment->room_id) . ' sang trạng thái ' . $this->statusLabels()[$payment->status]['label'],
            $payment,
            ['room_number' => $payment->room->room_number ?? null, 'billing_month' => $payment->billing_month],
            $before,
            $payment->fresh()->only(['status', 'payment_date', 'payment_method'])
        );

        return back()->with('success', 'Đã ghi nhận thanh toán tiền phòng thành công!');
    }

    /**
     * Bấm nút Xuất CSV đối soát: tải xuống tệp danh sách đối soát thanh toán định dạng .csv
     */
    public function export(Request $request): StreamedResponse
    {
        $tenantId = $this->currentTenantId();
        $filters = $this->filters($request);

        // Trường hợp 1: Xuất báo cáo doanh thu theo kỳ (quý, năm, tháng)
        if ($request->filled('report_period')) {
            $rows = $this->revenueReports($tenantId, $filters);
            $fileName = 'bao-cao-doanh-thu-' . $filters['report_period'] . '-' . now()->format('Ymd-His') . '.csv';

            return response()->streamDownload(function () use ($rows, $filters) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Ky bao cao', 'So hoa don da thanh toan', 'Doanh thu']);

                foreach ($rows as $row) {
                    fputcsv($handle, [$row['label'], $row['paid_count'], $row['total_revenue']]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['Loai bao cao', $filters['report_period']]);
                fclose($handle);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        // Trường hợp 2: FEAT_29_PAYMENTS - Bấm nút Xuất CSV đối soát chi tiết
        $records = $this->filteredRecordQuery($tenantId, $filters)
            ->orderByDesc('billing_month')
            ->orderBy('room_id')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record));

        $fileName = 'doi-soat-thanh-toan-' . ($filters['billing_month'] ?: now()->format('Y-m')) . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($records, $filters) {
            $handle = fopen('php://output', 'w');
            // Ghi UTF-8 BOM để Excel hiển thị đúng dấu tiếng Việt
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Mã HĐ',
                'Kỳ hóa đơn',
                'Số phòng',
                'Tòa nhà / Cơ sở',
                'Khách thuê',
                'Tiền phòng (đ)',
                'Tiền điện (đ)',
                'Tiền nước (đ)',
                'Phí dịch vụ (đ)',
                'Tổng tiền (đ)',
                'Đã thanh toán (đ)',
                'Còn nợ (đ)',
                'Trạng thái',
                'Phương thức',
                'Ngày thanh toán',
                'Mã tham chiếu ngân hàng',
            ]);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->id ?: 'DRAFT',
                    $row->billing_month,
                    'P.' . $row->room_number,
                    $row->building_name,
                    $row->resident_name,
                    $row->room_amount,
                    $row->electricity_amount,
                    $row->water_amount,
                    $row->service_amount,
                    $row->total_amount,
                    $row->paid_amount,
                    $row->remaining_debt,
                    $row->status_label,
                    self::PAYMENT_METHODS[$row->payment_method] ?? ($row->payment_method ?: 'Chưa thanh toán'),
                    $row->payment_date ? $row->payment_date->format('d/m/Y') : '',
                    $row->transaction_ref ?: '',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'X-Notification-Code' => 'ERR_29_03',
            'X-Notification-Message' => rawurlencode('Tải xuống tệp danh sách đối soát thanh toán định dạng .csv thành công.'),
        ]);
    }

    private function filters(Request $request): array
    {
        return [
            'billing_month' => $this->normalizeMonth($request->query('billing_month')) ?? now()->format('Y-m'),
            'from_month' => $this->normalizeMonth($request->query('from_month')),
            'to_month' => $this->normalizeMonth($request->query('to_month')),
            'status' => in_array($request->query('status'), ['draft', 'sent', 'paid', 'overdue'], true) ? $request->query('status') : null,
            'payment_method' => array_key_exists($request->query('payment_method'), self::PAYMENT_METHODS) ? $request->query('payment_method') : null,
            'room_id' => $request->filled('room_id') ? (int) $request->query('room_id') : null,
            'q' => trim((string) $request->query('q', '')),
            'report_period' => in_array($request->query('report_period'), ['month', 'quarter', 'year'], true) ? $request->query('report_period') : 'month',
        ];
    }

    private function filteredRecordQuery(int $tenantId, array $filters)
    {
        return UtilityRecord::with(['room.building', 'room.residents' => fn ($query) => $query->where('status', 'active')])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->when($filters['from_month'], fn ($query) => $query->where('billing_month', '>=', $filters['from_month']))
            ->when($filters['to_month'], fn ($query) => $query->where('billing_month', '<=', $filters['to_month']))
            ->when(!$filters['from_month'] && !$filters['to_month'], fn ($query) => $query->where('billing_month', $filters['billing_month']))
            ->when($filters['status'] && $filters['status'] !== 'draft', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['payment_method'], fn ($query) => $query->where('payment_method', $filters['payment_method']))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $identityKeyword = $this->prefixLike($filters['q']);
                $nameKeyword = $this->containsLike($filters['q']);

                $query->whereHas('room', function ($roomQuery) use ($identityKeyword, $nameKeyword) {
                    $roomQuery->where('room_number', 'like', $identityKeyword)
                        ->orWhereHas('residents', function ($residentQuery) use ($identityKeyword, $nameKeyword) {
                            $residentQuery->where('name', 'like', $nameKeyword)
                                ->orWhere('phone', 'like', $identityKeyword)
                                ->orWhere('cccd', 'like', $identityKeyword);
                        });
                });
            });
    }

    private function draftRooms(int $tenantId, array $filters): Collection
    {
        if ($filters['payment_method'] || ($filters['status'] && $filters['status'] !== 'draft')) {
            return collect();
        }

        return Room::with(['building', 'residents' => fn ($query) => $query->where('status', 'active')])
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'empty')
            ->whereDoesntHave('utilityRecords', fn ($query) => $query->where('billing_month', $filters['billing_month']))
            ->when($filters['room_id'], fn ($query) => $query->where('id', $filters['room_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $identityKeyword = $this->prefixLike($filters['q']);
                $nameKeyword = $this->containsLike($filters['q']);

                $query->where(function ($roomQuery) use ($identityKeyword, $nameKeyword) {
                    $roomQuery->where('room_number', 'like', $identityKeyword)
                        ->orWhereHas('residents', function ($residentQuery) use ($identityKeyword, $nameKeyword) {
                            $residentQuery->where('name', 'like', $nameKeyword)
                                ->orWhere('phone', 'like', $identityKeyword)
                                ->orWhere('cccd', 'like', $identityKeyword);
                        });
                });
            })
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room) use ($filters) {
                $total = (int) $room->price + self::SERVICE_FEE;
                $cleanMonth = str_replace('-', '', $filters['billing_month']);
                $addInfo = rawurlencode("Thanh toan P{$room->room_number} T{$cleanMonth}");
                $vietqrUrl = "https://img.vietqr.io/image/MB-999988880001-compact2.png?amount={$total}&addInfo={$addInfo}&accountName=TRAN%20VAN%20HOANG";

                return (object) [
                    'id' => null,
                    'room_id' => $room->id,
                    'room_number' => $room->room_number,
                    'building_name' => $room->building->name ?? 'Chưa có tòa nhà',
                    'resident_name' => optional($room->residents->first())->name ?? 'Chưa gán cư dân',
                    'billing_month' => $filters['billing_month'],
                    'status' => 'draft',
                    'status_label' => 'Chưa gửi',
                    'status_class' => 'bg-slate-500/10 text-slate-300 border-slate-500/20',
                    'room_amount' => (int) $room->price,
                    'electricity_usage' => 0,
                    'electricity_amount' => 0,
                    'water_usage' => 0,
                    'water_amount' => 0,
                    'service_amount' => self::SERVICE_FEE,
                    'total_amount' => $total,
                    'paid_amount' => 0,
                    'remaining_debt' => $total,
                    'payment_date' => null,
                    'payment_method' => null,
                    'transaction_ref' => null,
                    'vietqr_url' => $vietqrUrl,
                ];
            });
    }

    private function decorateRecord(UtilityRecord $record): object
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $roomAmount = (int) optional($record->room)->price;
        $electricityAmount = $electricityUsage * (int) $record->electricity_price;
        $waterAmount = $waterUsage * (int) $record->water_price;
        $labels = $this->statusLabels();
        $totalAmount = $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE;

        $roomNumber = $record->room->room_number ?? ('#' . $record->room_id);
        $cleanMonth = str_replace('-', '', $record->billing_month);
        $addInfo = rawurlencode("Thanh toan P{$roomNumber} T{$cleanMonth}");
        $vietqrUrl = "https://img.vietqr.io/image/MB-999988880001-compact2.png?amount={$totalAmount}&addInfo={$addInfo}&accountName=TRAN%20VAN%20HOANG";

        $paidAmount = $record->status === 'paid' ? $totalAmount : 0;
        $remainingDebt = $record->status === 'paid' ? 0 : $totalAmount;

        return (object) [
            'id' => $record->id,
            'room_id' => $record->room_id,
            'room_number' => $roomNumber,
            'building_name' => $record->room->building->name ?? 'Chưa có tòa nhà',
            'resident_name' => optional($record->room->residents->first())->name ?? 'Chưa gán cư dân',
            'billing_month' => $record->billing_month,
            'status' => $record->status,
            'status_label' => $labels[$record->status]['label'] ?? $record->status,
            'status_class' => $labels[$record->status]['class'] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20',
            'room_amount' => $roomAmount,
            'electricity_usage' => $electricityUsage,
            'electricity_amount' => $electricityAmount,
            'water_usage' => $waterUsage,
            'water_amount' => $waterAmount,
            'service_amount' => self::SERVICE_FEE,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_debt' => $remainingDebt,
            'payment_date' => $record->payment_date,
            'payment_method' => $record->payment_method,
            'transaction_ref' => $record->transaction_ref ?? null,
            'vietqr_url' => $vietqrUrl,
        ];
    }

    private function revenueReports(int $tenantId, array $filters): Collection
    {
        return UtilityRecord::with('room')
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('status', 'paid')
            ->when($filters['from_month'], fn ($query) => $query->where('billing_month', '>=', $filters['from_month']))
            ->when($filters['to_month'], fn ($query) => $query->where('billing_month', '<=', $filters['to_month']))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->when($filters['payment_method'], fn ($query) => $query->where('payment_method', $filters['payment_method']))
            ->orderBy('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record))
            ->groupBy(fn ($record) => $this->periodKey($record->billing_month, $filters['report_period']))
            ->map(function (Collection $items, string $key) {
                return [
                    'key' => $key,
                    'label' => $this->periodLabel($key),
                    'paid_count' => $items->count(),
                    'total_revenue' => $items->sum('total_amount'),
                ];
            })
            ->sortBy('key')
            ->values();
    }

    private function periodKey(string $billingMonth, string $period): string
    {
        [$year, $month] = explode('-', $billingMonth);

        return match ($period) {
            'quarter' => $year . '-Q' . (int) ceil(((int) $month) / 3),
            'year' => $year,
            default => $billingMonth,
        };
    }

    private function periodLabel(string $key): string
    {
        if (preg_match('/^(\d{4})-(\d{2})$/', $key, $matches)) {
            return 'Tháng ' . (int) $matches[2] . '/' . $matches[1];
        }

        if (preg_match('/^(\d{4})-Q([1-4])$/', $key, $matches)) {
            return 'Quý ' . $matches[2] . '/' . $matches[1];
        }

        return 'Năm ' . $key;
    }

    private function statusLabels(): array
    {
        return [
            'draft' => ['label' => 'Chưa gửi', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/20'],
            'sent' => ['label' => 'Đã gửi', 'class' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20'],
            'paid' => ['label' => 'Đã thanh toán', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'],
            'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-rose-500/10 text-rose-300 border-rose-500/20'],
        ];
    }

    private function normalizeMonth($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            abort(404, 'Tháng không hợp lệ.');
        }

        return $value;
    }

    private function currentTenantId(): int
    {
        // Ưu tiên scoped_tenant_id từ middleware tenant.scope
        $scopedTenantId = request()->attributes->get('scoped_tenant_id');
        if ($scopedTenantId) {
            return (int) $scopedTenantId;
        }

        $tenantId = Auth::user()?->tenant_id;
        if ($tenantId) {
            return (int) $tenantId;
        }

        $fallbackTenantId = Tenant::query()->value('id');
        if (!$fallbackTenantId) {
            abort(404, 'Không tìm thấy cơ sở (tenant) để hiển thị thanh toán.');
        }

        return (int) $fallbackTenantId;
    }

    private function tenantCanReceiveOnlinePayments(int $tenantId): bool
    {
        return Tenant::where('id', $tenantId)
            ->whereIn('verification_status', ['kyc_verified', 'premium_pending', 'premium_verified'])
            ->exists();
    }

    private function prefixLike(string $value): string
    {
        return $this->escapeLike($value) . '%';
    }

    private function containsLike(string $value): string
    {
        return '%' . $this->escapeLike($value) . '%';
    }

    private function escapeLike(string $value): string
    {
        return addcslashes(trim($value), '\%_');
    }

    private function authorizeTenantRecord(UtilityRecord $record): void
    {
        $record->loadMissing('room');

        if (!$record->room || $record->room->tenant_id !== $this->currentTenantId()) {
            abort(404);
        }
    }

    private function syncRoomPaymentStatus(int $roomId): void
    {
        $room = Room::find($roomId);

        if (!$room || $room->status === 'empty' || $room->status === 'maintenance') {
            return;
        }

        $hasUnpaid = UtilityRecord::where('room_id', $roomId)
            ->where('status', '!=', 'paid')
            ->exists();

        $room->update(['status' => $hasUnpaid ? 'overdue' : 'occupied']);
    }
}
