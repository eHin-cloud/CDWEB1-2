<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\RoomEquipment;
use App\Models\UtilityRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private const SERVICE_FEE = 150000;

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $billingMonth = $this->normalizeBillingMonth($request->query('billing_month'));

        $equipmentReports = Equipment::where('tenant_id', $tenantId)
            ->withCount([
                'roomAllocations as using_rooms_count' => fn ($query) => $query->where('quantity', '>', 0),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (Equipment $equipment) {
                return [
                    'id' => $equipment->id,
                    'code' => $equipment->code,
                    'name' => $equipment->name,
                    'unit' => $equipment->unit,
                    'total_quantity' => (int) $equipment->total_quantity,
                    'allocated_quantity' => (int) $equipment->allocated_quantity,
                    'stock_quantity' => $equipment->stock_quantity,
                    'using_rooms_count' => (int) $equipment->using_rooms_count,
                ];
            });

        $allocationReports = RoomEquipment::with(['room.building', 'equipment'])
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        $unpaidQuery = UtilityRecord::with(['room.building', 'room.residents' => function ($query) {
                $query->where('status', 'active');
            }])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('status', '!=', 'paid');

        if ($billingMonth) {
            $unpaidQuery->where('billing_month', $billingMonth);
        }

        $unpaidUtilities = $unpaidQuery
            ->orderByDesc('billing_month')
            ->orderBy('room_id')
            ->get()
            ->map(function (UtilityRecord $record) {
                $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
                $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
                $electricityAmount = $electricityUsage * (int) $record->electricity_price;
                $waterAmount = $waterUsage * (int) $record->water_price;
                $roomAmount = (int) optional($record->room)->price;

                $record->electricity_usage = $electricityUsage;
                $record->water_usage = $waterUsage;
                $record->electricity_amount = $electricityAmount;
                $record->water_amount = $waterAmount;
                $record->service_amount = self::SERVICE_FEE;
                $record->total_amount = $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE;

                return $record;
            });

        $summary = [
            'equipment_total' => $equipmentReports->sum('total_quantity'),
            'equipment_allocated' => $equipmentReports->sum('allocated_quantity'),
            'equipment_stock' => $equipmentReports->sum('stock_quantity'),
            'unpaid_room_count' => $unpaidUtilities->pluck('room_id')->unique()->count(),
            'unpaid_total_amount' => $unpaidUtilities->sum('total_amount'),
        ];

        // Truy vấn danh sách thu chi (Transactions) phục vụ Sổ Quỹ & Báo Cáo Lợi Nhuận
        $transactionsQuery = \App\Models\Transaction::where('tenant_id', $tenantId);
        if ($billingMonth) {
            $transactionsQuery->where('transaction_date', 'like', $billingMonth . '%');
        }
        $transactions = $transactionsQuery->orderByDesc('transaction_date')->orderByDesc('id')->get();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $financialSummary = [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
        ];

        return view('admin.reports.index', compact(
            'billingMonth',
            'equipmentReports',
            'allocationReports',
            'unpaidUtilities',
            'summary',
            'transactions',
            'financialSummary'
        ));
    }

    public function storeTransaction(Request $request)
    {
        // 1. ERR_19_01: Để trống số tiền giao dịch
        if (!$request->filled('amount') || $request->input('amount') === null || $request->input('amount') === '') {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_19_01',
                    'message' => 'Vui lòng nhập số tiền phát sinh thu hoặc chi.',
                    'field' => 'amount',
                ], 422);
            }
            return back()->withInput()->withErrors(['amount' => 'Vui lòng nhập số tiền phát sinh thu hoặc chi.']);
        }

        // 2. ERR_19_02: Nhập số tiền bằng 0 hoặc số âm
        $amount = $request->input('amount');
        if (!is_numeric($amount) || floatval($amount) <= 0) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_19_02',
                    'message' => 'Số tiền giao dịch phải là số nguyên dương lớn hơn 0 đồng.',
                    'field' => 'amount',
                ], 422);
            }
            return back()->withInput()->withErrors(['amount' => 'Số tiền giao dịch phải là số nguyên dương lớn hơn 0 đồng.']);
        }

        // 3. ERR_19_03: Để trống danh mục thu chi
        if (!$request->filled('category')) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_19_03',
                    'message' => 'Vui lòng chọn danh mục giao dịch (Tiền phòng, Sửa chữa, Vệ sinh...).',
                    'field' => 'category',
                ], 422);
            }
            return back()->withInput()->withErrors(['category' => 'Vui lòng chọn danh mục giao dịch (Tiền phòng, Sửa chữa, Vệ sinh...).']);
        }

        // 4. ERR_19_04: Để trống ngày phát sinh giao dịch
        $txDate = $request->input('date') ?: $request->input('transaction_date');
        if (empty($txDate)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'ERR_19_04',
                    'message' => 'Ngày ghi nhận giao dịch không được để trống.',
                    'field' => 'date',
                ], 422);
            }
            return back()->withInput()->withErrors(['date' => 'Ngày ghi nhận giao dịch không được để trống.']);
        }

        $rawType = $request->input('type', 'income');
        $type = in_array(strtolower($rawType), ['income', 'thu', '+'], true) ? 'income' : 'expense';
        $description = $request->input('note') ?: $request->input('description');

        $tenantId = Auth::user()->tenant_id;
        $transaction = \App\Models\Transaction::create([
            'tenant_id' => $tenantId,
            'type' => $type,
            'amount' => floatval($amount),
            'category' => trim($request->input('category')),
            'description' => $description ? trim($description) : null,
            'transaction_date' => $txDate,
        ]);

        // 5. ERR_19_05: Ghi nhận giao dịch thành công
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'code' => 'ERR_19_05',
                'message' => 'Ghi nhận giao dịch vào sổ quỹ thành công!',
                'transaction' => $transaction,
            ], 200);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Ghi nhận giao dịch vào sổ quỹ thành công!');
    }

    /**
     * Sổ quỹ thu - chi tài chính và biểu đồ dòng tiền (Bảng 131)
     * GET /smartroom/admin/reports/cash-flow
     */
    public function cashFlow(Request $request)
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            $tenantId = Auth::user()->tenant_id;
            $billingMonth = $this->normalizeBillingMonth($request->query('billing_month'));
            $query = \App\Models\Transaction::where('tenant_id', $tenantId);
            if ($billingMonth) {
                $query->where('transaction_date', 'like', $billingMonth . '%');
            }
            $transactions = $query->orderByDesc('transaction_date')->orderByDesc('id')->get();
            $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
            $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
            $netProfit = $totalIncome - $totalExpense;

            return response()->json([
                'success' => true,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit,
                'transactions' => $transactions,
            ]);
        }

        return $this->index($request);
    }

    /**
     * Xuất báo cáo sổ quỹ tài chính ra định dạng CSV/Excel UTF-8 (Bảng 131)
     * GET /smartroom/admin/reports/export-excel
     */
    public function exportExcel(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $billingMonth = $this->normalizeBillingMonth($request->query('billing_month'));
        $query = \App\Models\Transaction::where('tenant_id', $tenantId);
        if ($billingMonth) {
            $query->where('transaction_date', 'like', $billingMonth . '%');
        }
        $transactions = $query->orderByDesc('transaction_date')->orderByDesc('id')->get();

        $filename = 'so_quy_thu_chi_' . ($billingMonth ?: date('Y-m')) . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");
            fputcsv($output, ['STT', 'Ngày ghi nhận', 'Loại phiếu', 'Khoản mục', 'Số tiền (VNĐ)', 'Diễn giải chi tiết']);

            foreach ($transactions as $index => $tx) {
                fputcsv($output, [
                    $index + 1,
                    $tx->transaction_date ? $tx->transaction_date->format('d/m/Y') : '',
                    $tx->type === 'income' ? 'Phiếu Thu (+)' : 'Phiếu Chi (-)',
                    $tx->category,
                    $tx->amount,
                    $tx->description ?? '',
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function normalizeBillingMonth($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            abort(404, 'Tháng báo cáo không hợp lệ.');
        }

        return $value;
    }
}
