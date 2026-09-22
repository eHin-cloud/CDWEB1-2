<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\ElectronicInvoice;
use App\Models\Tenant;
use App\Models\UtilityRecord;
use App\Services\EInvoice\EInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EInvoiceController extends Controller
{
    public function __construct(private readonly EInvoiceService $eInvoiceService)
    {
    }

    /**
     * Danh sách Hóa đơn điện tử của Tenant hiện tại (Quản lý thuế)
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()?->tenant_id;
        if (!$tenantId && Auth::user()?->isAdmin()) {
            $tenantId = Tenant::first()?->id ?? 1;
        }

        $query = ElectronicInvoice::with(['room.building', 'resident', 'bill', 'utilityRecord'])
            ->where('tenant_id', $tenantId);

        // Lọc theo trạng thái CQT
        if ($request->filled('cqt_status')) {
            $query->where('cqt_status', $request->cqt_status);
        }

        // Lọc theo nhà cung cấp (MISA, VNPT, Viettel, Mock)
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        // Lọc theo từ khóa (Số HĐ, Mã CQT, Tên khách thuê, Mã tra cứu)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('tax_authority_code', 'like', "%{$search}%")
                    ->orWhere('lookup_code', 'like', "%{$search}%")
                    ->orWhere('buyer_name', 'like', "%{$search}%");
            });
        }

        // Thống kê tổng quan Thuế & Hóa đơn
        $stats = [
            'total_count' => (clone $query)->count(),
            'cqt_accepted' => (clone $query)->where('cqt_status', 'CQT_ACCEPTED')->count(),
            'total_revenue' => (clone $query)->where('cqt_status', 'CQT_ACCEPTED')->sum('total_amount'),
            'total_tax' => (clone $query)->where('cqt_status', 'CQT_ACCEPTED')->sum('tax_amount'),
        ];

        $invoices = $query->orderByDesc('id')->paginate(15);
        $tenant = Tenant::find($tenantId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'invoices' => $invoices,
                'config' => $tenant?->einvoice_config,
            ]);
        }

        return view('admin.einvoice.index', compact('invoices', 'stats', 'tenant'));
    }

    /**
     * Xem bản thể hiện Hóa đơn điện tử chuẩn Nghị định 123/2020/NĐ-CP & TT 78
     */
    public function viewRepresentation($id)
    {
        $invoice = ElectronicInvoice::with(['room.building', 'resident', 'tenant'])
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                    ->orWhere('lookup_code', $id)
                    ->orWhere('tax_authority_code', $id);
            })
            ->firstOrFail();

        return view('admin.einvoice.representation', compact('invoice'));
    }

    /**
     * Tải bản PDF thể hiện hóa đơn điện tử
     */
    public function downloadPdf($id)
    {
        $invoice = ElectronicInvoice::with(['room.building', 'resident', 'tenant'])
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                    ->orWhere('lookup_code', $id);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.einvoice.representation', [
            'invoice' => $invoice,
            'isPdfExport' => true,
        ])->setPaper('a4', 'portrait');

        $fileName = 'HDDT_' . $invoice->invoice_symbol . '_' . $invoice->invoice_number . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Tải tệp XML hóa đơn điện tử ký số hợp chuẩn Tổng cục Thuế (QĐ 1450/QĐ-TCT)
     */
    public function downloadXml($id)
    {
        $invoice = ElectronicInvoice::where('id', $id)
            ->orWhere('lookup_code', $id)
            ->firstOrFail();

        $xmlContent = $invoice->xml_content;
        if (empty($xmlContent)) {
            // Sinh XML fallback nếu chưa có sẵn
            $driver = new \App\Services\EInvoice\Drivers\MockEInvoiceDriver();
            $xmlContent = $driver->issueInvoice([
                'invoice_number' => $invoice->invoice_number,
                'seller' => [
                    'name' => $invoice->seller_name,
                    'tax_code' => $invoice->seller_tax_code,
                    'address' => $invoice->seller_address,
                    'phone' => $invoice->seller_phone,
                    'bank_account' => $invoice->seller_bank_account,
                ],
                'buyer' => [
                    'name' => $invoice->buyer_name,
                    'tax_code' => $invoice->buyer_tax_code,
                    'id_card' => $invoice->buyer_id_card,
                    'address' => $invoice->buyer_address,
                    'phone' => $invoice->buyer_phone,
                    'email' => $invoice->buyer_email,
                ],
                'items' => $invoice->items ?? [],
                'subtotal_amount' => $invoice->subtotal_amount,
                'tax_amount' => $invoice->tax_amount,
                'total_amount' => $invoice->total_amount,
                'total_amount_in_words' => $invoice->total_amount_in_words,
            ])['xml_content'];
        }

        $fileName = 'XML_HDDT_' . $invoice->invoice_symbol . '_' . $invoice->invoice_number . '.xml';

        return response($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Phát hành HĐĐT thủ công cho Bill hoặc UtilityRecord
     */
    public function manualIssue(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bill,utility',
            'target_id' => 'required|integer',
        ]);

        try {
            $invoice = null;
            if ($request->type === 'bill') {
                $bill = Bill::with(['room.building', 'tenant'])->findOrFail($request->target_id);
                $invoice = $this->eInvoiceService->issueFromBill($bill);
            } else {
                $utility = UtilityRecord::with(['room.building', 'tenant'])->findOrFail($request->target_id);
                $invoice = $this->eInvoiceService->issueFromUtility($utility);
            }

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể phát hành hóa đơn. Vui lòng kiểm tra lại cấu hình nhà cung cấp.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Đã phát hành Hóa đơn điện tử số {$invoice->invoice_number} thành công và được CQT cấp mã!",
                'invoice' => $invoice,
            ]);
        } catch (\Throwable $e) {
            Log::error('Manual E-Invoice issue error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi phát hành: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gửi lại Email hoặc Zalo cho khách thuê
     */
    public function sendNotification($id, Request $request)
    {
        $invoice = ElectronicInvoice::findOrFail($id);
        $sendEmail = $request->boolean('send_email', true);
        $sendZalo = $request->boolean('send_zalo', true);

        $result = $this->eInvoiceService->sendInvoiceNotifications($invoice, $sendEmail, $sendZalo);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo bản thể hiện HĐĐT tới khách thuê!',
            'details' => $result,
        ]);
    }

    /**
     * Lưu cấu hình nhà cung cấp e-Invoice cho Tenant
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:misa,vnpt,viettel,mock',
            'tax_code' => 'required|string|max:30',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'template_symbol' => 'nullable|string|max:20',
            'invoice_series' => 'nullable|string|max:20',
            'api_endpoint' => 'nullable|string|max:255',
            'app_id' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'auto_issue_on_payment' => 'nullable|boolean',
            'auto_send_email' => 'nullable|boolean',
            'auto_send_zalo' => 'nullable|boolean',
        ]);

        $tenantId = Auth::user()?->tenant_id;
        $tenant = Tenant::findOrFail($tenantId);

        $config = [
            'provider' => $request->provider,
            'tax_code' => $request->tax_code,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'template_symbol' => $request->template_symbol ?: '1C26TAA',
            'invoice_series' => $request->invoice_series ?: 'C26TAA',
            'api_endpoint' => $request->api_endpoint,
            'app_id' => $request->app_id,
            'secret_key' => $request->secret_key,
            'tax_rate' => (float)($request->tax_rate ?? 8.0),
            'auto_issue_on_payment' => $request->boolean('auto_issue_on_payment', true),
            'auto_send_email' => $request->boolean('auto_send_email', true),
            'auto_send_zalo' => $request->boolean('auto_send_zalo', true),
        ];

        $tenant->update(['einvoice_config' => $config]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cấu hình Hóa đơn điện tử thành công!',
                'config' => $config,
            ]);
        }

        return back()->with('success', 'Đã lưu cấu hình kết nối nhà cung cấp Hóa đơn điện tử!');
    }

    /**
     * Cổng tra cứu Hóa đơn điện tử công khai cho khách thuê
     */
    public function lookup(Request $request)
    {
        $code = trim($request->input('code', ''));
        $invoice = null;
        $searched = false;

        if (!empty($code)) {
            $searched = true;
            $invoice = ElectronicInvoice::with(['room.building', 'resident', 'tenant'])
                ->where('lookup_code', $code)
                ->orWhere('tax_authority_code', $code)
                ->orWhere('invoice_number', $code)
                ->first();
        }

        return view('public.invoice_lookup', compact('code', 'invoice', 'searched'));
    }
}
