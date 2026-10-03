<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Bill;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResidentController extends Controller
{
    // Lấy thông tin Resident gắn liền với tài khoản đăng nhập
    private function getResident()
    {
        $userId = Auth::id();
        $resident = Resident::with(['room.building', 'tenant'])->where('user_id', $userId)->where('status', 'active')->first();

        if (!$resident) {
            abort(response()->json([
                'success' => false,
                'message' => 'Tài khoản chưa được kích hoạt thành Cư dân nội bộ bởi Chủ trọ.'
            ], 403));
        }

        return $resident;
    }

    // 1. Dashboard cư dân
    public function dashboard()
    {
        $res = $this->getResident();
        $room = $res->room;

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Cư dân chưa được chỉ định phòng trọ.'
            ], 412);
        }

        // Lấy hợp đồng active
        $contract = Contract::where('resident_id', $res->id)->where('room_id', $room->id)->where('status', 'active')->first();

        // Lấy hóa đơn chưa thanh toán mới nhất
        $unpaidBill = Bill::where('room_id', $room->id)->where('status', '!=', 'paid')->orderBy('billing_month', 'desc')->first();

        // Lấy 3 hóa đơn gần nhất
        $recentBills = Bill::where('room_id', $room->id)->orderBy('billing_month', 'desc')->take(3)->get();

        return response()->json([
            'success' => true,
            'resident' => [
                'id' => $res->id,
                'name' => $res->name,
                'phone' => $res->phone,
                'email' => $res->email,
                'start_date' => $res->start_date,
            ],
            'room' => [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => $room->price,
                'area' => $room->area,
                'amenities' => $room->amenities,
                'building' => [
                    'name' => $room->building->name,
                    'address' => $room->building->address,
                ]
            ],
            'contract' => $contract ? [
                'contract_code' => $contract->contract_code,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'deposit' => $contract->deposit,
                'status' => $contract->status,
                'terms' => $contract->terms
            ] : null,
            'unpaid_bill' => $unpaidBill,
            'recent_bills' => $recentBills
        ]);
    }

    // 2. Lịch sử chi phí để vẽ biểu đồ
    public function bills()
    {
        $res = $this->getResident();
        $room = $res->room;

        if (!$room) {
            return response()->json(['success' => true, 'bills' => [], 'chart_data' => []]);
        }

        $bills = Bill::where('room_id', $room->id)->orderBy('billing_month', 'asc')->get();

        // Dữ liệu biểu đồ so sánh chi phí điện nước và tổng tiền qua các tháng
        $months = [];
        $electricityCosts = [];
        $waterCosts = [];
        $totalAmounts = [];

        foreach ($bills as $b) {
            $monthNum = explode('-', $b->billing_month)[1];
            $months[] = 'Tháng ' . intval($monthNum);
            $electricityCosts[] = $b->electricity_cost;
            $waterCosts[] = $b->water_cost;
            $totalAmounts[] = $b->total_amount;
        }

        return response()->json([
            'success' => true,
            'bills' => $bills->reverse()->values(), // Trả về danh sách hoá đơn mới nhất lên đầu
            'chart_data' => [
                'labels' => $months,
                'electricity' => $electricityCosts,
                'water' => $waterCosts,
                'total' => $totalAmounts
            ]
        ]);
    }

    // 3. Báo cáo sự cố (gửi Ticket)
    public function storeTicket(Request $request)
    {
        $res = $this->getResident();
        $room = $res->room;

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng cho cư dân này'
            ], 412);
        }

        $request->validate([
            'title' => 'required|string|min:5|max:100',
            'description' => 'required|string|min:10',
            'category' => 'required|in:điện,nước,nội thất,khóa cửa,khác,electric,water,furniture,lock,maintenance,housekeeping,other',
            'urgency' => 'nullable|in:normal,urgent,emergency,Bình thường,Gấp,Khẩn cấp',
            'specific_location' => 'nullable|string|max:150',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sự cố cần sửa chữa',
            'title.min' => 'Tiêu đề sự cố phải có từ 5 đến 100 ký tự.',
            'title.max' => 'Tiêu đề sự cố tối đa 100 ký tự.',
            'description.required' => 'Vui lòng mô tả chi tiết sự cố hư hỏng gặp phải.',
            'description.min' => 'Mô tả sự cố phải có ít nhất 10 ký tự',
            'category.required' => 'Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).',
            'category.in' => 'Vui lòng chọn loại sự cố (Điện, Nước, Khóa cửa, Khác...).',
            'image.max' => 'Ảnh chụp sự cố hiện trường tối đa 5MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $urgency = match ($request->input('urgency', 'normal')) {
            'urgent', 'Gấp' => 'urgent',
            'emergency', 'Khẩn cấp' => 'emergency',
            default => 'normal',
        };

        $ticket = Ticket::create([
            'tenant_id' => $res->tenant_id,
            'room_id' => $room->id,
            'resident_id' => $res->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'urgency' => $urgency,
            'specific_location' => $request->specific_location,
            'image_path' => $imagePath ? '/storage/' . $imagePath : null,
            'status' => 'pending'
        ]);

        try {
            event(new \App\Events\TicketCreated($ticket));
        } catch (\Throwable $e) {
            Log::warning('TicketCreated broadcast failed: ' . $e->getMessage());
        }

        // Gửi thông báo tự động cho chủ trọ qua Telegram Bot khi có ticket mới
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');
        if ($botToken && $chatId) {
            $msg = "🚨 [SmartRoom] BÁO HỎNG MỚI từ cư dân!\n"
                 . "Phòng: {$room->room_number} (Tòa: {$room->building->name})\n"
                 . "Cư dân: {$res->name} ({$res->phone})\n"
                 . "Danh mục: {$ticket->category}\n"
                 . "Tiêu đề: {$ticket->title}\n"
                 . "Mô tả: {$ticket->description}";
            try {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $msg
                ]);
            } catch (\Exception $e) {
                Log::error("Telegram New Ticket Notify error: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'code' => 'ERR_28_03',
            'message' => 'Đã gửi yêu cầu sửa chữa sự cố thành công! Kỹ thuật viên sẽ xử lý sớm nhất.',
            'ticket' => $ticket
        ], 201);
    }

    // 4. Lấy VietQR của một hoá đơn cụ thể
    public function getBillQr($id)
    {
        $res = $this->getResident();
        $room = $res->room;

        $bill = Bill::where('room_id', $room->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'bill_id' => $bill->id,
            'billing_month' => $bill->billing_month,
            'total_amount' => $bill->total_amount,
            'vietqr_url' => $bill->vietqr_url
        ]);
    }
}
