<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Resident;
use App\Models\Ticket;
use App\Models\UtilityRecord;
use App\Services\AiManagementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResidentPortalController extends Controller
{
    private const SERVICE_FEE = 150000;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
            }

            $user = Auth::user();
            if (!$user->isResident()) {
                $route = match (true) {
                    $user->isAdmin() => 'user.list',
                    $user->canAccessLandlordDashboard() => 'smartroom.admin',
                    default => 'renty.user',
                };

                return redirect()->route($route)->with('error', 'Tài khoản của bạn không có quyền truy cập cổng cư dân.');
            }

            return $next($request);
        });
    }

    /**
     * Cổng dịch vụ cá nhân hóa dành cho cư dân đang thuê phòng (FEAT_27_PORTAL)
     * GET /smartroom/resident/portal
     */
    public function index()
    {
        $resident = $this->currentResident();

        if (!$resident || !$resident->room) {
            return view('resident.portal', [
                'resident' => $resident,
                'room' => null,
                'bills' => collect(),
                'latestBill' => null,
                'unpaidTotal' => 0,
                'contract' => null,
                'contractLocked' => true,
                'errorCode' => 'ERR_27_02',
                'errorMessage' => 'Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt.',
                'tickets' => collect(),
                'statusLabels' => $this->ticketStatusLabels(),
                'tenant' => null,
                'landlord' => null,
                'landlordName' => 'Ban quản lý',
                'landlordPhone' => '0987654321',
                'landlordEmail' => 'hotline@smartroom.local',
            ]);
        }

        $room = $resident->room;
        $tenant = $room->tenant;
        $landlord = $tenant?->users()
            ->whereIn('role', ['landlord', 'unverified_landlord'])
            ->first();
        $landlordProfile = \App\Models\LandlordProfile::where('tenant_id', $tenant?->id)->first();
        $landlordName = $landlordProfile?->full_name 
            ?? $landlord?->name 
            ?? $tenant?->bank_account_name 
            ?? 'Ban quản lý cơ sở';
        $landlordPhone = $landlordProfile?->phone 
            ?? $landlord?->phone 
            ?? $tenant?->phone 
            ?? '0987654321';
        $landlordEmail = $landlord?->email ?? $tenant?->email ?? 'hotline@smartroom.local';

        // Lấy hợp đồng thuê phòng của cư dân
        $contract = Contract::where('resident_id', $resident->id)
            ->where('room_id', $room->id)
            ->orderByRaw("case when status = 'active' then 0 when status = 'pending' then 1 else 2 end")
            ->orderByDesc('end_date')
            ->first();

        // Điều kiện tiên quyết: Hợp đồng thuê phòng phải còn hiệu lực (active hoặc pending)
        $hasActiveContract = $contract && in_array($contract->status, ['active', 'pending']);
        $contractLocked = !$hasActiveContract;

        // Lấy danh sách hóa đơn tiện ích của chính căn phòng cư dân đang thuê (Bảo mật Multi-tenancy)
        $bills = UtilityRecord::with('room')
            ->where('room_id', $room->id)
            ->orderByDesc('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateBill($record));

        $latestBill = $bills->first();

        $tickets = Ticket::with('room')
            ->where('resident_id', $resident->id)
            ->where('room_id', $room->id)
            ->latest()
            ->get();

        $maintenanceTickets = $tickets->where('category', '!=', 'housekeeping');
        $housekeepingTickets = $tickets->where('category', 'housekeeping');

        return view('resident.portal', [
            'resident' => $resident,
            'room' => $room,
            'tenant' => $tenant,
            'landlord' => $landlord,
            'landlordName' => $landlordName,
            'landlordPhone' => $landlordPhone,
            'landlordEmail' => $landlordEmail,
            'bills' => $bills,
            'latestBill' => $latestBill,
            'unpaidTotal' => $bills->where('status', '!=', 'paid')->sum('total_amount'),
            'contract' => $contract,
            'contractLocked' => $contractLocked,
            'errorCode' => $contractLocked ? 'ERR_27_02' : null,
            'errorMessage' => $contractLocked ? 'Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt.' : null,
            'tickets' => $tickets,
            'maintenanceTickets' => $maintenanceTickets,
            'housekeepingTickets' => $housekeepingTickets,
            'statusLabels' => $this->ticketStatusLabels(),
        ]);
    }

    /**
     * Xem danh sách hóa đơn, lịch sử thanh toán và quét mã VietQR cá nhân (FEAT_27_PORTAL)
     * GET /smartroom/resident/invoices
     */
    public function invoices(Request $request)
    {
        $resident = $this->currentResident();

        if (!$resident || !$resident->room) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ERR_27_02',
                    'message' => 'Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt.',
                ], 403);
            }

            return redirect()->route('smartroom.resident.portal');
        }

        $room = $resident->room;
        $tenant = $room->tenant;
        $landlord = $tenant?->users()
            ->whereIn('role', ['landlord', 'unverified_landlord'])
            ->first();
        $landlordProfile = \App\Models\LandlordProfile::where('tenant_id', $tenant?->id)->first();
        $landlordName = $landlordProfile?->full_name ?? $landlord?->name ?? 'Ban quản lý';
        $landlordPhone = $landlordProfile?->phone ?? $landlord?->phone ?? '0987654321';

        // Lấy hóa đơn bảo mật: chỉ lấy hóa đơn của chính căn phòng cư dân đang thuê
        $bills = UtilityRecord::with('room')
            ->where('room_id', $room->id)
            ->orderByDesc('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateBill($record));

        $unpaidTotal = $bills->where('status', '!=', 'paid')->sum('total_amount');
        $latestBill = $bills->first();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'room_number' => $room->room_number,
                'building_name' => $room->building->name ?? null,
                'unpaid_total' => $unpaidTotal,
                'invoices' => $bills,
            ]);
        }

        return view('resident.invoices', [
            'resident' => $resident,
            'room' => $room,
            'tenant' => $tenant,
            'bills' => $bills,
            'latestBill' => $latestBill,
            'unpaidTotal' => $unpaidTotal,
            'landlordName' => $landlordName,
            'landlordPhone' => $landlordPhone,
        ]);
    }

    /**
     * Tải hợp đồng thuê phòng dạng PDF đã ký số (FEAT_27_PORTAL DoD)
     * GET /smartroom/resident/contract/{id}/pdf hoặc GET /smartroom/resident/contract/pdf
     */
    public function downloadContractPdf($id = null)
    {
        $resident = $this->currentResident();
        if (!$resident) {
            abort(403, 'Tài khoản chưa được kích hoạt hồ sơ cư dân.');
        }

        $query = Contract::where('resident_id', $resident->id)
            ->with(['tenant', 'room.building', 'resident']);

        if ($id) {
            $contract = $query->where('id', $id)->firstOrFail();
        } else {
            $contract = $query->orderByRaw("case when status = 'active' then 0 when status = 'pending' then 1 else 2 end")
                ->orderByDesc('end_date')
                ->firstOrFail();
        }

        $pdf = Pdf::loadView('admin.pdf_contract', compact('contract'))->setPaper('a4');

        return $pdf->download('hop_dong_' . $contract->contract_code . '.pdf');
    }

    /**
     * Trả về dữ liệu VietQR Napas 247 cho Modal popup (FEAT_27_PORTAL ERR_27_03)
     * GET /smartroom/resident/bills/{id}/qr-data
     */
    public function billQrData($id)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            return response()->json([
                'success' => false,
                'code' => 'ERR_27_02',
                'message' => 'Hợp đồng thuê của phòng này đã kết thúc hoặc chưa được kích hoạt.',
            ], 403);
        }

        $record = UtilityRecord::where('room_id', $resident->room_id)->find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn không tồn tại hoặc không thuộc phòng của bạn.',
            ], 404);
        }
        $bill = $this->decorateBill($record);

        $bankAccountNo = $resident->tenant?->bank_account_no ?: '1051572297';
        $bankName = $resident->tenant?->bank_name ?: 'Vietcombank (VCB)';
        $accountName = $resident->tenant?->bank_account_name ?: 'BAN QUAN LY SMARTROOM';
        $transferContent = 'Thanh toan phong ' . $resident->room->room_number . ' thang ' . $bill->billing_month;

        return response()->json([
            'success' => true,
            'code' => 'ERR_27_03',
            'message' => 'Hiển thị mã VietQR thanh toán tiền nhà chuẩn Napas 247.',
            'bill' => $bill,
            'qr_url' => $this->vietQrUrl($resident->room->room_number, $bill->billing_month, $bill->total_amount),
            'bank_name' => $bankName,
            'bank_account_no' => $bankAccountNo,
            'bank_account_name' => $accountName,
            'amount' => $bill->total_amount,
            'transfer_content' => $transferContent,
        ]);
    }

    public function storeTicket(Request $request)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            return back()->with('error', 'Tài khoản chưa được gán phòng.');
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = strip_tags(trim(preg_replace('/\s+/u', ' ', $value)));
            }
        }
        $request->merge($input);

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:100',
            'description' => 'required|string|min:10|max:1000',
            'category' => 'required|in:electric,water,furniture,lock,maintenance,housekeeping,other',
            'urgency' => 'nullable|in:normal,urgent,emergency,Bình thường,Gấp,Khẩn cấp',
            'specific_location' => 'nullable|string|max:150',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sự cố cần sửa chữa',
            'title.min' => 'Tiêu đề sự cố phải có từ 5 đến 100 ký tự.',
            'title.max' => 'Tiêu đề sự cố tối đa 100 ký tự.',
            'description.required' => 'Vui lòng mô tả chi tiết sự cố hư hỏng gặp phải.',
            'description.min' => 'Mô tả sự cố phải có ít nhất 10 ký tự',
            'category.required' => 'Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).',
            'category.in' => 'Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).',
            'image.max' => 'Ảnh chụp sự cố hiện trường tối đa 5MB.',
            'image.image' => 'Tệp tải lên phải là hình ảnh hợp lệ (jpeg, jpg, png, webp).',
            'image.mimes' => 'Hình ảnh chỉ chấp nhận định dạng jpeg, jpg, png hoặc webp.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = '/storage/' . $request->file('image')->store('tickets', 'public');
        }

        $urgency = match ($validated['urgency'] ?? 'normal') {
            'urgent', 'Gấp' => 'urgent',
            'emergency', 'Khẩn cấp' => 'emergency',
            default => 'normal',
        };

        $ticket = Ticket::create([
            'tenant_id' => $resident->tenant_id,
            'room_id' => $resident->room_id,
            'resident_id' => $resident->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'urgency' => $urgency,
            'specific_location' => $validated['specific_location'] ?? null,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        try {
            event(new \App\Events\TicketCreated($ticket));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('TicketCreated broadcast failed: ' . $e->getMessage());
        }

        if ($ticket->category === 'housekeeping' && $resident->room) {
            $resident->room->update([
                'cleaning_status' => 'dirty',
                'version' => $resident->room->version + 1,
            ]);
        }

        $successMsg = 'Đã gửi yêu cầu sửa chữa sự cố thành công! Kỹ thuật viên sẽ xử lý sớm nhất.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'code' => 'ERR_28_03',
                'message' => $successMsg,
                'ticket' => $ticket->load(['room', 'resident']),
            ]);
        }

        return redirect()
            ->route('smartroom.resident', ['tab' => 'tickets'])
            ->with('success', $successMsg);
    }

    public function storeHousekeepingRequest(Request $request)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            return back()->with('error', 'Tài khoản chưa được gán phòng.');
        }

        $validated = $request->validate([
            'note' => 'required|string|min:5|max:1000',
            'requested_time' => 'nullable|string|max:100',
            'urgency' => 'nullable|in:normal,urgent,emergency,Bình thường,Gấp,Khẩn cấp',
        ], [
            'note.required' => 'Vui lòng nhập chi tiết yêu cầu dọn dẹp phòng.',
            'note.min' => 'Chi tiết yêu cầu dọn phòng cần ít nhất 5 ký tự.',
            'note.max' => 'Chi tiết yêu cầu dọn phòng tối đa 1000 ký tự.',
        ]);

        $room = $resident->room;
        $note = strip_tags(trim($validated['note']));
        $requestedTime = !empty($validated['requested_time']) ? strip_tags(trim($validated['requested_time'])) : null;
        
        $urgency = match ($validated['urgency'] ?? 'normal') {
            'urgent', 'Gấp' => 'urgent',
            'emergency', 'Khẩn cấp' => 'emergency',
            default => 'normal',
        };

        $description = $requestedTime 
            ? "Thời gian mong muốn: {$requestedTime}\nChi tiết: {$note}" 
            : $note;

        $ticket = Ticket::create([
            'tenant_id' => $resident->tenant_id,
            'room_id' => $resident->room_id,
            'resident_id' => $resident->id,
            'title' => "Yêu cầu dọn dẹp phòng (P.{$room->room_number})",
            'description' => $description,
            'category' => 'housekeeping',
            'urgency' => $urgency,
            'specific_location' => 'Toàn bộ phòng',
            'status' => 'pending',
        ]);

        $room->update([
            'cleaning_status' => 'dirty',
            'version' => $room->version + 1,
        ]);

        try {
            event(new \App\Events\TicketCreated($ticket));
            event(new \App\Events\RoomStatusUpdated($room, $room->status, 'resident_housekeeping_request'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Housekeeping broadcast failed: ' . $e->getMessage());
        }

        $successMsg = 'Đã gửi yêu cầu dọn dẹp phòng thành công! Bộ phận buồng phòng đã tiếp nhận và sẽ thực hiện sớm nhất.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'ticket' => $ticket->load(['room', 'resident']),
            ]);
        }

        return redirect()
            ->route('smartroom.resident', ['tab' => 'housekeeping'])
            ->with('success', $successMsg);
    }

    public function analyzeTicket(Request $request, AiManagementService $aiManagementService)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'description' => 'required|string|min:5|max:1000',
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $aiManagementService->analyzeMaintenanceTicket(
                strip_tags($validated['description']),
                isset($validated['title']) ? strip_tags($validated['title']) : null
            ),
        ]);
    }

    public function billQr($id)
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            abort(404);
        }

        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            abort(404);
        }

        if (!in_array(($resident->tenant?->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true)) {
            return redirect()
                ->route('smartroom.resident.portal')
                ->with('error', 'Chủ trọ đang hoàn tất xác minh nhận tiền. Vui lòng liên hệ ban quản lý để nhận hướng dẫn thanh toán.');
        }

        $record = UtilityRecord::where('room_id', $resident->room_id)->findOrFail($id);
        $bill = $this->decorateBill($record);

        return view('resident.qr', [
            'resident' => $resident,
            'bill' => $bill,
            'qrUrl' => $this->vietQrUrl($resident->room->room_number, $bill->billing_month, $bill->total_amount),
            'staticQrUrl' => 'https://img.vietqr.io/image/VCB-1051572297-compact.png',
        ]);
    }

    private function currentResident(): ?Resident
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return Resident::with(['room.building', 'tenant'])
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);

                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }

                if ($user->email) {
                    $query->orWhere('email', $user->email);
                }
            })
            ->first();
    }

    private function decorateBill(UtilityRecord $record): object
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        if ($electricityUsage === 0 && !empty($record->electricity_usage)) {
            $electricityUsage = (int) $record->electricity_usage;
        }
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        if ($waterUsage === 0 && !empty($record->water_usage)) {
            $waterUsage = (int) $record->water_usage;
        }
        $roomAmount = (int) optional($record->room)->price;
        $electricityPrice = (int) ($record->electricity_price ?: 3500);
        $waterPrice = (int) ($record->water_price ?: 25000);
        $electricityAmount = $electricityUsage * $electricityPrice;
        $waterAmount = $waterUsage * $waterPrice;
        $totalAmount = $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE;

        return (object) [
            'id' => $record->id,
            'billing_month' => $record->billing_month,
            'status' => $record->status,
            'status_label' => $this->billStatusLabels()[$record->status] ?? $record->status,
            'room_amount' => $roomAmount,
            'old_electricity' => (int) $record->old_electricity,
            'new_electricity' => (int) $record->new_electricity,
            'electricity_price' => (int) $record->electricity_price,
            'electricity_usage' => $electricityUsage,
            'electricity_amount' => $electricityAmount,
            'old_water' => (int) $record->old_water,
            'new_water' => (int) $record->new_water,
            'water_price' => (int) $record->water_price,
            'water_usage' => $waterUsage,
            'water_amount' => $waterAmount,
            'service_amount' => self::SERVICE_FEE,
            'total_amount' => $totalAmount,
            'payment_date' => $record->payment_date,
            'payment_method' => $record->payment_method,
        ];
    }

    private function vietQrUrl(string $roomNumber, string $billingMonth, int $amount): string
    {
        $addInfo = rawurlencode('Thanh toan phong ' . $roomNumber . ' thang ' . $billingMonth);

        return "https://img.vietqr.io/image/VCB-1051572297-compact.png?amount={$amount}&addInfo={$addInfo}";
    }

    private function billStatusLabels(): array
    {
        return [
            'sent' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'overdue' => 'Quá hạn',
        ];
    }

    private function ticketStatusLabels(): array
    {
        return [
            'pending' => 'Chờ tiếp nhận',
            'processing' => 'Đang xử lý',
            'resolved' => 'Đã hoàn thành',
        ];
    }

    public function requestRenewal(Request $request, $id)
    {
        $resident = $this->currentResident();
        if (!$resident) {
            return back()->with('error', 'Không tìm thấy thông tin cư dân.');
        }

        $contract = Contract::where('resident_id', $resident->id)
            ->findOrFail($id);

        $request->validate([
            'renewal_months' => 'required|integer|min:1|max:60',
            'renewal_note' => 'nullable|string|max:1000',
        ]);

        $contract->update([
            'renewal_status' => 'requested',
            'renewal_months' => $request->renewal_months,
            'renewal_note' => strip_tags(trim($request->renewal_note)),
        ]);

        return redirect()
            ->route('smartroom.resident', ['tab' => 'contract'])
            ->with('success', 'Gửi yêu cầu gia hạn hợp đồng thành công! Chủ nhà sẽ xem xét.');
    }
}
